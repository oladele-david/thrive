<?php
require_once('../includes/autoload.php');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

$account = new Account();
$savings = new Savings();
$userLoans = new UserLoan();
$savingsHistory = new SavingsHistory();

$userInSession = $_SESSION['userInSession'];
$lastName = $_SESSION['lastName'];
$firstName = $_SESSION['firstName'];
$emailId = $_SESSION['emailId'];
$phoneNo = $_SESSION['phoneNo'];

if (empty($_SESSION['userInSession'])) {
    header("Location: signin.php");
    die("Redirecting to signin.php");
}

$pageTitle = "Users Loan";
$data_account = $account->getAccountById($userInSession);
$userLoanId = $_POST['userLoanId'];

?>

<?php
// add_to_savings.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required data is provided
    if (isset($_POST['userLoanId'], $_POST['addAmount'])) {
        $userLoanId = $_POST['userLoanId'];
        $addAmount = $_POST['addAmount'];

        // Implement your code to update the savings amount in the database
        $updateResult = $savings->updateSaving($userLoanId, $addAmount, $userInSession);

        // Return the response as JSON
        ob_clean();
        echo $updateResult;
        exit();
    }
}


// ! PHP code to handle the cancel action
if (isset($_POST['action']) && $_POST['action'] === 'approve') {
    $userLoanId = $_POST['userLoanId'];
    $status = $_POST['status']; // Get the PIN value from the AJAX request
    // Validate the PIN here (you can use your existing validation code)
    $response = $userLoans->processLoan($userLoanId, $status);
    ob_clean();
    echo $response;
    exit();
}

?>


