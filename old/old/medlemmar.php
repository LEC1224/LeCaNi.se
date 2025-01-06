<!DOCTYPE HTML>
<html>
	<head>
		<?php include "head.php"; ?>
	</head>
	<body>
		<div id="wrapper">
			<?php include "header.php"; ?>
			<?php
				$dir = new DirectoryIterator("accepterade");
				$i = 4;
				foreach ($dir as $fileinfo) {
					if (!$fileinfo->isDot()) {
						$file = "accepterade/" . $fileinfo->getFilename();
						$fh = fopen($file, 'r');
						$arr = explode("\n", fread($fh, filesize($file)));
						echo "<div class=medlem><img id=\"$arr[10]\" class=\"face\" src=\"http://minecraft-skin-viewer.com/face.php?u=$arr[10]&s=64\"/><br><span>$arr[10]</span></div>";
						fclose($fh);
						$i++;
					}
				}
				for($a = 0; $a < $i; $a++)
					echo "<br/>";
			?>
			<?php include "footer.php"; ?>
		</div>
	</body>
</html>