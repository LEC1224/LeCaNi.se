<?php
$directory = "text/";
$files = glob($directory . "*.txt");

// Remove the directory path for display purposes
$files = array_map(function($file) {
    return basename($file);
}, $files);

echo json_encode($files);
?>