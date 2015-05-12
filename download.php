<?php
require_once('include.php');
$result = mysql_query("SELECT * FROM storage WHERE filename='" . mysql_real_escape_string($_GET['link']) . "';");
$data = mysql_fetch_row($result);
if (count($data) < 2) {
	header('Location: ./nofile');
	exit();
}
$metadata = unserialize($data[5]);
if ($metadata['filename'] === '') $metadata['filename'] = $str['hiddenfilename'] . ' (' . human_filesize($metadata['filesize']) . ', ' . str_replace('%d', date('Y-m-d', strtotime($data[7])), $str['until']) . ')';
else $metadata['filename'] .= ' (' . human_filesize($metadata['filesize']) . ', ' . str_replace('%d', date('Y-m-d', strtotime($data[7])), $str['until']) . ')';
$title = $metadata['filename'] . ' - ' . $str['servicename'];

if (isset($metadata['password_delete'])) $deletion = true;
else $deletion = false;
?><!doctype HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<?php
	$mode = 'download';
	require_once('header.php');
	?>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="site.min.css">
	<script src="jquery.js"></script>
	<script src="bootstrap.min.js"></script>
	<script src="./cryptojs/rollups/sha256.js"></script>
	<script src="jquery.zclip.min.js"></script>
	<script src="https://www.google.com/recaptcha/api.js?onload=loadRecaptcha&amp;render=explicit" async defer></script>
	<style type="text/css">
		@font-face {
			font-family: 'Open Sans';
			font-style: normal;
			font-weight: 400;
			src: local('Open Sans'), local('OpenSans'), url(./open_sans.woff2) format('woff2');
			unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
		}
		.navbar-brand {
			color: white !important;
		}
		body {
			background-color: #f0f0f0;
		}
		div.main {
			margin-top: 65px;
		}
		div.content {
			padding: 15px;
		}
		h3.filename {
			margin-top: 0px;
			margin-bottom: 20px;
		}
		button.btn-download {
			margin-top: 15px;
		}
		div.spacer1 {
			height: 10px;
		}
		div.spacer2 {
			height: 15px;
		}
		@media screen and (min-width: 768px) {
			div.recaptcha {
				position: relative;
				float: right;
				top: 5px;
			}
		}
		@media screen and (max-width: 767px) {
			div.recaptcha {
				position: relative;
				top: 0px;
				left: 0px;
				height: 90px;
			}
		}
		@media screen and (max-width: 365px) {
			div.recaptcha {
				position: relative;
				left: calc(50% - 151px);
			}
		}
		div.ad {
			text-align:center;
		}
		.footer {
			background-color: #FFF;
			padding: 10px 15px 10px 15px;
			width: 100%;
			position: relative;
		}
		h4 {
			display: inline;
		}
		.modal-dialog {
			z-index: 1200;
		}
		.password {
			margin-left: 15px;
			margin-right: 15px;
			width: calc(100% - 30px);
		}
	</style>
	<script>
		"use strict";
		var recaptcha;
		var loadRecaptcha = function() {
			recaptcha = grecaptcha.render('recaptcha', {'sitekey': '<?php echo $recaptcha_sitekey; ?>'});
			$("alert").css('display', 'block');
		};

		$(document).ready(function() {
			if ((typeof navigator.plugins != "undefined" && typeof navigator.plugins["Shockwave Flash"] == "object") || (window.ActiveXObject && (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) != false)) {
				$('#copy').zclip({
					path:'ZeroClipboard.swf',
					copy:'<?php echo $str['link'] . $_GET['link']; ?>',
					afterCopy:function(){
						alert('<?php echo $str['copied_link']; ?>');
					}
				});
			} else {
				$('#copy').click(function() {
					alert('<?php echo $str['cannotcopy']; ?>');
				});
			}
		});

		var download = function() {
			var password = $("#password").val();
			if (password == '') {
				alert('<?php echo $str['err_enterpw']; ?>');
				return;
			} else if (grecaptcha.getResponse(recaptcha) == '') {
				alert('<?php echo $str['err_entercaptcha']; ?>');
				return;
			}
			var pw_hashed = CryptoJS.SHA256('<?php echo $metadata['salt']; ?>' + password).toString();
			if ($("#aswift_0_expand")[0] == null) {
				alert('<?php echo $str['err_noadblock']; ?>');
				return;
			}
			$("#download").attr('disabled', '');
			$.getJSON('auth/<?php echo $_GET['link']; ?>/' + pw_hashed + '/' + grecaptcha.getResponse(recaptcha)).done(function(data) {
				if (data.result === 'succeed') {
					$('#downloader').attr('href', 'fetch/<?php echo $_GET['link']; ?>/' + pw_hashed);
					document.getElementById('downloader').click();
					recaptcha = grecaptcha.render('recaptcha', {'sitekey': '<?php echo $recaptcha_sitekey; ?>'});
				} else {
					alert(data.result);
					$("#recaptcha").html('');
					recaptcha = grecaptcha.render('recaptcha', {'sitekey': '<?php echo $recaptcha_sitekey; ?>'});
				}
				$("#download").removeAttr('disabled');
			});
		};

		var keypress = function(e) {
			if (e.keyCode == 13) {
				download();
			}
		};

		var keypress_delete = function(e) {
			if (e.keyCode == 13) {
				reqdelete();
			}
		};

		var reqdelete = function() {
			var pw_delete = $("#pw_delete").val();
			$.getJSON('delete/<?php echo $_GET['link']; ?>/' + pw_delete).done(function(data) {
				if (data.result === 'succeed') {
					alert('<?php echo $str['deleted']; ?>');
					location.href = './';
				} else {
					alert(data.result);
				}
				$("#download").removeAttr('disabled');
			});
		};
	</script>
