<html>
<head>
	<title>LEC Poetry</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="icon" href="../img/icon.png">
	<meta charset="UTF-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="show.js"></script>
</head>
<body onload="hide()">
  <div id="wrapper">
	<?php include "header.php" ?>
	<div class="page">
		<div class="poem" onclick="getTextFile(this)">
			<p class="name" id="no_rest_for_the_wicked">No Rest for the Wicked</p>
			<p class="hide"><object data="no_rest_for_the_wicked.txt"></object> </p>
		</div>
		<div class="poem" onclick="getTextFile(this)">
			<p class="name" id="heart_of_gold">Heart of Gold</p>
			<p class="hide"><object data="heart_of_gold.txt"></object> </p>
		</div>
		<div class="poem" onclick="getTextFile(this)">
			<p class="name" id="silhouette">Silhouette</p>
			<p class="hide"><object data="silhouette.txt"></object> </p>
		</div>
		<div class="poem">
			<iframe id="iframe" src="no_rest_for_the_wicked.txt"></iframe>
		</div>
	</div>
	<?php include "footer.php" ?>
  </div>
</body>
</html>