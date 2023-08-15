<?php
require_once('../includes/autoload.php');


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

$pageTitle = "Withdrawals List";
$data_account = $account->getAccountById($userInSession);

?>

<?php

// ! PHP code to handle the cancel action
if (isset($_POST['action']) && $_POST['action'] === 'approve') {
    $withdrawalId = $_POST['withdrawalId'];
    $status = $_POST['status']; // Get the PIN value from the AJAX request
    // Validate the PIN here (you can use your existing validation code)
    $response = $withdrawals->processWithdrawal($withdrawalId, $status);
    ob_clean();
    echo $response;
    exit();
}


// * PHP code to handle the loading records
if (isset($_POST['loadRecords'])) {
?>
    <table id="listrecords" class="display min-w850">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Full Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>
            <?php
            // Get the withdrawals data
            $withdrawalsData = $withdrawals->listWithdrawals();
            $counter = 0;
            foreach ($withdrawalsData['withdrawals'] as $withdrawal) {
                $counter++;
            ?>
                <tr>
                    <td><span class="badge badge-pill badge-primary p-2"><?php echo str_pad($counter, 3, "0", STR_PAD_LEFT); ?></span></td>
                    <td><?php echo $withdrawal['last_name'] . " ". $withdrawal['first_name']; ?></td>
                    <td>₦<?php echo number_format($withdrawal['amount'], 2); ?></td>
                    <td>
                        <?php if ($withdrawal['status'] === 'pending') : ?>
                            <span class="badge light badge-warning">
                                <i class="fa fa-circle text-warning mr-1"></i>
                                Pending
                            </span>
                        <?php elseif ($withdrawal['status'] === 'approved') : ?>
                            <span class="badge light badge-success">
                                <i class="fa fa-circle text-success mr-1"></i>
                                Approved
                            </span>
                        <?php else : ?>
                            <span class="badge light badge-danger">
                                <i class="fa fa-circle text-danger mr-1"></i>
                                Cancelled
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
                                <?php if ($withdrawal['status'] === 'pending') : ?>
                                    <a class="dropdown-item approve" href="javascript:void(0)" data-withdrawal-id="<?php echo $withdrawal['id']; ?>">Process Request</a>
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
                <th>S/N</th>
                <th>Full Name</th>
                <th>Amount</th>
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

<!--**********************************
    Content body start
***********************************-->
<div class="content-body">
    <div class="container-fluid">
        <div class="page-titles">
            <h4>View Withdrawals</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="withdrawals.php">Withdrawals</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Withdrawals List</a></li>
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
            url: 'withdrawals-list.php',
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

    // JavaScript code to handle click events for "Add to Withdrawals" option
    $(document).ready(function() {
        $('.cancel-brtn').click(function(event) {
            event.preventDefault();

            var withdrawalId = $(this).data("withdrawal-id");
            // Store the reference to $(this) in a variable
            var $this = $(this);

            // Display confirmation dialog with input field
            Swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!',
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
            }).then((result) => {
                if (result.isConfirmed) {
                    var pin = result.value; // Get the entered PIN value
                    console.log(pin);
                    // Send AJAX request to cancel the record along with the PIN
                    $.ajax({
                        type: 'POST',
                        url: 'withdrawals-list.php', // Replace with the actual PHP file name
                        data: {
                            action: "cancel",
                            withdrawalId: withdrawalId,
                            pin: pin, // Include the PIN in the data object
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.response === 'success') {
                                // Show success message
                                Swal({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.msg
                                });

                                // Refresh the table
                                getRecords();
                            } else {
                                // Show error message
                                Swal({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.msg
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            // Show error message if AJAX request fails
                            Swal({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while processing the request.'
                            });
                        }
                    });
                }
            });

            // Prevent the event from bubbling up and losing context
            event.stopPropagation();
        });

        $('body').on('click', '.approve', function() {
            let withdrawalId = $(this).data("withdrawal-id");
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
                    approved: 'Approve',
                    cancelled: 'Cancel',
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
                                url: 'withdrawals-list.php', // Replace with the actual PHP file name
                                data: {
                                    action: "approve", // Replace
                                    withdrawalId: withdrawalId,
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
                                console.log(withdrawalId); // Log the responseJSON object
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