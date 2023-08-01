<?php
    $curl_states_origin = curl_init();

    curl_setopt_array($curl_states_origin, array(
        CURLOPT_URL => $serverURL . "states/countryId/" . $cn,
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

    $response_states_origin = curl_exec($curl_states_origin);
    $err_states_origin = curl_error($curl_states_origin);

    curl_close($curl_states_origin);

    $data_states_origin = json_decode($response_states_origin, true);
?>

<?php
   $curl_states_residence = curl_init();

   curl_setopt_array($curl_states_residence, array(
        CURLOPT_URL => $serverURL . "states/countryId/" . $cr,
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

    $response_states_residence = curl_exec($curl_states_residence);
    $err_states_residence = curl_error($curl_states_residence);

    curl_close($curl_states_residence);

    if ($err_states_residence) {
        echo "cURL Error #:" . $err_states_residence;
    } else {
        $data_states_residence = json_decode($response_states_residence, true);
    }
?>

<?php
   $curl_states = curl_init();

   curl_setopt_array($curl_states, array(
        CURLOPT_URL => $serverURL . "states/",
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

    $response_states = curl_exec($curl_states);
    $err_states = curl_error($curl_states);

    curl_close($curl_states);

    if ($err_states) {
        echo "cURL Error #:" . $err_states;
    } else {
        $data_states = json_decode($response_states, true);
    }
?>