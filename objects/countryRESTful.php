<?php
    $curl_countries = curl_init();

    curl_setopt_array($curl_countries, array(
        CURLOPT_URL => $serverURL . "countries/",
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

    $response_countries = curl_exec($curl_countries);
    $err_countries = curl_error($curl_countries);

    curl_close($curl_countries);

    $data_countries = json_decode($response_countries, true);
?>