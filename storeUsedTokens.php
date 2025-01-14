<?php
header('Content-Type: application/json');

// File path to the JSON file
$jsonFile = 'usedToken.json';

// Check if the file exists
if (!file_exists($jsonFile)) {
    echo json_encode(["error" => "The file usedToken.json does not exist."]);
    exit;
}

// Read the file contents
$jsonData = file_get_contents($jsonFile);
if ($jsonData === false) {
    echo json_encode(["error" => "Unable to read the JSON file."]);
    exit;
}

// Decode the JSON to check its validity
$data = json_decode($jsonData, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON format in usedToken.json."]);
    exit;
}

// Return the JSON data
echo json_encode($data);
?>
