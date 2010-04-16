<div class="section">
<h1>API Key Registration</h1>
<p class="longtext">Please fill out the information below to receive your API Key. The key will allow you to access the XML data files directly.  We do monitor for abuse, so make sure you're following our guidelines.</p>
<p>
<ul>
<li>If you will be using the API information in a commercial product or website, you must email <a href="mailto:scott@thetvdb.com?subject=Commercial Key">scott@thetvdb.com</a> and wait for authorization before using the API.  However, you <b>MAY</b> use the API for development and testing before a public release.
<li>If you have a publicly available program, you <b>MUST</b> inform your users of this website and request that they help contribute information and artwork if possible.
<li>You <b>MUST</b> familiarize yourself with our data structure, which is detailed in the <a href="/wiki/index.php?title=Programmers_API" target="_blank">wiki documentation</a>.
<li>You <b>MUST NOT</b> perform more requests than are necessary for each user. This means no downloading all of our content (we'll provide the database if you need it).  Play nice with our server.
<li>You <b>MUST NOT</b> directly access our data without using the documented API methods.
<li>You <b>MUST</b> keep the email address in your account information current and accurate in case we need to contact you regarding your key (we hate spam as much as anyone, so we'll never release your email address to anyone else).
<li>Please feel free to contact us and request changes to our site and/or API.  We'll happily consider all reasonable suggestions.
<li>By pressing the button below, you are verifying you understand all of the requirements for using our API.
</ul>
</p>

<?php	## Only show the form if they're logged in
	if ($user->id)  {
?>

	<form action="<?=$fullurl?>" method="POST">
	<div id="red"><?=$errormessage?></div>
	<table cellspacing="2" cellpadding="2" border="0" align="center">
	<tr>
		<td><b>Project Name</b></td>
		<td><input type="text" name="projectname" size="40"></td>
	</tr>

	<tr>
		<td><b>Project Website</b></td>
		<td><input type="text" name="projectwebsite" value="http://" size="40"></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="function" value="Retrieve API Key"></td>
	</tr>
	</table>
	</form>
<?php
	}  else  {
		print "<p>You must be logged in to get an API key</p>\n";
	}
?>
</div>
