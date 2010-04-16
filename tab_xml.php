<div class="section">
<h1>Interface Specification</h1>
<p>The following are the specifications for the database interfaces.  I recommend allowing your users to configure their list of mirrors, in case the primary server ever goes down.  Set this list of mirrors to include the primary server by default.</p>
<p><b>We have two requirements for anyone that uses these interfaces:</b>
<ul><li>First, you <b>MUST</b> inform your users about this site and ask them to contribute.
<li>Second, once you publicly release your program, plugin, or script, you <b>MUST</b> <a href="mailto: scott-tvdb@zsori.com">inform us</a> about it.  This way we can link back to you and keep tabs on everyone accessing the interfaces.
</ul></p>

<p>An update of a client-side library should work as follows (it looks more complicated than it is):</p>
<ol>
	<li>Query GetMirrors using a random server from the client's mirror list.
	<li>Update their mirrors list with the results.
	<li>Randomly select a banner and interface site for your updates.  If you cannot contact a banner or interface site, check the next one.  Rinse, repeat.
	<li>Loop through the database. For each series:
	<ul>
		<li>Look up series that are missing a tvdb seriesid.  Look up and store their seriesid using the GetSeries interface.
		<li>Append the seriesid to a comma-separated list for all shows (new and existing).
	</ul>
	<li>Send the comma-separated list of tvdb seriesids to the SeriesUpdates interface along with the client's last update timestamp (returned in the last update as SyncTime).
	<li>For each item returned:
	<ul>
		<li>Update the associated series record with the returned data.
		<li>If &lt;NotValid>1&lt;/NotValid> is set:
		<ul>
			<li>Look up the series using GetSeries.
			<li>Send the id along with a lasttime of 0 to SeriesUpdates.
			<li>Update the series record if there is returned data.
		</ul>
	</ul>
	<li>For each episode:
	<ul>
		<li>Look up episodes that are missing a tvdb episodeid.  Look up and store their episodeid using the GetEpisode interface.
		<li>Append the episodeid to a comma-separated list for all episodes (new and existing).
	</ul>
	<li>Send the comma-separated list of tvdb episodeids to the EpisodeUpdates interface along with the client's last update timestamp (returned in the last update as SyncTime).
	<li>For each item returned:
	<ul>
		<li>Update the associated episode record with the returned data.
		<li>If &lt;NotValid>1&lt;/NotValid> is set:
		<ul>
			<li>Look up the episode using GetEpisode.
			<li>Send the id along with a lasttime of 0 to EpisodeUpdates.
			<li>Update the episode record if there is returned data.
		</ul>
	</ul>
</ol>

<p>There is another logical method for checking the database for updates.  This one is provided as an example.</p>

<p><b>For all of the following, <i>http://thetvdb.com</i> can be used as an example {mirrorsite}.</b>
</div>

<div class="section">
<h1>GetMirrors</h1>
<table width="100%" cellpadding="3" cellspacing="0" border="0" id="infotable">
<tr>
	<td width="25%" valign="top">Purpose</td>
	<td valign="top">Retrieve a list of all mirrors you can query from.  This list is randomized, so you can always select the first one.</td>
</tr>
<tr>
	<td valign="top">Location</td>
	<td valign="top">{mirrorsite}/interface/GetMirrors.php</td>
</tr>
<tr>
	<td valign="top">Parameters</td>
	<td valign="top">none</td>
</tr>
<tr>
	<td valign="top">Example POST/GET Data</td>
	<td valign="top">none</td>
</tr>
<tr>
	<td valign="top">Returned Data</td>
	<td valign="top"><a target="_blank" href="http://thetvdb.com/interfaces/GetMirrors.php">View results</a>
	</td>
</tr>
</table>
</div>


<div class="section">
<h1>GetLanguages</h1>
<table width="100%" cellpadding="3" cellspacing="0" border="0" id="infotable">
<tr>
	<td width="25%" valign="top">Purpose</td>
	<td valign="top">Retrieve a list of all database languages and their id's.</td>
</tr>
<tr>
	<td valign="top">Location</td>
	<td valign="top">{mirrorsite}/interface/GetLanguages.php</td>
</tr>
<tr>
	<td valign="top">Parameters</td>
	<td valign="top">none</td>
</tr>
<tr>
	<td valign="top">Example POST/GET Data</td>
	<td valign="top">none</td>
</tr>
<tr>
	<td valign="top">Returned Data</td>
	<td valign="top"><a target="_blank" href="http://thetvdb.com/interfaces/GetLanguages.php">View results</a>
	</td>
</tr>
</table>
</div>


