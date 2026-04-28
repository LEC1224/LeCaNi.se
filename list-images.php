<?php
header('Content-Type: application/json');

// Get the folder parameter
$folder = isset($_GET['folder']) ? $_GET['folder'] : '';

if (empty($folder)) {
    echo json_encode(['error' => 'No folder specified']);
    exit;
}

// Sanitize the folder path to prevent directory traversal
$folder = str_replace('..', '', $folder);
$folder = trim($folder, '/');

const THUMB_DIR = 'thumbs';

function imageBaseName($file) {
    return preg_replace('/\.(jpg|jpeg|png|gif|webp)$/i', '', $file);
}

function imagePath($folder, $file) {
    return 'img/' . $folder . '/' . $file;
}

function thumbPath($folder, $file) {
    return 'img/' . $folder . '/' . THUMB_DIR . '/' . $file;
}

// Build the full path
$basePath = __DIR__ . '/img/' . $folder;
$thumbPath = $basePath . '/' . THUMB_DIR;

// Check if folder exists
if (!is_dir($basePath)) {
    echo json_encode(['error' => 'Folder not found', 'images' => []]);
    exit;
}

// Get all original image files. Thumbnails live in img/<folder>/thumbs.
$thumbnailImages = [];
$fullsizeImages = [];
$files = scandir($basePath);

foreach ($files as $file) {
    if (is_dir($basePath . '/' . $file) || preg_match('/_480\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
        continue;
    }

    if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
        $baseName = imageBaseName($file);
        $fullsizeImages[$baseName] = imagePath($folder, $file);
    }
}

if (is_dir($thumbPath)) {
    foreach (scandir($thumbPath) as $file) {
        if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
            $baseName = imageBaseName($file);
            $thumbnailImages[$baseName] = thumbPath($folder, $file);
        }
    }
}

// Match thumbnails with full-size images and create pairs.
$imagePairs = [];
foreach ($fullsizeImages as $baseName => $fullPath) {
    $imagePairs[] = [
        'thumbnail' => $thumbnailImages[$baseName] ?? $fullPath,
        'fullsize' => $fullPath
    ];
}

// Sort by base name naturally (so 1.jpg, 2.jpg, 10.jpg instead of 1.jpg, 10.jpg, 2.jpg)
usort($imagePairs, function($a, $b) {
    $aName = basename($a['thumbnail']);
    $bName = basename($b['thumbnail']);
    // Use natural sort which handles numbers correctly
    return strnatcasecmp($aName, $bName);
});

echo json_encode(['images' => $imagePairs]);
?>

