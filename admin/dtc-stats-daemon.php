#!/usr/bin/env php
<?php

/*
Rudd-O SAYS:

FIXME

I have removed the xenupdate.py and xengraph.py from dtc-xen, and moved the
data collection to dtc-xen itself.  dtc-xen samples the data every sixty
seconds and dumps the sample into a directory.

When this "daemon" call getCollectedPerformanceData() via SOAP on to the
dtc-xen server running at the node, dtc-xen feeds you the data (the format is
documented in the dtc-xen.py file), and then DELETES IT from the node.

Your task is to get that data, insert it into the database on a stats table,
then do some code that graphs that data so DTC can present that to the user in
his VPS management screen.  The proposed data table row would be something
like:

[ nodehostname,owner,domUname,datatype,measurement ]

(the owner column is my suggestion so we don't accrue accounting data to the
wrong owner, in case a VPS is canceled and then taken up by a different
customer)

where measurement is probably going to need 64 bits of storage in the case of
ints, or some arbitrary precision number to avoid wraparounds (most likely).
The measurements are NOT DELTAS -- for sequential measurements of the same
type, you will most of the time expect an increment: 52.6, 55.5, 66.8... and
so on.  However, measurements CAN wrap around in the following scenarios:

 - the /sys or /proc counter itself wraps around
 - the node hosting the VPSes is rebooted
 - the VPS itself is powered off and restarted

(Python's integer data types are mostly safe from wraparounds (ints autocast
to longs) you shouldn't have a problem in dtc-xen because of it, but maybe
the SOAP transport is vulnerable to integer overflows, so heads up there)

What this means, in practice, is that you will have a series of measurements in
the database table, and where the N value will be a large one, the N + 1 will
suddenly be small or zero.  Since:

 - for accounting, you need a sum of deltas, not of measurements
 - for charting, you need to plot the deltas, not the measurements

you can get the deltas with an algorithm like this

def deltas ( sequential_measurements ):
	for num,val in enumerate(sequential_measurements):
		try: delta = sequential_measurements[num+1] - val
		except IndexError: continue
		if delta < 0: delta = sequential_measurements[num+1]
		yield delta

>>> vals = [1,2,3,4,5,6,7,8,9,0,10,56,1,45,46,2,3,4,5]
>>> ds = [ d for d in deltas(vals) ]
>>> print ds
[1, 1, 1, 1, 1, 1, 1, 1, 0, 10, 46, 1, 44, 1, 2, 1, 1, 1]

Please note how from, 56 to 1 there is a delta of 1 (if we summed the
measurements blindly, we would get a negative number in this case)

In practical terms, we only lose measurement data from the point of last
measurement (maximum fifty-nine seconds in case of VPS/node reboots), or the
difference between MAXINT-1 and any arbitrary very large measurement (in case
of a counter wraparound) both of which are acceptable inaccuracies.

So, with this in hand, we can create a world-class statistics system for DTC.
Store the data in an RRD file or a table, keyed by VPS+owner (the RRD file is
probably going to be much faster and it may have provisions for wraparounds
and deltas), then do the graphing and accounting that we now have done but
only half-assed.

Who's up to the challenge?

*/

// cd to /usr/share/dtc/admin so we can do the includes later on
chdir(dirname(__FILE__));
if (!is_file("../shared/autoSQLconfig.php")) { chdir("../share/dtc/admin"); }

$panel_type="cronjob";
require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

function daemonize() {
	$child = pcntl_fork();
	if($child) {
		exit(0); // kill parent
	}
	posix_setsid(); // become session leader
	umask(0); // clear umask
	return posix_getpid();
}

// daemonize the process so it's sits in the background
// we don't do it in Debian since there, we use start-stop-daemon that does all the job
if( !file_exists("/etc/debian_version")){
	$pid = daemonize();
	$fp = fopen("/var/run/dtc-stats-daemon.pid","w");
	fwrite($fp,$pid."\n");
	fclose($fp);
}

$log_fp = fopen("/var/log/dtc-stats-daemon.log","a+");

error_reporting(E_ALL);

$conf_time_delay_in_seconds=62;

fwrite($log_fp, date("Y-m-d H:i:s")." dtc-stats-daemon starting up...\n");

