<?php
// Author: spetrow (Nexcess.net LLC)
// Date: 8-19-10

// Set up memcache conenction details
$server = "127.0.0.1";
$port = "11211";

// Try to connect
$memcache = new Memcache;
$memcache->connect($server, $port) or die ("Could not connect to memcached daemon at " . $server . " on port " .$port);

// Refresh the page every two seconds
header('Refresh: 2');

function printDetails($status){ 
global $server, $port;
        $percCacheHit=((real)$status ["get_hits"]/ (real)$status ["cmd_get"] *100); 
        $percCacheHit=round($percCacheHit,3); 
        $percCacheMiss=100-$percCacheHit; 
        $MBRead= (real)$status["bytes_read"]/(1024*1024); 
        $MBWrite=(real) $status["bytes_written"]/(1024*1024) ; 
        $MBSize=(real) $status["limit_maxbytes"]/(1024*1024) ; 
// CSS for NexStyle
echo '
<html>
<head>
<title=Magento Cron Status>
<style type="text/css">
html {
    width: 100%;
    font-family: Helvetica, Arial, sans-serif;
}
body {
    background-color:#00AEEF;
    color:#FFFFFF;
    line-height:1.0em;
    font-size: 125%;
}
b {
    color: #FFFFFF;
}
table{
    border-spacing: 1px;
    border-collapse: collapse;
    width: 800px;
}
th {
    text-align: center;
    font-size: 125%;
    font-weight: bold;
    padding: 5px;
    border: 2px solid #FFFFFF;
    background: #00AEEF;
    color: #FFFFFF;
}
td {
    text-align: left;
    padding: 4px;
    border: 2px solid #FFFFFF;
    color: #FFFFFF;
    background: #666;
}
</style>
</head>';

echo "<h2>Memcache status for " . $server . ":" . $port . "<h2>";
echo "<table border='1'>"; 

        echo "<tr><td>pid:</td><td> ".$status ["pid"]."</td></tr>"; 
        echo "<tr><td>uptime:</td><td> ".$status ["uptime"]." Seconds</td></tr>"; 
        echo "<tr><td>memcached server time:</td><td> ".date('r',$status ["time"])."</td></tr>"; 
        echo "<tr><td>local server time:</td><td> ".date('r')."</td></tr>"; 
        echo "<tr><td>version:</td><td> ".$status ["version"]."</td></tr>"; 
        echo "<tr><td>rusage_user:</td><td> ".$status ["rusage_user"]."</td></tr>"; 
        echo "<tr><td>rusage_system:</td><td> ".$status ["rusage_system"]."</td></tr>"; 
        echo "<tr><td><b>curr_items:</b></td><td><b> ".$status ["curr_items"]."</b></td></tr>"; 
        echo "<tr><td>total_items:</td><td> ".$status ["total_items"]."</td></tr>"; 
        echo "<tr><td>bytes:</td><td> ".$status ["bytes"]."</td></tr>"; 
        echo "<tr><td>curr_connections:</td><td> ".$status ["curr_connections"]."</td></tr>"; 
        echo "<tr><td>total_connections:</td><td> ".$status ["total_connections"]."</td></tr>"; 
        echo "<tr><td>connection_structures:</td><td> ".$status ["connection_structures"]."</td></tr>"; 
        echo "<tr><td>cmd_get:</td><td> ".$status ["cmd_get"]."</td></tr>"; 
        echo "<tr><td>cmd_set:</td><td> ".$status ["cmd_set"]."</td></tr>"; 
        echo "<tr><td>get_hits:</td><td> ".$status ["get_hits"]."</td></tr>"; 
        echo "<tr><td>get_misses:</td><td> ".$status ["get_misses"]."</td></tr>"; 
        echo "<tr><td><b>Get hit rate:</b></td><td><b>".$status ["get_hits"] / $status["cmd_get"] * 100 ."%</b></td></tr>"; 
        echo "<tr><td>bytes_read:</td><td> ".$status ["bytes_read"]."</td></tr>"; 
        echo "<tr><td>limit_maxbytes:</td><td> ".$status ["limit_maxbytes"]."</td></tr>"; 
        echo "<tr><td>Number of bytes this server is allowed to use for storage.</td><td>".$MBSize." MB</td></tr>"; 
        echo "<tr><td><b>Percentage of allocated memory in use:</b></td><td><b>".$status ["bytes"] / $status["limit_maxbytes"] * 100 ."%</b></td></tr>"; 


        echo "<tr><td>Number of keys that have been requested and found present </td><td>".$status ["get_hits"]." ($percCacheHit%)</td></tr>"; 
        echo "<tr><td>Number of items that have been requested and not found </td><td>".$status ["get_misses"]."($percCacheMiss%)</td></tr>"; 


        echo "<tr><td>Total number of bytes read by this server from network </td><td>".$MBRead." MB</td></tr>"; 
        echo "<tr><td>Total number of bytes sent by this server to network </td><td>".$MBWrite." MB</td></tr>"; 
        echo "<tr><td>Number of valid items removed from cache to free memory for new items.</td><td>".$status ["evictions"]."</td></tr>"; 

echo "</table>"; 

    } 
?>

<html>
<br />
<INPUT type="button" value="Flush cache (runs in a new window)" onClick="window.open('memcache-flush.php','mywindow','width=400,height=200')"> 
</form>
</html>




<?php

// print "Before:";

printDetails($memcache->getStats()); 

/* Get test (part 1/2)
$get_result = $memcache->get('key');
echo "Data from the cache:<br/>\n";

var_dump($get_result);

*/




// print "After:";
// printDetails($memcache->getStats()); 
 

/* Get test (part 2/2)
$get_result = $memcache->get('key');
echo "Should come back empty:<br/>\n";

var_dump($get_result);
*/

?>
