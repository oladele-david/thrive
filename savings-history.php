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

?>

<?php
// add_to_savings.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required data is provided
    if (isset($_POST['savingsId'], $_POST['addAmount'])) {
        $savingId = $_POST['savingsId'];
        $addAmount = $_POST['addAmount'];

        // Implement your code to update the savings amount in the database
        $updateResult = $savings->updateSaving($savingId, $addAmount, $userInSession);

        // Return the response as JSON
        ob_clean();
        echo $updateResult;
        exit();
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
                <th>Amount</th>
                <th>Minimum Amount</th>
                <th>Saving Intervals</th>
                <th>Start Date</th>
                <th>Status</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>
            <?php
            // Get the savings data
            $savingsData = $savings->getSavingHistory($userInSession);
            foreach ($savingsData as $saving) {
            ?>
                <tr>
                    <td><?php echo $saving['ref_no']; ?></td>
                    <td>₦<?php echo number_format($saving['amount'], 2); ?></td>
                    <td>₦<?php echo number_format($saving['minimum_amount'], 2); ?></td>
                    <td><?php echo $saving['saving_interval']; ?></td>
                    <td><?php echo $saving['start_date']; ?></td>
                    <td>
                        <?php if ($saving['status'] === 'active') : ?>
                            <span class="badge light badge-warning">
                                <i class="fa fa-circle text-warning mr-1"></i>
                                Active
                            </span>
                        <?php else : ?>
                            <span class="badge light badge-success">
                                <i class="fa fa-circle text-success mr-1"></i>
                                Ended
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
                                <a class="dropdown-item add-amount" href="#" data-saving-id="<?php echo $saving['id']; ?>">Add to Savings</a>
                                <a class="dropdown-item view-details" href="#" data-saving-id="<?php echo $saving['id']; ?>">Payment History</a>
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
                <th>Amount</th>
                <th>Saving Interval</th>
                <th>Start Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </tfoot>
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
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Savings</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Views Savings</a></li>
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
                <form method="post" id="addToSavingsForm">
                    <div class="form-group">
                        <label for="addAmount">Amount to Add: <small>Balance: ₦ <?php echo number_format($data_account['account_balance'], 2) ?></small></label>
                        <input type="number" class="form-control" id="addAmount" name="addAmount" required>
                    </div>
                    <input type="hidden" id="savingsId" name="savingsId" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitAddToSavingsForm">Add to Savings</button>
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
            url: 'savings-history.php',
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
            url: 'savings-history.php',
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

            // Set the saving ID in the hidden input field in the "addToSavingsModal" modal
            $("#savingsId").val(savingId);
            // Show the modal
            $("#addToSavingsModal").modal('show');
        });


    });
</script>
</body>

</html>