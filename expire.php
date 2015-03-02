<?php
$result = mysql_query("SELECT * FROM storage WHERE expires < NOW();");
while ($data = mysql_fetch_row($result)) {
	unlink('filedata/' . $data[1]);
	unlink('filedata_big/' . $data[1]);
}
$result = mysql_query("SELECT * FROM storage WHERE lastop < DATE_SUB(NOW(), INTERVAL 60 DAY);");
while ($data = mysql_fetch_row($result)) {
	unlink('filedata/' . $data[1]);
	unlink('filedata_big/' . $data[1]);
}
mysql_query("DELETE FROM storage WHERE expires < NOW();");
mysql_query("DELETE FROM storage WHERE lastop < DATE_SUB(NOW(), INTERVAL 60 DAY);");
?>