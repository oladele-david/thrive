<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('includes/autoload.php');

$accountId = "43878086369";
$planId = "10";

// require_once('includes/autoload.php');

$userLoan = new UserLoan();

$register_loan = $userLoan->createLoan($accountId, $planId);

if ($register_loan) {

    $amount = $data_withdrawal['amount'];
    ob_clean();
    
    echo json_encode($register_loan);
    exit();
} else {
    ob_clean();
    echo json_encode($register_loan);

    exit();
}

// $register_loan = $userLoan->createLoan($accountId, $planId);

// if ($register_loan) {

//     $amount = $data_withdrawal['amount'];
//     ob_clean();
//     echo $register_loan;
//     exit();
// } else {
//     ob_clean();
//     echo $register_loan;
//     exit();
// }

// echo $register_loan;

// try {
//     $pdo = Database::connect();
//     if ($pdo) {
//         echo "Database connection successful!";
//     } else {
//         echo "Failed to connect to the database.";
//     }
// } catch (PDOException $e) {
//     echo "Database connection error: " . >getMessage();
// }
