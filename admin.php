<?php
// require_once('utilities/dbconnection.php');
include("includes/autoload.php");
$admin = new Admin();
if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

$username = $_POST['username'];
$password = base64_encode($_POST['password']);
$result = $admin->listAdmins();
$admins = $result['admins'];

if ($_POST["action"] == "login") {
    $login_result =  $admin->login($username, $password);
    echo json_encode($login_result);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="h-100">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ThrivePay | Admin Access</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <link href="vendor/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

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
                                    <h4 class="text-center mb-4 text-white">Admin Login</h4>
                                    <form method="POST" class="account-login">
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Username</strong></label>
                                            <select name="username" class="form-control" required>
                                                <option value=""></option>
                                                <?php
                                                foreach ($admins as $admin) {
                                                ?>
                                                    <option value="<?php echo $admin['username'] ?>"><?php echo $admin['full_name'] ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Password</strong></label>
                                            <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                                        </div>
                                        <div class="form-row d-flex justify-content-between mt-4 mb-2">
                                            <!-- <div class="form-group">
                                                <div class="custom-control custom-checkbox ml-1 text-white">
                                                    <input type="checkbox" class="custom-control-input" id="basic_checkbox_1">
                                                    <label class="custom-control-label" for="basic_checkbox_1">Remember my preference</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <a class="text-white" href="page-forgot-password.html">Forgot Password?</a>
                                            </div> -->
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" id="buttonSave" class="btn bg-white text-primary btn-block buttonSave"> Authorize Access </button>
                                        </div>
                                    </form>

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
        $(".account-login").on('submit', (function(e) {
            e.preventDefault();
            let form_data = $(this).serialize();
            // let randomID = rand(1000, 9999);
            form_data += '&action=login';

            // let password = $(this).find('.password').val();
            // let confirmPassword = $(this).find('.confirmPassword').val();

            let button_save = $('#buttonSave');
            button_save.html('Authorizing  <i class="fa fa-spinner fa-spin"></i>');
            button_save.prop("disabled", true);


            $.ajax({
                url: "admin.php",
                type: "POST",
                data: form_data,
                success: function(response) {
                    // console.log(form_data + " - " + response);

                    var jsonData = JSON.parse(response)

                    if (jsonData.response == "success") {


                        swal({
                            type: jsonData.response,
                            title: jsonData.title,
                            text: jsonData.msg,
                            confirmButtonText: 'Continue'
                        }).then(function(result) {
                            if (true) {
                                window.location = "controls/dashboard.php";
                            }
                        });

                    } else {
                        button_save.html('Authorize Access');
                        button_save.prop("disabled", false);
                        swal({
                            type: jsonData.response,
                            title: jsonData.title,
                            text: jsonData.msg,
                            confirmButtonText: 'Try Again'
                        });
                    }

                    // console.log(form_data + " - " + data);

                },
                error: function() {}
            });



            // console.log(form_data  + " - " + password);
        }));
    </script>


</body>


</html>