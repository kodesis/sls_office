<?php

$filePath = 'vendor/mikey179/vfsstream/src/main/php/org/bovigo/vfs/vfsStream.php';

// Check if the file exists
if (file_exists($filePath)) {

    // Read the file's content into a string
    $fileContents = file_get_contents($filePath);

    // Perform the replacement: change 'name{0}' to 'name[0]'
    $fileContents = str_replace('name{0}', 'name[0]', $fileContents);

    // Save the modified content back to the file
    file_put_contents($filePath, $fileContents);

    echo "Replacement done successfully!";
} else {
    // If the file doesn't exist, output an error message
    echo "Error: File not found at " . $filePath;
}
