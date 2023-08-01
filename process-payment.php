<?php
 require_once('includes/autoload.php');

 if (session_status() != PHP_SESSION_ACTIVE)
   session_start();
  $account = new Account();
 
      $userInSession = $_SESSION['userInSession'];
      $firstName = $_SESSION['firstName'];
      $lastName = $_SESSION['lastName'];

      $data_account = $account->getAccountById($userInSession);

  if ($data_account['email_id'] == "" || $data_account['email_id'] == NULL) {
      header("Location: profile.php");
    } else {
   
    $emailId= $data_account['email_id'];

    $amount = $_SESSION['amount'];
    $amount = $amount * 100;

    $callback_url = 'https://thrive.vividdavid.com/confirmPayment.php'; 

      $curl = curl_init();

      //$email = "ayodele.peters@opensdigital.com";
      //$amount = 130000;  //the amount in kobo. This value is actually NGN 300

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
          'amount'=>$amount,
          'email'=>$emailId,
          'callback_url' => $callback_url
        ]),
        CURLOPT_HTTPHEADER => [
          "authorization: Bearer sk_test_57b7457af73f296d087c0ba31c959b8bb4925995", //replace this with your own test key
          "content-type: application/json",
          "cache-control: no-cache"
        ],
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      if($err){
        // there was an error contacting the Paystack API
        die('Curl returned error: ' . $err);
      }

      $tranx = json_decode($response, true);

      if(!$tranx->status){
        // there was an error from the API
        print_r('API returned error: ' . $tranx['message']);
      }

      // comment out this line if you want to redirect the user to the payment page
      //print_r($tranx);


      // redirect to page so User can pay
      // uncomment this line to allow the user redirect to the payment page
      header('Location: ' . $tranx['data']['authorization_url']);
         
    }
?>

