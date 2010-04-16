<div class="section">
<h1>Digital Millennium Copyright Act (1998)<br/>Takedown Request</h1>

<div id="red"><?=$errmsg?></div>

<p>Under the conditions of the Digital Millennium Copyright Act of 1998 the copyright owner of a digital work is entitled to file a Takedown Request (section 512) which notifies us that TheTVDB may be using copyrighted material.</p>
<p>From the DMCA 1998 Section 512:<br/>"Under the notice and takedown procedure, a copyright owner submits a notification <b>under penalty of perjury</b>, including a list of specified elements, to us (TheTVDB). Failure to comply substantially with the statutory requirements means that the notification will not be considered in determining the requisite level of knowledge or infringement by us. If, upon receiving a proper notification, we promptly remove or block access to the material identified in the notification, we are exempt from monetary liability. In addition, we are protected from any liability to any person for claims based on its having taken down the material. <b>Penalties are provided for knowing material misrepresentations in either a notice or a counter notice.</b> Any person who knowingly materially misrepresents that material is infringing, or that it was removed or blocked through mistake or misidentification, is liable for any resulting damages (including costs and attorneys' fees) incurred by the alleged infringer, the copyright owner or its licensee, or us."</p>
<p>If you are (or if you represent) the copyright owner of an image on this website then please complete the form below. All fields are mandatory and both tick-boxes must be ticked. An incomplete or unticked submission will not be considered a proper notification under the terms of the act. </p>

<form action="<?=$fullurl?>" method="POST">
	<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td>Infringed Work Name:</td>
			<td><input type="text" name="workname" size="40"></td>
		</tr>
		<tr>
			<td>Direct Link to Infringement:</td>
			<td><input type="text" name="link" size="40"></td>
		</tr>
		<tr>
			<td>Copyright Owner:</td>
			<td><input type="text" name="copyown" size="40"></td>
		</tr>
		<tr>
			<td colspan="2"><b>Takedown request is hereby made by:</td>
		</tr>
		<tr>
			<td>Name / Company:</td>
			<td><input type="text" name="byname" size="40"></td>
		</tr>
		<tr>
			<td>E-mail Address:</td>
			<td><input type="text" name="byemail" size="40"></td>
		</tr>
		<tr>
			<td>Other Info / General Remarks:</td>
			<td><textarea rows="4" cols="50" name="byremarks"></textarea></td>
		</tr>
	</table>
	<p>
		<input type="checkbox" value="yes" name="agree1">I understand by subitting this Takedown Request I may be liable for penalties if the information provided is incorrect.<br/>
		<input type="checkbox" value="yes" name="agree2">I am, or I represent, the copyright owner of the image submitted in this Takedown Request.
		<input type="hidden" value="DMCA" name="tab">
	</p>
	<p><input type="submit" value="Submit Takedown Request" name="function"></p>
	<p>Upon receipt of a Takedown Request we will endeavour to ascertain the authenticity of the request and, upon verification, we will remove the copywritted work. Please allow up to 5 days for your request to be processed.</p>
</form>
</div>
