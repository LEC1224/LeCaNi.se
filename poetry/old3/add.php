<html>
<?php include "head.php" ?>
<body onload="load()">
  <div id="wrapper">
	<?php include "header.php" ?>
	<div class="page">
		<h1>Add Poem</h1>
		<form>
			<h4>Name</h4>
			<input type="text" id="add_form_name"></input>
			
			<h4>Category</h4>
			<input type="radio" id="css" name="add_form_category" value="Poems">Poems</input><br />
			<input type="radio" id="css" name="add_form_category" value="Lyrics">Lyrics</input><br />
			<input type="radio" id="css" name="add_form_category" value="Unserious Poems">Unserious Poems</input><br />
			<input type="radio" id="css" name="add_form_category" value="Unserious Lyrics">Unserious Lyrics</input><br />
			<input type="radio" id="css" name="add_form_category" value="Parody Lyrics">Parody Lyrics</input>
			
			<h4>Content</h4>
            <textarea id="add_form_content" style="width: 300px; height: 400px;"></textarea>
			
			<h4>Submit</h4>
			<input type="submit"></input>
			<br /><br /><br /><br /><br /><br />
        </form>
	</div>
	<?php include "footer.php" ?>
  </div>
</body>
</html>