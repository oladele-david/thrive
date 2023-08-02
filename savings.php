<?php
require_once('includes/autoload.php');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

$account = new Account();
$savings = new Savings();

$userInSession = $_SESSION['userInSession'];
$lastName = $_SESSION['lastName'];
$firstName = $_SESSION['firstName'];
$emailId = $_SESSION['emailId'];
$phoneNo = $_SESSION['phoneNo'];

if (empty($_SESSION['userInSession'])) {
    header("Location: signin.php");
    die("Redirecting to signin.php");
}

$pageTitle = "Create Savings";
$data_account = $account->getAccountById($userInSession);


if (isset($_POST['amount'], $_POST['savingInterval'])) {
    $accountId = $_SESSION['userInSession'];
    $amount = $_POST['amount'];
    $savingInterval = $_POST['savingInterval'];
    $startDate = date("Y-m-d");
    $minimumAmount = $_POST['minimumAmount'];

    $duration = $_POST['duration'];
    $special = isset($_POST['special']) ? true : false;

    $createSavingResult = $savings->createSaving($accountId, $amount, $minimumAmount, $savingInterval, $startDate, $duration, $special);
    ob_clean();
    echo $createSavingResult;
    exit();
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
            <h4>Create Savings</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Savings</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Create Savings</a></li>
            </ol>
        </div>

        <!-- row -->
        <?php include('alerts.php') ?>
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="profile-tab">
                            <div class="custom-tab-1">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item"><a href="#create-saving" data-toggle="tab" class="nav-link active show">Create Savings <small>Balance: ₦ <?php echo number_format($data_account['account_balance'], 2) ?></small></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="create-saving" class="tab-pane fade active show">
                                        <div class="pt-3">
                                            <div class="settings-form">
                                                <form method="post" id="create-saving-form" onsubmit="createSavings(); return false;">
                                                    <div class="form-group">
                                                        <label for="amount">Amount</label>
                                                        <input type="number" name="amount" id="amount" class="form-control" min="100" required>
                                                        <div class="invalid-feedback animated fadeInUp" style="display: block;">Starting Amount</div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="minimumAmount">Minimum Amount</label>
                                                        <input type="number" name="minimumAmount" id="minimumAmount" class="form-control" required>
                                                        <div class="invalid-feedback  animated fadeInUp" style="display: block;">Specify the minimum amount expected</div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="saving-interval">Saving Interval</label>
                                                        <select name="savingInterval" id="saving-interval" class="form-control" required>
                                                            <option value="daily">Daily</option>
                                                            <option value="weekly">Weekly</option>
                                                            <option value="monthly">Monthly</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="duration">Saving Duration</label>
                                                        <select name="duration" id="duration" class="form-control" required>
                                                            <option value="3">3 Months</option>
                                                            <option value="6">6 Months</option>
                                                            <option value="12">12 Months</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-check mb-4">
                                                            <input type="checkbox" class="form-check-input" id="special" name="special" value="true" >
                                                            <label class="form-check-label" for="special">Special Savings?</label>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary" id="submit-button">
                                                        <span id="button_save">Create Savings</span>
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
    function createSavings() {
        // Show the Font Awesome spinner on the button
        $("#button_save").html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        var formData = $("#create-saving-form").serialize();
        // console.log(formData);
        $.ajax({
            type: 'POST',
            url: 'savings.php',
            data: formData,
            dataType: 'json',
            success: function(response) {

                $("#button_save").html('Create Savings');

                // Display the SweetAlert based on the response
                swal({
                    type: response.response,
                    title: response.title,
                    text: response.msg,
                    confirmButtonText: 'Continue'
                });
                // $("#create-saving-form").reset();
                document.getElementById("create-saving-form").reset();
            },
            error: function(response) {
                // Hide the Font Awesome spinner on the button
                $("#button_save").html('Create Savings');

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