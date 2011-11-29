</div><!-- end contentWrapper -->
</div><!-- end page -->

<div id="footer-wrap">

	<div id="footer" style="position: fixed; width: 100%; bottom: 0px; z-index: 200; text-align: center;">
		<div id="footerbarShadow" style="width: 100%; background: url(<?php bloginfo('template_directory'); ?>/img/bg_footerbar-shadow.png) repeat-x center center; height: 15px; opacity: 0.5"></div>
		<div id="footerbar" style="width: 100%; background: url(<?php bloginfo('template_directory'); ?>/img/bg_footerbar.png) repeat-x center center; height: 30px;">
			<div id="Terms" style="padding-top: 5px; padding-left: 25px; float: left; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
				<a href="<?=$baseurl?>/terms/" style="color: #333;">Terms &amp; Conditions</a>
			</div>
			
			<div id="theTeam" style="padding-top: 5px; padding-right: 25px; float: right; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
				<a rel="facebox" href="#credits" style="color: #333;">TheGamesDB Team</a>
			</div>
			
			<div style="padding-top: 4px;">
				<a href="http://www.facebook.com/thegamesdb" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/facebook_dark.png" alt="Visit us on Facebook" title="Visit us on Facebook" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/facebook_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/facebook_dark.png')" /></a>
				<a href="http://twitter.com/thegamesdb" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/twitter_dark.png" alt="Visit us on Twitter" title="Visit us on Twitter" onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/twitter_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/twitter_dark.png')" /></a>
				<a href="https://plus.google.com/116977810662942577082/posts" target="_blank"><img src="<?= $baseurl ?>/images/common/icons/social/24/google_dark.png" alt="Visit us on Google Plus" title="Visit us on Google Plus"  onmouseover="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/google_active.png')" onmouseout="$(this).attr('src', '<?= $baseurl ?>/images/common/icons/social/24/google_dark.png')" /></a>
			</div>
		</div>
	</div>
	
	<div id="credits" style="display: none;">
	<div style="font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; text-shadow: 0px 2px 6px #666;">
		<h1>The Team</h1>
		<p>Here at TheGamesDB.net we have a small but very passionate and dedicated team.</p>
		<p>We are always striving to find ways to improve this site to provide our users with the best experience possible.</p>
		<p>&nbsp;</p>
		<p><strong>Owner:</strong> Scott Brant <em>(smidley)</em></p>
		<p><strong>Coding &amp; Design:</strong> Alex Nazaruk <em>(flexage)</em></p>
		<p><strong>Coding &amp; Design:</strong> Matt McLaughlin</p>
		<p>&nbsp;</p>
		<p>We would also like to give a big thanks to all our contributers, without your involvement this site wouldn't be as good as it is today.</p>
	</div>
	</div>

</div><!-- end footer-wrap -->

<div id="credits" style="display: none;">
	<div style="font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; color: #333 !important; text-shadow: 0px 2px 6px #666;">
		<h1>The Team</h1>
		<p>Here at TheGamesDB.net we have a small but very passionate and dedicated team.</p>
		<p>We are always striving to find ways to improve this site to provide our users with the best experience possible.</p>
		<p>&nbsp;</p>
		<p><strong>Owner:</strong> Scott Brant <em>(smidley)</em></p>
		<p><strong>Coding &amp; Design:</strong> Alex Nazaruk <em>(flexage)</em></p>
		<p><strong>Coding &amp; Design:</strong> Matt McLaughlin</p>
		<p>&nbsp;</p>
		<p>We would also like to give a big thanks to all our contributers, without your involvement this site wouldn't be as good as it is today.</p>
	</div>
</div>
	
<?php wp_footer(); ?>

<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-16803563-1']);
	_gaq.push(['_trackPageview']);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>

</body>
</html>