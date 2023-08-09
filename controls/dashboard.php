<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once('../includes/autoload.php');


if (session_status() != PHP_SESSION_ACTIVE)
	session_start();

$admin = new Admin();
$account = new Account();
$userLoans = new UserLoan();
$savings = new Savings();
$withdrawal = new Withdrawal();
$deposit = new Deposit();

$userInSession = $_SESSION['userInSession'];
$lastName = $_SESSION['lastName'];
$firstName = $_SESSION['firstName'];
$emailId = $_SESSION['emailId'];
$phoneNo = $_SESSION['phoneNo'];

if(empty($_SESSION['userInSession']))
{
    header("Location: signin.php");

    die("Redirecting to signin.php");
}
$pageTitle = "Home";

// require_once('objects/accountRESTful.php');
$data_admin = $admin->getAdminById($userInSession);

$data_accounts_results = $account->listAccounts();
$data_pending_user_loans_results = $userLoans->listUserLoans("pending");
$data_active_savings_results = $savings->listSavings("active");
$data_pending_withdrawals_results = $withdrawal->listWithdrawals("pending");
$latest_deposits = $deposit->listLatestDepositsWithAccountInfo();

$data_accounts = $data_accounts_results['accounts'];
$data_pending_user_loans = $data_pending_user_loans_results['userLoans'];
$data_active_savings = $data_active_savings_results['savings'];
$data_pending_withdrawals = $data_pending_withdrawals_results['withdrawals'];
$data_latest_deposits = $latest_deposits['latest_deposits'];
?>

<?php include('includes/header.php') ?>
<?php include('includes/sidebar.php') ?>

<!--**********************************
            Content body start
        ***********************************-->
<div class="content-body">

	<!-- row -->
	<div class="container-fluid">
		<div class="form-head mb-4">
			<h2 class="text-black font-w600 mb-0">Admin Dashboard</h2>
		</div>

		<?php //include('includes/alerts.php') ?>

		<div class="row">
			<div class="col-xl-12 col-xxl-12 col-md-12">
				<div class="row">
					<div class="col-xl-3 col-lg-6 col-sm-6">
						<a href="users.php">
							<div class="widget-stat card">
								<div class="card-body p-4">
									<div class="media ai-icon">
										<span class="mr-3 bgl-primary text-primary">
											<!-- <i class="ti-user"></i> -->
											<svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
												<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
												<circle cx="12" cy="7" r="4"></circle>
											</svg>
										</span>
										<div class="media-body">
											<p class="mb-1">Total Users</p>
											<h4 class="mb-0"><?php echo count($data_accounts)?></h4>
											<!-- <span class="badge badge-primary">+3.5%</span> -->
										</div>
									</div>
								</div>
							</div>
						</a>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-sm-6">
						<a href="userLoans.php">
							<div class="widget-stat card">
								<div class="card-body p-4">
									<div class="media ai-icon">
										<span class="mr-3 bgl-warning text-warning">
											<svg id="icon-orders" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
												<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
												<polyline points="14 2 14 8 20 8"></polyline>
												<line x1="16" y1="13" x2="8" y2="13"></line>
												<line x1="16" y1="17" x2="8" y2="17"></line>
												<polyline points="10 9 9 9 8 9"></polyline>
											</svg>
										</span>
										<div class="media-body">
											<p class="mb-1">Pending Loans</p>
											<h4 class="mb-0"><?php echo count($data_pending_user_loans)?></h4>
											<!-- <span class="badge badge-warning">+3.5%</span> -->
										</div>
									</div>
								</div>
							</div>
						</a>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-sm-6">
						<a href="savings.php">
							<div class="widget-stat card">
								<div class="card-body  p-4">
									<div class="media ai-icon">
										<span class="mr-3 bgl-danger text-danger">
											<svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
												<line x1="12" y1="1" x2="12" y2="23"></line>
												<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
											</svg>
										</span>
										<div class="media-body">
											<p class="mb-1">Active Savings</p>
											<h4 class="mb-0"><?php echo count($data_active_savings)?></h4>
											<!-- <span class="badge badge-danger">-3.5%</span> -->
										</div>
									</div>
								</div>
							</div>
						</a>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-sm-6">
						<a href="withdrawals.php">
							<div class="widget-stat card">
								<div class="card-body p-4">
									<div class="media ai-icon">
										<span class="mr-3 bgl-success text-success">
											<svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
												<ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
												<path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
												<path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
											</svg>
										</span>
										<div class="media-body">
											<p class="mb-1">Pending Withdrawals</p>
											<h4 class="mb-0"><?php echo count($data_pending_withdrawals)?></h4>
											<!-- <span class="badge badge-success">-3.5%</span> -->
										</div>
									</div>
								</div>
							</div>
						</a>
                    </div>
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Recent Deposits</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-responsive-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
											<?php 
												$counter = 1;
												foreach ($data_latest_deposits as $latest_deposit):
											?>
                                                <th><?php echo  $counter ?></th>
                                                <td><?php echo  $latest_deposit['first_name'] . ' ' . $latest_deposit['last_name'] ?></td>
                                                <td><span class="badge badge-primary light"><?php echo  $latest_deposit['status'] ?></span>
                                                </td>
                                                <td><?php echo $latest_deposit['deposit_date'] ?></td>
                                                <td class="color-primary">₦<?php echo number_format($latest_deposit['amount'], 2) ?></td>
                                            </tr>
                                           <?php $counter++; endforeach; ?>
                                        </tbody>
                                    </table>
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

</body>

</html>