$last_loop = 0;
// loop until we want to shutdown... 
$shutdown = false;
while (!$shutdown){
	if ($last_loop > 0){
		$time_elapsed_since_last_run = time() - $last_loop;

		// if the time elapsed is less than 10 minutes, sleep until it is
		if ($time_elapsed_since_last_run < $conf_time_delay_in_seconds){
			$time_to_sleep = $conf_time_delay_in_seconds - $time_elapsed_since_last_run;
			fwrite($log_fp, date("Y-m-d H:i:s")." Time since last run $time_elapsed_since_last_run seconds: sleeping for " . $time_to_sleep . " seconds...\n");
			sleep ($time_to_sleep);
		}else{
			fwrite($log_fp, date("Y-m-d H:i:s")." Less than one minute since last run: will continue without sleeping...\n");
		}
	}
	$last_loop = time();

	if (!mysql_ping()) {
		fwrite($log_fp, date("Y-m-d H:i:s")." Lost connection to DB! Trying to reconnect...\n");
		mysql_close();
		$ressource_id = mysql_connect($conf_mysql_host, $conf_mysql_login, $conf_mysql_pass);
		if($ressource_id === FALSE){
			fwrite($log_fp, date("Y-m-d H:i:s")." ".mysql_error()."\n");
			continue;
		}else{
			fwrite($log_fp, date("Y-m-d H:i:s")." Reconnect successful!\n");
			$ressource_db = mysql_select_db($conf_mysql_db);
			if($ressource_db === FALSE){
				fwrite($log_fp, date("Y-m-d H:i:s")." ".mysql_error()."\n");
			}
		}
	}

	$vps_query = "SELECT * FROM $pro_mysql_vps_server_table;";
	if( ($vps_servers_result = mysql_query($vps_query)) === FALSE){
		fwrite($log_fp, date("Y-m-d H:i:s")." ".mysql_error()."\n");
		continue;
	}
	//die("Cannot query $query !!!".mysql_error());
	$vps_servers_num_rows = mysql_num_rows($vps_servers_result);
	for ($i=0;$i<$vps_servers_num_rows;$i++){
		// sleep 5 seconds between every soap call, we don't want to kill the soap servers
		// sleep (5);
		$all_recs = array();
		$vps_servers_row = mysql_fetch_array($vps_servers_result);
		$vps_server = $vps_servers_row['hostname'];

		fwrite($log_fp, date("Y-m-d H:i:s")." Fetching stats from server $i/$vps_servers_num_rows: $vps_server...\n");
		$soap_client = connectToVPSServer($vps_server);
		$r = $soap_client->call("getCollectedPerformanceData",array("count" => 64),"","","");
		$err = $soap_client->getError();
		if ($err) {
			fwrite($log_fp, date("Y-m-d H:i:s")." ".$err);
			continue;
		}
		//
		// Save collected datas in /var/lib/dtc/dtc-xenservers-rrds
		//

		// Create the folder if it doesn't exists
		if( ! file_exists("/var/lib/dtc/dtc-xenservers-rrds/$vps_server") ){
			mkdir("/var/lib/dtc/dtc-xenservers-rrds/$vps_server",0755);
		}
		if( !is_array($r) ){
			fwrite($log_fp, date("Y-m-d H:i:s")." No data in this fetch!\n");
			continue;
		}

		// Records are ordered by timestamps, we need something ordered by VPS name,
		// so we do the maths...
		$num_records = sizeof($r);
		fwrite($log_fp, date("Y-m-d H:i:s")." Now ordering $num_records record(s)...\n");
		for($rec=0;$rec<$num_records;$rec++){
			$cur = $r[$rec];
			// If there is no VPS running, then $cur is not an array: we have to test that!
			if( is_array($cur) ){
				$keys = array_keys($cur);
				$num_vps = sizeof($keys);
				for($vps=0;$vps<$num_vps;$vps++){
					$vps_data = $cur[ $keys[$vps] ];

					$all_recs[ $keys[$vps] ][] = $vps_data;
				}
			}
		}
		// Now for each VPS, let's record all data collected
		$num_vps = sizeof($all_recs);
		$keys = array_keys($all_recs);
		fwrite($log_fp, date("Y-m-d H:i:s")." $num_vps VPS...");
		for($vps=0;$vps<$num_vps;$vps++){
			$vps_name = $keys[$vps];
			$vps_number = substr($vps_name,3);
			$all_vps_data = $all_recs[ $vps_name ];
			$vps_num_recs = sizeof($all_vps_data);

			// Let's calculate the full path of the filename for each of the 5 rrd files per VPS
			$cpu_file = "/var/lib/dtc/dtc-xenservers-rrds/$vps_server/". $vps_name . "-cpu.rrd";
			$netin_file = "/var/lib/dtc/dtc-xenservers-rrds/$vps_server/". $vps_name . "-netin.rrd";
			$netout_file = "/var/lib/dtc/dtc-xenservers-rrds/$vps_server/". $vps_name . "-netout.rrd";
			$hdd_file = "/var/lib/dtc/dtc-xenservers-rrds/$vps_server/". $vps_name . "-iofs.rrd";
			$swap_file = "/var/lib/dtc/dtc-xenservers-rrds/$vps_server/". $vps_name . "-ioswap.rrd";
			// Now we create all the rrd files if they do not exist yet.
			// CPU rrd
			if(!file_exists($cpu_file)){
				$cmd = "rrdtool create $cpu_file --step 60 DS:cpuseconds:GAUGE:900:0:120 RRA:AVERAGE:0.5:1:20160 ".
"RRA:AVERAGE:0.5:30:2016 RRA:AVERAGE:0.5:60:105120 RRA:MAX:0.5:1:1440 RRA:MAX:0.5:30:2016 RRA:MAX:0.5:60:105120";
				$result = exec($cmd,$lines,$return_val);
			}
			// netin bytes
			if(!file_exists($netin_file)){
				$cmd = "rrdtool create $netin_file --step 60 DS:netbytesin:GAUGE:900:0:U RRA:AVERAGE:0.5:1:20160 ".
"RRA:AVERAGE:0.5:30:2016 RRA:AVERAGE:0.5:60:105120 RRA:MAX:0.5:1:1440 RRA:MAX:0.5:30:2016 RRA:MAX:0.5:60:105120";
				$result = exec($cmd,$lines,$return_val);
			}
			// netout bytes
			if(!file_exists($netout_file)){
				$cmd = "rrdtool create $netout_file --step 60 DS:netbytesout:GAUGE:900:0:U RRA:AVERAGE:0.5:1:20160 ".
"RRA:AVERAGE:0.5:30:2016 RRA:AVERAGE:0.5:60:105120 RRA:MAX:0.5:1:1440 RRA:MAX:0.5:30:2016 RRA:MAX:0.5:60:105120";
				$result = exec($cmd,$lines,$return_val);
			}
			// swap sectors
			if(!file_exists($swap_file)){
				$cmd = "rrdtool create $swap_file --step 60 DS:swapsects:GAUGE:900:0:U RRA:AVERAGE:0.5:1:20160 ".
"RRA:AVERAGE:0.5:30:2016 RRA:AVERAGE:0.5:60:105120 RRA:MAX:0.5:1:1440 RRA:MAX:0.5:30:2016 RRA:MAX:0.5:60:105120";
				$result = exec($cmd,$lines,$return_val);
			}
			// filesystem sectors
			if(!file_exists($hdd_file)){
				$cmd = "rrdtool create $hdd_file --step 60 DS:fssects:GAUGE:900:0:U RRA:AVERAGE:0.5:1:20160 ".
"RRA:AVERAGE:0.5:30:2016 RRA:AVERAGE:0.5:60:105120 RRA:MAX:0.5:1:1440 RRA:MAX:0.5:30:2016 RRA:MAX:0.5:60:105120";
				$result = exec($cmd,$lines,$return_val);
			}


			// Now we need to record all the data by packets of 256 records,
			// in order to make sure that we do not have a command line too big.
			// Now, using $NUM_RECS_AT_A_TIME = 60 instead of 256 so the derive is
			// only 1 hour for the UPDATE query (maximum one hour accounting error mistake in a month log)
			$NUM_RECS_AT_A_TIME = 60;
			$remaining_256_loop = $vps_num_recs % $NUM_RECS_AT_A_TIME;
			$num_256_loop = ($vps_num_recs - $remaining_256_loop) / $NUM_RECS_AT_A_TIME;
			for($z=0;$z<=$num_256_loop;$z++){
				$cmd_cpu    = "rrdtool update ".$cpu_file;
				$cmd_netin  = "rrdtool update ".$netin_file;
				$cmd_netout = "rrdtool update ".$netout_file;
				$cmd_swap   = "rrdtool update ".$swap_file;
				$cmd_hdd    = "rrdtool update ".$hdd_file;
				if($z == $num_256_loop){
					$num_iter = $remaining_256_loop;
				}else{
					$num_iter = $NUM_RECS_AT_A_TIME;
				}
				$total_cpu = 0;
				$total_netin = 0;
				$total_netout = 0;
				$total_swap_sec = 0;
				$total_hdd_sec = 0;
				for($y=0;$y<$num_iter;$y++){
					$vps_data = $all_vps_data[ $z*$NUM_RECS_AT_A_TIME + $y ];
					$vps_cpu = $vps_data["diff_cpu_time"];
					$vps_net_in = $vps_data["diff_net_inbytes"];
					$vps_net_out = $vps_data["diff_net_outbytes"];
					$vps_swap_sectors = $vps_data["diff_swap_sectors"];
					$vps_fs_sectors = $vps_data["diff_filesystem_sectors"];
					$temp_array = explode(".",$vps_data["timestamp"]);
					$timestamp = $temp_array[0];

					$cmd_cpu    .= " $timestamp:$vps_cpu";
					$cmd_netin  .= " $timestamp:$vps_net_in";
					$cmd_netout .= " $timestamp:$vps_net_out";
					$cmd_swap   .= " $timestamp:$vps_swap_sectors";
					$cmd_hdd    .= " $timestamp:$vps_fs_sectors";
					$total_cpu += $vps_cpu;
					$total_netin += $vps_net_in;
					$total_netout += $vps_net_out;
					$total_swap_sec += $vps_swap_sectors;
					$total_hdd_sec += $vps_fs_sectors;
				}
				$result = exec($cmd_cpu,$lines,$return_val);
				$result = exec($cmd_netin,$lines,$return_val);
				$result = exec($cmd_netout,$lines,$return_val);
				$result = exec($cmd_swap,$lines,$return_val);
				$result = exec($cmd_hdd,$lines,$return_val);

				// Create a record if it doesn't exists
				// An INSERT IGNORE should be faster than a SELECT, then checking if the row exists...
				$q2 = "INSERT IGNORE INTO $pro_mysql_vps_stats_table (vps_server_hostname,vps_xen_name,month,year,cpu_usage,network_in_count,network_out_count,diskio_count,swapio_count)
VALUES ('".$vps_servers_row["hostname"]."','xen$vps_number','".date("m",$timestamp)."','".date("Y",$timestamp)."','0','0','0','0','0');";
				if(mysql_query($q2) === FALSE){
					continue;
				}
				//die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
				$q2 = "UPDATE $pro_mysql_vps_stats_table
SET cpu_usage=cpu_usage + '$vps_cpu', network_in_count=network_in_count + '$vps_net_in', network_out_count=network_out_count + '$vps_net_out',
diskio_count=diskio_count + '$vps_fs_sectors', swapio_count=swapio_count + '$vps_swap_sectors'
WHERE vps_server_hostname='".$vps_servers_row["hostname"]."' AND vps_xen_name='xen".$vps_number."' AND month='".date("m",$timestamp)."' AND year='".date("Y",$timestamp)."'";
				$flag = 0;
				if(mysql_query($q2) === FALSE){
					continue;
				}
				//die("Cannot query $q2 line ".__LINE__." file ".__FILE__." sql said: ".mysql_error());
			}
		}
		fwrite($log_fp, " recorded\n");
	}
	mysql_free_result($vps_servers_result);
}
fclose($log_fp);
fwrite($log_fp, date("Y-m-d H:i:s")." dtc-stats-daemon shutting down...\n");
unlink("/var/run/dtc-stats-daemon.pid");

?> 
