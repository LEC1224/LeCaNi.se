<meta charset="UTF-8"> 
<title>LeCaNi - Mer än en server</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
	$(function(){
    $.get("http://198.251.85.27:8888/aktivitet/").done(function(d) {
      $("#aktivitet_holder").html("<h4 class=\"title\">Aktivitet</h4>" + d);
      $(".hidden").hide();
      $(".clickable").click(function(){
        $("#" + $(this).attr("id") + "_sub").toggle(500);
      });
    });
    $.get("http://198.251.85.27:8888/online/").done(function(d) {
      $("#online_holder").html("<h4 class=\"title\">Online</h4>" + d);
    });
    $(".hidden").hide();
    $(".clickable").click(function(){
      $("#" + $(this).attr("id") + "_sub").toggle(500);
    });
	});
</script>