<?php
echo '<meta property="og:title" content="YOTTA - Fully-encrypted censorship-free file sharing service">
	<meta property="og:description" content="';
if ($mode === 'download') {
	echo $metadata['filename'] . ' - Download';
} else if ($mode === 'upload') {
	echo 'Upload new file on YOTTA';
} else if ($mode === 'about') {
	echo 'Introducing YOTTA';
} else if ($mode === 'list') {
	echo 'Public files on YOTTA';
}
echo '">
';
?>