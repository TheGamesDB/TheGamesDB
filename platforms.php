<?php
	include ('include.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Platforms Selector</title>
	<link rel=stylesheet href="<?= $baseurl ?>/pngHack/pngHack.css" type="text/css">
	<link rel="stylesheet" type="text/css" href="<?= $baseurl ?>/default.css">
	<script type="text/javascript" src="<?= $baseurl ?>/xfade2.js"></script>
	<script type="text/javascript" src="<?= $baseurl ?>/niftycube.js"></script>
	<script type="text/javascript">
		window.onload=function(){
			Nifty("DIV.section","big");
			Nifty("DIV.footer","big");
			Nifty("DIV.titlesection","big");
		}
	</script>
	<script type="text/javascript">
		function copyplatform() {
			var platlist=""
<?
				$query = "SELECT count(*) AS amount FROM platforms";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$max = mysql_fetch_object($result)->amount;
				$counter=0;
				while ($max != $counter){
					echo "			if (document.platformsform.chkplatforms[$counter].checked== true){platlist = platlist+\"|\"+document.platformsform.chkplatforms[$counter].value}\n";
					$counter++;
				}
?>
			if (platlist.length>1){platlist = platlist+"|"}
			if (platlist.length>99){
				alert("You have selected too many platforms.")
			}
			else{
				opener.document.seriesform.Platform.value = platlist;
				opener.document.seriesform.Platformfake.value = platlist;
				self.close();
			}
			return false;
		}
	</script>
</head>
<body style="color:#fff;">
<table width="95%" cellspacing="0" cellpadding="0" border="0" align="center">
<tr><td>
	<div class="titlesection">
		<h1>Platforms</h1>
		<h3>Please select the platforms for <?=$GameTitle?>.</h3>
	</div>

	<div class="section">

			<form action="#" method="POST" name="platformsform" onSubmit="return false">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" id="datatablelist">
<?
				$query = "SELECT * FROM platforms ORDER BY name";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$tr = 1;								
				while ($platforms = mysql_fetch_object($result)) {
					if ($tr == 1){echo "				<tr>\n";}
?>
					<td><?=$platforms->name?>: </td>
					<td><INPUT TYPE="checkbox" NAME="chkplatforms" VALUE="<?=$platforms->name?>" <?if (strstr($Platform,"$platforms->name")){echo "checked";}?>></td>
<?
					if ($tr == 2){echo "				</tr>\n"; $tr = 1;}else{$tr = 2;}
				}
?>								
				<tr>
					<td style="text-align: right" colspan="4">
						<input type="submit" name="function" value="Accept" class="submit" onClick="return copyplatform()">
					</td>
				</tr>
			</table>
		</form>
	</div>
</td></tr>
</table>
</body>
</html>