<div id="gameWrapper">
	<div id="gameHead">
	
		<h2>Upload Area - Game Art</h2>
		
		<a style="float: right; color: orange; padding: 20px;" href="<?= $baseurl ?>/game-edit/<?= $gameid ?>/">Return to Edit Game Page</a>
		
		<p>Pease use the controls below to upload images to the site. </p>
		<p>Find the section relating to the image type you'd like to upload, then you can drag your image files onto the "Drag Files Here" section.  If you're using an older browser, you can click the links that say "Select Files".</p>
		<p>For more information on artwork and uploading vist the <a href="http://wiki.thegamesdb.net" style="color: orange;">site wiki</a></p>

		<iframe src="<?= $baseurl ?>/uploadarea.php?gameid=<?= $gameid ?>" frameborder="0" width="100%" height="1500" align="middle"></iframe>
	
	</div>
</div>