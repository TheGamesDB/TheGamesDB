<?php if ($adminuserlevel == 'ADMINISTRATOR') { ?>
		
<div id="gameWrapper">
	<div id="gameHead">
	
		<?php if($errormessage): ?>
		<div class="error"><?= $errormessage ?></div>
		<?php endif; ?>
		<?php if($message): ?>
		<div class="message"><?= $message ?></div>
		<?php endif; ?>
		
		<h2>Add Publisher/Developer Keywords &amp; Logo</h2>
		
		<p>&nbsp;</p>
		<p>Here you can add the keywords and logo for a new publisher/developer. Separate keywords with a comma.</p>
		<p>&nbsp;</p>
				
				<form method="post" action="<?= $baseurl ?>/admincp/?cptab=pubdev" enctype="multipart/form-data" style="text-align: center; width: 700px; margin: auto; background-color: #555555; border: 1px solid #999; border-radius: 6px;">
					<input type="hidden" name="function" value="Add New Publisher" />
					<p>Publisher/Developer Keywords: <input type="text" name="publisherKeywords" value="<?= $publisherResult->keywords ?>" /></p>
					<p>Publisher/Developer Logo: <input type="file" name="publisherlogo" /></p>
					<p><input type="submit" />
				</form>
		
	</div>
</div>

<?php } ?>