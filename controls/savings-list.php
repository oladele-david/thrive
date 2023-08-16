<?php
require_once('../includes/autoload.php');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

$account = new Account();
$savings = new Savings();
$savingsHistory = new SavingsHistory();

$accountId  = (isset($_REQUEST['accountId'])) ? $_REQUEST['accountId'] : $_POST['accountId'] ;

$userInSession = $_SESSION['userInSession'];
$lastName = $_SESSION['lastName'];
$firstName = $_SESSION['firstName'];
$emailId = $_SESSION['emailId'];
$phoneNo = $_SESSION['phoneNo'];

if (empty($_SESSION['userInSession'])) {
    header("Location: signin.php");
    die("Redirecting to signin.php");
}

$pageTitle = "Savings List";
$data_account = $account->getAccountById($accountId);

?>

<?php
// add_to_savings.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required data is provided
    if (isset($_POST['savingId'], $_POST['addAmount'])) {
        $savingId = $_POST['savingId'];
        $addAmount = $_POST['addAmount'];

        // Implement your code to update the savings amount in the database
        $updateResult = $savings->updateSaving($savingId, $addAmount, $accountId);

        // Return the response as JSON
        ob_clean();
        echo $updateResult;
        exit();
    }
}

// ! PHP code to handle the cancel action
if (isset($_POST['action']) && $_POST['action'] === 'end') {
    $savingId = $_POST['savingId'];
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

        $end_savings = $savings->endSavings($userInSession, $savingId);

        if ($end_savings) {
            ob_clean();            
            echo $end_savings;
            exit();
        } else {
            ob_clean();
            echo $end_savings;
            exit();
        }
    }
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
                <th>Amount</th>
                <th>Minimum Amount</th>
                <th>Start Date</th>
                <th>Status/Type</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>
            <?php
            // Get the savings data
            $savingsData = $savings->listSavingsWithUserInfo();
            foreach ($savingsData['savings'] as $saving) {
            ?>
                <tr>
                    <td><?php echo $saving['ref_no']; ?></td>
                    <td><?php echo $saving['last_name'] . " ". $saving['first_name']; ?></td>
                    <td>₦<?php echo number_format($saving['amount'], 2); ?></td>
                    <td>₦<?php echo number_format($saving['minimum_amount'], 2); ?></td>
                    <td><?php echo $saving['start_date']; ?></td>
                    <td>
                        <?php if ($saving['status'] === 'active') : ?>
                            <span class="badge light badge-warning">
                                <i class="fa fa-circle text-warning mr-1"></i>
                                Active
                            </span>&nbsp;|&nbsp;
                        <?php else : ?>
                            <span class="badge light badge-danger">
                                <i class="fa fa-circle text-danger mr-1"></i>
                                Ended
                            </span>&nbsp;|&nbsp;
                        <?php endif; ?>

                        <?php if ($saving['special'] == 1) : ?>
                            <span class="badge light badge-success">
                                <i class="fa fa-circle text-success mr-1"></i>
                                Special
                            </span>
                        <?php else : ?>
                            <span class="badge light badge-info">
                                <i class="fa fa-circle text-info mr-1"></i>
                                Normal
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
                                <a class="dropdown-item open-record" href="javascript:void(0)" data-saving-id="<?php echo $saving['id']; ?>" data-account-id="<?php echo $saving['account_id']; ?>">View Savings</a>
                                <a class="dropdown-item view-history" href="javascript:void(0)" data-saving-id="<?php echo $saving['id']; ?>" data-account-id="<?php echo $saving['account_id']; ?>">Saving History</a>

                                <?php if ($saving['status'] === 'active') : ?>
                                    <a class="dropdown-item add-amount" href="javascript:void(0)" data-saving-id="<?php echo $saving['id']; ?>" data-account-id="<?php echo $saving['account_id']; ?>">Add to Savings</a>
                                <?php endif; ?>
                                <?php if ($saving['status'] === 'active' && $saving['special'] == 1) : ?>
                                    <a class="dropdown-item end" href="javascript:void(0)" data-saving-id="<?php echo $saving['id']; ?>">End Savings</a>
                                <?php endif; ?>
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
                <th>Amount</th>
                <th>Minimum Amount</th>
                <th>Start Date</th>
                <th>Status/Type</th>
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
    $savingId = $_POST['savingId'];
    $history = $savingsHistory->getSavingsHistoryBySavingId($savingId);

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
                $amount =0;
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
        <label for="">Total Amount:  ₦ <?php echo number_format($amount, 2) ?></label>
    </div>
<?php
    exit();
}
?>

