<div id="gameWrapper">
	<div id="gameHead">
	
		<?php if($errormessage): ?>
		<div class="error"><?= $errormessage ?></div>
		<?php endif; ?>
		<?php if($message): ?>
		<div class="message"><?= $message ?></div>
		<?php endif; ?>
		
		<h1>Oops!</h1>
		<p>&nbsp;</p>
		<h2 style="text-align: center;">Javascript Required</h2>
		<p style="text-align: center;">Many of the features on this website require Javascript to be enabled. Please enable javascript.<br />If your browser doesn't support Javascript, please leave 1995 behind and download a modern browser.</p>
		<p>&nbsp;</p>
		<div style="width: 200px; margin: auto;">
			<p style="font-size: 18px;"><img src="<?= $baseurl; ?>/images/common/icons/browsers/Chrome-32.png" style="vertical-align: middle;" />&nbsp;<a style="color: orange;" href="http://www.google.com/chrome?hl=en-GB" target="_blank">Google Chrome</a></p>
			<p style="font-size: 18px;"><img src="<?= $baseurl; ?>/images/common/icons/browsers/Firefox-32.png" style="vertical-align: middle;" />&nbsp;<a style="color: orange;" href="http://www.getfirefox.com" target="_blank">Mozilla Firefox</a></p>
			<p style="font-size: 18px;"><img src="<?= $baseurl; ?>/images/common/icons/browsers/Opera-32.png" style="vertical-align: middle;" />&nbsp;<a style="color: orange;" href="http://www.opera.com" target="_blank">Opera</a></p>
			<p style="font-size: 18px;"><img src="<?= $baseurl; ?>/images/common/icons/browsers/Safari-32.png" style="vertical-align: middle;" />&nbsp;<a style="color: orange;" href="http://www.apple.com/safari/" target="_blank">Apple Safari</a></p>
		</div>
	</div>
</div>
