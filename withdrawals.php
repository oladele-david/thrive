<?php
require_once('includes/autoload.php');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

$account = new Account();
$withdrawals = new Withdrawal();

$userInSession = $_SESSION['userInSession'];
$lastName = $_SESSION['lastName'];
$firstName = $_SESSION['firstName'];
$emailId = $_SESSION['emailId'];
$phoneNo = $_SESSION['phoneNo'];

if (empty($_SESSION['userInSession'])) {
    header("Location: signin.php");
    die("Redirecting to signin.php");
}

$pageTitle = "Request Withdrawals";
$data_account = $account->getAccountById($userInSession);


if (isset($_POST['amount'])) {
    $accountId = $_SESSION['userInSession'];
    $amount = $_POST['amount'];
    $pin = $_POST['pin'];
    $withdrawalDate = date("Y-m-d");

    $pinResult = $account->validatePin($accountId, $pin);
    if (!$pinResult['valid']) {
        if ($pinResult['locked']) {
            // The account is locked, show a message indicating that the user needs to wait before trying again
            ob_clean();
            $value_return = array("response" => "error", "title" => "Account Locked", "msg" => "Too many incorrect PIN attempts. Please try again later.");
            echo json_encode($value_return);
            exit();
        } else {
            // The PIN is invalid, show a message indicating that the PIN is incorrect
            ob_clean();
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Invalid Pin.");
            echo json_encode($value_return);
            exit();
        }
    } else {
        $createWithdrawalResult = $withdrawals->createWithdrawal($accountId, $amount, $withdrawalDate);
        ob_clean();
        echo $createWithdrawalResult;
        exit();
    }
}
?>

<?php include('includes/header.php') ?>
<?php include('includes/sidebar.php') ?>
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
<!--**********************************
    Content body start
***********************************-->
<div class="content-body">
    <div class="container-fluid">
        <div class="page-titles">
            <h4>Request Withdrawals</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Withdrawals</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Request Withdrawals</a></li>
            </ol>
        </div>

        <!-- row -->
        <?php include('./includes/alerts.php') ?>
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="profile-tab">
                            <div class="custom-tab-1">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a href="#create-withdrawal" data-toggle="tab" class="nav-link active show">Request Withdrawals</a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="create-withdrawal" class="tab-pane fade active show">
                                        <div class="pt-3">
                                            <div class="settings-form">
                                                <form method="post" id="create-withdrawal-form" onsubmit="createWithdrawals(); return false;" >
                                                    <div class="form-group">
                                                        <label for="amount">Amount</label>
                                                        <input type="number" name="amount" id="amount" class="form-control" min="100"  autocomplete="off" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="pin">PIN</label>
                                                        <input type="password" name="pin" id="pin" class="form-control" maxlength="4"  autocomplete="off" required>
                                                        <div class="invalid-feedback  animated fadeInUp" style="display: block;">* Please enter your 4-digit secret pin</div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary" id="submit-button">
                                                        <span id="button_save">Request Withdrawal</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
    Content body end
***********************************-->

<?php include('includes/footer.php') ?>

</div>
<!--**********************************
        Main wrapper end
    ***********************************-->

<!--**********************************
        Scripts
    ***********************************-->

<script src="js/ajax-scripts.js"></script>


<!-- Chart piety plugin files -->
<script src="vendor/peity/jquery.peity.min.js"></script>

<!-- Apex Chart -->
<script src="vendor/apexchart/apexchart.js"></script>

<!-- Dashboard 1 -->
<script src="js/dashboard/my-wallet.js"></script>

<!-- Datatable -->
<script src=" vendor/datatables/js/jquery.dataTables.min.js"></script>

<script>
    function createWithdrawals() {
        // Show the Font Awesome spinner on the button
        $("#button_save").html('<i class="fa fa-spinner fa-spin"></i> Requesting...');

        var formData = $("#create-withdrawal-form").serialize();
        // console.log(formData);
        $.ajax({
            type: 'POST',
            url: 'withdrawals.php',
            data: formData,
            dataType: 'json',
            success: function(response) {

                $("#button_save").html('Request Withdrawal');

                // Display the SweetAlert based on the response
                swal({
                    type: response.response,
                    title: response.title,
                    text: response.msg,
                    confirmButtonText: 'Continue'
                });
                // $("#create-withdrawal-form").reset();
                document.getElementById("create-withdrawal-form").reset();
            },
            error: function(response) {
                // Hide the Font Awesome spinner on the button
                $("#button_save").html('Request Withdrawal');

                console.log(response);
                // Display an error SweetAlert
                swal({
                    type: 'error',
                    title: 'Oops!',
                    text: 'An error occurred. Please try again.',
                    confirmButtonText: 'Try Again'
                });
            }
        });
    }
</script>
</body>

</html>