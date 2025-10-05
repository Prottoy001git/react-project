<?php
function upload($file, $path = "uploads", $name = "", $maxKb = 500) {
    // return "file working";
    $allowed = [
        'jpg','jpeg','png','gif','webp', // images
        'pdf',                           // documents
        'mp3','wav','ogg','aac','m4a',   // audio
        'mp4','avi','mov','wmv','mkv','webm' // video
    ];

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['error' => 'Not a valid uploaded file'];
    }

    $path = rtrim($path, '/');
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    $sizeKb = $file['size'] / 1024;
    if ($sizeKb > $maxKb) {
        return ['error' => 'File too large'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); // pdf,jpg,png
    if (!in_array($ext, $allowed)) {
        return ['error' => 'Invalid file type'];
    }

    // Use datetime if no name provided
    if ($name !== "") {
        $base = slugify($name);
    } else {
        $base = date('Ymd-His'); // e.g. 20250801-231045
    }

    // replace (overwrite) the existing file if it already exists
    $finalName = $base . '.' . $ext;
    $fullPath = "{$path}/{$finalName}";

    // If file exists, add a numeric suffix to avoid overwrite
    // $counter = 1;
    // while (file_exists($fullPath)) {
    //     $finalName = $base . '_' . $counter . '.' . $ext;
    //     $fullPath = "{$path}/{$finalName}";
    //     $counter++;
    // }

    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
        return ['error' => 'Failed to move uploaded file'];
    }

    return ['success' => $fullPath];
}

function slugify($text) {
    $text = preg_replace('/[^A-Za-z0-9]+/', '-', $text);
    return strtolower(trim($text, '-')) ?: date('Ymd-His');
}

?>