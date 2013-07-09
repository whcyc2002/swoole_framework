<?php
$fp = stream_socket_client("udp://127.0.0.1:8006", $errno, $errstr);
$data = $argv[1];
if(!$fp)
{
    echo "ERROR: $errno - $errstr<br />\n";
}
else
{
    fwrite($fp, "$data\n");
    //echo fread($fp, 26);
    fclose($fp);
}
?>