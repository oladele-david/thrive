<?php
    require_once('includes/autoload.php');

    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    if (session_status() != PHP_SESSION_ACTIVE)
      session_start();
     $account = new Account();
     $deposit = new Deposit();

    $userInSession = $_SESSION['userInSession'];
    $firstName = $_SESSION['firstName'];
    $lastName = $_SESSION['lastName'];
    $amount = $_SESSION['amount'];
    $data_account = $account->getAccountById($userInSession);

    $curl = curl_init();
    $reference = isset($_GET['reference']) ? $_GET['reference'] : '';

    if(!$reference){
      die('No reference supplied');
    }

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: Bearer sk_test_57b7457af73f296d087c0ba31c959b8bb4925995",
        "cache-control: no-cache"
      ],
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    if($err){
        // there was an error contacting the Paystack API
        die('Curl returned error: ' . $err);
    }

    $tranx = json_decode($response);

    if(!$tranx->status){
        
        die('API returned error: ' . $tranx->message);
    }

    if('success' == $tranx->data->status){
        // transaction was successful...
        // please check other things like whether you already gave value for this ref
        // if the email matches the customer who owns the product etc
        // Give value
        $status = "completed";
        $createDeposit = $deposit->createDeposit($userInSession,$amount,$status);
        $updateBalance = $account->updateBalance($userInSession, $amount);

        // unset($_SESSION['amount']);
        // echo var_dump($tranx)."<br>";
        // echo  $createDeposit ."<br>". $updateBalance;
        echo "<script>window.location = 'index.php'</script>";
    }
