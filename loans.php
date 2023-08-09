<?php
require_once('includes/autoload.php');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

$account = new Account();
$loanPlan = new LoanPlan();
$userLoan = new UserLoan();

$userInSession = $_SESSION['userInSession'];
$lastName = $_SESSION['lastName'];
$firstName = $_SESSION['firstName'];
$emailId = $_SESSION['emailId'];
$phoneNo = $_SESSION['phoneNo'];

if (empty($_SESSION['userInSession'])) {
    header("Location: signin.php");
    die("Redirecting to signin.php");
}

$pageTitle = "Available Loans";
$data_account = $account->getAccountById($userInSession);
$data_plans = $loanPlan->getAllLoanPlans();

// ! PHP code to handle the cancel action
if (isset($_POST['action']) && $_POST['action'] === 'apply') {
    $planId = $_POST['planId'];
    $pin = $_POST['pin']; // Get the PIN value from the AJAX request
    // Validate the PIN here (you can use your existing validation code)
    $pinResult = $account->validatePin($userInSession, $pin);
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

        $register_loan = $userLoan->createLoan($userInSession, $planId);

        if ($register_loan) {
            ob_clean();            
            echo $register_loan;
            exit();
        } else {
            ob_clean();
            echo $register_loan;
            exit();
        }
    }
}

?>

<?php include('includes/header.php') ?>
<?php include('includes/sidebar.php') ?>

<!--**********************************
    Content body start
***********************************-->
<div class="content-body">
    <div class="container-fluid">
        <div class="page-titles">
            <h4>Create Loans</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Loans</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Available Loans</a></li>
            </ol>
        </div>

        <!-- row -->
        <?php include('alerts.php') ?>
        <div class="row justify-content-center">

            <?php
            foreach ($data_plans as $data_plan) :
            ?>

                <div class="col-xl-4 col-lg-12 col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <h2 class="card-title"><?php echo ucwords($data_plan['name']) ?></h2>
                        </div>
                        <div class="card-body pb-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex px-0 justify-content-between">
                                    <strong>Duration</strong>
                                    <span class="mb-0"><?php echo ucwords($data_plan['duration']) ?> Months</span>
                                </li>
                                <li class="list-group-item d-flex px-0 justify-content-between">
                                    <strong>Interest (%)</strong>
                                    <span class="mb-0"><?php echo ucwords($data_plan['interest_rate']) ?></span>
                                </li>

                                <?php if ($data_plan['type'] == "normal") : ?>
                                    <li class="list-group-item d-flex px-0 justify-content-between">
                                        <strong>Amount</strong>
                                        <span class="mb-0">Double of Active Savings</span>
                                    </li>
                                <?php elseif ($data_plan['type'] == "electronic") : ?>
                                    <li class="list-group-item d-flex px-0 justify-content-between">
                                        <strong>Amount</strong>
                                        <span class="mb-0">100% of Active Savings</span>
                                    </li>
                                <?php elseif ($data_plan['type'] == "emergency") : ?>
                                    <li class="list-group-item d-flex px-0 justify-content-between">
                                        <strong>Amount</strong>
                                        <span class="mb-0">₦100,000</span>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                        <div class="card-footer mt-0">
                            <button class="btn btn-primary btn-lg btn-block apply" data-plan-id="<?php echo $data_plan['id']; ?>" data-plan-name="<?php echo $data_plan['name']; ?>">Apply</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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



<!-- Chart piety plugin files -->
<script src="vendor/peity/jquery.peity.min.js"></script>

<!-- Apex Chart -->
<script src="vendor/apexchart/apexchart.js"></script>

<!-- Dashboard 1 -->
<script src="js/dashboard/my-wallet.js"></script>

<!-- Datatable -->
<script src=" vendor/datatables/js/jquery.dataTables.min.js"></script>

<script>
    function createLoans() {
        // Show the Font Awesome spinner on the button
        $("#button_save").html('<i class="fa fa-spinner fa-spin"></i> Loan...');

        var formData = $("#create-loan-form").serialize();
        // console.log(formData);
        $.ajax({
            type: 'POST',
            url: 'loans.php',
            data: formData,
            dataType: 'json',
            success: function(response) {

                $("#button_save").html('Create Loans');

                // Display the SweetAlert based on the response
                swal({
                    type: response.response,
                    title: response.title,
                    text: response.msg,
                    confirmButtonText: 'Continue'
                });
                // $("#create-loan-form").reset();
                document.getElementById("create-loan-form").reset();
            },
            error: function(response) {
                // Hide the Font Awesome spinner on the button
                $("#button_save").html('Create Loans');

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

    $(document).ready(function() {
        $(document).on("click", ".apply", function() {
            // Get the saving ID
            var planId = $(this).data("plan-id");
            var planName = $(this).data("plan-name");

            swal({
                title: 'Appy for '+ planName + '?',
                text: "You won't be able to cancel Application!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Apply!',
                showLoaderOnConfirm: true,
                input: 'password', // Set the input type to password
                inputAttributes: {
                    autocapitalize: 'off',
                    placeholder: 'Enter your PIN', // Add a placeholder for the input field
                    maxlength: 4, // Set the maxlength to 4
                    autocomplete: 'off',
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to enter your PIN!'; // Display an error message if the input field is empty
                    }
                },
                preConfirm: function() {
                    return new Promise(function(resolve) {
                        let pin = Swal.getInput().value;

                        $.ajax({
                                type: 'POST',
                                url: 'loans.php', // Replace with the actual PHP file name
                                data: {
                                    action: "apply",
                                    planId: planId,
                                    pin: pin, // Include the PIN in the data object
                                },
                                dataType: 'json',
                            })
                            .done(function(results) {
                                // var jsonData = JSON.parse(response)
                                if (results.response == "success") {
                                    
                                    Swal({
                                        type: results.response,
                                        title: results.title,
                                        text: results.msg,
                                        confirmButtonText: 'Okay'
                                    });
                                } else {
                                    Swal({
                                        type: results.response,
                                        title: results.title,
                                        text: results.msg,
                                        confirmButtonText: 'Try Again'
                                    });
                                }

                            })
                            .fail(function(results) {
                                // console.log(results)
                                swal('Oops!', 'Something went wrong with this request!', 'error');
                            });
                    });
                },
                allowOutsideClick: false
            });

        });
    });
</script>
</body>

</html>