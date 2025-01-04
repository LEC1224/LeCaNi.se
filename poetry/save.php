<?php

// Check the provided password
if (!isset($_POST['password']) || $_POST['password'] !== 'GPT4ever') {
    echo 'Incorrect password!';
    exit;
}

$directory = "text/";
$backupDirectory = "text/old/";
$content = $_POST['content'];
$filename = $directory . $_POST['filename'] . ".txt";

// Check if file already exists
if (file_exists($filename)) {
    // If it does, move the existing file to the backup directory with timestamp appended
    $backupFilename = $_POST['filename'] . "_" . date("Y-m-d_H-i-s") . ".txt"; // Format: originalName_YYYY-MM-DD_HH-MM-SS.txt
    if (!is_dir($backupDirectory)) {
        mkdir($backupDirectory, 0777, true); // Create backup directory if it doesn't exist
    }
    rename($filename, $backupDirectory . $backupFilename);
}

// Save the new content
if (file_put_contents($filename, $content)) {
    echo "File saved successfully!";
} else {
    echo "Failed to save the file.";
}

// Check if type parameter is set
if (isset($_POST['type'])) {
    $type = $_POST['type'];
	$title = ucwords(str_replace('_', ' ', $_POST['filename'])); // Convert filename to title format with capitalized words

    $new_entry = '<li><a href="?n=' . str_replace(' ', '_', $title) . '">' . $title . '</a></li>';

    if ($type == 'poem') {
        $file_to_modify = 'listPoems.php';
    } else if ($type == 'lyrics') {
        $file_to_modify = 'listLyrics.php';
	} else if ($type == 'upoems') {
        $file_to_modify = 'listUpoems.php';
    } else if ($type == 'ulyrics') {
        $file_to_modify = 'listUlyrics.php';
	} else if ($type == 'parodies') {
        $file_to_modify = 'listParodies.php';
	} else if ($type == 'tributes') {
        $file_to_modify = 'listTributes.php';
	}else if ($type == 'essays') {
        $file_to_modify = 'listEssays.php';
	}
    if (isset($file_to_modify)) {
        // Load the file content
        $content = file_get_contents($file_to_modify);

        // Insert the new entry before the closing </ul> tag
        $content = str_replace('</ul>', $new_entry . "
</ul>", $content);

        // Save the modified content back to the file
        file_put_contents($file_to_modify, $content);
    }
}

?>