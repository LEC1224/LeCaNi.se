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

// Build the full path
$basePath = __DIR__ . '/img/' . $folder;

// Check if folder exists
if (!is_dir($basePath)) {
    echo json_encode(['error' => 'Folder not found', 'images' => []]);
    exit;
}

// Get all image files (support .jpg, .jpeg, .png)
$thumbnailImages = [];
$fullsizeImages = [];
$files = scandir($basePath);

foreach ($files as $file) {
    // Check for thumbnail versions (_480.jpg, _480.png, etc.)
    if (preg_match('/_480\.(jpg|jpeg|png)$/i', $file, $matches)) {
        // This is a thumbnail
        $baseName = preg_replace('/_480\.(jpg|jpeg|png)$/i', '', $file);
        $thumbnailImages[$baseName] = 'img/' . $folder . '/' . $file;
    } elseif (preg_match('/\.(jpg|jpeg|png)$/i', $file)) {
        // This is a full-size image (exclude _480 versions)
        $baseName = preg_replace('/\.(jpg|jpeg|png)$/i', '', $file);
        $fullsizeImages[$baseName] = 'img/' . $folder . '/' . $file;
    }
}

// Match thumbnails with full-size images and create pairs
$imagePairs = [];
foreach ($thumbnailImages as $baseName => $thumbPath) {
    if (isset($fullsizeImages[$baseName])) {
        $imagePairs[] = [
            'thumbnail' => $thumbPath,
            'fullsize' => $fullsizeImages[$baseName]
        ];
    } else {
        // If no full-size version exists, use thumbnail for both
        $imagePairs[] = [
            'thumbnail' => $thumbPath,
            'fullsize' => $thumbPath
        ];
    }
}

// Also include full-size images that don't have thumbnails
foreach ($fullsizeImages as $baseName => $fullPath) {
    if (!isset($thumbnailImages[$baseName])) {
        $imagePairs[] = [
            'thumbnail' => $fullPath,
            'fullsize' => $fullPath
        ];
    }
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

