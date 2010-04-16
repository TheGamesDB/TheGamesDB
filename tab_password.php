<div class="section">
<h1>Reset Password</h1>
<form action="<?=$fullurl?>" method="POST">
<div id="red"><?=$errormessage?></div>
<table cellspacing="2" cellpadding="2" border="0" align="center">
<tr>
	<td colspan="2">Please enter your email address below and we will send you a replacement password.</td>
</tr>
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