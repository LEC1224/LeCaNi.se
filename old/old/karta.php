<!DOCTYPE HTML>
<html>
	<head>
		<?php include "head.php"; ?>
	</head>
	<body>
		<div id="wrapper">
			<?php include "header.php"; ?>
      <script>
        $(function() {
          $("#map").height($(window).height() - $("#footer").height() - $("#wrapper").height());
          $("#map_full_btn").click(function() {
            $("#map_full").toggle();
          });
        })
      </script>
			<div id="page">
				<iframe id=map src=http://mc.lecani.se:8123/?worldname=LeCaNi&mapname=surface&zoom=1 frameBorder=0 seamless></iframe>
				<iframe id=map_full src=http://mc.lecani.se:8123/?worldname=LeCaNi&mapname=surface&zoom=1 frameBorder=0 seamless class=hidden></iframe>
        <button id=map_full_btn>Fullskärm</button>
			</div>
    </div>
    <?php include "footer.php" ?>
	</body>
</html>