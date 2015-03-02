<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="./" hreflang="<?php echo $_SESSION['lang'];?>"><?php echo $str['servicename']; ?></a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li<?php if ($mode === 'upload') echo ' class="active"'; ?>><a href="./" hreflang="<?php echo $_SESSION['lang'];?>"><?php echo $str['newfile']; ?></a></li>
					<li<?php if ($mode === 'list') echo ' class="active"'; ?>><a href="./list" hreflang="<?php echo $_SESSION['lang'];?>"><?php echo $str['list']; ?></a></li>
					<li<?php if ($mode === 'about') echo ' class="active"'; ?>><a href="./about" hreflang="ko"><?php echo $str['about']; ?></a></li>
<?php
					if ($mode === 'download') {
						echo '					<li class="active"><a href="#">' . $str['download'] . ' - ' . ((mb_strlen($metadata['filename'], 'UTF-8') > 10) ? (mb_substr($metadata['filename'], 0, 7, 'UTF-8') . '...') : ($metadata['filename'])) . '</a></li>';
					}
					?>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="./lang"<?php if ($_SESSION['lang'] == 'ko') {
						echo ' hreflang="en">' . $str['en'];
					} else {
						echo ' hreflang="ko">' . $str['ko'];
					}  ?></a></li>
				</ul>
			</div>
		</diV>
	</nav>
