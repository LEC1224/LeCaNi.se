<!DOCTYPE HTML>
<html>
	<?php include "auxHead.php" ?>
	<body>
    <div class="container-fluid col-xs-12 col-md-12 col-sm-12 col-lg-12" id="wrapper">
        <?php include "auxHeader.php" ?>
		  <div class="page hidden-xs hidden-sm">
			<div id="welcome" class="content-box col-md-2 col-lg-2"> <!--col-xs-12 col-sm-12-->
			  <img src="img/front.jpg" id="front" />
				<div class="sidepanel col-md-12 col-lg-12">
				  <div id="sidetitle">
					<h1>Snabblänkar</h1>
				  </div>
				  <div class="sidemenu">
					<ul>
					  <a href="https://discord.gg/NxdY6Gk"><li>Discord</li></a>
					  <a href="http://mc.lecani.se:8123"><li>Kartan</li></a>
					  <a href="img/nav.png"><li>Nethervägar</li></a>
					  <a><li id="copyButton">IP: mc.lecani.se</li></a>
					  <a href="https://youtube.com/LeCaNiVideos"><li>LeCaNiVideos</li></a>
					</ul>
					<p style="visibility: hidden" id="textToCopy">mc.lecani.se:25565</p>
				  </div>
				</div>
			</div>
			<div id="welcome" class="content-box col-md-7 col-lg-7">
			<?php include "homeContent.php" ?>
			</div>
			<div class="content-box col-md-3 col-lg-3"> 
			<!--<?php include "auxTwitter.php" ?>-->
			<?php include "news.php" ?>
			</div>
		  </div>

      <div class="page-mo col-xs-12 col-sm-12 hidden-md hidden-lg" id="mo-welcome">
        <?php include "homeContent.php" ?>
        <!--<div id="mo-twitter" class="col-md-3 col-lg-2">
          <a href="https://twitter.com/intent/tweet?button_hashtag=LeCaNi&ref_src=twsrc%5Etfw" class="twitter-hashtag-button" data-show-count="false">Tweet #LeCaNi</a>
		  <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>-->
		<?php include "news.php" ?>
      </div>
      <?php include "auxFooter.php" ?>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script src="copyText.js"></script>
	</body>
</html>