<div id="gameWrapper">
	<div id="gameHead">

	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	
<div style="text-align: center; margin: 26px 0px;">
<h1>Reset Password</h1>
<p>&nbsp;</p>
<p>Please enter your email address below and we will send you a replacement password.</p>
<form action="<?=$fullurl?>" method="POST">
<div id="red"><?=$errormessage?></div>
<table cellspacing="2" cellpadding="2" border="0" align="center" style="text-align: left;">
<tr>
	<td><b>Email Address</b></td>
	<td><input type="text" name="email" value="<?=$email?>"></td>
</tr>
<tr>
	<td></td>
	<td><input type="submit" name="function" value="Reset Password"></td>
</tr>
</table>

</form>
</div>

	</div>
</div>