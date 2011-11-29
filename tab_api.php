<div id="gameWrapper">
	<div id="gameHead">
	
	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	
	<div style="padding: 30px 0px;">
		<h1 style="text-align: center;">TheGamesDB.net API</h1>
		<div style="border: 1px solid #666; background-color: #333; margin-top: 16px;">
			<div style="width: 30%; float: left; text-align: center;">
				<p><a href="<?= $baseurl; ?>/api/" style="color: orange">API Home</a></p>
				<p><a href="<?= $baseurl; ?>/api/apikey/" style="color: orange">How do I get an API key?</a></p>
				<p><a href="<?= $baseurl; ?>/api/updates/" style="color: orange">API Updates</a></p>
				<p><a href="<?= $baseurl; ?>/api/methods/" style="color: orange">API Methods</a></p>
				<p><a href="http://code.google.com/p/thegamesdb/w/list" target="_blank" style="color: orange">Site Wiki</a></p>
			</div>
			<div style="width: 66%; float: left; background-color: #444; border-left: 1px solid #666; padding: 18px;">
			
				<?php
				switch($apiarea)
				{

					default:
					?>
						<h2>Overview</h2>
						<p>Our API is what makes it possible for you to bring all of this artwork and metadata together and integrate it into your application of choice.  You can use any number of programs to scrape from our website via our API.  For more information on some of the most widely used programs, check out our showcase page.  And for questions about the API, visit our forums.</p>
						<p>All of the API methods are currently available in XML, and will soon be available in JSON format.  If you are a designer that has incorporated our API in any way, we would love to showcase it; so please let us know in the developer forum.</p>
						
						<h2>Who can use the API?</h2>
						<p>The API is available for anyone to use, but if you're a developer, we would like you to make an introduction post in our forums.  We will be implementing an API key system in our next version of the API, so the developers we know about will get first access to that.  We ask that all users read and follow our Terms and Conditions.  <a href="<?= $baseurl; ?>/terms/" style="color: orange;"><?= $baseurl; ?>/terms/</a></p>
						<h2>Showcase</h2>
						<p>Imagine yourself sitting on your couch with your remote in hand. You're browsing through an endless number of video games, all beautifully displayed on your screen with high resolution art work and accurate info. Now stop imagining, and start living the dream!</p>
						<p>Here's some examples of how TheGamesDB's API and database can be used to display artwork and metadata for your games.</p>
						<p style="text-align: center;"><a href="<?= $baseurl; ?>/showcase/"><img src="<?= $baseurl; ?>/images/showcase.png" alt="Click to view our API Showcase" title="Click to view our API Showcase" /></a></p>
					<?php
					break;
					?>
					
					<?php
					case 'apikey':
					?>
						<h2>Request an API key</h2>
						<hr />
						<p>We will soon begin the process of restricting the use of the API to members with valid API keys. This is to ensure fair usage of our API and resources. The developers we know about and have worked with previously will get first access to the new API.</p>
						<p>Once our next version of the API is written, we will publish information on how to request an API key here.</p>
						<p>To ensure that we don't break your applications that use our API, we will most likely allow the current API to exist for a fair transition period, so that you may update your projects to use our upcoming API. Dates and specifics on this will be published beforehand.</p>
						<hr />
						
					<?php
					break;
					?>
					
					<?php
					case 'updates':
					?>
						<h2>API Updates</h2>
						<p>Here you find news pertaining to all updates to the API:</p>
						<hr />
						
						<h3 style="color: gold;">&laquo;GetGamesList&raquo;</h3>
						<p>7-17-11: Added Genre Filtering Support.</p>
						<hr />
						
						<h3 style="color: gold;">&laquo;GetGame&raquo;</h3>
						<p>7-19-11: Added Youtube Trailer Support. Removed CRC Support.<br />
						7-17-11: Added CRC Support.<br />
						6-13-11: Added Co-op tag. Games that are checked with having Co-op capability can now be returned via the API.</p>
						<hr />
						
						<h3 style="color: gold;">&laquo;GetArt&raquo;</h3>
						<p>[None]</p>
						<hr />
						
						<h3 style="color: gold;">&laquo;User_Favorites&raquo;</h3>
						<p>[None]</p>
						<hr />
						
						<h3 style="color: gold;">&laquo;User_Rating&raquo;</h3>
						<p>[None]</p>
						<hr />
						
						<h3 style="color: gold;">&laquo;Updates&raquo;</h3>
						<p>[None]</p>
						<hr />
						
					<?php
					break;
					?>
					
					<?php
					case 'methods':
					?>
						<h2>API Methods</h2>
						<p>The GamesDB API can do the following:</p>
						<ul>
							<li>Search for specific games: &laquo;GetGamesList&raquo;</li>
							<li>Search for a loose list of games.</li>
							<li>Retrieve artwork for games.</li>
							<li>Update user favorites.</li>
							<li>Request all updated or new content since a given date.</li>
							<li>Update user ratings.</li>
						</ul>
						<p>API requests are made by issuing a GET request to the API URL and a XML document is returned.</p>
						<p><b>Base API URL:</b> <a href="<?= $baseurl; ?>/api/" target="_blank" style="color: orange"><?= $baseurl; ?>/api/</a></p>
						<p><b>Example Game Search:</b> <a href="<?= $baseurl; ?>/api/GetGamesList.php?name=halo" target="_blank" style="color: orange"><?= $baseurl; ?>/api/GetGamesList.php?name=halo</a></p>
						<hr />
						
						<h3 style="color: gold;">&laquo;GetGamesList&raquo;</h3>
						<p><b>Desc:</b> Returns a listing of games matched up with loose search terms.</p>
						<p><em>Note: We have implemented special character stripping and loose word order searching in an attempt to provide better matching and a return a greater number of relevant hits.</em></p>
						<p><b>Available Parameters:</b></p>
						<ul>
							<li>name (string / required) - The game title to search for</li>
							<li>platform (string / optional) - Filters results by platform</li>
							<li>genre (string / optional) - Filters results by genre</li>
						</ul>
						<p><b>Example:</b></p>
						<p>A search for "x-men": <a style="color: orange;" href="<?= $baseurl; ?>/api/GetGamesList.php?name=x-men" target="_blank"><?= $baseurl; ?>/api/GetGamesList.php?name=x-men</a></p>
						<hr />
						
						<h3 style="color: gold;">&laquo;GetGame&raquo;</h3>
						<p><b>Desc:</b> If 'name' is specified, returns a list of games including data that match the string, or if a game ID is given it returns the data for that specific game.</p>
						<p><em>Note: An id overrides a name search.</em></p>
						<p><b>Available Parameters:</b></p>
						<ul>
							<li>name (string) - The game title to list and return data for.</li>
							<li>id (int) - The game ID to return data for.</li>
							<li>platform (string / optional) - Filters results by platform</li>
						</ul>
						<p><b>Example Request:</b></p>
						<p>A request for game ID '170' <em>(New Super Mario Bros. Wii)</em>: <a style="color: orange;" href="<?= $baseurl; ?>/api/GetGame.php?id=170" target="_blank"><?= $baseurl; ?>/api/GetGame.php?id=170</a></p>
						<hr />
						
						<h3 style="color: gold;">&laquo;GetArt&raquo;</h3>
						<p><b>Desc:</b> This API feature returns a list of available artwork types and locations specific to the requested game ID in the database. It also lists the resolution of any images available. Scrapers can be set to use a minimum or maximum resolution for specific images.</p>
						<p><b>Available Parameters:</b></p>
						<ul>
							<li>id (int / required) - The game ID to return art data for.</li>
						</ul>
						<p><b>Example Request:</b></p>
						<p>A request for the artwork data for game ID '170' <em>(New Super Mario Bros. Wii)</em>: <a style="color: orange;" href="<?= $baseurl; ?>/api/GetArt.php?id=170" target="_blank"><?= $baseurl; ?>/api/GetArt.php?id=170</a></p>
						<hr />
						
						<h3 style="color: gold;">&laquo;User_Favorites&raquo;</h3>
						<p><b>Desc:</b> Allows you to get, set and remove a favorite game from/to a users profile. Always returns a list of the current favorite game ID's.</p>
						<p><em>Note: To use this method, a user must provide their unique 'account identifier' which can be found on their 'My User Info' page.</em></p>
						<p><b>Available Parameters:</b></p>
						<ul>
							<li>accountid (int / required) - The unique 'account identifier' of the user in question.</li>
							<li>type (string {add|remove} / optional) - Sets the action (add or remove) for the request, if not specified, the list will be returned (get) only.</li>
							<li>gameid (int / optional) - The game ID to perform the 'type' action on (this parameter is required if 'type' is set).</li>
						</ul>
						<p><b>Example Request:</b></p>
						<p>Adds game ID '2' <em>(Crysis)</em> to user's favorites:<br /><a style="color: orange;" href="<?= $baseurl; ?>/api/User_Favorites.php?accountid=58536D31278176DA&type=add&gameid=2" target="_blank"><?= $baseurl; ?>/api/User_Favorites.php?accountid=58536D31278176DA&amp;type=add&amp;gameid=2</a></p>
						<hr />
						
						<h3 style="color: gold;">&laquo;User_Rating&raquo;</h3>
						<p><b>Desc:</b> Allows you to get and set a user rating on a game.</p>
						<p><em>Note: To use this method, a user must provide their unique 'account identifier' which can be found on their 'My User Info' page.</em></p>
						<p><b>Available Parameters:</b></p>
						<ul>
							<li>accountid (int / required) - The unique 'account identifier' of the user in question.</li>
							<li>gameid (int / required) - Sets the action (add or remove) for the request, if not specified, the list will be returned (get) only.</li>
							<li>rating (int {0 to 10} / optional) - The rating to set (if user rating is 0 then the user rating will be deleted, If no rating is supplied the current rating will be returned).</li>
						</ul>
						<p><b>Example Request:</b></p>
						<p>Gets rating for game ID '2' <em>(Crysis)</em> :<br /><a style="color: orange;" href="<?= $baseurl; ?>/api/User_Rating.php?accountid=58536D31278176DA&itemid=2" target="_blank"><?= $baseurl; ?>/api/User_Rating.php?accountid=58536D31278176DA&amp;itemid=2</a></p>
						<hr />
						
						<h3 style="color: gold;">&laquo;Updates&raquo;</h3>
						<p><b>Desc:</b> Returns a list (game ID's) of all games updated since a given time in seconds (max. 30 days back).</p>
						<p><b>Available Parameters:</b></p>
						<ul>
							<li>time (int / required) - The time (in seconds) that you would like to show updated games for (max. 30 days back).</li>
						</ul>
						<p><b>Example Request:</b></p>
						<p>Gets a list of games updated in the past 2,000 seconds: <a style="color: orange;" href="<?= $baseurl; ?>/api/Updates.php?time=2000" target="_blank"><?= $baseurl; ?>/api/Updates.php?time=2000</a></p>
						<hr />
						
					<?php
					break;
					?>
				
				<?php
				}
				?>
				
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	
	</div>
</div>