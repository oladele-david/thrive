<?php
    // require_once('utilities/appServer.php');
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    require_once('includes/autoload.php');

    if(session_status() != PHP_SESSION_ACTIVE)
    session_start();
    $account = new Account();


    $randomID = mt_rand(10000000000, 99999999999);

    $lastName = ucfirst($_POST['lastName']);
    $firstName = ucfirst($_POST['firstName']);
    $phoneNo = $_POST['phoneNo'];
    $emailId = strtolower($_POST['emailId']);
    $password = $_POST['password'];

    // A sample PHP Script to POST data using cURL
    // Data in JSON format
    
    if(isset($_POST['create'])) {

        // create a new investment
		$createAccountResult = $account->createAccount($randomID, $phoneNo, $phoneNo, $lastName, $firstName, $emailId, $password);
    
        $response = json_decode($createAccountResult);   
        if ($response->response =="error") {
            ob_clean();
            echo json_encode($createAccountResult);
            exit;
        }
        
        

        $_SESSION['userInSession'] = $randomID;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['firstName'] = $firstName;
        $_SESSION['emailId'] = $emailId;
        $_SESSION['phoneNo'] = $phoneNo;

        ob_clean();
        echo $createAccountResult;              
        exit;
    }

    if (isset($_POST['emailCheck'])) {

        $emailExists = $account->isEmailExists($emailId);

        if  ($emailExists) {
            ob_clean();
            echo "exist";
            exit;
        } else {
            ob_clean();
            echo "notExist";
            exit;
        }
        exit;
    }

    if (isset($_POST['phoneCheck'])) {
        $phoneNoExists = $account->isPhoneNoExists($phoneNo);

        if  ($phoneNoExists) {
            ob_clean();
            echo "exist";
            exit;
        } else {
            ob_clean();
            echo "notExist";
            exit;
        }
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en" class="h-100">


<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>ThrivePay | Sign up</title>
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
                                    <h4 class="text-center mb-4 text-white">Create your account</h4>
                                    <form method="POST" onsubmit="return createAccount()">
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Last Name</strong></label>
                                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>First Name</strong></label>
                                            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Phone Number</strong></label>
                                            <input type="text" class="form-control" id="phoneNo" name="phoneNo" placeholder="Phone Number" maxlength="11" onchange="return checkPhoneNo();" required>
                                            <div id="val-phone-error" class="invalid-feedback animated fadeInUp" style="display: none;">Phone No Exists Already, Try Another</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Email</strong></label>
                                            <input type="email" id="emailId" name="textEmailId" class="form-control" placeholder="hello@example.com" onchange="return checkEmail();" required>
                                            <div id="val-email-error" class="invalid-feedback animated fadeInUp" style="display: none;">Email Exists Already, Try Another</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Password</strong></label>
                                            <input type="password" id="password" name="password" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1 text-white"><strong>Confirm Password</strong></label>
                                            <input type="password" id="confirmPassword" name="connfirmPassword" class="form-control" required>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" id="buttonSignup" class="btn bg-white text-primary btn-block">Sign me up</button>
                                            <button type="submit" id="loading_spinner" class="btn bg-white text-primary btn-block" style="display: none;"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Creating Account</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <p class="text-white">Already have an account? <a class="text-white" href="signin.php">Sign in</a></p>
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

<!-- signup script -->
<script>
    function checkEmail(){
        let emailId = $('#emailId').val();
        
        // console.log('== EmailId= '+emailId);  
        
        $.ajax
        ({
            type:'post',
            url:'signup.php',
            data:{
                emailCheck:"emailCheck",
                emailId:emailId
            },
            success:function(response) {
                if(response =="exist"){
                    $("#val-email-error").css({"display":"block"});
                    $("#buttonSignup").prop("disabled",true);
                    // console.log('This is==> '+ response + '== EmailId= '+emailId);  
                    $('#emailId').val("");              
                }
                else {
                    $("#val-email-error").css({"display":"none"});
                    $("#buttonSignup").prop("disabled",false);
                    // console.log('This is==> '+ response + '== EmailId= '+emailId);  
                }
            }
        });
    }

    function checkPhoneNo(){
        let phoneNo = $('#phoneNo').val();
                
        $.ajax
        ({
            type:'post',
            url:'signup.php',
            data:{
                phoneCheck:"phoneCheck",
                phoneNo:phoneNo
            },
            success:function(response) {
                if(response =="exist"){
                    $("#val-phone-error").css({"display":"block"});
                    $("#buttonSignup").prop("disabled",true);
                    $("#phoneNo").val('');
                    // console.log('This is==> '+ JSON.stringify(response, null, "  "));
                }
                else {
                    $("#val-phone-error").css({"display":"none"});
                    $("#buttonSignup").prop("disabled",false);
                    // console.log('This is==> '+ JSON.stringify(response, null, "  "));
                
                }
            }
        });

    }

    // Sign Up script
    function createAccount(){
                
        //let accountNumber = $('#accountNumber').val();
        let lastName = $('#lastName').val();
        let firstName = $('#firstName').val();
        let phoneNo = $('#phoneNo').val();
        let emailId = $('#emailId').val();
        let password = $('#password').val();
        let confirmPassword = $('#confirmPassword').val();
       
        if (password == confirmPassword) {

            if(emailId!="" && phoneNo!="" && password!="") {
                
                $("#buttonSignup").css({"display":"none"});
                $("#loading_spinner").css({"display":"block"});
                $.ajax
                ({
                    type:'post',
                    url:'signup.php',
                    data:{
                        create:"create",
                        lastName:lastName,
                        firstName:firstName,
                        phoneNo:phoneNo,
                        emailId:emailId,
                        password:password
                    },
                    success:function(data) {
                        // console.log('== EmailId= '+data);  

                        var jsonData = JSON.parse(data)

                        if (jsonData.response == "success") {
                            $("#loading_spinner").css({"display":"none"});

                            swal({
                                
                                title: 'Account Created Successfully!',
                                text: "Hello "  + lastName + " " + firstName + ", welcome to Thrive",
                                confirmButtonText: 'Continue',
                                type: 'success',
                            }).then(function (result) {
                                if (true) {
                                window.location = "index.php";
                                }
                            })
                            // window.location.href="";
                        }
                        else {
                            $("#loading_spinner").css({"display":"none"});
                            $("#buttonSignup").css({"display":"block"});

                            swal({
                                type: 'error',
                                title: 'Declined!',
                                text: jsonData.msg,
                                confirmButtonText: 'Try Again'
                            });
                            // alert("Wrong Details" + response);
                            //console.log(response + " - " + clientAccessID + " - " + phoneNo + " - " + wardId + " - " + password);
                        }
                    }
                });
                //console.log("Values entered are == " + lastName + " -- " + firstName + " -- " + emailId + " -- " + password + " -- " + confirmPassword + " -- " + phoneNo + " -- " );
                return false;

            }
            else
            {
                swal({
                type: 'error',
                title: 'Halt!',
                text: 'Please Fill All The Details'
                })
                //alert("Please Fill All The Details");
            }

            return false;

            //console.log("Values entered are == " + customerName + " -- " + emailID + " -- " + password + " -- " + confirmPassword + " -- " + phoneNo + " -- " + stateOfResidenceId + " -- " +  bankId + " -- " +  bankId + " -- " +  bankAccountName + " -- " +  bankAccountNo);
            //swal("Good job!", "You clicked the button! Welcome " + customerName + " -- " + emailID, "success");

        } else {
            //swal("Hey, Good job !!", "You clicked the button !!", "success")
            swal({
                type: "error",
                title: "Oh No!!",
                text: "Your passwords didn't match " + lastName + " " + firstName ,
                confirmButtonText: "Try Again"
            });
            //console.log("wrong password -- Values entered are == " + lastName + " -- " + firstName + " -- " + emailId + " -- " + password + " -- " + confirmPassword + " -- " + phoneNo + " -- " );
        return false;
        }
        //console.log("Values entered are == " + lastName + " -- " + firstName + " -- " + emailId + " -- " + password + " -- " + confirmPassword + " -- " + phoneNo + " -- " );
        return false;
    }
</script>

</body>

</html>