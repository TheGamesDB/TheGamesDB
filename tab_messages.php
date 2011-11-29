<div id="gameWrapper">
	<div id="gameHead">
	
	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	
	<?php
		if($loggedin == 1)
		{
	?>
		<h1 style="text-transform: capitalize;"><?= $user->username ?>'s Messages</h1>
		<?php
			$messagesQuery = mysql_query("SELECT m.id, m.subject, m.status, m.timestamp, u.username AS fromname FROM messages AS m, users AS u WHERE m.to = '$user->id' AND m.from = u.id");
		?>
		<table style="margin: 36px auto 20px auto; width: 100%; background-color: #444;" cellpadding="6">
			<tr style="background-color: #666;">
				<th>From</th>
				<th>Subject</th>
				<th>Date and Time</th>
				<th>Status</th>
			</tr>
			<?php
				if(mysql_num_rows($messagesQuery) != 0)
				{
					while($messages = mysql_fetch_object($messagesQuery))
					{
						if($rowcolor == "#bbb")
						{
							$rowcolor = "#999";
						}
						else
						{
							$rowcolor = "#bbb";
						}
			?>
			<tr style="background-color: <?= $rowcolor; ?>; color: #222; font-weight: bold;">
				<td align="center"><?= $messages->fromname; ?></td>
				<td><a href="<?= $baseurl; ?>/message/<?= $messages->id; ?>/" style="color: #000;"><?= $messages->subject; ?></a></td>
				<td><?= date("l, jS F Y - g:i A (T)", strtotime($messages->timestamp)); ?></td>
				<td align="center" style="text-transform:capitalize;"><?= $messages->status; ?></td>
			</tr>
			<?php
					}
				}
				else
				{
			?>
			<tr style="background-color: #bbb; color: #222; font-weight: bold;">
				<td colspan="4" align="center" style="padding: 10px;">You do not currently have any messages...</td>
			</tr>
			<?php
				}
			?>
		</table>
	<?php
		}
		else
		{
	?>
			<h1>Oops!</h1>
			<h2 style="text-align: center;">You must be logged in to access your messages!</h2>
			<p style="text-align: center;"><a href="<?= $baseurl; ?>/login/" style="color: orange;">Click here to log in</a></p>
	<?php
		}
	?>
	</div>
</div>