<?php
if (isset($_POST['loadRecords'])) {
?>

    <table id="listrecords" class="display min-w850">
        <thead>
            <tr>
                <th>Ref No.</th>
                <th>Full Name</th>
                <th>Account Balance</th>
                <th>Amount</th>
                <th>Interest</th>
                <th>Loan Name</th>
                <th>Start&nbsp;Date/End&nbsp;Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Get the savings data
            $data_loans = $userLoans->listUserLoansWithAccountAndPlanInfo();
            foreach ($data_loans['userLoans'] as $loan) {
            ?>
                <tr>
                    <td><?php echo $loan['ref_no']; ?></td>
                    <td><?php echo $loan['last_name'] . " " . $loan['first_name']; ?></td>
                    <td>₦<?php echo number_format($loan['account_balance'], 2); ?></td>
                    <td>₦<?php echo number_format($loan['amount'], 2); ?></td>
                    <td><?php echo number_format($loan['interest_rate'], 2); ?>%</td>
                    <td><?php echo $loan['name']; ?></td>

                    <td>
                        <?php if ($loan['start_date'] == null && $loan['end_date'] == null) : ?>
                            <span class="badge light badge-info">
                                <i class="fa fa-circle text-info mr-1"></i>
                                Pending
                            </span>
                        <?php else : ?>
                            <span class="badge light badge-success">
                                <i class="fa fa-circle text-success mr-1"></i>
                                <?php
                                $startDate = $loan['start_date'];
                                $endDate = $loan['end_date'];
                                echo date('d-m-Y', strtotime($startDate)) . " / " . date('d-m-Y', strtotime($startDate));
                                ?>
                            </span>
                        <?php endif; ?>

                    </td>
                    <td>
                        <?php if ($loan['status'] === 'active') : ?>
                            <span class="badge light badge-warning">
                                <i class="fa fa-circle text-warning mr-1"></i>
                                Active
                            </span>
                        <?php else : ?>
                            <span class="badge light badge-danger">
                                <i class="fa fa-circle text-danger mr-1"></i>
                                <?php echo ucwords($loan['status']); ?>
                            </span>
                        <?php endif; ?>

                    </td>

                    <td>
                        <!-- Dropdown menu for actions -->
                        <div class="dropdown ml-auto justify-content-center">
                            <div class="btn-link" data-toggle="dropdown" aria-expanded="false">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                        <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                        <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                    </g>
                                </svg>
                            </div>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item open-record" href="javascript:void(0)" data-loan-id="<?php echo $loan['id']; ?>">View Loan</a>
                                <?php if ($loan['status'] === 'pending') : ?>
                                    <a class="dropdown-item approve" href="javascript:void(0)" data-loan-id="<?php echo $loan['id']; ?>">Approve Loan</a>
                                <?php endif; ?>

                                <a class="dropdown-item view-history" href="javascript:void(0)" data-loan-id="<?php echo $loan['id']; ?>">Payment History</a>
                            </div>
                        </div>
                    </td>

                </tr>
            <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Ref No.</th>
                <th>Full Name</th>
                <th>Account Balance</th>
                <th>Amount</th>
                <th>Interest</th>
                <th>Loan Name</th>
                <th>Start&nbsp;Date/End&nbsp;Date</th>
                <th>Status</th>
                <th>Action</th>

            </tr>
        </tfoot>
    </table>

<?php
    exit();
}
?>


<?php
if (isset($_POST['getHistory'])) {
    $userLoanId = $_POST['userLoanId'];
    // $history = $savingsHistory->getSavingsHistoryByUserLoanId($userLoanId);

?>

    <table id="historyRecord" class="table table-striped">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Amount</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Get the savings data
            $counter = 0;
            $amount = 0;
            foreach ($history as $hist) {
                $counter++;
                $amount = $amount + $hist['amount'];
            ?>
                <tr>
                    <td><span class="badge badge-pill badge-primary p-2"><?php echo str_pad($counter, 3, "0", STR_PAD_LEFT); ?></span></td>
                    <td>₦<?php echo number_format($hist['amount'], 2); ?></td>
                    <td><?php echo $hist['transaction_date']; ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>S/N</th>
                <th>Amount</th>
                <th>Transaction Date</th>
            </tr>
        </tfoot>
    </table>
    <div class="form-group">
        <label for="">Total Amount: ₦ <?php echo number_format($amount, 2) ?></label>
    </div>
<?php
    exit();
}
?>

<?php
if (isset($_POST['addSavings'])) {
    $userLoanId = $_POST['userLoanId'];
?>
    <div class="form-group">
        <label for="addAmount">Amount to Add: <small>Balance: ₦ <?php echo number_format($data_account['account_balance'], 2) ?></small></label>
        <input type="number" class="form-control" id="addAmount" name="addAmount" required>
    </div>
    <input type="hidden" id="userLoanId" name="userLoanId" value="<?php echo $userLoanId ?>">
<?php
    exit();
}
?>

<?php
if (isset($_POST['userLoanId'])) {
    $userLoanId = $_POST['userLoanId'];
    $userLoan = $userLoans->userLoansWithAccountAndPlanInfo($userLoanId);
    // echo var_dump($userLoan);
?>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td>Ref No.</td>
                <td><?php echo $userLoan['ref_no']; ?></td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>₦<?php echo number_format($userLoan['amount'], 2); ?></td>
            </tr>
            <tr>
                <td>Amount To Pay</td>
                <td>₦<?php echo number_format($userLoan['amount_return'], 2); ?></td>
            </tr>
            <tr>
                <td>Start Date</td>
                <td><?php echo $userLoan['start_date']; ?></td>
            </tr>
            <tr>
                <td>End Date</td>
                <td><?php echo $userLoan['end_date']; ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <?php if ($userLoan['status'] === 'active') : ?>
                        <span class="badge light badge-warning">
                            <i class="fa fa-circle text-warning mr-1"></i>
                            Active
                        </span>
                    <?php else : ?>
                        <span class="badge light badge-danger">
                            <i class="fa fa-circle text-danger mr-1"></i>
                            Ended
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Account Holder</td>
                <td><?php echo $userLoan['first_name'] . ' ' . $userLoan['last_name']; ?></td>
            </tr>
            <tr>
                <td>Account Balance</td>
                <td>₦<?php echo number_format($userLoan['account_balance'], 2); ?></td>
            </tr>
            <tr>
                <td>Loan Plan</td>
                <td><?php echo $userLoan['name']; ?></td>
            </tr>
        </tbody>
    </table>
<?php
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
    /* input[type=number] {
        -moz-appearance: textfield;
    } */
</style>
<!--**********************************
    Content body start
***********************************-->
<div class="content-body">
    <div class="container-fluid">
        <div class="page-titles">
            <h4>View Users Loan</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href=".">Dashboards</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Users Loan</a></li>
            </ol>
        </div>

        <!-- row -->
        <?php include('alerts.php') ?>
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive" id="dataCanvas"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--**********************************
    Content body end
***********************************-->

<!-- Add to Savings Modal -->
<div class="modal fade" id="addToSavingsModal" tabindex="-1" role="dialog" aria-labelledby="addToSavingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToSavingsModalLabel">Add to Savings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="addToSavingsForm" class="record-body">

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitAddToSavingsForm">Add to Savings</button>
            </div>
        </div>
    </div>
</div>

<!-- View User Loan Modal -->
<div class="modal fade" id="viewRecordModal" tabindex="-1" role="dialog" aria-labelledby="viewRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRecordModalLabel">View User Loan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body record-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View History Modal -->
<div class="modal fade" id="viewHistoryModal" tabindex="-1" role="dialog" aria-labelledby="viewHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewHistoryModalLabel">View User Loan History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body record-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php') ?>

</div>

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
    getRecords();
    // Get records
    function getRecords() {

        tableLoading = $("#loading_spinner");
        tableLoading.removeAttr("style");


        // console.log(publisherId + " -- " + subjectId);


        $.ajax({
            type: 'post',
            url: 'userLoans.php',
            data: {
                loadRecords: "loadRecords",
            },
            cache: false,
            success: function(response) {
                $("#dataCanvas").html(response);
                $("table").DataTable();
                tableLoading.css({
                    "display": "none"
                });


            }
        })
    }


    // JavaScript code for handling AJAX request to add to savings amount
    // Handle update button click
    $(document).on('click', '#submitAddToSavingsForm', function(event) {
        event.preventDefault();

        $("#submitaddtosavingsform").html('<i class="fa fa-spinner fa-spin"></i> Saving...');

        var formData = $("#addToSavingsForm").serialize();

        $.ajax({
            type: 'POST',
            url: 'userLoans.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Hide the Font Awesome spinner on the button
                if (response.response === 'success') {
                    // Hide the Font Awesome spinner on the button
                    $("#submitaddtosavingsform").html('Add to Savings');

                    // Display the SweetAlert based on the response
                    swal({
                        type: response.response,
                        title: response.title,
                        text: response.msg,
                        confirmButtonText: 'Continue'
                    });
                    // $("#create-saving-form").reset();
                    document.getElementById("addToSavingsForm").reset();

                    $("#addToSavingsModal").modal('hide');

                    getRecords();
                } else {
                    // Hide the Font Awesome spinner on the button
                    $("#submitaddtosavingsform").html('Add to Savings');

                    // Display the SweetAlert based on the response
                    swal({
                        type: response.response,
                        title: response.title,
                        text: response.msg,
                        confirmButtonText: 'Try Again'
                    });

                }

            },
            error: function(response) {
                // Hide the Font Awesome spinner on the button
                $("#submitaddtosavingsform").html('Add to Savings');

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
    });

    // JavaScript code to handle click events for "Add to Savings" option
    $(document).ready(function() {
        $(document).on("click", ".add-amount", function() {
            // Get the saving ID
            var userLoanId = $(this).data("saving-id");

            $.ajax({
                url: 'userLoans.php',
                type: 'post',
                data: {
                    addSavings: 'addSavings',
                    userLoanId: userLoanId
                },
                success: function(response) {
                    // Add response in Modal body

                    $('.record-body').html(response);
                    // Display Modal
                    $("#addToSavingsModal").modal('show');
                    // var stateId2 = $(response).find('.stateId').val();
                    // console.log(stateId2);
                }
            });

        });

        $(document).on("click", ".open-record", function() {
            // Get the saving ID
            var userLoanId = $(this).data("loan-id");

            $.ajax({
                url: 'userLoans.php',
                type: 'post',
                data: {
                    getRecord: 'getRecord',
                    userLoanId: userLoanId
                },
                success: function(response) {
                    // Add response in Modal body
                    $('.record-body').html(response);
                    // Display Modal
                    $("#viewRecordModal").modal('show');

                }
            });

        });

        $(document).on("click", ".view-history", function() {
            // Get the saving ID
            var userLoanId = $(this).data("saving-id");

            $.ajax({
                url: 'userLoans.php',
                type: 'post',
                data: {
                    getHistory: 'getHistory',
                    userLoanId: userLoanId
                },
                success: function(response) {
                    // Add response in Modal body
                    $('.record-body').html(response);
                    // Display Modal
                    $("#viewHistoryModal").modal('show');

                }
            });

        });

        $('body').on('click', '.approve', function() {
            let userLoanId = $(this).data("loan-id");
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Process it!',
                showLoaderOnConfirm: true,
                input: 'select', // Set the input type to select
                inputOptions: {
                    active: 'Active',
                    cancelled: 'Cancelled',
                },
                inputPlaceholder: 'Select an option', // Add a placeholder for the input field
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to select an option!'; // Display an error message if no option is selected
                    }
                },
                preConfirm: function(selectedValue) {
                    return new Promise(function(resolve) {
                        $.ajax({
                                type: 'POST',
                                url: 'userLoans.php', // Replace with the actual PHP file name
                                data: {
                                    action: "approve", // Replace
                                    userLoanId: userLoanId,
                                    status: selectedValue, // Include the selected status in the data object
                                },
                                dataType: 'json',
                            })
                            .done(function(results) {
                                if (results.response == "success") {
                                    getRecords();
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
                                    getRecords();
                                }
                            })
                            .fail(function(xhr, textStatus, errorThrown) {
                                console.log(userLoanId); // Log the responseJSON object
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