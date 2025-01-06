<!DOCTYPE HTML>
<html>
	<head>
		<?php include "head.php"; ?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script>
			function submit(){
				document.forms[0].submit();
			}
			$(function(){
				year = new Date().getFullYear();
				for(i = year-100; i < year; i++)
					$("#fodd_ar").append(new Option(i, i));
				$("#fodd_ar").append(new Option(year, year));
				$("textarea").keypress(function (e) {
					if (e.keyCode != 13) return;
					var msg = $(this).val().replace(/\n/g, "");
					$(this).val(msg);
					return false;
				});
			});
		</script>
	</head>
	<body>
		<div id="wrapper">
			<?php include "header.php"; ?>
			<div id="page">
        <p>Ans&ouml;kningarna fungerar inte som de ska just nu. D&auml;rav m&aring;ste du sj&auml;lv skicka ett mail till info@lecani.se d&auml;r du svarar p&aring; de h&auml;r fr&aring;gorna:</p>
        <p>
          Hur länge har du spelat minecraft?<br />
          Vilket år är du född?<br />
          Vad heter du på minecraft?<br />
          Vad heter du på Skype?<br />
          Känner du någon på LeCaNi? Vem/Vilka?<br />
          Har du läst och kommer f&ouml;lja <a href="regler.php">LeCaNis regler</a>?<br />
          Varf&ouml;r vill du komma in p&aring; LeCaNi?
        </p>
        <p>Du kan &auml;ven passa p&aring; att kolla lite p&aring; <a href="infomation.php">infomationsidan</a> d&auml;r du hittar allt du beh&ouml;ver veta f&ouml;r att ha kul p&aring; LeCaNi!</p>
      </div>
			<!--<div id="page">
				<h3> Uppgifter </h3>
				<p>
					Så här gör du: <br />
					Fyll i fälten nedanför, och klicka på skicka<br />
				</p>
				<form action="MAILTO:lecanistuff@mail.com" method=POST id=form enctype="text/plain">
					<label for="spelat_mc_tid">Hur länge har du spelat minecraft?</label><br/>
					<div id="spelat_mc_tid">
						<label for="spelat_mc_1">1 år eller mindre</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_1" value="1"/><br/>
						
						<label for="spelat_mc_2">1-2 år</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_2" value="2"/><br/>
						
						<label for="spelat_mc_3">2-3 år</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_3" value="3"/><br/>
						
						<label for="spelat_mc_4">3-4 år</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_4" value="4"/><br/>
						
						<label for="spelat_mc_5">4+ år</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_5" value="5"/><br/>
					</div><br/>
					
					<label for="fodd_ar">Vilket år är du född?</label><br/>
					<select id="fodd_ar" form="form" required="required" name="fodd_ar"></select><br/>

					<label for="mc_namn">Vad heter du på minecraft?</label><br/>
					<input type=text id="mc_namn" name="mc_namn"/><br/>
					
					<label for="skype_namn">Vad heter du på Skype?</label><br/>
					<input type=text id="skype_namn" name="skype_namn"/><br/>
					
					<label for="kanner">Känner du någon på LeCaNi? Vem/Vilka? (Minecraft namn)</label><br/>
					<input type=text id="kanner" name="kanner"/><br/>
					
					<label for="last_regler">Har du läst och kommer f&ouml;lja <a href="regler.php">LeCaNis regler</a></label><br/>
					<div id="last_regler">
						<label for="ja">Ja</label>
						<input type="radio" name="last_regler" id="ja" value="1"/><br/>
						<label for="nej">Nej</label>
						<input type="radio" name="last_regler" id="nej" value="0"/><br/>
					</div><br/>

					<input type="hidden" name="submit" value="1"/>
					<button id="send" onclick="submit">Skicka!</button>
				</form>
        
        <!--<form action=index.php method=POST id=form>
					<label for="spelat_mc_tid">Hur länge har du spelat minecraft?</label><br/>
					<div id="spelat_mc_tid">
						<label for="spelat_mc_1">1 år eller mindre</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_1" value="1"/><br/>
						
						<label for="spelat_mc_2">1-2 år</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_2" value="2"/><br/>
						
						<label for="spelat_mc_3">2-3 år</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_3" value="3"/><br/>
						
						<label for="spelat_mc_4">3-4 år</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_4" value="4"/><br/>
						
						<label for="spelat_mc_5">4+ år</label>
						<input type="radio" name="spelat_mc_tid" id="spelat_mc_5" value="5"/><br/>
					</div><br/>
					
					<label for="vetat_lecani_tid">Hur länge har du vetat vad LeCaNi är?</label><br/>
					<div id="vetat_lecani_tid">
						<label for="vetat_lecani_1">1 år eller mindre</label>
						<input type="radio" name="vetat_lecani_tid" id="vetat_lecani_1" value="1"/><br/>
						
						<label for="vetat_lecani_2">1-2 år</label>
						<input type="radio" name="vetat_lecani_tid" id="vetat_lecani_2" value="2"/><br/>
						
						<label for="vetat_lecani_3">2-3 år</label>
						<input type="radio" name="vetat_lecani_tid" id="vetat_lecani_3" value="3"/><br/>
						
						<label for="vetat_lecani_4">3+ år</label>
						<input type="radio" name="vetat_lecani_tid" id="vetat_lecani_4" value="4"/><br/>
					</div><br/>
					
					<label for="varfor">Varför vill du ansöka till LeCaNi?</label><br/>
					<textarea name="varfor" id="varfor" rows=5 cols=50 form="form" rquired="required"></textarea><br/>
					
					<label for="social">Hur social i minecraftchatten och skype kommer du vara?</label><br/>
					<div id="social">
						<label for="svd">Social? Vad ar det?</label>
						<input type="radio" name="social" id="svd" value="svd"/><br/>
						
						<label for="aldrig">Aldrig i livet</label>
						<input type="radio" name="social" id="aldrig" value="aldrig"/><br/>
						
						<label for="lite">Lite</label>
						<input type="radio" name="social" id="lite" value="lite"/><br/>
						
						<label for="daoda">Då och då</label>
						<input type="radio" name="social" id="daoda" value="daoda"/><br/>
						
						<label for="ndb">Nar det behovs</label>
						<input type="radio" name="social" id="ndb" value="ndb"/><br/>
						
						<label for="gm">Ganska mycket</label>
						<input type="radio" name="social" id="gm" value="gm"/><br/>
						
						<label for="daoda">Mycket</label>
						<input type="radio" name="social" id="mycket" value="mycket"/><br/>
					</div><br/>
					
					<label for="fodd_ar">Vilket år är du född? (OBS, om du låter fältet stå på 1915 är din ansökan ogiltig, svara ärligt)</label><br/>
					<select id="fodd_ar" form="form" required="required" name="fodd_ar"></select><br/>
					
					<label for="aktiv">Hur aktiv kommer du/kan du vara?</label><br/>
					<input type=text id="aktiv" name="aktiv"/><br/>
					
					<label for="liknande_servrar">Hur många liknande servrar har du varit aktiv på?</label><br/>
					<input type=text id="liknande_servrar" name="liknande_servrar"/><br/>
					
					<label for="bannad_servrar">Hur många servrar har du blivit bannad på, vilka anlednignar?</label><br/>
					<input type=text id="bannad_servrar" name="bannad_servrar"/><br/>
					
					<label for="har_egen">Har du haft/har du någon egen privatserver uppe?</label><br/>
					<input type=text id="har_egen" name="har_egen"/><br/>
					
					<label for="bra_mc">Vad är du bra på i minecraft?</label><br/>
					<textarea id="bra_mc" name="bra_mc" rows=5 cols=50 form="form" rquired="required"></textarea><br/>
					
					<label for="mc_namn">Vad heter du på minecraft?</label><br/>
					<input type=text id="mc_namn" name="mc_namn"/><br/>
					
					<label for="skype_namn">Vad heter du på Skype?</label><br/>
					<input type=text id="skype_namn" name="skype_namn"/><br/>
					
					<label for="kanner">Känner du någon på LeCaNi? Vem/Vilka? (Minecraft namn)</label><br/>
					<input type=text id="kanner" name="kanner"/><br/>
					
					<label for="last_regler">Har du läst <a href="regler.php"> LeCaNis regler </a> och förstått dess innebörder?</label><br/>
					<div id="last_regler">
						<label for="ja">Ja</label>
						<input type="radio" name="last_regler" id="ja" value="1"/><br/>
						<label for="nej">Nej</label>
						<input type="radio" name="last_regler" id="nej" value="0"/><br/>
					</div><br/>
					
					<label for="kommer_folja">Lovar du att följa LeCaNis regler i alla lägen?</label><br/>
					<div id="last_regler">
						<label for="ja">Ja</label>
						<input type="radio" name="kommer_folja" id="ja" value="1"/><br/>
						<label for="nej">Nej</label>
						<input type="radio" name="kommer_folja" id="nej" value="0"/><br/>
					</div><br/>
					<input type="hidden" name="submit" value="1"/>
					<button id="send" onclick="submit">Skicka!</button>
				</form>
			</div>-->
			<?php include "footer.php" ?>
		</div>
	</body>
</html>
