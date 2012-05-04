<?php
	include ('include.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>Genres Selector</title>
	<link rel=stylesheet href="<?= $baseurl ?>/pngHack/pngHack.css" type="text/css">
	<link rel="stylesheet" type="text/css" href="<?= $baseurl ?>/standard.css">
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
		function copygenre() {
			var genlist=""
<?
				$query = "SELECT count(*) AS amount FROM genres";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$max = mysql_fetch_object($result)->amount;
				$counter=0;
				while ($max != $counter){
					echo "			if (document.genresform.chkgenres[$counter].checked== true){genlist = genlist+\"|\"+document.genresform.chkgenres[$counter].value}\n";
					$counter++;
				}
?>
			if (genlist.length>1){genlist = genlist+"|"} 
			if (genlist.length>99){
				alert("You have selected too many genres.")
			}
			else{
				opener.document.editGameForm.Genre.value = genlist;
				opener.document.editGameForm.Genrefake.value = genlist;
				self.close();
			}
			return false;
		}
	</script>
</head>
<body style="background-color: #333; color: #fff; font-family: ">
<table width="95%" cellspacing="0" cellpadding="0" border="0" align="center">
<tr><td>
	<div class="titlesection">
		<h1>Genres</h1>
		<h3>Please select the genres for <?=$GameTitle?>.</h3>
	</div>

	<div class="section">

			<form action="#" method="POST" name="genresform" onSubmit="return false">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" id="datatablelist">
<?
				$query = "SELECT * FROM genres ORDER BY genre";
				$result = mysql_query($query) or die('Query failed: ' . mysql_error());
				$tr = 1;								
				while ($genres = mysql_fetch_object($result)) {
					if ($tr == 1){echo "				<tr>\n";}
?>
					<td><INPUT TYPE="checkbox" NAME="chkgenres" VALUE="<?=$genres->genre?>" <?if (strstr($Genre,"$genres->genre")){echo "checked";}?>></td>
					<td><?=$genres->genre?> </td>
<?
					if ($tr == 2){echo "				</tr>\n"; $tr = 1;}else{$tr = 2;}
				}
?>								
				<tr>
					<td style="text-align: right" colspan="4">
						<input type="submit" name="function" value="Accept" class="submit" onClick="return copygenre()">
					</td>
				</tr>
			</table>
		</form>
	</div>
</td></tr>
</table>
</body>
</html>