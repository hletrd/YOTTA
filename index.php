<?php
require_once("include.php");

//mysql_query("CREATE TABLE storage(id INT NOT NULL auto_increment, filename TEXT NOT NULL, filedata MEDIUMBLOB NOT NULL, password TEXT NOT NULL, ip TEXT NOT NULL, metadata MEDIUMTEXT NOT NULL, filename_enc MEDIUMTEXT NOT NULL, expires DATETIME NOT NULL, lastop DATETIME NOT NULL, enablelist BOOLEAN NOT NULL, PRIMARY KEY (id));");

$title = $str['title'];
?><!doctype HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<?php
	$mode = 'upload';
	require_once('header.php');
	?>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="site.min.css">
	<link rel="stylesheet" type="text/css" href="jquery-ui.min.css">
	<script src="jquery.js"></script>
	<script src="jquery-ui.min.js"></script>
	<script src="bootstrap.min.js"></script>
	<script src="./cryptojs/rollups/sha256.js"></script>
	<script src="jquery.zclip.min.js"></script>
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
		.btn-upload {
			margin-top: 10px;
		}
		.container-checkbox {
			padding: 0px;
			margin-top: 10px;
		}
		.spacer1 {
			height: 5px;
		}
		.spacer2 {
			height: 0px;
		}
		@media screen and (max-width: 767px) {
			.spacer3 {
				height: 10px;
			}
		}
		.spacer4 {
			height: 15px;
		}
		.label-expires {
			margin-top: 15px;
		}
		.label-expires-desc {
			margin-left: 15px;
			margin-top: 10px;
		}
		.ui-datepicker {
			top: 142px !important;
		}
		@media screen and (max-width: 767px) {
			.ui-datepicker {
				top: 164px !important;
			}
		}
		div.datepicker {
			padding-left: 0px;
			padding-right: 0px;
		}
		input.file {
			display:none;
		}
		.popover {
			position: absolute;
			top: 33px;
		}
		.btn-popover {
			width: calc(50% - 16px);
			margin-left: 10px;
		}
		.footer {
			background-color: #FFF;
			padding: 10px 15px 10px 15px;
			width: 100%;
			position: relative;
		}
	</style>
	<script>
		"use strict";
		var file = null;
		var password_created = '';

		$(document).ready(function() {
			$("#datepicker").datepicker();
			if (window.location.pathname.indexOf('nofile') >= 0) {
				alert('<?php echo $str['err_doesntexist']; ?>');
			}
			var date = new Date;
			var date = new Date(date.getFullYear(), date.getMonth() + 1, 0);
			$("#datepicker").val(date.getFullYear() + '/' + (date.getMonth() + 1) + '/' + date.getDate());
		});

		var selectFile = function(e) {
			file = e.target.files[0];
			<?php if (!isset($_GET['secret'])) { ?>
			if (file.size > <?php echo $sizelimit; ?>) {
				alert('<?php echo $str['err_sizelimit']; ?>');
				file = null;
				return;
			}
			<?php } ?>

			var size_h;
			if (file.size > Math.pow(2,30)) {
				size_h = Math.round(file.size / Math.pow(2,30) * 100) / 100 + 'GB';
			} else if (file.size > Math.pow(2,20)) {
				size_h = Math.round(file.size / Math.pow(2,20) * 100) / 100 + 'MB';
			} else if (file.size > Math.pow(2,10)) {
				size_h = Math.round(file.size / Math.pow(2,10) * 100) / 100 + 'KB';
			} else {
				size_h = file.size + 'B';
			}
			$("#btn_select").html(file.name + ' (' + size_h + ') <?php echo $str['selected']; ?>');
		};

		var upload = function() {
			var password = $("#password").val();
			var password_delete = $("#password_delete").val();
			if (window.location.protocol !== "https:") {
				alert('<?php echo $str['err_nohttps']; ?>');
				return;
			} else if (file === null) {
				alert('<?php echo $str['err_nofile']; ?>');
				return;
			} else if (password === '') {
				alert('<?php echo $str['err_nopw']; ?>');
				return;
			} else if (password_delete === '') {
				alert('<?php echo $str['err_nopw_delete']; ?>');
				return;
			} else if ($("#datepicker").val() === '') {
				alert('<?php echo $str['err_nodate']; ?>');
				return;
			} else if (Math.floor(Date.now() / 86400000) > Math.floor(Date.parse($("#datepicker").val()) / 86400000)) {
				alert('<?php echo $str['err_date']; ?>');
				return;
			} else if (password !== $("#password_re").val()) {
				alert('<?php echo $str['err_pwmatch']; ?>');
				return;
			} else if (password_delete !== $("#password_re_delete").val()) {
				alert('<?php echo $str['err_pwmatch_delete']; ?>');
				return;
			}

			$('#form >').attr('disabled', 'disabled');
			$('#form > >').attr('disabled', 'disabled');
			$('#form > > >').attr('disabled', 'disabled');
			$('#form > > > >').attr('disabled', 'disabled');
			$('#form > > > > >').attr('disabled', 'disabled');

			var salt = CryptoJS.lib.WordArray.random(128/8);
			var pw_hashed = CryptoJS.SHA256(salt + password).toString();

			var xhr = new XMLHttpRequest();

			var data = new FormData;
			data.append('file', file);
			data.append('password', pw_hashed);
			data.append('password_delete', password_delete);
			data.append('salt', salt);
			data.append('expires', $('#datepicker').val());
			data.append('showfilename', $('#chk1').is(':checked')?'true':'false');
			data.append('enablelist', $('#chk2').is(':checked')?'true':'false');
			
			xhr.upload.onprogress = function(e) {
				var done = e.position || e.loaded, total = e.totalSize || e.total;
				$('#prog').html('<div class="progress progress-striped active" style="margin-bottom: 2px; margin-top: 15px"><div id="prog_height" class="progress-bar progress-bar-success" style="width: ' + Math.floor(done/total*100) + '%"></div></div>')
			};
			xhr.onreadystatechange = function(e) {
				if (this.readyState === 4) {
					try {
						var parsed = JSON.parse(xhr.responseText);
					} catch (e) {
						console.log (xhr.responseText);
					}
					if (parsed.result === 'error') alert(parsed.desc);
					else {
						location.href = './' + parsed.link;
					}
				}
			};

			xhr.open('post', 'upload', true);
			xhr.send(data);
		};

		var createPass = function(what) {
			password_created = '';
			var basestr = '._-=0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			for(var i = 0; i < Math.floor(20 + Math.random() * 21); i++) {
				password_created = password_created + basestr[Math.floor(Math.random() * 66)];
			}
			$('#popover' + (1 - what)).css('display', 'none');
			$("#password" + (what?'_delete':'')).val(password_created);
			$("#password_re" + (what?'_delete':'')).val(password_created);
			$("#pw_show" + what).html('<nobr>' + password_created + '</nobr>');
			$("#popover" + what).css('display', 'block');
			$("#popover" + what).css('left', - parseInt($("#popover" + what).css('width')) / 2 + parseInt($(".createpass").css('width')) / 2 + 16 + 'px');
			if ((typeof navigator.plugins != "undefined" && typeof navigator.plugins["Shockwave Flash"] == "object") || (window.ActiveXObject && (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) != false)) {
				$('#copy' + what).zclip({
					path:'ZeroClipboard.swf',
					copy:password_created,
					afterCopy:function(){
						alert('<?php echo $str['copied']; ?>');
					}
				});
			} else {
				$('#copy' + what).click(function() {
					alert('<?php echo $str['cannotcopy']; ?>');
				});
			}
		};
	</script>