</head>
<body>
	<!--[if lte IE 8]><script>alert('<?php echo $str['err_noie']; ?>');</script><![endif]-->
	<?php
	$mode = 'download';
	require_once('menu.php');
	?>
	<div class="container main">
		<div class="panel content">
			<div id="recaptcha" class="recaptcha"></div>
			<h3 class="filename"><?php echo htmlspecialchars($metadata['filename']); ?></h3>
			<h4><?php echo $str['linktofile-desc'] . ' <a href="' . $str['link'] . $_GET['link'] . '">' . $str['link'] . $_GET['link'] . '</a>';?></h4>&emsp;<button type="button" id="copy" class="btn btn-warning"><?php echo $str['copy']; ?></button><?php if ($deletion) echo '&emsp;<button type="button" id="delete" class="btn btn-danger" data-toggle="modal" data-target="#modal">' . $str['delete'] . '</button>'; ?>
			<div class="spacer1"></div>
			<div class="row">
				<div class="col-sm-2">
					<label><?php echo $str['password']; ?></label>
				</div>
				<div class="col-sm-10">
					<input type="password" class="form-control" id="password" onkeypress="keypress(event)">
				</div>
			</div>
			<button type="button" id="download" class="btn btn-success btn-block btn-download" onclick="download()"><?php echo $str['download']; ?></button>
			<div class="spacer2"></div>
			<div class="row">
				<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-8739077797209742" data-ad-slot="5765795967" data-ad-format="auto"></ins>
				<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
				</script>
			</div>
		</div>
	</div>
	<?php require_once('footer.php'); ?>
	<a id="downloader" style="display:hidden;" href="#"></a>
	<div id="modal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><?php echo $str['deletion']; ?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
					<input id="pw_delete" type="password" class="form-control password" placeholder="<?php echo $str['enterpw_delete']; ?>" onkeypress="keypress_delete()">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $str['close']; ?></button>
					<button type="button" class="btn btn-primary" onclick="reqdelete();"><?php echo $str['req_delete']; ?></button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>