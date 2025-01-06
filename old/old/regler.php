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
		<div id="wrapper">
			<?php include "header.php"; ?>
			<div id="info">
				<h2> Regler </h2>
        <p>Om alla kan vara schyssta och anv&auml;nd sunt f&ouml;rnuft s&aring; beh&ouml;vs det inga regler. Om du kan det s&aring; &auml;r du v&auml;lkommen in!</p>
				<!--<?php include "regler_src.php"; ?>-->
			</div>
			<?php include "footer.php" ?>
		</div>
	</body>
</html>
