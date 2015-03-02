<?php
require_once('include.php');
$result = mysql_query("SELECT * FROM storage WHERE filename='" . mysql_real_escape_string($_GET['link']) . "';");
$data = mysql_fetch_row($result);
if (count($data) < 2) {
	header('Location: ./');
	exit();
}
$metadata = unserialize($data[5]);

if ($metadata['password_delete'] === hash('sha512', $metadata['salt_delete'] . $_GET['password'])) {
	mysql_query("DELETE FROM storage WHERE filename='" . mysql_real_escape_string($_GET['link']) . "';");
	if (file_exists('filedata/' . $_GET['link'])) unlink('filedata/' . $_GET['link']);
	else unlink('filedata_big/' . $_GET['link']);
	echo '{"result":"succeed"}';
} else {
	echo '{"result":"' . $str['err_wrongpw'] . '"}';
}
?>