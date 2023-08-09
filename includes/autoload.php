<?php
spl_autoload_register('myAutoLoader');

function myAutoLoader($className) {
    $path = __DIR__ . "/../classes/"; // Corrected path
    $extension = ".class.php";
    $fullPath = $path . $className . $extension;

    // echo "Trying to load: $fullPath<br>"; // Debugging output

    if (!file_exists($fullPath)) {
        return false;
    } 
 
    include_once $fullPath;
}

// spl_autoload_register(function ($className) {
//     $file = __DIR__ . '/classes/' . str_replace('\\', '/', $className) . '.class.php';
//     if (file_exists($file)) {
//         require_once $file;
//     }
// });