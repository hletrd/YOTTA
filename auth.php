<?php
require_once('include.php');
set_time_limit(0);
$req = curl_init('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptcha_secret . '&response=' . $_GET['captcha'] . '&remoteip=' . $_SERVER['REMOTE_ADDR']);
curl_setopt($req, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($req, CURLOPT_SSL_VERIFYPEER, FALSE);
$data = curl_exec($req);
$captcha = json_decode($data, true);
if ($captcha['success'] === false) {
	echo '{"result":"' . $str['err_captcha'] . '"}';
	$_SESSION[$_GET['link']]['captcha'] = false;
	exit();
}

$_SESSION[$_GET['link']]['captcha'] = 'true';
$_SESSION[$_GET['link']]['time'] = microtime(true);

$result = mysql_query("SELECT * FROM storage WHERE filename='" . mysql_real_escape_string($_GET['link']) . "';");
$data = mysql_fetch_row($result);
if (count($data) < 2) {
	header('Location: ./');
	exit();
}
$metadata = unserialize($data[5]);
if (hash('sha512', explode('$', $data[3])[2] . $_GET['password']) === explode('$', $data[3])[1]) {
	echo '{"result":"succeed"}';
} else {
	echo '{"result":"' . $str['err_wrongpw'] . '"}';
}
?>