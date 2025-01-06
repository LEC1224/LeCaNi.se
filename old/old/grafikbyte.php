<!DOCTYPE HTML>
<html>
	<head>
		<?php include "head.php"; ?>
		<script>
			$(function(){
				$(".clickable").click(function(){
					console.log($(this).attr("id"));
					$("#" + $(this).attr("id") + "_sub").toggle(500);
				});
			});
		</script>
	</head>
	<body>
		<h1>Du Har Blivit Utsatt För En Busringning</h1>
	</body>
</html>
