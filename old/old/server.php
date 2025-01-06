<!DOCTYPE HTML>
<html>
	<head>
		<?php include "head.php"; ?>
	</head>
	<body>
		<div id="wrapper">
			<?php include "header.php"; ?>
			<div id="info">
				<h2> LeCaNi Server </h2>
				<li><a id="regler" class="clickable">Regler</a>
				<div class="hidden" id="regler_sub">
					<?php include "regler_src.php"; ?>
				</div></li>
				<li><a href="stader.php">Städer</a></li>
				<li><a href="stadsgrundning.php">Grunda en stad</a></li>
				<li><a href="medlemmar.php">Spelare</a></li>
				<li><a href="karta.php">Karta</a></li>
				<li><a href="events.php">Events</a></li>
				<p> Vill du veta mer, vilka fler länkar vill du ha? </p>
			</div>
			<?php include "footer.php" ?>
		</div>
	</body>
</html>