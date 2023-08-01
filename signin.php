<?php
require_once('includes/autoload.php');

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();
$account = new Account();

$phoneNo = $_POST['phoneNo'];
$password = $_POST['password'];

if (isset($_POST['login'])) {
    // Perform account login
    $loginResult = $account->accountLogin($phoneNo, $password);

    // Decode the JSON-encoded result into an associative array
    $resultArray = json_decode($loginResult, true);

    // Access the values in the array
    $response = $resultArray['response'];
    $data_account_access = $resultArray['account'];

    if ($response === "success") {
        // Account login successful
        $_SESSION['userInSession'] = $data_account_access['id'];
        $_SESSION['lastName'] = $data_account_access['last_name'];
        $_SESSION['firstName'] = $data_account_access['first_name'];
        $_SESSION['emailId'] = $data_account_access['email_id'];
        $_SESSION['phoneNo'] = $data_account_access['phone_no'];
        ob_clean();
        echo $loginResult;
        exit;
    } else {
        // Account login failed
        ob_clean();
        echo $loginResult;
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-100">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ThrivePay | Account Access</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <link href="vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="container-fluid">
                    <br>
                    <?php include('alerts.php') ?>
                </div>
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="text-center mb-3">
                                        <a href="."><img src="images/logo-full.png" width="150px" alt=""></a>
                                    </div>
                                    <h4 class="text-center mb-4 text-white">Sign in your account</h4>
                                    <form method="POST" onsubmit="return login()">
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Phone No</strong></label>
                                            <input type="number" id="phoneNo" name="textPhoneNo" class="form-control" maxlength="11" placeholder="Phone No">
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Password</strong></label>
                                            <input type="password" id="password" name="textPassword" class="form-control" placeholder="Password">
                                        </div>
                                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                            <div class="form-group">
                                                <div class="custom-control custom-checkbox ml-1 text-white">
                                                    <input type="checkbox" class="custom-control-input" id="basic_checkbox_1">
                                                    <label class="custom-control-label" for="basic_checkbox_1">Remember my preference</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <a class="text-white" href="page-forgot-password.html">Forgot Password?</a>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" id="buttonSignin" class="btn bg-white text-primary btn-block">Sign Me In</button>
                                            <button type="submit" id="loading_spinner" class="btn bg-white text-primary btn-block" style="display: none;"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Signing In</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <p class="text-white">Don't have an account? <a class="text-white" href="signup.php">Sign up</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="vendor/global/global.min.js"></script>
    <script src="vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="js/custom.min.js"></script>
    <script src="js/deznav-init.js"></script>
    <script src="vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        function login() {
            let phoneNo = $("#phoneNo").val();
            let password = $("#password").val();

            if (phoneNo != "" && password != "") {

                $("#buttonSignin").css({
                    "display": "none"
                });
                $("#loading_spinner").css({
                    "display": "block"
                });
                $.ajax({
                    type: 'post',
                    url: 'signin.php',
                    data: {
                        login: "login",
                        phoneNo: phoneNo,
                        password: password
                    },
                    success: function(data) {

                        var jsonData = JSON.parse(data)

                        if (jsonData.response == "success") {
                            $("#loading_spinner").css({
                                "display": "none"
                            });

                            swal({
                                type: jsonData.response,
                                title: jsonData.title,
                                text: jsonData.msg,
                                confirmButtonText: 'Continue',
                            }).then(function(result) {
                                if (true) {
                                    window.location = "index.php";
                                }
                            })
                        } else {
                            $("#loading_spinner").css({
                                "display": "none"
                            });
                            $("#buttonSignin").css({
                                "display": "block"
                            });

                            swal({
                                type: jsonData.response,
                                title: jsonData.title,
                                text: jsonData.msg,
                                confirmButtonText: 'Try Again'
                            });
                            // alert("Wrong Details" + response);
                            // console.log(response + " - " + phoneNo + " - " + password);
                        }
                    }
                });

            } else {
                swal({
                    type: "error",
                    title: "Oops!",
                    text: "Please Fill All Details",
                    confirmButtonText: "Try Again"
                });
            }
            return false;
            //console.log(response + " - " + phoneNo + " - " + password);
        }
    </script>

</body>


</html>