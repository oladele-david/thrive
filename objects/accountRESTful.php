<?php
    $curl_accounts = curl_init();

    curl_setopt_array($curl_accounts, array(
        CURLOPT_URL => $serverURL . "accounts/" ,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer " . $token
        ),
    ));

    $response_accounts = curl_exec($curl_accounts);
    $err_accounts = curl_error($curl_accounts); curl_close($curl_accounts);

    //echo $response;
    $data_accounts = json_decode($response_accounts, true);
?>


<!-- ######## account RESTful by ID ######## -->

<?php
    $curl_account = curl_init();

    curl_setopt_array($curl_account, array(
        CURLOPT_URL => $serverURL . "accounts/id/" . $userInSession,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer " . $token
        ),
    ));

    $response_account = curl_exec($curl_account);
    $err_account = curl_error($curl_account); curl_close($curl_account);

    //echo $response;
    $data_account = json_decode($response_account, true);
?>