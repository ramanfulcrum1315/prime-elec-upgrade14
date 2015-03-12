<?php
// Author: spetrow (Nexcess.net LLC)
// Date: 8-19-10

// Set up memcache conenction details
$server = "127.0.0.1";
$port = "11211";

// Try to connect
$memcache = new Memcache;
$memcache->connect($server, $port) or die ("Could not connect to memcached daemon at " . $server . " on port " .$port);

// If the flush cache button is pressed, flush cache
$memcache->flush() or die ("Cache not flushed");
// Sleep one second to avoid invalidating all flushed items with the same second timestamp
$time = time()+1; 
while(time() < $time) { 
  //sleep 
}
echo "memcached cache for " . $server . " on port " . $port . " flushed! <br />";
echo date("r");
?>

