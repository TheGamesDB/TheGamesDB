<?php
## Connect to the database
include("include.php");
?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>TheGamesDB.net Artwork Upload Area</title>

<!-- Load jQuery & jQuery UI -->
<link rel="stylesheet" href="<?php echo $baseurl; ?>/js/jquery-ui/css/trontastic/jquery-ui-1.8.14.custom.css" type="text/css" media="all" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
<!-- Third party script for BrowserPlus runtime (Google Gears included in Gears runtime now) -->
<script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script>

<!-- Load plupload and all it's runtimes and finally the jQuery UI queue widget -->
<link rel="stylesheet" href="<?= $baseurl; ?>/js/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css" type="text/css" media="all" />
<script type="text/javascript" src="<?= $baseurl ?>/js/plupload/js/plupload.full.js"></script>
<script type="text/javascript" src="<?= $baseurl ?>/js/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>

<style type="text/css">
	.button {
		border: 1px solid #555;
		border-radius: 5px;
		box-shadow: 0px 0px 1px 0px #FFF;
		color: #fff;
		background-color: #222;
		margin: 0px 5px 0px 2px;
		padding: 4px 8px;
		cursor: pointer;
	}
</style>

</head>

<body style="background-color: #111111;">

<div id="uploadArea" style="width: 100%; background-color: #111111; color: #FFFFFF; font-family:Arial; font-size:10pt; margin: auto;">
	
	<div style="background-color: #444444; padding: 10px; border: 1px solid #999999; border-radius: 6px; margin: 4px; float: left; width: 442px;">
		<h3 style="padding: 0px; margin: 0px;"><img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> Front Boxart</h3>
		<p id="front_runtime" style="padding: 0px; margin: 0px; color: #aaa; padding: 5px; font-decoration: italic;">We didn't manage to find a runtime for uploading. Your uploads will not work.</p>
		<div id="front_container">
			<table style="width: 100%; border: 0px;">
				<tr>
					<td>
						<div id="front_filelist"><strong>Files To Upload:</strong></div>
						<p style="text-align: center;"><a id="front_pickfiles" class="button" href="#">[Select files]</a></p>
						<p style="text-align: center;"><a id="front_uploadfiles" class="button" href="#">[Upload files]</a></p>
					</td>
					<td style="width: 289px;">			
						<div id="front_drop" style="height: 100px; border: 2px dashed #fff; background-color: #222; border-radius: 20px;">
							<h2 style="line-height: 66px; text-align: center; color: #999"><img src="<?= $baseurl ?>/images/common/icons/dropbox_64.png" style="vertical-align: middle; margin-right: 20px;" />Drag Files Here</h2>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div style="background-color: #444444; padding: 10px; border: 1px solid #999999; border-radius: 6px; margin: 4px; float: left; width: 442px;">
		<h3 style="padding: 0px; margin: 0px;"><img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> Rear Boxart</h3>
		<p id="back_runtime" style="padding: 0px; margin: 0px; color: #aaa; padding: 5px; font-decoration: italic;">We didn't manage to find a runtime for uploading. Your uploads will not work.</p>
		<div id="back_container">
			<table style="width: 100%; border: 0px;">
				<tr>
					<td>
						<div id="back_filelist"><strong>Files To Upload:</strong></div>
						<p style="text-align: center;"><a id="back_pickfiles" class="button" href="#">[Select files]</a></p>
						<p style="text-align: center;"><a id="back_uploadfiles" class="button" href="#">[Upload files]</a></p>
					</td>
					<td style="width: 289px;">			
						<div id="back_drop" style="height: 100px; border: 2px dashed #fff; background-color: #222; border-radius: 20px;">
							<h2 style="line-height: 66px; text-align: center; color: #999"><img src="<?= $baseurl ?>/images/common/icons/dropbox_64.png" style="vertical-align: middle; margin-right: 20px;" />Drag Files Here</h2>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div style="clear: both;"></div>
	
	<div style="background-color: #444444; padding: 10px; border: 1px solid #999999; border-radius: 6px; margin: 4px; float: left; width: 442px;">
		<h3 style="padding: 0px; margin: 0px;"><img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> Fanart</h3>
		<p id="fanart_runtime" style="padding: 0px; margin: 0px; color: #aaa; padding: 5px; font-decoration: italic;">We didn't manage to find a runtime for uploading. Your uploads will not work.</p>
		<div id="fanart_container">
			<table style="width: 100%; border: 0px;">
				<tr>
					<td>
						<div id="fanart_filelist"><strong>Files To Upload:</strong></div>
						<p style="text-align: center;"><a id="fanart_pickfiles" class="button" href="#">[Select files]</a></p>
						<p style="text-align: center;"><a id="fanart_uploadfiles" class="button" href="#">[Upload files]</a></p>
					</td>
					<td style="width: 289px;">			
						<div id="fanart_drop" style="height: 100px; border: 2px dashed #fff; background-color: #222; border-radius: 20px;">
							<h2 style="line-height: 66px; text-align: center; color: #999"><img src="<?= $baseurl ?>/images/common/icons/dropbox_64.png" style="vertical-align: middle; margin-right: 20px;" />Drag Files Here</h2>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div style="background-color: #444444; padding: 10px; border: 1px solid #999999; border-radius: 6px; margin: 4px; float: left; width: 442px;">
		<h3 style="padding: 0px; margin: 0px;"><img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> Banners</h3>
		<p id="banner_runtime" style="padding: 0px; margin: 0px; color: #aaa; padding: 5px; font-decoration: italic;">We didn't manage to find a runtime for uploading. Your uploads will not work.</p>
		<div id="banner_container">
			<table style="width: 100%; border: 0px;">
				<tr>
					<td>
						<div id="banner_filelist"><strong>Files To Upload:</strong></div>
						<p style="text-align: center;"><a id="banner_pickfiles" class="button" href="#">[Select files]</a></p>
						<p style="text-align: center;"><a id="banner_uploadfiles" class="button" href="#">[Upload files]</a></p>
					</td>
					<td style="width: 289px;">			
						<div id="banner_drop" style="height: 100px; border: 2px dashed #fff; background-color: #222; border-radius: 20px;">
							<h2 style="line-height: 66px; text-align: center; color: #999"><img src="<?= $baseurl ?>/images/common/icons/dropbox_64.png" style="vertical-align: middle; margin-right: 20px;" />Drag Files Here</h2>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div style="clear: both;"></div>
	
	<div style="background-color: #444444; padding: 10px; border: 1px solid #999999; border-radius: 6px; margin: 4px; float: left; width: 442px;">
		<h3 style="padding: 0px; margin: 0px;"><img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> Screenshots</h3>
		<p id="screenshot_runtime" style="padding: 0px; margin: 0px; color: #aaa; padding: 5px; font-decoration: italic;">We didn't manage to find a runtime for uploading. Your uploads will not work.</p>
		<div id="screenshot_container">
			<table style="width: 100%; border: 0px;">
				<tr>
					<td>
						<div id="screenshot_filelist"><strong>Files To Upload:</strong></div>
						<p style="text-align: center;"><a id="screenshot_pickfiles" class="button" href="#">[Select files]</a></p>
						<p style="text-align: center;"><a id="screenshot_uploadfiles" class="button" href="#">[Upload files]</a></p>
					</td>
					<td style="width: 289px;">			
						<div id="screenshot_drop" style="height: 100px; border: 2px dashed #fff; background-color: #222; border-radius: 20px;">
							<h2 style="line-height: 66px; text-align: center; color: #999"><img src="<?= $baseurl ?>/images/common/icons/dropbox_64.png" style="vertical-align: middle; margin-right: 20px;" />Drag Files Here</h2>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div style="background-color: #444444; padding: 10px; border: 1px solid #999999; border-radius: 6px; margin: 4px; float: left; width: 442px;">
		<h3 style="padding: 0px; margin: 0px;"><img src='<?= $baseurl ?>/images/common/icons/upload_24.png' style='border: 0px; vertical-align: -7px;' alt='Upload Artwork' /> ClearLOGO's</h3>
		<p id="clearlogo_runtime" style="padding: 0px; margin: 0px; color: #aaa; padding: 5px; font-decoration: italic;">We didn't manage to find a runtime for uploading. Your uploads will not work.</p>
		<div id="clearlogo_container">
			<table style="width: 100%; border: 0px;">
				<tr>
					<td>
						<div id="clearlogo_filelist"><strong>Files To Upload:</strong></div>
						<p style="text-align: center;"><a id="clearlogo_pickfiles" class="button" href="#">[Select files]</a></p>
						<p style="text-align: center;"><a id="clearlogo_uploadfiles" class="button" href="#">[Upload files]</a></p>
					</td>
					<td style="width: 289px;">			
						<div id="clearlogo_drop" style="height: 100px; border: 2px dashed #fff; background-color: #222; border-radius: 20px;">
							<h2 style="line-height: 66px; text-align: center; color: #999"><img src="<?= $baseurl ?>/images/common/icons/dropbox_64.png" style="vertical-align: middle; margin-right: 20px;" />Drag Files Here</h2>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div style="clear: both;"></div>
