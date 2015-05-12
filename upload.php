<?php
require_once('include.php');
if (isset($_FILES["file"]) && isset($_POST['password']) && isset($_POST['salt']) && isset($_POST['password_delete']) && isset($_POST['expires']) && isset($_POST['showfilename']) && isset($_POST['enablelist'])) {
	if ($_FILES["file"]["size"] > $sizelimit) {
		if (isset($_GET['secret']) && $_GET['secret'] === $secret) {

		} else {
			echo '{"result":"error","desc":"' . $str['err_sizelimit'] . '"}';
			exit();
		}
	}

	set_time_limit(0);

	$rand_base = 'abcdefghijklmnopqrstuvwxyz0123456789';

	while(1){
	$filename_md5 = '';
	for($i = 0; $i < 8; $i++) $filename_md5 .= $rand_base[rand(0, 35)];
	$query = "SELECT * FROM storage WHERE filename='" . $filename_md5 . "'";
	$result = mysql_query($query);
	$data = mysql_fetch_row($result);
	if (count($data) < 2) break;
	usleep(10);
	}

	$content = file_get_contents($_FILES["file"]["tmp_name"]);
	$key = pack('H*', $_POST['password']);

	$iv = mcrypt_create_iv(16, MCRYPT_RAND);
	$content = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $content, MCRYPT_MODE_CBC, $iv);
	if ($_FILES["file"]["size"] > $file_threshold) {
		file_put_contents('filedata_big/' . $filename_md5, $content);
	} else {
		file_put_contents('filedata/' . $filename_md5, $content);
	}

	$metadata['filesize'] = $_FILES["file"]["size"];
	$metadata['filename'] = $_POST['showfilename'] === 'true' ? $_FILES['file']['name'] : '';
	$metadata['date'] = date('Y-m-d H:i:s');
	$metadata['salt'] = $_POST['salt'];

	$metadata['salt_delete'] = '';
	for($i = 0; $i < 20; $i++) $metadata['salt_delete'] .= $rand_base[rand(0, 35)];
	$metadata['password_delete'] = hash('sha512', $metadata['salt_delete'] . $_POST['password_delete']);


	$salt2 = uniqid(mt_rand(), true);

	$metadata_serialized = serialize($metadata);

	$query = "INSERT INTO storage SET filename='" . $filename_md5 . "', ip='" . mysql_real_escape_string($_SERVER['REMOTE_ADDR']) . "', filedata='" . mysql_real_escape_string($iv) . "', password='" . mysql_real_escape_string($_POST['salt']) . '$' . hash('sha512', mysql_real_escape_string($salt2) . mysql_real_escape_string($_POST['password'])) . "$" . $salt2 . "', metadata='" . mysql_real_escape_string($metadata_serialized) . "', filename_enc=AES_ENCRYPT('" . mysql_real_escape_string($_FILES['file']['name']) . "', '" . mysql_real_escape_string($_POST['password']) . "'), expires='" . date('Y-m-d 23:59:59', strtotime($_POST['expires'])) . "', lastop='" . date('Y-m-d H:i:s') . "', enablelist='" . ($_POST['enablelist'] === 'true'?'1':'0') . "';";
	mysql_query($query);
	echo '{"result":"succeed","link":"' . $filename_md5 . '"}';
} else {
	echo '{"result":"error","desc":"' . $str['err_parametermissing'] . '"}';
	exit();
}
?>