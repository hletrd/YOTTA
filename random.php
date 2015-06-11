<?php
if (!isset($_GET['length'])) $_GET['length'] = 64;
$length = intval($_GET['length']);
if ($length > 10000) $length = 10000;
$rndbase = 'abcdefghijklmnopABCDEFGHIJKLMNOP0123456789';
$rnd = fopen('/dev/urandom', 'rb');
$data = fread($rnd, $length);
echo base64_encode($data);
?>