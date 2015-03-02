<?php
require_once('include.php');
set_time_limit(0);

mysql_query("CREATE TABLE log(id INT NOT NULL auto_increment, filename TEXT NOT NULL, IP TEXT NOT NULL, result TEXT NOT NULL, time DATETIME NOT NULL, PRIMARY KEY (id));");

if ($_SESSION[$_GET['link']]['captcha'] !== 'true') {
	echo '{"result":"' . $str['err_captcha'] . '"}';
	mysql_query("INSERT INTO log SET filename='" . mysql_real_escape_string($_GET['link']) . "', IP='" . $_SERVER['REMOTE_ADDR'] . "', result='captchawrong', time='" . date("Y-m-d H:i:s") . "';");
	exit();
} else if (microtime(true) - $_SESSION[$_GET['link']]['time'] > 10) {
	echo '{"result":"' . $str['err_session'] . '"}';
	mysql_query("INSERT INTO log SET filename='" . mysql_real_escape_string($_GET['link']) . "', IP='" . $_SERVER['REMOTE_ADDR'] . "', result='sessionexpired', time='" . date("Y-m-d H:i:s") . "';");
	exit();
}

$result = mysql_query("SELECT * FROM storage WHERE filename='" . mysql_real_escape_string($_GET['link']) . "';");
$data = mysql_fetch_row($result);
if (count($data) < 2) {
	mysql_query("INSERT INTO log SET filename='" . mysql_real_escape_string($_GET['link']) . "', IP='" . $_SERVER['REMOTE_ADDR'] . "', result='wrongfile', time='" . date("Y-m-d H:i:s") . "';");
	header('Location: ./');
	exit();
}
mysql_query("INSERT INTO log SET filename='" . mysql_real_escape_string($_GET['link']) . "', IP='" . $_SERVER['REMOTE_ADDR'] . "', result='succeed', time='" . date("Y-m-d H:i:s") . "';");
$metadata = unserialize($data[5]);
if (hash('sha512', explode('$', $data[3])[2] . $_GET['password']) === explode('$', $data[3])[1]) {
	$result = mysql_query("SELECT filedata, AES_DECRYPT(filename_enc, '" . mysql_real_escape_string($_GET['password']) . "') FROM storage WHERE filename='" . mysql_real_escape_string($_GET['link']) . "'");
	mysql_query("UPDATE storage SET lastop='" . date('Y-m-d H:i:s') . "' WHERE filename='" . mysql_real_escape_string($_GET['link'])  . "';");
	$data = mysql_fetch_row($result);
	if (file_exists('filedata/' . $_GET['link'])) $content = file_get_contents('filedata/' . $_GET['link']);
	else $content = file_get_contents('filedata_big/' . $_GET['link']);
	$content = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, pack('H*', $_GET['password']), $content, MCRYPT_MODE_CBC, $data[0]);
	header('Content-Type: application/octet-stream', FALSE);
	header("Content-Transfer-Encoding: Binary", FALSE); 
	header("Content-Disposition: attachment; filename=\"" . $data[1] . "\"", FALSE);
	header("Content-Length: " . $metadata['filesize'], FALSE);
	print($content);
} else {
	echo '{"result":"error"}';
}
?>