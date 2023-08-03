<?php
require_once('includes/autoload.php');


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

$account = new Account();
$deposits = new Deposit();

$userInSession = $_SESSION['userInSession'];
$lastName = $_SESSION['lastName'];
$firstName = $_SESSION['firstName'];
$emailId = $_SESSION['emailId'];
$phoneNo = $_SESSION['phoneNo'];

if (empty($_SESSION['userInSession'])) {
    header("Location: signin.php");
    die("Redirecting to signin.php");
}

$pageTitle = "Deposits List";
$data_account = $account->getAccountById($userInSession);

?>


<?php
if (isset($_POST['loadRecords'])) {
?>

    <table id="listrecords" class="display min-w850">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Ref No.</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Get the deposits data
            $depositsData = $deposits->getDepositsByAccountId($userInSession);
            $counter = 0;
            foreach ($depositsData as $deposit) {
                $counter++;
            ?>
                <tr>
                    <td><span class="badge badge-pill badge-primary p-2"><?php echo str_pad($counter, 3, "0", STR_PAD_LEFT); ?></span></td>
                    <td><?php echo $deposit['ref_no']; ?></td>
                    <td>₦<?php echo number_format($deposit['amount'], 2); ?></td>
                    <td><?php echo $deposit['deposit_date']; ?></td>
                    <td>
                        <?php if ($deposit['status'] === 'completed') : ?>
                            <span class="badge light badge-success">
                                <i class="fa fa-circle text-success mr-1"></i>
                                Completed
                            </span>
                        <?php else : ?>
                            <span class="badge light badge-danger">
                                <i class="fa fa-circle text-danger mr-1"></i>
                                Ended
                            </span>
                        <?php endif; ?>
                    </td>


                </tr>
            <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>S/N</th>
                <th>Ref No.</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
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
            <h4>View Deposits</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="deposits.php">Deposits</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Deposits List</a></li>
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
            url: 'deposit-list.php',
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

</script>
</body>

</html>