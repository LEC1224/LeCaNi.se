<!DOCTYPE html>

<html lang="en">
<?php include "head.php" ?>
<body>
  <div id="wrapper">
	<?php include "header.php" ?>
	<div class="edit_page">
	
		<select id="fileDropdown" onchange="loadSelectedText()">
		<option disabled="" selected="" value="">Select a file to load...</option>
		<!-- Files will be populated here by JavaScript -->
		</select><br />
		
		<textarea cols="50" rows="10" id="content_area"></textarea><br/>
		
		<input id="filename" placeholder="Filename (without .txt)" type="text"/>
		<button onclick="saveText()">Save</button><br/>
		
		<select id="typeDropdown">
			<option value="poem">Poem</option>
			<option value="lyrics">Lyric</option>
			<option value="upoems">Unserious Poem</option>
			<option value="ulyrics">Unserious Lyric</option>
			<option value="parodies">Parody</option>
			<option value="tributes">Teammate Tribute</option>
			<option value="essays">Essay</option>
		</select>
		<button onclick="publishText()">Publish</button><br/>
		
		<input id="password" placeholder="Enter password" type="password"/>
		<br /><br /><br /><br /><br /><br />
	</div>
	<?php include "footer.php" ?>
  </div>
<script>
document.addEventListener("DOMContentLoaded", populateDropdown);

function saveText() {
    let text = document.getElementById('content_area').value;
    let filename = document.getElementById('filename').value;
    let password = document.getElementById('password').value; // Get the password value
    fetch('save.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'password=' + encodeURIComponent(password) + '&content=' + encodeURIComponent(text) + '&filename=' + encodeURIComponent(filename),
    }).then(response => response.text()).then(data => {
        alert(data);
        populateDropdown(); // Refresh dropdown list after saving
    });
}

function loadSelectedText() {
    let selectedFile = document.getElementById('fileDropdown').value;
    
    fetch('loadFile.php?filename=' + encodeURIComponent(selectedFile)).then(response => response.text()).then(data => {
        document.getElementById('content_area').value = data;
        document.getElementById('filename').value = selectedFile.replace('.txt', '');
    });
}

function populateDropdown() {
    fetch('listFiles.php').then(response => response.json()).then(files => {
        let dropdown = document.getElementById('fileDropdown');
        dropdown.innerHTML = '<option value="" disabled selected>Select a file to load...</option>'; // Clear and set default option

        files.forEach(file => {
            let option = document.createElement('option');
            option.value = file;
            option.textContent = file;
            dropdown.appendChild(option);
        });
    });
}
</script>
<script>
function publishText() {
    let text = document.getElementById('content_area').value;
    let filename = document.getElementById('filename').value;
    let type = document.getElementById('typeDropdown').value;
    let password = document.getElementById('password').value; // Get the password value
    
    fetch('save.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'password=' + encodeURIComponent(password) + '&content=' + encodeURIComponent(text) + '&filename=' + encodeURIComponent(filename) + '&type=' + encodeURIComponent(type),
    }).then(response => response.text()).then(data => {
        alert(data);
        populateDropdown(); // Refresh dropdown list after saving
    });
}
</script></body>
</html>