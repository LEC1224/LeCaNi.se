<?php
$directory = "text/";
$files = glob($directory . "*.txt");

if ($files) {
    $latestFile = end($files); // Assuming you want the latest saved file
    echo file_get_contents($latestFile);
} else {
    echo "No files found.";
}
?>