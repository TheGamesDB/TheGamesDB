<div class="section">
<h1>Add A Show</h1>
  <p id="red"><?=$errmsg?></p>
  <h2>Rules for adding a show</h2>
    <p>Always check to make sure a show doesn't already exsist before adding it. If it is found to be a duplicate it will be deleted. Diffrent order, languages or names DO NOT qualify for duplicate entries. If you believe you've found a special case where a duplicate should be allowed please come to the forums and ask first or it will be deleted.  Use the advanced search tool to try and find your show before adding it. We may have it listed under a name you arn't aware of, so if you know the imdb.com or tv.com id for it you may be able to find it that way.</p>
    <p>Webisodes for a show such as "Lost: Missing Pieces" generally would not qualify for their own entry and should be entered under the main show instead. If you believe an exception should be made please come to the forums and ask first.</p>
    <p>If you ever notice a show you've added has disapeared and you can't figure out why please come to the forums and ask, do not simply readd it as it is likely to be deleted again.</p>
    <p>Shows are added immeditaly but new entries are monitored and incorrect entries will get deleted. If in doubt always come to the forums and ask before attmpting to add a show.</p>  
  
  <form action="./index.php?" method="POST">
		<input type="text" name="SeriesName">
		<input type="submit" name="function" value="Add Series">
	</form>
</div>