</div>

<!-- Start plUpload -->
<script type="text/javascript">
// Boxart Front
$(function() {
	var boxfront_uploader = new plupload.Uploader({
		runtimes : 'html5,flash,html4',
		browse_button : 'front_pickfiles',
		container : 'front_container',
		drop_element : 'front_drop',
		max_file_size : '8mb',
		url : '/upload.php?gameid=<?= $gameid ?>&arttype=boxfront',
		flash_swf_url : '/js/plupload/js/plupload.flash.swf',
		silverlight_xap_url : '/js/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,png"},
		]
	});

	boxfront_uploader.bind('Init', function(up, params) {
		if(params.runtime != "html5")
		{
			$('#front_runtime').html("You are using " + params.runtime + ". Drag & Drop is only supported in html5.");
		}
		else
		{
			$('#front_runtime').html("You are using " + params.runtime);
		}
	});

	$('#front_uploadfiles').click(function(e) {
		boxfront_uploader.start();
		e.preventDefault();
	});

	boxfront_uploader.init();

	boxfront_uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$('#front_filelist').append(
				'<div style="padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;" id="' + file.id + '"><img class=\"tick\" src=\"<?= $baseurl ?>/images/common/icons/tick_16.png\" style=\"display: none; vertical-align: -2px;\" />&nbsp;File: ' +
				file.name + ' <em>(' + plupload.formatSize(file.size) + ')</em> <div style=\"margin: auto; margin-top: 3px; width: 100px; height: 20px; border: 1px solid #fff; border-radius: 6px; background-color: #222;\"><div class="progressbar" style=\"width: 0px; height: 16px; padding: 2px 0px; background-color: #ccc; border-radius: 6px; text-align: center;\"><b style="font-size: 16px; color: #222;"></b></div></div>' +
			'</div>');
		});

		up.refresh(); // Reposition Flash/Silverlight
	});

	boxfront_uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
		$('#' + file.id + " .progressbar").css("width", (file.percent));
	});

	boxfront_uploader.bind('Error', function(up, err) {
		$('#front_filelist').append("<div style=\"padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;\"><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;"  +
			(err.file ? "File: " + err.file.name + "<br />" : "") + err.message + "</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	boxfront_uploader.bind('FileUploaded', function(up, file, info) {
		$('#' + file.id + " .tick").show();
		
		//printObject(info);
		
		var response = jQuery.parseJSON(info.response);
		
		if(response.error.message.length > 0)
		{
			$("#" + file.id).append("<hr /><div><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;" + response.error.message + "</div>");
			//alert(response.error.message);
		}
	});
});

