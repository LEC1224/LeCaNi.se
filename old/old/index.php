<?php
	function request(){
		if(isset($_POST["submit"])){
			save();
			$tr = "<p>";
			$tr .= "Din ansökan har sparats!<br/>Den kommer att granskas snart.";
			return $tr . "</p>";
		}
		else return "";
	}
	
	$fail = false;
	
	function save(){
		global $fail;
		$file = "ansokan/" . $_POST["mc_namn"] . ".ud";
		$fh = fopen($file, 'w');
		
		w($fh, "spelat_mc_tid");
		w($fh, "vetat_lecani_tid");
		w($fh, "varfor");
		w($fh, "social");
		w($fh, "fodd_ar");
		w($fh, "aktiv");
		w($fh, "liknande_servrar");
		w($fh, "bannad_servrar");
		w($fh, "har_egen");
		w($fh, "bra_mc");
		w($fh, "mc_namn");
		w($fh, "skype_namn");
		w($fh, "kanner");
		w($fh, "last_regler");
		w($fh, "kommer_folja");
		
		fclose($fh);
		if($fail)
			unlink($file);
	}
	
	function w($fh, $m){
		global $fail;
		if(isset($_POST[$m]))
			fwrite($fh, $_POST[$m] . "\n");
		else
			$fail = true;
	}
?>
<!DOCTYPE HTML>
<html>
	<head>
		<?php include "head.php"; ?>
	</head>
	<body>
		<div id="wrapper">
			<?php include "header.php"; ?>
			<div id="page">
				<h2>Välkommen</h2>
				<?php echo request(); ?>
				<p><b>Hej!</b> Välkommen till LeCaNis hemsida! Här hittar du allt du behöver veta för att hänga med i LeCaNis svängar! Du kan även <a href="ansok.php">ansöka</a> till vår server!</p>
				<p>Hittade du hemsidan på annat sätt än att redan veta om LeCaNi? Isåfall, här är en kort beskrivning av LeCaNi:<br/>
			  LeCaNi är en gamingcomunity. Till absolut största delen är LeCaNi en minecraftserver. De flesta känner till LeCaNi via youtubekanalen <a href="http://youtube.com/lecanivideos">LeCaNiVideos</a> som hålls av 					samma person som skriver detta, Carl!</p>
				<p>Klicka <a href="kopia.php">här</a> för att komma till en likadan sida!</p>
            </div>

		  <div class="news"><br>
				<h2>Newsfeed</h2>
           	<p><b>Portaler</b><br />              
       	   	<img src="Bilder/news/teleport1.png" width="250">
			<img src="Bilder/news/teleport2.png" width="250">
            Efter lite problem i en dryg månad kan man nu äntligen teleportera mellan Nya och Gamla LeCaNi! Det finns en portal i varje värld, i Nya LeCaNi ligger den bredvid Spawnhuset och i Gamla LeCaNi ligger den nära det
			 gamla spawntornet i Fabulania. Med sina fyr-karaktäriska röd-vita väggar är portalerna svåra att missa!</p>
			
			<p><b>The Endurstrial Farm</b><br />              
       	   	<img src="Bilder/news/xp_riv.jpg" width="530" height="330">
            Efter att ha haft samma endfarm i över 1½ år så var det dags för något nytt. Här ser vi Carl och
            Nicke som just rivit farmen och bara har golvet kvar.
            <img src="Bilder/news/xp_eva.jpg" width="530" height="330">
			Den nya farmen "The Endustrial Farm" byggdes av Gitarren, RMW och MinerC med Gitarren som
            initiativtagare och projektledare. Denna byggnad producerar lite mer endermen än den förra och är
            dessutom mycket lägre och resursvänligare. Designen gjordes från början av <a href="http://youtube.com/docm77">Docm77</a>. <br />
			Till skillnad från de flesta endfarmer som byggs långt bort från ön
            för effektivitetens skull så brillerar The Endustrial Farm med att vara byggd på en endö som är fullt täckt av slabs, vilket hindrar endermen att spawna!<img src="Bilder/news/xp_iva.jpg" width="530" height="330">
            Som grädden på moset så är farmen mycket snyggare och efter 1½ år av erfarenhet så har vi lärt 
            oss vad vi gjort fel med den förra så den är smartare och bekvämare att använda.</p>
            </div>
			<?php include "footer.php" ?>
		</div>
	</body>
</html>