<?php
	file_get_contents("http://198.251.85.27:8888/whitelist/?pass=tackkacgal&username=" . $_POST["name"]);
	copy("../ansokan/" . $_POST["name"] . ".ud", "../accepterade/" . $_POST["name"] . ".ud");
	unlink("../ansokan/" . $_POST["name"] . ".ud");
?>