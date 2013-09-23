
	<div id="gameHead">

	<?php if($errormessage): ?>
	<div class="error"><?= $errormessage ?></div>
	<?php endif; ?>
	<?php if($message): ?>
	<div class="message"><?= $message ?></div>
	<?php endif; ?>
	
<div style="text-align: center; padding: 24px 0px;">
<h1>User Login</h1>
<p>&nbsp;</p>
<div style="margin: auto; padding: 24px; border: 1px solid #999; border-radius: 6px; background-color: #404040;">
	<h1>Notice:</h1>
	<h3>We have recently made some upgrades to our service.</h3>
	<p>If you registered an account with us prior to Thursday 18th July 2013, we require you to <a href="<?php echo $baseurl;?>/password/" style="color: orange">reset your password</a> before you are able to login.</p>
	<p>You need only do this once. We apologize for any inconvenience this may cause.</p>
</div>
<p>&nbsp;</p>
<form action="<?=$baseurl;?>/" method="POST">
<div style="margin: auto; padding: 24px; border: 1px solid #999; border-radius: 6px; background-color: #404040;">
<p>Please fill in your details below to log in to your account.</p>

<table cellspacing="2" cellpadding="2" border="0" align="center">
<tr>
	<td><b>Username</b></td>
	<td><input type="text" name="username"></td>
</tr>
<tr>
	<td><b>Password</b></td>
	<td><input type="password" name="password" id="gray"></td>
</tr>
<tr>
	<td><b>Remember Me On This Computer</b></td>
	<td><input type="checkbox" name="setcookie"></td>
</tr>
<tr>
	<td colspan="2">
		<input type="hidden" name="function" value="Log In" />
		<input type="hidden" name="redirect" value="<?= $redirect ?>" />
		<input type="submit" name="submit" value="Log In" />
	</td>
</tr>
</table>

<a href="<?php echo $baseurl;?>/register/" style="color: orange">Register for an account</a> <br>
<a href="<?php echo $baseurl;?>/password/" style="color: orange">Forgot your username or password?</a>

</div>
</form>


</div>

	</div>