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
				<h2>Donera till LeCaNi</h2>
				<?php echo request(); ?>
				<p>
				<b>Hej! Innan du donerar till LeCaNi, läs detta!</b><br />
				Alla donationer går till LeCaNi på ett eller annat sätt. Primärt går donationer till att hyra servern från <a href="http://minerack.org">minerack.org</a> och att ha uppe lecani.se!<br />
				Donationsknappen nedan är i första hand gjord för LeCaNi-spelare som vill hjälpa till, om du inte spelar på LeCaNi så rekommenderar jag att du inte ger LeCaNi pengar, men självklart får du det om du vill!
				Jag är oerhört glad att du vill donera men gör det inte om du inte verkligen vill, och om du verkligen vill donera så donera inga stora summor (Håll dig gärna under 20kr)!
				Jag vill inte ta dina pengar ifrån dig ;)<br />
				Om du är en LeCaNier och vill hjälpa din server så rekommenderar jag att donera runt 5-20kr! LeCaNi kostar ungefär 40kr/månad att hålla igång (2015-07-24). Om jag får in
				mer än 30kr/månad så kan jag öka minnet och få LeCaNi mer stabil så att servern inte krashar hela tiden!</p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="LUE687NDSTTVY">
					<input type="image" src="https://www.paypalobjects.com/sv_SE/SE/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – ett tryggt och smidigt sätt att betala på nätet med.">
					<img alt="" border="0" src="https://www.paypalobjects.com/sv_SE/i/scr/pixel.gif" width="1" height="1">
				</form>
				
            </div>

            </div>
			<?php include "footer.php" ?>
		</div>
	</body>
</html>