<div class="section">
<h1>GetBanners</h1>
<table width="100%" cellpadding="3" cellspacing="0" border="0" id="infotable">
<tr>
	<td width="25%" valign="top">Purpose</td>
	<td valign="top">Retrieve a list of banners associated with a series.  This includes series and season banners.</td>
</tr>
<tr>
	<td valign="top">Location</td>
	<td valign="top">{mirrorsite}/interface/GetBanners.php</td>
</tr>
<tr>
	<td valign="top">Parameters</td>
	<td valign="top">
		<b>seriesname:</b> The name of the series you want banners for.<br>
		<b>seriesid:</b> The tvdb id of the series you want banners for. (overrides seriesname if sent)<br>
		<b>lasttime:</b> The last time you queried the banners interface.  Interface will return all banners added since this epoch time. (defaults to 0)<br>
		<b>language:</b> The language id for the results.  English is default.<br>
		<b>user:</b> Does nothing now, but will soon return banners in the user's preferred language AND only their preferred banners if available.<br>
	</td>
</tr>
<tr>
	<td valign="top">Example POST/GET Data</td>
	<td valign="top">
		seriesid=75397<br>
		seriesname=Battlestar%20Galactica<br>
		seriesname=Battlestar%20Galactica&amp;lasttime=1160551948<br>

	</td>
</tr>
<tr>
	<td valign="top">Returned Data</td>
	<td valign="top">
		<a target="_blank" href="http://thetvdb.com/interfaces/GetBanners.php?seriesid=75397">View results (example 1)</a><br>
		<a target="_blank" href="http://thetvdb.com/interfaces/GetBanners.php?seriesname=Battlestar%20Galactica">View results (example 2)</a><br>
		<a target="_blank" href="http://thetvdb.com/interfaces/GetBanners.php?seriesname=Battlestar%20Galactica&amp;lasttime=1160551948">View results (example 3)</a>
	</td>
</tr>
</table>
</div>



<div class="section">
<h1>GetSeries</h1>
<table width="100%" cellpadding="3" cellspacing="0" border="0" id="infotable">
<tr>
	<td width="25%" valign="top">Purpose</td>
	<td valign="top">Retrieve a tvdb seriesid using the series name.  This uses fuzzy logic to find the most appropriate series and sort them accordingly.</td>
</tr>
<tr>
	<td valign="top">Location</td>
	<td valign="top">{mirrorsite}/interface/GetSeries.php</td>
</tr>
<tr>
	<td valign="top">Parameters</td>
	<td valign="top"><b>seriesname:</b> The name of the series.</td>
</tr>
<tr>
	<td valign="top">Example POST/GET Data</td>
	<td valign="top">seriesname=My+Name+Is+Earl</td>
</tr>
<tr>
	<td valign="top">Returned Data</td>
	<td valign="top"><a target="_blank" href="http://thetvdb.com/interfaces/GetSeries.php?seriesname=My+Name+Is+Earl">View results</a>
	</td>
</tr>
</table>
</div>




<div class="section">
<h1>GetEpisodes</h1>
<table width="100%" cellpadding="3" cellspacing="0" border="0" id="infotable">
<tr>
	<td width="25%" valign="top">Purpose</td>
	<td valign="top">Retrieve a tvdb episodeid using a combination of seriesid, season, episode, and episodename.</td>
</tr>
<tr>
	<td valign="top">Location</td>
	<td valign="top">{mirrorsite}/interface/GetEpisodes.php</td>
</tr>
<tr>
	<td valign="top">Parameters</td>
	<td valign="top">
		<b>seriesid:</b> The tvdb series id.<br>
		<b>episode:</b> The episode number (optional).<br>
		<b>season:</b> The season number (required if episode is sent).<br>
		<b>episodename:</b> The name of the episode (optional. overrides episode and season parameters).<br>
		<b>lasttime:</b> If set, the interface will return all episodes updated since this epoch time. (optional. defaults to 0).<br>
		<b>firstaired:</b> First Airdate (optional) Only used if episode name and number do not match (YYYY-MM-DD)<br>
		<b>order:</b> Episode Order (optional) If the get series interface returns an additional &lt;EpisodeOrders> value of dvd or absolute then you can request that alternate order with this operator<br>
		<b>language:</b> The language id for the results.  English is default.  If no translation is available, English is returned.<br>
		<b>user:</b> The user's "Account Identifier" from their account settings page.  Overrides the language parameter.  Additional future features.<br>
	</td>
</tr>
<tr>
	<td valign="top">Example POST/GET Data</td>
	<td valign="top">
		seriesid=75397<br>
		seriesid=75397&amp;season=1<br>
		seriesid=75397&amp;season=1&amp;episode=3<br>
		seriesid=75397&amp;episodename=Quit+Smoking<br>
		seriesid=75397&amp;firstaired=2007-01-18<br>
		seriesid=78874&amp;order=dvd<br>
		seriesid=78857&amp;order=absolute
	</td>
