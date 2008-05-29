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


chdir(dirname(__FILE__));

$panel_type="cronjob";
require("../shared/autoSQLconfig.php"); // Our main configuration file
require_once("$dtcshared_path/dtc_lib.php");

// Daemon for pulling stats from dtc-xen servers
// Damien Mascord <damien@gplhost.com>

// setup syslog params
define_syslog_variables();
// open syslog, include the process ID and also send
// the log to standard error, and use a user defined
// logging mechanism
openlog("dtc-stats-daemon", LOG_PID | LOG_PERROR, LOG_LOCAL0);

/**
 * Run the current script as a daemon.  Used mostly (always?) for command line scripts.
 *
 * @author      Matt Curry <matt@mcurry.net>
 * @version     1.0.0
 */
 
function daemonize() {
   $child = pcntl_fork();
   if($child) {
       exit; // kill parent
   }
   posix_setsid(); // become session leader
   chdir("/");
   umask(0); // clear umask
   return posix_getpid();
}

// daemonize the process so it's sits in the background
// daemonize();


error_reporting(E_ALL);

$conf_time_delay_in_seconds=60;

syslog(LOG_INFO, "dtc-stats-daemon starting up...");

$last_loop = 0;
// loop until we want to shutdown... 
$shutdown = false;
while (!$shutdown){
	if ($last_loop > 0){
		$time_elapsed_since_last_run = time() - $last_loop;
		echo "Time since last run $time_elapsed_since_last_run seconds...\n";

		// if the time elapsed is less than 10 minutes, sleep until it is
		if ($time_elapsed_since_last_run < $conf_time_delay_in_seconds){
			$time_to_sleep = $conf_time_delay_in_seconds - $time_elapsed_since_last_run;
			echo "Sleeping for " . $time_to_sleep . " seconds...\n";
			sleep ($time_to_sleep);
		}
	}
	$last_loop = time();

	if (!mysql_ping()) {
	    echo 'Lost connection to DB!';
            syslog(LOG_WARNING, "Lost connection to DB! Will retry later...");
	    continue;
	}

	$vps_query = "SELECT * FROM $pro_mysql_vps_table;";
	$vps_result = mysql_query($vps_query)or die("Cannot query $query !!!".mysql_error());
	$vps_num_rows = mysql_num_rows($vps_result);
	echo "We have to process $vps_num_rows VPS accounts...\n";
	for ($i=0;$i<$vps_num_rows;$i++){
		// sleep 5 seconds between every soap call, we don't want to kill the soap servers
		sleep (5);
		echo "$i/$vps_num_rows\n";
		$vps_row = mysql_fetch_array($vps_result);
		// print_r ($row);
		$vps_server = $vps_row['vps_server_hostname'];
		$vps_name = "xen" . $vps_row['vps_xen_name'];
		$cpu_usage = 0;
		$io_usage_disk = 0;
		$io_usage_swap = 0;
		$network_usage_in = 0;
		$network_usage_out = 0;

		echo "Fetching stats from $vps_server for $vps_name...\n";
		$soap_client = connectToVPSServer($vps_server);
		$r = $soap_client->call("getCPUUsage",array("vpsname" => $vps_name),"","","");
		$err = $soap_client->getError();
		if ($err) {
			echo $err;
		} else {
			echo "Soap client returned: $r\n";
			$cpu_usage = $r;
		}
			
		$r = $soap_client->call("getNetworkUsage",array("vpsname" => $vps_name),"","","");
		$err = $soap_client->getError();
		if ($err) {
			echo $err;
		} else {
			echo "Soap client returned: $r\n";
			$network_usage_array=split(",", $r);
			$network_usage_in=$network_usage_array[0];
			$network_usage_out=$network_usage_array[1];
		}
			
		$r = $soap_client->call("getIOUsage",array("vpsname" => $vps_name),"","","");
		$err = $soap_client->getError();
		if ($err) {
			echo $err;
		} else {
			echo "Soap client returned: $r\n";
			if ($r != "NOTOK"){
				$io_usage_disk = $r[0];
				$io_usage_swap= $r[1];
			}
		}
			
		echo "Stats are as follows:\n";
		echo " - CPU Usage: $cpu_usage\n";
		echo " - Network Incoming: $network_usage_in\n";
		echo " - Network Outgoing: $network_usage_out\n";
		echo " - Disk IO Usage: $io_usage_disk\n";
		echo " - Swap IO Usage: $io_usage_swap\n";			
			
		// get the previous values in the table for calculation purposes
		$current_month = date("m");
		$current_year = date("Y");

		$last_query = "SELECT * from vps_stats where vps_server_hostname='$vps_server' and vps_xen_name='$vps_name' and month='$current_month' and year='$current_year';";
		$last_result = mysql_query($last_query)or print("Cannot query $query !!!".mysql_error());
		$last_num_rows = mysql_num_rows($last_result);
		// reset this variable
		$vps_last_run = 0;
			
		// we need to insert a row here if it doesn't exist yet
		if ($last_num_rows == 0){
				$insert_query = "INSERT INTO vps_stats (vps_server_hostname,vps_xen_name,month,year) values ('$vps_server','$vps_name','$current_month','$current_year');";
				$insert_result = mysql_query($insert_query)or print("Cannot query $query !!!".mysql_error());
		}else if ($last_num_rows == 1) {
			$last_row = mysql_fetch_array($last_result);
			$vps_last_run = $last_row['last_run'];
			$vps_last_cpu = $last_row['cputime_last'];
			$vps_last_network_in = $last_row['network_in_last'];
			$vps_last_network_out = $last_row['network_out_last'];
			$vps_last_diskio = $last_row['diskio_last'];
			$vps_last_swapio = $last_row['swapio_last'];
			echo "Last values are as follows:\n";
			echo " - CPU Usage: $vps_last_cpu\n";
			echo " - Network Incoming: $vps_last_network_in\n";
			echo " - Network Outgoing: $vps_last_network_out\n";
			echo " - Disk IO Usage: $vps_last_diskio\n";
			echo " - Swap IO Usage: $vps_last_swapio\n";

		}else{
			echo "Corrupt vps_stats table, please check...\n";
		}
				
		$current_time = time();
		// if we have actually run before, then we can calculate stats since last run
		if (isset($vps_last_run) && $vps_last_run > 0){
			echo "We have a last run...\n";
			// ignore anything that has a last run of zero		
			$time_now = $current_time;		
			echo "Time now $time_now\n";
			echo "Time last run $vps_last_run\n";
								
			$time_diff = $time_now - $vps_last_run;	
				
			// first calculate $cpu_diff
			$cpu_diff = 0;
			if ($vps_last_cpu > 0){
				if ($vps_last_cpu < $cpu_usage){
					$cpu_diff = $cpu_usage - $vps_last_cpu;
				} elseif ($vps_last_cpu != $cpu_usage) {
					$cpu_diff = $cpu_usage;
				}
			}
			echo "Used $cpu_diff CPU time over $time_diff seconds...\n";
				
			echo "CPU% " . ($cpu_diff / $time_diff) . "\n";
				
			// then calculate network bytes change
			$network_in_diff = 0;
			if ($vps_last_network_in > 0){
				if ($vps_last_network_in < $network_usage_in){
					$network_in_diff = $network_usage_in - $vps_last_network_in;
				} elseif ($vps_last_network_in != $network_usage_in) {
					$network_in_diff = $network_usage_in;
				}
			}
				
			echo "Used $network_in_diff network incoming over $time_diff seconds...\n";
				
			$network_out_diff = 0;
			if ($vps_last_network_out > 0){
				if ($vps_last_network_out < $network_usage_out){
					$network_out_diff = $network_usage_out - $vps_last_network_out;
				} elseif ($vps_last_network_out != $network_usage_out) {
					$network_out_diff = $network_usage_out;
				}
			}
				
			echo "Used $network_out_diff network outgoing over $time_diff seconds...\n";
				
			// then calculate disk IO and swap IO
			$diskio_diff = 0;
			if ($vps_last_diskio > 0){
				if ($vps_last_diskio < $io_usage_disk){
					$diskio_diff = $io_usage_disk - $vps_last_diskio;
				} elseif ($vps_last_diskio != $io_usage_disk) {
					$diskio_diff = $io_usage_disk;
				}
			}
				
			echo "Used $diskio_diff disk IO over $time_diff seconds...\n";
			$swapio_diff = 0;
			if ($vps_last_swapio > 0){
				if ($vps_last_swapio < $io_usage_swap){
					$swapio_diff = $io_usage_swap - $vps_last_swapio;
				} elseif ($vps_last_swapio != $io_usage_swap) {
					$swapio_diff = $io_usage_swap;
				}
			}
				
			echo "Used $swapio_diff swap IO over $time_diff seconds...\n";
				
			// finally populate the details into the table
			$update_query = "UPDATE vps_stats set last_run='$current_time',cputime_last='$cpu_usage',cpu_usage=cpu_usage + '$cpu_diff',network_in_last='$network_usage_in',network_out_last='$network_usage_out',";
			$update_query .= "network_in_count=network_in_count+'$network_in_diff',network_out_count=network_out_count+'$network_out_diff',diskio_last='$io_usage_disk',swapio_last='$io_usage_swap',diskio_count=diskio_count + '$diskio_diff',swapio_count=swapio_count + '$swapio_diff'";
			$update_query .= " where vps_server_hostname='$vps_server' and vps_xen_name='$vps_name' and month='$current_month' and year='$current_year';";
			$update_result = mysql_query($update_query)or print("Cannot query $query !!!".mysql_error());
		}else{
			echo "We don't have a last run...\n";
			// otherwise we just need to zero out the count columns, and have the last columns updated
			$update_query = "UPDATE vps_stats set last_run='$current_time',cputime_last='$cpu_usage',cpu_usage='0',network_in_last='$network_usage_in',network_out_last='$network_usage_out',";
			$update_query .= "network_in_count='0',network_out_count='0',diskio_last='$io_usage_disk',swapio_last='$io_usage_swap',diskio_count='0',swapio_count='0'";
			$update_query .= " where vps_server_hostname='$vps_server' and vps_xen_name='$vps_name' and month='$current_month' and year='$current_year';";
			$update_result = mysql_query($update_query)or print("Cannot query $query !!!".mysql_error());
		}
	}
	
}
syslog(LOG_INFO, "dtc-stats-daemon shutting down...");

?> 
