<div class="section">
<h1>Advanced Search</h1>
	<form action="tab_radsearch.php" method="GET">
	<table border="0" cellspacing="0" cellpadding="2" width="50%">
		<tr>
			<td>Series Name: </td>
			<td><input type="text" name="seriesname" value="<?=$seriesname?>"></td>
		</tr>
		<tr>
			<td>Year: </td>
			<td><input type="text" name="year" value="<?=$year?>"></td>
		</tr>
		<tr>
			<td>Zap2it: </td>
			<td><input type="text" name="zap2it_id" value="<?=$zap2it_id?>"></td>
		</tr>
		<tr>
			<td>TV.com ID: </td>
			<td><input type="text" name="tvcom_id" value="<?=$tvcom_id?>"></td>
		</tr>
		<tr>
			<td>IMDB.com ID:</td>
			<td><input type="text" name="imdb_id" value="<?=$imdb_id?>"></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="Search">
				<input type="hidden" name="tab" value="advancedsearch">
			</td>
		</tr>
	</table>
	</form>
</div>

<div class="section">
<h1>Results</h1>
	<p><?=$errmsg?></p>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" id="listtable">
		<tr>
			<td class="head">Series ID</td>
			<td class="head">Series Name</td>
			<td class="head">Genre</td>
			<td class="head">Status</td>
		</tr>
	</table>
</div>