// Boxart Back
$(function() {
	var boxback_uploader = new plupload.Uploader({
		runtimes : 'html5,flash,html4',
		browse_button : 'back_pickfiles',
		container : 'back_container',
		drop_element : 'back_drop',
		max_file_size : '8mb',
		url : '/upload.php?gameid=<?= $gameid ?>&arttype=boxback',
		flash_swf_url : '/js/plupload/js/plupload.flash.swf',
		silverlight_xap_url : '/js/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,png"},
		]
	});

	boxback_uploader.bind('Init', function(up, params) {
		if(params.runtime != "html5")
		{
			$('#back_runtime').html("You are using " + params.runtime + ". Drag & Drop is only supported in html5.");
		}
		else
		{
			$('#back_runtime').html("You are using " + params.runtime);
		}
	});

	$('#back_uploadfiles').click(function(e) {
		boxback_uploader.start();
		e.preventDefault();
	});

	boxback_uploader.init();

	boxback_uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$('#back_filelist').append(
				'<div style="padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;" id="' + file.id + '"><img class=\"tick\" src=\"<?= $baseurl ?>/images/common/icons/tick_16.png\" style=\"display: none; vertical-align: -2px;\" />&nbsp;File: ' +
				file.name + ' <em>(' + plupload.formatSize(file.size) + ')</em> <div style=\"margin: auto; margin-top: 3px; width: 100px; height: 20px; border: 1px solid #fff; border-radius: 6px; background-color: #222;\"><div class="progressbar" style=\"width: 0px; height: 16px; padding: 2px 0px; background-color: #ccc; border-radius: 6px; text-align: center;\"><b style="font-size: 16px; color: #222;"></b></div></div>' +
			'</div>');
		});

		up.refresh(); // Reposition Flash/Silverlight
	});

	boxback_uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
		$('#' + file.id + " .progressbar").css("width", (file.percent));
	});

	boxback_uploader.bind('Error', function(up, err) {
		$('#back_filelist').append("<div style=\"padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;\"><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;"  +
			(err.file ? "File: " + err.file.name + "<br />" : "") + err.message + "</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	boxback_uploader.bind('FileUploaded', function(up, file, info) {
		$('#' + file.id + " .tick").show();
		
		//printObject(info);
		
		var response = jQuery.parseJSON(info.response);
		
		if(response.error.message.length > 0)
		{
			$("#" + file.id).append("<hr /><div><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;" + response.error.message + "</div>");
			//alert(response.error.message);
		}
	});
});

