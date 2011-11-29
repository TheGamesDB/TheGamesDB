<div id="gameWrapper">
	<div id="gameHead">
	
	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	
		<?php
			$messageQuery = mysql_query("SELECT m.id, m.to, m.subject, m.message, m.timestamp, m.status, u.username AS fromname FROM messages AS m, users AS u WHERE m.id = $messageid AND m.from = u.id;");
			$message = mysql_fetch_object($messageQuery);
			if($message->to == $user->id)
			{
				mysql_query(" UPDATE messages SET status = 'read' WHERE id = '$messageid' ");
		?>
		
		<h1 style="text-transform: capitalize;">Your Message From <?= $message->fromname; ?></h1>
		<p>&nbsp;</p>
		<div style="width: 700px; margin: auto; border: 1px solid #666; background-color: #333; color: #fff; padding: 12px; fon't-size: 14px;">
			<span style="float: right;"><?= date("l, jS F Y - g:i A (T)", strtotime($message->timestamp)); ?></span>
			<h3 style="margin: 0 0 16px 0;"><span style="color: orange;">From:</span>&nbsp;<?= $message->fromname; ?></h3>
			<h3 style="margin: 0 0 16px 0;"><span style="color: orange;">Subject:</span>&nbsp;<?= $message->subject; ?></h3>
			<div style="border: 1px solid #666; background-color: #eee; color: #222; padding: 12px; fon't-size: 14px;"><?= $message->message; ?></div>
			<form action="<?= $baseurl; ?>/messages/" methos="post" style="float: right; margin-top: 8px;">
				<input type="submit" name="function" value="Delete PM" />
				<input type="hidden" name="pmid" value="<?= $message->id; ?>" />
			</form>
			<p><span style="color: orange;">&laquo;-- </span><a href="<?= $baseurl; ?>/messages/" style="color: orange;">Back to Your Messages</a></p>
		</div>
		
		<?php
			}
			else
			{
		?>
				<h1>Oops!</h1>
				<h2 style="text-align: center;">Sorry, you are not allowed to view other members messages!</h2>
				<p style="text-align: center;">If you believe you have recieved this message in error, please let us know.</p>
				<p style="text-align: center;"><a href="<?= $baseurl; ?>/" style="color: orange;">Click here to return to the homepage</a></p>
		<?
			}
		?>
	</div>
</div>