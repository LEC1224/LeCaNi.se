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
				<h2>Callemansrätten</h2><ul>
				<li>Calleman får gå precis vart han vill</li>
				<li>Calleman får se ut hur han vill</li>
				<li>Calleman får tycka att han kan spraya bra</li>
				<li>Calleman får kalla sig LEM om han vill</li>
				<li>Calleman får både sjunga och dansa</li>
				<li>Calleman får skjuta vem han vill</li>
				<li>Calleman får uttrycka sig med ord som "Osthyvel", "Hicka", mm</li>
				<li>Calleman får få ace:ar om han vill</li>
				<li>Calleman får spela minecraft om han vill</li>
				<li>Calleman får göra så om han inte gjort det förut</li>
				<li>Calleman får tro att han kan saker</li>
				<li><b>Calleman får vara bättre än dig</b></li></ul>
            </div>
			<?php include "footer.php" ?>
		</div>
	</body>
</html>