<html>
<head>
	<title>LEC Poetry</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="icon" href="../img/icon.png">
	<meta charset="UTF-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="main.js"></script>
</head>
<body onload="load()">
  <div id="wrapper">
	<?php include "header.php" ?>
	<div class="page">
		<h1 id="poemTitle"></h1>
		<p id="poem"></p>
	</div>
	<div class="page">
		<h2>Poems</h2>
		<?php include "listPoems.php" ?>
		<h2>Lyrics</h2>
		<?php include "listLyrics.php" ?>
		<h2>Unserious Poems</h2>
		<?php include "listUpoems.php" ?>
		<h2>Unserious Lyrics</h2>
		<?php include "listUlyrics.php" ?>
		<h2>Parody Lyrics</h2>
		<?php include "listParodies.php" ?>
		<br /><br /><br /><br /><br /><br />
	</div>
	<?php include "footer.php" ?>
  </div>
</body>
</html>