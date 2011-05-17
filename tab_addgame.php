<div class="section">
<center><h1>Add A Game</h1></center>
  <p class="error"><?=$errmsg?></p>
  <h2>Rules for adding a game</h2>
    <p>Always check to make sure a game doesn't already exsist before adding it.  If it is found to be a duplicate, it will be deleted.  If you believe you've found a special case where a duplicate should be allowed, please come to the forums and ask first or it will be deleted.  Use the advanced search tool to try and find your game before adding it.  We may have it listed under a name you aren't aware of, so if you know the ID for it, you may be able to find it that way.</p>
    <p>Map packs and certain add-on's for games generally don't qualify for their own entry and should be entered under the main game instead.  If you believe an exception should be made, please come to the forums and ask first.</p>
    <p>If you ever notice a game you've added has dissapeared and you can't figure out why, please come to the forums and ask.  Do not simply re-add it as it is likely to be deleted again.</p>
    <p><b>DO NOT</b> put non english information into english fields.  If a game is published in another language, and is never translated from that language into english, we still do not want non-english information in the english fields.</p>
    <p>Games are added immediately but new entries are monitored and incorrect entries will get deleted.  If in doubt, always come to the forums and ask before attempting to add a game.</p>
      
  <form action="<?php echo $baseurl;?>/index.php?" method="POST">
		<input type="text" name="GameTitle">
		<input type="submit" name="function" value="Add Game">
	</form>
</div>
