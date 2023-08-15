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

$userInSession = $_SESSION['userInSession'];
$lastName = $_SESSION['lastName'];
$firstName = $_SESSION['firstName'];
$emailId = $_SESSION['emailId'];
$phoneNo = $_SESSION['phoneNo'];

if (empty($_SESSION['userInSession'])) {
    header("Location: signin.php");
    die("Redirecting to signin.php");
}

$pageTitle = "Users";
$data_accounts = $account->listAccounts();

?>

<?php
// add_to_savings.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required data is provided
    if (isset($_POST['savingId'], $_POST['addAmount'])) {
        $savingId = $_POST['savingId'];
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
                <th>Account&nbsp;ID.</th>
                <th>Full&nbsp;Name</th>
                <th>EmailId</th>
                <th>Balance</th>
                <th>Thrive No.</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>
            <?php
            // Get the savings data
            foreach ($data_accounts['accounts'] as $data_account) {
            ?>
                <tr>
                    <td><?php echo $data_account['id']; ?></td>
                    <td><?php echo $data_account['last_name'] . " ". $data_account['first_name']; ?></td>
                    <td><?php echo $data_account['email_id']; ?></td>
                    <td>₦<?php echo number_format($data_account['account_balance'], 2); ?></td>
                    <td><?php echo $data_account['account_number']; ?></td>
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
                                <a class="dropdown-item " href="profile.php?accountId=<?php echo $data_account['id']; ?>">Profile</a>
                                <a class="dropdown-item" href="security.php?accountId=<?php echo $data_account['id']; ?>" >Security</a>
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
                <th>Account&nbsp;ID.</th>
                <th>Full&nbsp;Name</th>
                <th>EmailId</th>
                <th>Balance</th>
                <th>Thrive No.</th>
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
            <h4>View Users</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href=".">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Users</a></li>
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
            url: 'users.php',
            data: {
                loadRecords: "loadRecords",
            },
            cache: false,
            success: function(response) {
                console.log(response);
                $("#dataCanvas").html(response);
                $("table").DataTable();
                tableLoading.css({
                    "display": "none"
                });


            }
        })
    }

</script>
</body>

</html>