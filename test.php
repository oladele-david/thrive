<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $emailId = "tdboy51@gmail.com";
// $password = "1234567";

// require_once('includes/autoload.php');

// $account = new Account();
// $emailExists = $account->emailExists($emailId);


require_once('classes/Database.class.php');

try {
    $pdo = Database::connect();
    if ($pdo) {
        echo "Database connection successful!";
    } else {
        echo "Failed to connect to the database.";
    }
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage();
}
