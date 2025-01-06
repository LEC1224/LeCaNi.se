<?php
	if(isset($_GET["pass"]) && !isset($_GET["tackkacgal"])){
		setcookie("pass", $_GET["pass"], time() + (86400*30), "/");
		echo "<script>window.location.href = \"/admin/index.php\";</script>";
	}
	else if(!isset($_COOKIE["pass"]) && !isset($_GET["tackkacgal"])){
		echo "Password is requied!";
	}
	else if(!isset($_GET["tackkacgal"]) && !($_COOKIE["pass"] == "tackkacgal")){
		echo "Incorrect password!";
	}
	else {
?>
<!DOCTYPE HTML>
<html>
	<head>
		<?php include "../head.php"; ?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script>
			var ppl = [];
			<?php
				$dir = new DirectoryIterator("../ansokan");
				foreach ($dir as $fileinfo) {
					if (!$fileinfo->isDot()) {
						$file = "../ansokan/" . $fileinfo->getFilename();
						$fh = fopen($file, 'r');
						$arr = explode("\n", fread($fh, filesize($file)));
						echo "$arr[10] = [];";
						foreach($arr as $val){
							echo "$arr[10].push(\"$val\");";
						}
						echo "ppl.push($arr[10]);";
					}
				}
			?>
			$(function(){
				$(".face").click(function(){
					i = 0;
					while(i < ppl.length){
						if(ppl[i][10] == $(this).attr("id"))
							break;
						i++;
					}
					pers = ppl[i];
					var d = $("#display");
					$("#name").attr("value", pers[10]);
					d.hide(500, function(){
						d.html("<ul>");
						d.append("<li>Har spelat MC i " + getMCYears(pers[0]) + " år</li>");
						d.append("<li>Har vetat om LeCaNi i " + getLCNYears(pers[1]) + " år</li>");
						d.append("<li>Anledning: " + pers[2] + "</li>");
						d.append("<li>Socialitet: " + getSocialLevel(pers[3]) + "</li>");
						d.append("<li>Född: " + pers[4] + "</li>");
						d.append("<li>Aktivitet: " + pers[5] + "</li>");
						d.append("<li>Liknande servrar: " + pers[6] + "</li>");
						d.append("<li>Bannad fran servrar: " + pers[7] + "</li>");
						d.append("<li>Egen server: " + pers[8] + "</li>");
						d.append("<li>Bra pa i MC: " + pers[9] + "</li>");
						d.append("<li>MC namn: " + pers[10] + "</li>");
						d.append("<li>Skype namn: " + pers[11] + "</li>");
						d.append("<li>Kanner pa lecani: " + pers[12] + "</li>");
						d.append("<li>Har last regler: " + getYesNo(pers[13]) + "</li>");
						d.append("<li>Kommer folja regler: " + getYesNo(pers[14]) + "</li>");
						d.append("</ul>");
						$("#display").show(500);
					});
				});
				$("#accept").click(function(){
					t = $("#name").attr("value");
					if(t == "n"){
						alert("Du maste valja nagan!");
						return;
					}
          $.get("http://188.40.96.3:8888/whitelist/", {
            pass: "tackkacgal",
            username: t
          }).done(function() {
            $.post("accept.php", {
              name: t
            }).done(function(){
              window.location.reload();
            });
          });
				});
				$("#decline").click(function(){
					t = $("#name").attr("value");
					if(t == "n"){
						alert("Du maste valja nagan!");
						return;
					}
					$.post("decline.php", {
						name: t
					}).done(function(){
						window.location.reload();
					});
				});
			});
			function getYesNo(val){
				return (val == "1" ? "Ja" : "Nej");
			}
			function getSocialLevel(val){
				switch(val){
					case "svd": return "Social? Vad är det?";
					case "aldrig": return "Aldrig i livet";
					case "lite": return "Lite";
					case "daoda": return "Då och då";
					case "ndb": return "När det behövs";
					case "gm": return "Ganska mycket";
					case "mycket": return "Mycket";
					default: return "odefinerat";
				}
			}
			function getMCYears(val){
				switch(val){
					case "1": return "1 eller mindre";
					case "2": return "1-2";
					case "3": return "2-3";
					case "4": return "3-4";
					case "5": return "5+";
					default: return "odefinerat";
				}
			}function getLCNYears(val){
				switch(val){
					case "1": return "1 eller mindre";
					case "2": return "1-2";
					case "3": return "2-3";
					case "4": return "3+";
					default: return "odefinerat";
				}
			}
		</script>
	</head>
	<body>
		<div id=wrapper>
			<?php include "../header.php"; ?>
			<?php
				$dir = new DirectoryIterator("../ansokan");
				$i = 4;
				foreach ($dir as $fileinfo) {
					if (!$fileinfo->isDot()) {
						$file = "../ansokan/" . $fileinfo->getFilename();
						$fh = fopen($file, 'r');
						$arr = explode("\n", fread($fh, filesize($file)));
						echo "<img id=\"$arr[10]\" class=\"face\" src=\"http://minecraft-skin-viewer.com/face.php?u=$arr[10]&s=64\"/>";
						fclose($fh);
						$i++;
					}
				}
				for($a = 0; $a < $i; $a++)
					echo "<br/>";
			?>
			<div id="display">Klicka på ett ansite ovan för att se ansökan!</div>
			<br>
			<input type="hidden" value="n" id="name"/>
			<button id="accept">Whitelista</button>
			<button id="decline">Ta bort</button>
			<?php include "../footer.php"; ?>
		</div>
	</body>
</html>
<?php
	}
?>