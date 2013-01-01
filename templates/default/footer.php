					<!--/* Ad4Game iFrame Tag */-->
					<div style="padding: 10px; text-align: center; background-color: #0F0F0F;">
						<iframe id='a199c979' name='a199c979' src='http://ads.ad4game.com/www/delivery/afr.php?n=a199c979&amp;zoneid=27520&amp;target=_blank&amp;cb=INSERT_RANDOM_NUMBER_HERE' framespacing='0' frameborder='no' scrolling='no' width='728' height='90' allowtransparency='true'><a href='http://ads.ad4game.com/www/delivery/ck.php?n=ab890b4b&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://ads.ad4game.com/www/delivery/avw.php?zoneid=27520&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=ab890b4b' border='0' alt='' /></a></iframe>
						<script type='text/javascript' src='http://ads.ad4game.com/www/delivery/ag.php'></script>
					</div>
					
				</div>				

			</div>
			
		</div>
		
		
		<div id="footer" style="position: fixed; width: 100%; bottom: 0px; z-index: 200; text-align: center;">
			<div id="footerbarShadow" style="width: 100%; background: url(<?php echo $baseurl; ?>/images/bg_footerbar-shadow.png) repeat-x center center; height: 15px; opacity: 0.5"></div>
			<div id="footerbar" style="width: 100%; background: url(<?php echo $baseurl; ?>/images/bg_footerbar.png) repeat-x center center; height: 30px;">
				<div id="Terms" style="padding-top: 5px; padding-left: 25px; float: left; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
					<a href="<?=$baseurl?>/terms/" style="color: #333;">Terms &amp; Conditions</a>
				</div>
				
				<div id="theTeam" style="padding-top: 5px; padding-right: 25px; float: right; font-family: 'Segoe UI','HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Arial,Tahoma,Verdana,sans-serif; font-size: 14px; text-shadow: 0px 2px 6px #666;">
					<a href="http://wiki.thegamesdb.net" style="color: #333;">TheGamesDB Wiki</a>
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

		<script type="text/javascript">
		$(function() {
			var availableTags = [
				<?php
					if($titlesResult = mysql_query(" SELECT DISTINCT GameTitle FROM games ORDER BY GameTitle ASC; "))
					{
						while($titlesObj = mysql_fetch_object($titlesResult))
						{
							echo " \"$titlesObj->GameTitle\",\n";
						}
					}
				?>
			];
			$( ".autosearch" ).autocomplete({
				source: availableTags,
				position: { offset: "-30 3" },
				appendTo: '#autocompleteContainer',
				select: function(event, ui) { this.form.submit(); }
			});
		});
	</script>
		
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
		
		<script type="text/javascript">
			// jQuery Snow Script Instance
			// $('#main').snowfall({ flakeCount : 100, maxSpeed : 10, round: true, shadow: true, minSize: 2, maxSize: 4 });
		</script>
		
		<!-- Start Force instant run of cufon to circumvent IE delay -->
		<script type="text/javascript"> Cufon.now(); </script>
		<!-- End Force instant run of cufon to circumvent IE delay -->
		
		<!--/* Ad4Game Site-Skin Tag */-->
		<script type='text/javascript'><!--//<![CDATA[
		var ad4game_siteskin = {
			'contentWidth' : '1000px', // size of the regular content in pixel
			'leftOffset'   : '0px',   // left ad position adjustment -/+ pixel left/right
			'topOffset'    : '141px',   // top position of the ads
			'rightOffset'  : '0px',   // right ad adjustment
			'zIndex'       : '4',     // css style z-index for the ads
			'fixed'        : '0',     // 0=>ads scroll with content, 1=>ads stay fixed
			'hide'         : 'none',  // hide a banner: one of 'none', 'left', 'right'
			'random'       : Math.floor(Math.random() * 99999999999)
		};
		document.write('\x3cscript type="text/javascript" src="http://ads.ad4game.com/www/delivery/siteskin.php?zoneid=27522&target=_blank&charset=UTF-8&withtext=1&cb='+ad4game_siteskin.random+'"\x3e\x3c/script\x3e');
		//]]>--></script>
		
    </body>
	
</html>