<?php
    $curl_banks = curl_init();
    
    curl_setopt_array($curl_banks, array(
        CURLOPT_URL => $serverURL  . "banks/",
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

    $response_banks = curl_exec($curl_banks);
    $err_banks = curl_error($curl_banks);

    curl_close($curl_banks);

    $data_banks = json_decode($response_banks, true);
?>

<?php
    $curl_bank = curl_init();
    if($bankId != NULL) {
        curl_setopt_array($curl_bank, array(
            CURLOPT_URL => $serverURL  . "banks/id/" . $bankId,
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

        $response_bank = curl_exec($curl_bank);
        $err_bank = curl_error($curl_bank);

        curl_close($curl_bank);

        $data_bank = json_decode($response_bank, true);
    }
?>

<!-- ####### Lists Bank accounts based on clientAccessID ######### -->