</tr>
<tr>
	<td valign="top">Returned Data</td>
	<td valign="top">
		<a target="_blank" href="http://thetvdb.com/interfaces/GetEpisodes.php?seriesid=75397">View results (example 1)</a><br>
		<a target="_blank" href="http://thetvdb.com/interfaces/GetEpisodes.php?seriesid=75397&amp;season=1">View results (example 2)</a><br>
		<a target="_blank" href="http://thetvdb.com/interfaces/GetEpisodes.php?seriesid=75397&amp;season=1&amp;episode=3">View results (example 3)</a><br>
		<a target="_blank" href="http://thetvdb.com/interfaces/GetEpisodes.php?seriesid=75397&amp;episodename=Quit+Smoking">View results (example 4)</a><br>
		<a target="_blank" href="http://thetvdb.com/interfaces/GetEpisodes.php?seriesid=75397&amp;firstaired=2007-01-18">View results (example 5)</a><br>
		<a target="_blank" href="http://thetvdb.com/interfaces/GetEpisodes.php?seriesid=78874&amp;order=dvd">View results (example 6)</a><br>
		<a target="_blank" href="http://thetvdb.com/interfaces/GetEpisodes.php?seriesid=78857&amp;order=absolute">View results (example 7)</a>
	</td>
</tr>
</table>
</div>





<div class="section">
<h1>SeriesUpdates</h1>
<table width="100%" cellpadding="3" cellspacing="0" border="0" id="infotable">
<tr>
	<td width="25%" valign="top">Purpose</td>
	<td valign="top">Get all of the information for any series that have updated since the last time you checked. Includes &lt;NotValid>{seriesid}&lt;/NotValid> if the ID isn't valid. Also returns SyncTime attribute that can be stored and used as lasttime for the next inport.</td>
</tr>
<tr>
	<td valign="top">Location</td>
	<td valign="top">{mirrorsite}/interface/SeriesUpdates.php</td>
</tr>
<tr>
	<td valign="top">Parameters</td>
	<td valign="top">
		<b>lasttime:</b> The last time you queried the database in epochtime format. Set to 0 for first query of all data.<br>
		<b>idlist:</b> A comma-separated list of all the tvdb series IDs in the user database.<br>
		<b>language:</b> The language id for the results.  English is default.  If no translation is available, English is returned.<br>
		<b>user:</b> The user's "Account Identifier" from their account settings page.  Overrides the language parameter.  Additional future features.<br>
	</td>
</tr>
<tr>
	<td valign="top">Example POST/GET Data</td>
	<td valign="top">
		lasttime=0&amp;idlist=75397,70327,70328,70329,73762,1,72368,73420,73444
	</td>
</tr>
<tr>
	<td valign="top">Returned Data</td>
	<td valign="top">
		<a target="_blank" href="http://thetvdb.com/interfaces/SeriesUpdates.php?lasttime=0&amp;idlist=75397,70327,70328,70329,73762,1,72368,73420,73444">View results</a>
	</td>
</tr>
</table>
</div>






<div class="section">
<h1>EpisodeUpdates</h1>
<table width="100%" cellpadding="3" cellspacing="0" border="0" id="infotable">
<tr>
	<td width="25%" valign="top">Purpose</td>
	<td valign="top">Get all of the information for any episodes that have updated since the last time you checked. Includes &lt;NotValid>{episodeid}&lt;/NotValid> if the ID isn't valid. Also returns SyncTime attribute that can be stored and used as lasttime for the next inport.</td>
</tr>
<tr>
	<td valign="top">Location</td>
	<td valign="top">{mirrorsite}/interface/EpisodeUpdates.php</td>
</tr>
<tr>
	<td valign="top">Parameters</td>
	<td valign="top">
		<b>lasttime:</b> The last time you queried the database in epochtime format. Set to 0 for first query of all data.<br>
		<b>idlist:</b> A comma-separated list of all the tvdb episode IDs in the user database.<br>
		<b>language:</b> The language id for the results.  English is default.  If no translation is available, English is returned.<br>
		<b>user:</b> The user's "Account Identifier" from their account settings page.  Overrides the language parameter.  Additional future features.<br>
	</td>
</tr>
<tr>
	<td valign="top">Example POST/GET Data</td>
	<td valign="top">
		lasttime=0&amp;idlist=753970,222282,39920
	</td>
</tr>
<tr>
	<td valign="top">Returned Data</td>
	<td valign="top">
		<a target="_blank" href="http://thetvdb.com/interfaces/EpisodeUpdates.php?lasttime=0&amp;idlist=753970,222282,39920">View results</a>
	</td>
</tr>
</table>
</div>

