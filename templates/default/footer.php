					<!-- Page Footer -->
					<div style="padding: 10px; text-align: center; background-color: #0F0F0F; color: #666666; border-top: 2px solid #333; border-bottom: 2px solid #333;">
						<table style="width: 100%;" cellpadding="2">
							<tr>
								<td colspan="5"><h3>Friends of TheGamesDB.net</h3></td>
							</tr>
							<tr>
								<td style="width: 30%;"><a style="color: orange;" href="http://hostsphere.co.uk/" alt="HostSphere | Unlimited Web Hosting | VPS | Hybrid Servers | Dedicated Servers" title="HostSphere | Unlimited Web Hosting | VPS | Hybrid Servers | Dedicated Servers">HostSphere.co.uk</a></td>
								<td style="width: 5%;">|</td>
								<td style="width: 30%;"><a style="color: orange;" href="http://xbmc.org/">XBMC.org</a></td>
								<td style="width: 5%;">|</td>
								<td style="width: 30%;"><a style="color: orange;" href="http://fanart.tv/">Fanart.tv</a></td>
							</tr>
						</table>
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
					<a href="http://wiki.thegamesdb.net" style="color: #333;">TheGamesDB Wiki</a> | <a href="<?php echo $baseurl; ?>/showcase" style="color: #333;">Showcase</a> 
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
			// Ajax Quick Search
			$( ".ajaxSearch" ).bind("focus input paste", function(event) {
				var currentElement = $(this);
				if ( this.value )
				{
					$.post( "<?php echo $baseurl; ?>/scripts/ajax_searchgame.php", "searchterm=" + $(this).val(), function( data ) {
						if (data.result == 'success')
						{	
						  	var resultsArray = [];

						  	$.each(data.games, function(index, value) {
						  		var currentResult = ['<li>',
							  							'<a href="<?php $baseurl; ?>/game/' + value.id + '">' + value.title + '<br>',
							  								'<span>' + value.platform + '</span>',
							  							'</a>',
							  						'</li>'].join('\n');

							  	resultsArray.push(currentResult);
							});


						  	var resultDisplay = ['<ul>',
													resultsArray.join('\n'),
						  						'</ul>'].join('\n');

							currentElement.parent().children('.ajaxSearchResults').html(resultDisplay);
							currentElement.parent().children('.ajaxSearchResults').slideDown();
						}
						else
						{
							currentElement.parent().children('.ajaxSearchResults').html('');
							currentElement.parent().children('.ajaxSearchResults').slideUp('fast');	
						}
					}, "json");

				}
				else
				{
					$('.ajaxSearchResults').slideUp('fast');
				}

			});

			// Keyboard Navigation For Ajax QuickSearch
			$('.ajaxSearch, .ajaxSearchResults').bind('keydown', function(e) {
				var ajaxParent = $(this).closest('form').children('.ajaxSearchResults').children('ul');
				if ($('.ajaxSearch').is(':focus'))
				{
					if (e.keyCode == 40)
				    {
				        ajaxParent.children('li').first().children('a').focus();
				        return false;
				    }
				}
				else
				{
				    if (e.keyCode == 40)
				    {
				    	$(':focus').parent().next().children('a').focus();
						e.preventDefault();
				        return false;
				    }
				    else if (e.keyCode == 38)
				    {        
				        $(':focus').parent().prev().children('a').focus();
						e.preventDefault();
				        return false;
				    }
				    else if (e.keyCode == 8)
				    {
				        $(this).closest('form').children('.ajaxSearch').focus();
						e.preventDefault();
				        return false;
				    }
				}
			});

			// Hide Ajax QuickSearch When Clicking Outside of Results
			$(document).click( function (e)
			{
			    var container = $(".ajaxSearchResults");
			    if (!container.is(e.target) && container.has(e.target).length === 0)
			    {
			        container.slideUp('fast');
			    }
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
		
		<div id="fb-root"></div>
        <script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
		
    </body>
	
</html>