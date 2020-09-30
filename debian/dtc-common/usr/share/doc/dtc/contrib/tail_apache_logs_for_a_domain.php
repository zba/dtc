<?php
    if ( file_exists("/usr/share/dtc/shared/mysql_config.php") )
        include("/usr/share/dtc/shared/mysql_config.php");
    else
        die("Cannot load DTC MySQL config files -> /usr/share/dtc/shared/mysql_config.php");

    $dbc = mysql_connect($conf_mysql_host,$conf_mysql_login,$conf_mysql_pass);
    if ( $dbc === false )
        die( "Cannot connect using DTC MySQL Config parameters. Error was -> ".mysql_error()."\n");

    if ( ($results = mysql_select_db("apachelogs", $dbc)) === false )
        die("Database error trying to use database 'apachelogs', does it exist?\n");

    if ( ! empty($argv[1]) )
        if ( mysql_query("select count(*) from ".mysql_real_escape_string($argv[1])) )
            $tablename=addslashes($argv[1]);
        else
            die("table '".$argv[1]."' not found, is this the correct table name?\n");
    else
        die("Please provide the table name of the site logs.\n");

    if ( $results )
    {
        $tracktime=time()-10;
        while ( true )
        {
            $query = 'select * from '.$tablename.' where
time_stamp>'.$tracktime;

            $results = mysql_query($query, $dbc);
            if ( mysql_num_rows($results)>0 )
            {
                while( $row=mysql_fetch_assoc($results) )
                {
                    echo $row['time_stamp']." ";
                    echo $row['remote_host']." ";
                    echo $row['size']." ";
                    echo $row['request_line']." ";
                    echo $row['status']." \n";
                }
                sleep(1);
                $tracktime=time();
            }
        }
    }
?>