<?php
if (isset($_POST['addSavings'])) {
    $savingId = $_POST['savingId'];
?>
    <div class="form-group">
        <label for="addAmount">Amount to Add: <small>Balance: ₦ <?php echo number_format($data_account['account_balance'], 2) ?></small></label>
        <input type="number" class="form-control" id="addAmount" name="addAmount" required>
    </div>
    <input type="hidden" id="savingId" name="savingId" value="<?php echo $savingId ?>">
<?php
    exit();
}
?>

<?php
if (isset($_POST['savingId'])) {
    $savingId = $_POST['savingId'];
    $saving = $savings->getSaving($savingId)
?>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td>Ref No.</td>
                <td><?php echo $saving['ref_no']; ?></td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>₦<?php echo number_format($saving['amount'], 2); ?></td>
            </tr>
            <tr>
                <td>Minimum Amount</td>
                <td>₦<?php echo number_format($saving['minimum_amount'], 2); ?></td>
            </tr>
            <tr>
                <td>Saving Interval</td>
                <td><?php echo $saving['saving_interval']; ?></td>
            </tr>
            <tr>
                <td>Start Date</td>
                <td><?php echo $saving['start_date']; ?></td>
            </tr>
            <tr>
                <td>End Date</td>
                <td><?php echo $saving['ending_date']; ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <?php if ($saving['status'] === 'active') : ?>
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
                <td>Type</td>
                <td>
                    <?php if ($saving['special'] == 1) : ?>
                        <span class="badge light badge-success">
                            <i class="fa fa-circle text-success mr-1"></i>
                            Special
                        </span>
                    <?php else : ?>
                        <span class="badge light badge-info">
                            <i class="fa fa-circle text-info mr-1"></i>
                            Normal
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Next Removing Date</td>
                <td><?php echo $saving['next_removing_date']; ?></td>
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
            <h4>View Savings</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href=".">Savings</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Savings List</a></li>
            </ol>
        </div>

        <!-- row -->
        <?php //include('alerts.php') ?>
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

<!-- View Savings Modal -->
<div class="modal fade" id="viewSavingsModal" tabindex="-1" role="dialog" aria-labelledby="viewSavingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSavingsModalLabel">View Savings</h5>
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
                <h5 class="modal-title" id="viewHistoryModalLabel">View Savings History</h5>
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
            url: 'savings-list.php',
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
            url: 'savings-list.php',
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
            var savingId = $(this).data("saving-id");
            var accountId = $(this).data("account-id");

            $.ajax({
                url: 'savings-list.php',
                type: 'post',
                data: {
                    addSavings: 'addSavings',
                    savingId: savingId,
                    accountId: accountId
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
            var savingId = $(this).data("saving-id");
            var accountId = $(this).data("account-id");

            $.ajax({
                url: 'savings-list.php',
                type: 'post',
                data: {
                    getRecord: 'getRecord',
                    savingId: savingId,
                    accountId: accountId
                },
                success: function(response) {
                    // Add response in Modal body
                    $('.record-body').html(response);
                    // Display Modal
                    $("#viewSavingsModal").modal('show');

                }
            });

        });

        $(document).on("click", ".view-history", function() {
            // Get the saving ID
            var savingId = $(this).data("saving-id");
            var accountId = $(this).data("account-id");

            $.ajax({
                url: 'savings-list.php',
                type: 'post',
                data: {
                    getHistory: 'getHistory',
                    savingId: savingId,
                    accountId: accountId
                },
                success: function(response) {
                    // Add response in Modal body
                    $('.record-body').html(response);                
                    // Display Modal
                    $("#viewHistoryModal").modal('show');

                }
            });

        });

        $(document).on("click", ".end", function() {
            // Get the saving ID
            var savingId = $(this).data("saving-id");

            swal({
                title: 'End This Savings Plan?',
                text: "You won't be able to revert this process!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, End!',
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
                                url: 'savings-list.php', // Replace with the actual PHP file name
                                data: {
                                    action: "end",
                                    savingId: savingId,
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