// Fanart
$(function() {
	var fanart_uploader = new plupload.Uploader({
		runtimes : 'html5,flash,html4',
		browse_button : 'fanart_pickfiles',
		container : 'fanart_container',
		drop_element : 'fanart_drop',
		chunk_size : '1mb',
		max_file_size : '8mb',
		url : '/upload.php?gameid=<?= $gameid ?>&arttype=fanart',
		flash_swf_url : '/js/plupload/js/plupload.flash.swf',
		silverlight_xap_url : '/js/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,png"},
		]
});

	fanart_uploader.bind('Init', function(up, params) {
		if(params.runtime != "html5")
		{
			$('#fanart_runtime').html("You are using " + params.runtime + ". Drag & Drop is only supported in html5.");
		}
		else
		{
			$('#fanart_runtime').html("You are using " + params.runtime);
		}
	});

	$('#fanart_uploadfiles').click(function(e) {
		fanart_uploader.start();
		e.preventDefault();
	});

	fanart_uploader.init();

	fanart_uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$('#fanart_filelist').append(
				'<div style="padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;" id="' + file.id + '"><img class=\"tick\" src=\"<?= $baseurl ?>/images/common/icons/tick_16.png\" style=\"display: none; vertical-align: -2px;\" />&nbsp;File: ' +
				file.name + ' <em>(' + plupload.formatSize(file.size) + ')</em> <div style=\"margin: auto; margin-top: 3px; width: 100px; height: 20px; border: 1px solid #fff; border-radius: 6px; background-color: #222;\"><div class="progressbar" style=\"width: 0px; height: 16px; padding: 2px 0px; background-color: #ccc; border-radius: 6px; text-align: center;\"><b style="font-size: 16px; color: #222;"></b></div></div>' +
			'</div>');
		});

		up.refresh(); // Reposition Flash/Silverlight
	});

	fanart_uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
		$('#' + file.id + " .progressbar").css("width", (file.percent));
	});

	fanart_uploader.bind('Error', function(up, err) {
		$('#fanart_filelist').append("<div style=\"padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;\"><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;"  +
			(err.file ? "File: " + err.file.name + "<br />" : "") + err.message + "</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	fanart_uploader.bind('FileUploaded', function(up, file, info) {
		$('#' + file.id + " .tick").show();
		
		//printObject(info);
		
		var response = jQuery.parseJSON(info.response);
		
		if(response.error.message.length > 0)
		{
			$("#" + file.id).append("<hr /><div><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;" + response.error.message + "</div>");
			//alert(response.error.message);
		}
	});
});

