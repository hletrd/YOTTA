<?php
require_once("include.php");

$title = $str['servicename'];
?><!doctype HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<?php
	$mode = 'about';
	require_once('header.php');
	?>
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="site.min.css">
	<script src="jquery.js"></script>
	<script src="bootstrap.min.js"></script>
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
		p.about {
			padding: 15px;
			margin-top: -15px;
		}
		h4.about {
			margin-top: 0px;
			margin-bottom: 0px;
			padding: 15px;
		}
		ul {
			margin-right: 15px;
		}
		.footer {
			background-color: #FFF;
			padding: 10px 15px 10px 15px;
			width: 100%;
			position: relative;
		}
	</style>
</head>
<body>
	<!--[if lte IE 8]><script>alert('<?php echo $str['err_noie']; ?>');</script><![endif]-->
	<?php
	$mode = 'about';
	require_once('menu.php');
	?>
	<?php if ($_SESSION['lang'] == 'ko') { ?>
	<div class="container main">
		<div class="panel content">
			<h4 class="about"><?php echo $str['servicename']; ?>는 모든 파일이 암호화되어 보관되는 파일 스토리지입니다.</h4>
			<p class="about"><?php echo $str['servicename']; ?>는 업로드된 모든 파일을 AES256으로 안전하게 암호화하여 저장하므로 수사 기관에서 서버를 압수수색하거나 악의적인 목적을 가진 해커가 서버의 DB를 획득하더라도 업로드된 파일에 대해서는 알 수 없습니다. 또한, 유효 기간이 지나거나 60일 이상 다운로드되지 않은 파일은 DB상에서 완전히 제거됩니다.
			<br /><br />
			사용자가 파일을 업로드할 때에 입력한 암호 키는 복호화할 수 없도록 솔트(salt)가 더해져 SHA256로 해싱되고, 이렇게 해싱된 키로 파일이 AES256 암호화 알고리즘으로 암호화됩니다. 서버 로그 역시 파일의 암호 등 파일을 복구해낼 수 있는 어떤 정보도 기록되지 않기 때문에 처음에 파일을 업로드할 때 입력한 암호 키를 모를 경우 파일은 복구할 수 없습니다. 해싱된 키는 SHA512로 다시 한 번 더 해싱된 채 서버에 저장되며, 이는 추후 업로드된 파일을 다운로드하고자 할 때 사용자가 입력한 키가 올바른지 여부를 확인하기 위해 사용됩니다. 사용자가 파일을 업로드할 때 파일명을 비공개로 설정한 경우, 파일과 관련된 모든 메타데이터 역시 암호화된 채 저장되며, 처음에 업로드할 때 입력한 비밀번호를 모를 경우 메타데이터 역시 복구할 수 없습니다.
			<br /><br />
			</p>
			<h4 class="about"><strong>자주 묻는 질문</strong></h4>
			<br />
			<ul>
			<strong><li>사이트에 접속할 때 경고(신뢰할 수 없는 인증서)가 뜹니다.</li></strong>
			<br />
			본 사이트는 플랫폼 및 브라우저에 따라 HTTPS 인증서가 신뢰할 수 없다고 인식될 수 있습니다. 이는 대한민국 정부를 루트 인증 기관으로 하는 HTTPS 인증서를 해당 플랫폼 또는 브라우저에서 신뢰하도록 설정되어 있지 않기 때문입니다. 본 웹 사이트의 인증서는 Windows용 Internet Explorer 및 Google Chrome에서 신뢰할 수 있는 인증서로 처리됩니다.<br />
			<del>사실 저희도 Verisign같은 곳의 인증서를 쓰고 싶은데 도메인이 도메인인지라 정부 인증서를 받아 쓰는 수밖에 ㅠㅠ</del>
			<br /><br />
			<strong><li>사용자가 파일을 다운로드하는 과정에서 파일의 암호화가 풀린 뒤 다운로드될텐데, 그 사이 서버가 압수수색당하면 어떻게 되나요?</li></strong>
			<br />
			사용자가 파일을 다운로드받을 때에도 암호가 해제된 파일은 디스크에 기록되지 않으며, 모든 파일 데이터의 복호화 과정은 오직 메모리(RAM)상에서만 처리됩니다. 따라서 파일을 다운로드받는 사용자와의 연결이 끝나면 파일 데이터는 복구할 수 없으며, 수사기관에서 서버를 압수수색하기 위해 전원을 차단하면 데이터가 손실됩니다.
			<br /><br />
			<img src="./police.jpg" width="100%">
			<br />
			<del>사실 대한민국 경찰은 램과 그래픽카드를 증거물로 사용하는 능력을 갖추고 있...</del> <del>물론 믿으면 골룸</del>
			<br /><br />
			<strong><li>서비스 구조를 보니 서버 액세스 로그(/var/log/nginx/access.log) 쪽에 비밀번호가 남을 것 같은데...</li></strong>
			<br />
			사용자가 파일의 다운로드를 시도할 때에 한해서는 서버 로그가 남지 않도록 설정되어 있습니다.
			<br /><br />
			<strong><li>비밀번호가 맞는건지 틀린건지는 어떻게 아는건가요?</li></strong>
			<br />
			비밀번호는 복호화할 수 없도록 다시 한 번 암호화되어 파일과는 별도로 저장되며, 추후 사용자가 파일을 다운로드하려고 시도할 때 비밀번호의 일치 여부를 확인하기 위해 사용됩니다.
			<br /><br />
			<strong><li>버그나 취약점을 찾았어요</li></strong>
			<br />
			<del>그럴 리 없는데</del> <a href="mailto:01@0101010101.com">01@0101010101.com</a>으로 제보해 주세요. 사랑을 드릴게요(?)
			<br /><br />
			<strong><li>서비스가 안정적으로 계속 운영되는 건 확실한가요? 막 갑자기 예고 없이 중단된다든가...</li></strong>
			<br />
			어떤 일이 있어도 2016년 2월까지는 운영할 계획입니다.
			<br /><br />
			<strong><li>서버 사양은 어떤가요? 많이 이용해도 문제 없을까요?</li></strong>
			<br />
			현재 서버는 IBM X3550으로, 샌디브릿지 제온(E5-2609)에 12GB 램의 오버스펙입니다. 서버 좀 뻗게 해 주세요(...)
			<br /><br />
			</ul>
			<br /><br /><br />
			<p class="about">
			<strong>Copyright (c) 2014-<?php echo date('Y'); ?> <?php echo $str['servicename']; ?>. All rights reserved.</strong> (문의: 01@0101010101.com)<br />- 사용자가 업로드한 자료에 대한 저작권을 포함한 모든 권리는 사용자에게 있습니다.<br />- 사용자의 데이터는 RAID 1 스토리지에 저장되지만, 서버 상황에 따라 데이터는 유실될 수 있으며, <?php echo $str['servicename']; ?>는 사용자가 업로드한 자료의 유실에 대해 책임지지 않습니다.<br />- 일간 다운로드 횟수가 5000회를 넘는 등 서버에 과부하를 일으키는 파일의 경우 사전 공지 없이 임의로 삭제될 수 있습니다.<br />
			</p>
		</div>
	</div>
	<?php } else { ?>
	<div class="container main">
		<div class="panel content">
			<h4 class="about"><?php echo $str['servicename']; ?> is fully-encrypted, censorship-free online file sharing service.</h4>
			<p class="about"><?php echo $str['servicename']; ?> encrypts all uploaded files using AES256. Even if any unauthorized user or investigation agency access to database, nobody can get any informations of uploaded files. In addition, any expired file or any file never downloaded for more than 60days will be automatically removed completely from our database.
			<br /><br />
			The password that user typed in is hashed using SHA256, with salt, to create passphrase. Passphrase is used to encrypt uploaded file using AES256. Original file cannot be retrived without the password, even including server admistrator. Hashed password is hashed again using SHA512 with salt, and this hash is only used to verify the password in download page. If user decided to hide filename in upload page, all metadata of the file is also encrypted with the passphrase, and cannot be retrived without the password.
			<br /><br />
			</p>
			<h4 class="about"><strong>Frequently Asked Questions</strong></h4>
			<br />
			<ul>
			<strong><li>My browser shows that the HTTPS certificate is not verified.</li></strong>
			<br />
			This service uses HTTPS certificate issued from Government of Korea, but some browser or OSes may show that the certificate is not verified. HTTPS certificate of this service is trusted in Chrome for Windows, and Internet Explorer.
			<del>Actually, we wanted to use certificate from Verisign, but we cannot because of the domain</del>
			<br /><br />
			<strong><li>Encrypted file will be decrypted if any user try to download the file, but can't agencies retrive original file from those temporary files?</li></strong>
			<br />
			Nothing is written onto the storage of the server in download progress, and vice versa. All decrypting progress is only processed in RAM of the server, and no data can be recovered after downloading is completed, and vice versa. If any investigation agency tries to take server, all data will be lost.
			<br /><br />
			<img src="./police.jpg" width="100%">
			<br />
			<del>Actually, Korean police gets data from RAM and graphic cards...</del>
			<br /><br />
			<strong><li>I think access log(/var/log/nginx/access.log) may contain password...</li></strong>
			<br />
			Logging is disabled for uploads/downloads.
			<br /><br />
			<strong><li>How can be passwords verified?</li></strong>
			<br />
			Password is hashed again and saved in database to verify the password in download page.
			<br /><br />
			<strong><li>Found any bugs/vulnerabilities</li></strong>
			<br />
			<del>You shouldn't</del> Contact: <a href="mailto:01@0101010101.com">01@0101010101.com</a>
			<br /><br />
			<strong><li>Won't the service be discontinued?</li></strong>
			<br />
			Unless the Earth is destroyed, we will maintain the service until Feb, 2016.
			<br /><br />
			<strong><li>What is the specs of the server?</li></strong>
			<br />
			We're using IBM X3550, w/ Xeon Sandy Bridge(E5-2609) w/ 12GB RAM.
			<br /><br />
			</ul>
			<br /><br /><br />
			<p class="about">
			<strong>Copyright (c) 2014-<?php echo date('Y'); ?> <?php echo $str['servicename']; ?>. All rights except for uploaded files reserved.</strong> (Contact: 01@0101010101.com)<br />- All rights including copyright of uploaded files are reserved to users.<br />- Userdata is saved in RAID 1 storage, but data can be lost. <?php echo $str['servicename']; ?> does not guarantee for the data.<br />- Any file downloaded more than 5000 times daily may be deleted.<br />
			</p>
		</div>
	</div>
	<?php } ?>
	<?php require_once('footer.php'); ?>
</body>
</html>