</head>
<body>
	<!--[if lte IE 8]><script>alert('<?php echo $str['err_noie']; ?>');</script><![endif]-->
	<?php
	$mode = 'upload';
	require_once('menu.php');
	?>
	<div class="container main">
		<div id="form" class="panel content">
			<input type="file" class="file" id="fileinput" onchange="selectFile(event)">
			<div class="row">
				<div class="col-sm-12"><button id="btn_select" type="button" class="btn btn-success btn-block" onclick="$('#fileinput').click()"><?php echo $str['selectfile']; ?></button></div>
			</div>
			<div class="spacer4"></div>
			<div class="row">
				<div class="col-sm-4"><input class="form-control" type="password" id="password" placeholder="<?php echo $str['password']; ?>"></div>
				<div class="spacer3"></div>
				<div class="col-sm-4"><input class="form-control" type="password" id="password_re" placeholder="<?php echo $str['password_re']; ?>"></div>
				<div class="spacer3"></div>
				<div class="col-sm-4">
					<button type="button" class="btn btn-block btn-info createpass" onclick="createPass(0)"><?php echo $str['createpw']; ?></button>
					<div id="popover0" class="popover bottom">
						<div class="arrow"></div>
						<h3 class="popover-title"><?php echo $str['created_password']; ?></h3>
						<div class="popover-content">
							<p id="pw_show0"></p>
							<div class="row">
								<button id="copy0" type="button" class="btn btn-default btn-popover"><?php echo $str['copy']; ?></button>
								<button type="button" class="btn btn-popover" onclick="$('#popover0').css('display', 'none');"><?php echo $str['close']; ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="spacer4"></div>
			<div class="row">
				<div class="col-sm-4"><input class="form-control" type="password" id="password_delete" placeholder="<?php echo $str['password_delete']; ?>"></div>
				<div class="spacer3"></div>
				<div class="col-sm-4"><input class="form-control" type="password" id="password_re_delete" placeholder="<?php echo $str['password_delete_re']; ?>"></div>
				<div class="spacer3"></div>
				<div class="col-sm-4">
					<button type="button" class="btn btn-block btn-info createpass" onclick="createPass(1)"><?php echo $str['createpw']; ?></button>
					<div id="popover1" class="popover bottom">
						<div class="arrow"></div>
						<h3 class="popover-title"><?php echo $str['created_password']; ?></h3>
						<div class="popover-content">
							<p id="pw_show1"></p>
							<div class="row">
								<button id="copy1" type="button" class="btn btn-default btn-popover"><?php echo $str['copy']; ?></button>
								<button type="button" class="btn btn-popover" onclick="$('#popover1').css('display', 'none');"><?php echo $str['close']; ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6">
					<div class="spacer1"></div>
					<div class="container-checkbox">
						<label class="toggle"><input id="chk1" type="checkbox"><span class="handle"></span></label>&emsp;<label><?php echo $str['showfilename']; ?></label>
					</div>
					<div class="container-checkbox">
						<label class="toggle"><input id="chk2" type="checkbox"><span class="handle"></span></label>&emsp;<label><?php echo $str['enablelist']; ?></label>
					</div>
					<div class="spacer2"></div>
				</div>
				<div class="col-sm-6">
					<div class="col-sm-5"><label class="label-expires"><?php echo $str['expires']; ?></label></div>
					<div class="col-sm-7 datepicker">
						<div class="container-checkbox">
							<input class="form-control" type="text" id="datepicker">
						</div>
					</div>
					<label class="label-expires-desc"><small><?php echo $str['expires-desc']; ?></small></label>
				</div>
			</div>
			<div id="prog"></div>
			<button id="upload" type="button" class="btn btn-primary btn-block btn-upload" onclick="upload()"><?php echo $str['upload']; ?></button>
		</div>
	</div>
	<?php require_once('footer.php'); ?>
</body>
</html>