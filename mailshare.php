<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>TheGamesDB.net - Share via Email</title>
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/jquery-ui/css/trontastic/jquery-ui-1.8.14.custom.css" type="text/css" media="all" />
	<script type="text/javascript" src="<?php echo $baseurl; ?>/js/jquery-ui/js/jquery-ui-1.8.14.custom.min.js"></script>
	<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/ajax-fancy-captcha-php/captcha.css" type="text/css" media="all" />
	<script type="text/javascript" src="<?php echo $baseurl; ?>/js/ajax-fancy-captcha-php/jquery.captcha.js"></script>
	<script type="text/javascript" charset="utf-8">
		$(function() {
			$(".ajax-fc-container").captcha({formId: "shareviamail", captchaDir: "<?= $baseurl ?>/js/ajax-fancy-captcha-php", url: "<?= $baseurl ?>/js/ajax-fancy-captcha-php/captcha.php"});
		});
	</script>
	
	<style type="text/css">
		#sharemail p {
			margin: 0px;
			padding: 0px;
		}
	</style>
</head>

<body>
	<div id="sharemail" style="font-family: 'Arial', 'Tahoma', serif;">
		<h2>Share via Email</h2>
		<p>&nbsp;</p>
		<p>To share this page with a friend, please enter the required details below.</p>
		<p>&nbsp;</p>
		<form id="shareviamail" method="post" action="<?= $url; ?>/" onsubmit="if( $('#fromname').val() == '' || $('#fromaddress').val() == '' || $('#toaddress').val() == '' ) { alert('Whoops! Please fill in all required fields!'); return false; }">
			<p>Your Name:<span style="color: orange; font-size: small; vertical-align: super;">*required</span></p>
			<p><input style="width: 350px;" type="text" id="fromname" name="fromname" /></p>
			<p>&nbsp;</p>
			<p>Your email address:<span style="color: orange; font-size: small; vertical-align: super;">*required</span></p>
			<p><input style="width: 350px;" type="text" id="fromaddress" name="fromaddress" /></p>
			<p>&nbsp;</p>
			<p>Friends email address:<span style="color: orange; font-size: small; vertical-align: super;">*required</span></p>
			<p><input style="width: 350px;" type="text" id="toaddress" name="toaddress" /></p>
			<p>&nbsp;</p>
			<p>Message:<span style="color: green; font-size: small; vertical-align: super;">*optional</span></p>
			<p><textarea style="width: 350px; height: 160px" name="messagecontent"></textarea></p>
			<p>&nbsp;</p>
			<!-- Begin of captcha -->
			<div class="captchaForm ajax-fc-container" style="width: 312px; margin: auto; margin-bottom: 15px;"></div>
			<!-- End of captcha -->
			<input type="hidden" name="urlsubject" value="<?= $urlsubject; ?>" />
			<input type="hidden" name="url" value="<?= $url; ?>" />
			<p style="text-align: right;"><input type="submit" id="submit" name="function" value="Share via Email" style="padding: 5px;" /></p>
		</form>
	</div>
</body>