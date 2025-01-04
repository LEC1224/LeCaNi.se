<html>
<?php include "head.php" ?>
<body onload="load()">
  <div id="wrapper">
	<?php include "header.php" ?>
	<div class="page">
		<h1 id="poemTitle"></h1>
		<p id="poem"></p>
		<h2>Poems</h2>
		<?php include "listPoems.php" ?>
		<br />
		<h2>Lyrics</h2>
		<?php include "listLyrics.php" ?>
		<br />
		<h2>Unserious Poems</h2>
		<?php include "listUpoems.php" ?>
		<br />
		<h2>Unserious Lyrics</h2>
		<?php include "listUlyrics.php" ?>
		<br />
		<h2>Parody Lyrics</h2>
		<?php include "listParodies.php" ?>
		<br />
		<h2>Teammate Tributes</h2>
		<?php include "listTributes.php" ?>
		<h2>Essays</h2>
		<?php include "listEssays.php" ?>
		<br /><br /><br /><br /><br /><br />
	</div>
	<?php include "footer.php" ?>
  </div>
</body>
</html>