// Banner
$(function() {
	var banner_uploader = new plupload.Uploader({
		runtimes : 'html5,flash,html4',
		browse_button : 'banner_pickfiles',
		container : 'banner_container',
		drop_element : 'banner_drop',
		chunk_size : '1mb',
		max_file_size : '8mb',
		url : '/upload.php?gameid=<?= $gameid ?>&arttype=banner',
		flash_swf_url : '/js/plupload/js/plupload.flash.swf',
		silverlight_xap_url : '/js/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,png"},
		]
});

	banner_uploader.bind('Init', function(up, params) {
		if(params.runtime != "html5")
		{
			$('#banner_runtime').html("You are using " + params.runtime + ". Drag & Drop is only supported in html5.");
		}
		else
		{
			$('#banner_runtime').html("You are using " + params.runtime);
		}
	});

	$('#banner_uploadfiles').click(function(e) {
		banner_uploader.start();
		e.preventDefault();
	});

	banner_uploader.init();

	banner_uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$('#banner_filelist').append(
				'<div style="padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;" id="' + file.id + '"><img class=\"tick\" src=\"<?= $baseurl ?>/images/common/icons/tick_16.png\" style=\"display: none; vertical-align: -2px;\" />&nbsp;File: ' +
				file.name + ' <em>(' + plupload.formatSize(file.size) + ')</em> <div style=\"margin: auto; margin-top: 3px; width: 100px; height: 20px; border: 1px solid #fff; border-radius: 6px; background-color: #222;\"><div class="progressbar" style=\"width: 0px; height: 16px; padding: 2px 0px; background-color: #ccc; border-radius: 6px; text-align: center;\"><b style="font-size: 16px; color: #222;"></b></div></div>' +
			'</div>');
		});

		up.refresh(); // Reposition Flash/Silverlight
	});

	banner_uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
		$('#' + file.id + " .progressbar").css("width", (file.percent));
	});

	banner_uploader.bind('Error', function(up, err) {
		$('#banner_filelist').append("<div style=\"padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;\"><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;"  +
			(err.file ? "File: " + err.file.name + "<br />" : "") + err.message + "</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	banner_uploader.bind('FileUploaded', function(up, file, info) {
		$('#' + file.id + " .tick").show();
		
		//printObject(info);
		
		var response = jQuery.parseJSON(info.response);
		
		if(response.error.message.length > 0)
		{
			$("#" + file.id).append("<hr /><div><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;" + response.error.message + "</div>");
		    //alert(response.error.message);
		}
	});
});

// Screenshot
$(function() {
	var screenshot_uploader = new plupload.Uploader({
		runtimes : 'html5,flash,html4',
		browse_button : 'screenshot_pickfiles',
		container : 'screenshot_container',
		drop_element : 'screenshot_drop',
		chunk_size : '1mb',
		max_file_size : '8mb',
		url : '/upload.php?gameid=<?= $gameid ?>&arttype=screenshot',
		flash_swf_url : '/js/plupload/js/plupload.flash.swf',
		silverlight_xap_url : '/js/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,png"},
		]
});

	screenshot_uploader.bind('Init', function(up, params) {
		if(params.runtime != "html5")
		{
			$('#screenshot_runtime').html("You are using " + params.runtime + ". Drag & Drop is only supported in html5.");
		}
		else
		{
			$('#screenshot_runtime').html("You are using " + params.runtime);
		}
	});

	$('#screenshot_uploadfiles').click(function(e) {
		screenshot_uploader.start();
		e.preventDefault();
	});

	screenshot_uploader.init();

	screenshot_uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$('#screenshot_filelist').append(
				'<div style="padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;" id="' + file.id + '"><img class=\"tick\" src=\"<?= $baseurl ?>/images/common/icons/tick_16.png\" style=\"display: none; vertical-align: -2px;\" />&nbsp;File: ' +
				file.name + ' <em>(' + plupload.formatSize(file.size) + ')</em> <div style=\"margin: auto; margin-top: 3px; width: 100px; height: 20px; border: 1px solid #fff; border-radius: 6px; background-color: #222;\"><div class="progressbar" style=\"width: 0px; height: 16px; padding: 2px 0px; background-color: #ccc; border-radius: 6px; text-align: center;\"><b style="font-size: 16px; color: #222;"></b></div></div>' +
			'</div>');
		});

		up.refresh(); // Reposition Flash/Silverlight
	});

	screenshot_uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
		$('#' + file.id + " .progressbar").css("width", (file.percent));
	});

	screenshot_uploader.bind('Error', function(up, err) {
		$('#screenshot_filelist').append("<div style=\"padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;\"><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;"  +
			(err.file ? "File: " + err.file.name + "<br />" : "") + err.message + "</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	screenshot_uploader.bind('FileUploaded', function(up, file, info) {
		$('#' + file.id + " .tick").show();
		
		//printObject(info);
		
		var response = jQuery.parseJSON(info.response);
		
		if(response.error.message.length > 0)
		{
			$("#" + file.id).append("<hr /><div><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;" + response.error.message + "</div>");
			//alert(response.error.message);
		}
	});
});

