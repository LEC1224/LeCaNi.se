<!DOCTYPE html>
<html lang="en">
<?php include "head.php" ?>
<body>

<div id="fileList">
    <!-- Files will be listed here -->
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    populateFileList();
    
    // Check for query parameters on page load and display content if present
    let params = new URLSearchParams(window.location.search);
    if (params.has('n')) {
        let filename = params.get('n') + '.txt';
        let titleElement = $(`.file-title[data-filename="${filename}"]`);
        if (titleElement) {
            loadFileContent(filename, titleElement);
        }
    }
});

function populateFileList() {
    fetch('listFiles.php')
        .then(response => response.json())
        .then(data => {
            let fileListDiv = $('#fileList');
            fileListDiv.empty();  // Clear any existing content

            // Loop through each folder in the data
            for (let folder in data) {
                // Create a container div for the files of this folder
                let folderDiv = $('<div class="folder-section"></div>');

                // If the folder isn't the main directory, create a headline for it
                if (folder !== 'Main') {
                    let folderTitle = $('<h2></h2>').text(folder);
                    folderDiv.append(folderTitle);
                }

                // Loop through each file in the current folder
                data[folder].forEach(file => {
                    // Format the file name for display
                    let formattedTitle = formatTitle(file);

                    // Create a div for this file
                    let fileDiv = $('<div class="file-title"></div>');
                    fileDiv.text(formattedTitle);
                    // ... (rest of the logic remains unchanged)

                    // Append the fileDiv to the folderDiv
                    folderDiv.append(fileDiv);
                });

                // Append the folderDiv to the main fileListDiv
                fileListDiv.append(folderDiv);
            }
        });
}

function loadFileContent(filename, element) {
    fetch('loadFile.php?filename=' + encodeURIComponent(filename)).then(response => response.text()).then(data => {
        let formattedContent = data.replace(/\n/g, '<br />'); // Convert line breaks to HTML <br />
        let contentDiv = $('<div class="file-content"></div>');
        contentDiv.html(formattedContent); // Using .html() instead of .text() to render the <br />
        element.after(contentDiv);
        contentDiv.slideDown();
    });
}

function formatTitle(filename) {
    // Remove .txt extension, replace underscores with spaces, and capitalize each word
    return filename.replace('.txt', '').split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}
</script>

</body>
</html>