// ClearLOGO
$(function() {
	var clearlogo_uploader = new plupload.Uploader({
		runtimes : 'html5,flash,html4',
		browse_button : 'clearlogo_pickfiles',
		container : 'clearlogo_container',
		drop_element : 'clearlogo_drop',
		chunk_size : '1mb',
		max_file_size : '8mb',
		url : '/upload.php?gameid=<?= $gameid ?>&arttype=clearlogo',
		flash_swf_url : '/js/plupload/js/plupload.flash.swf',
		silverlight_xap_url : '/js/plupload/js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,png"},
		]
});

	clearlogo_uploader.bind('Init', function(up, params) {
		if(params.runtime != "html5")
		{
			$('#clearlogo_runtime').html("You are using " + params.runtime + ". Drag & Drop is only supported in html5.");
		}
		else
		{
			$('#clearlogo_runtime').html("You are using " + params.runtime);
		}
	});

	$('#clearlogo_uploadfiles').click(function(e) {
		clearlogo_uploader.start();
		e.preventDefault();
	});

	clearlogo_uploader.init();

	clearlogo_uploader.bind('FilesAdded', function(up, files) {
		$.each(files, function(i, file) {
			$('#clearlogo_filelist').append(
				'<div style="padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;" id="' + file.id + '"><img class=\"tick\" src=\"<?= $baseurl ?>/images/common/icons/tick_16.png\" style=\"display: none; vertical-align: -2px;\" />&nbsp;File: ' +
				file.name + ' <em>(' + plupload.formatSize(file.size) + ')</em> <div style=\"margin: auto; margin-top: 3px; width: 100px; height: 20px; border: 1px solid #fff; border-radius: 6px; background-color: #222;\"><div class="progressbar" style=\"width: 0px; height: 16px; padding: 2px 0px; background-color: #ccc; border-radius: 6px; text-align: center;\"><b style="font-size: 16px; color: #222;"></b></div></div>' +
			'</div>');
		});

		up.refresh(); // Reposition Flash/Silverlight
	});

	clearlogo_uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
		$('#' + file.id + " .progressbar").css("width", (file.percent));
	});

	clearlogo_uploader.bind('Error', function(up, err) {
		$('#clearlogo_filelist').append("<div style=\"padding: 4px; margin: 3px; border: 1px dotted #fff; border-radius: 6px; background-color: #333;\"><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;"  +
			(err.file ? "File: " + err.file.name + "<br />" : "") + err.message + "</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	clearlogo_uploader.bind('FileUploaded', function(up, file, info) {
		$('#' + file.id + " .tick").show();
		
		//printObject(info);
		
		var response = jQuery.parseJSON(info.response);
		
		if(response.error.message.length > 0)
		{
			$("#" + file.id).append("<hr /><div><img src=\"<?= $baseurl ?>/images/common/icons/cross_16.png\" style=\"vertical-align: -2px;\" />&nbsp;" + response.error.message + "</div>");
			//alert(response.error.message);
		}
	});
});

function printObject(o) {
  var out = '';
  for (var p in o) {
    out += p + ': ' + o[p] + '\n';
  }
  alert(out);
}
</script>
<!-- End plUpload -->

</body>
</html>