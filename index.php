<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once('includes/autoload.php');

if (session_status() != PHP_SESSION_ACTIVE)
	session_start();
$account = new Account();

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
$data_account = $account->getAccountById($userInSession);

if (isset($_POST['amount'])) {
	$_SESSION['amount'] = $_POST['amount'];
	header("Location: process-payment.php");
}
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
			<h2 class="text-black font-w600 mb-0">My Wallet</h2>
		</div>

		<?php include('includes/alerts.php') ?>

		<div class="row">
			<div class="col-xl-12 col-xxl-12 col-md-12">
				<div class="row">
					<div class="col-xl-12">
						<div class="card stacked-2">
							<div class="card-header flex-wrap border-0 pb-0 align-items-end">
								<div class="d-flex align-items-center mb-3 mr-3">
									<svg class="mr-3" width="68" height="68" viewBox="0 0 68 68" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M59.4999 31.688V19.8333C59.4999 19.0818 59.2014 18.3612 58.6701 17.8298C58.1387 17.2985 57.418 17 56.6666 17H11.3333C10.5818 17 9.86114 16.7014 9.32978 16.1701C8.79843 15.6387 8.49992 14.9181 8.49992 14.1666C8.49992 13.4152 8.79843 12.6945 9.32978 12.1632C9.86114 11.6318 10.5818 11.3333 11.3333 11.3333H56.6666C57.418 11.3333 58.1387 11.0348 58.6701 10.5034C59.2014 9.97208 59.4999 9.25141 59.4999 8.49996C59.4999 7.74851 59.2014 7.02784 58.6701 6.49649C58.1387 5.96514 57.418 5.66663 56.6666 5.66663H11.3333C9.07891 5.66663 6.9169 6.56216 5.32284 8.15622C3.72878 9.75028 2.83325 11.9123 2.83325 14.1666V53.8333C2.83325 56.0876 3.72878 58.2496 5.32284 59.8437C6.9169 61.4378 9.07891 62.3333 11.3333 62.3333H56.6666C57.418 62.3333 58.1387 62.0348 58.6701 61.5034C59.2014 60.9721 59.4999 60.2514 59.4999 59.5V47.6453C61.1561 47.0683 62.5917 45.9902 63.6076 44.5605C64.6235 43.1308 65.1693 41.4205 65.1693 39.6666C65.1693 37.9128 64.6235 36.2024 63.6076 34.7727C62.5917 33.3431 61.1561 32.265 59.4999 31.688ZM53.8333 56.6666H11.3333C10.5818 56.6666 9.86114 56.3681 9.32978 55.8368C8.79843 55.3054 8.49992 54.5847 8.49992 53.8333V22.1453C9.40731 22.4809 10.3658 22.6572 11.3333 22.6666H53.8333V31.1666H45.3333C43.0789 31.1666 40.9169 32.0622 39.3228 33.6562C37.7288 35.2503 36.8333 37.4123 36.8333 39.6666C36.8333 41.921 37.7288 44.083 39.3228 45.677C40.9169 47.2711 43.0789 48.1666 45.3333 48.1666H53.8333V56.6666ZM56.6666 42.5H45.3333C44.5818 42.5 43.8611 42.2015 43.3298 41.6701C42.7984 41.1387 42.4999 40.4181 42.4999 39.6666C42.4999 38.9152 42.7984 38.1945 43.3298 37.6632C43.8611 37.1318 44.5818 36.8333 45.3333 36.8333H56.6666C57.418 36.8333 58.1387 37.1318 58.6701 37.6632C59.2014 38.1945 59.4999 38.9152 59.4999 39.6666C59.4999 40.4181 59.2014 41.1387 58.6701 41.6701C58.1387 42.2015 57.418 42.5 56.6666 42.5Z" fill="#1EAAE7" />
									</svg>
									<div class="mr-auto">
										<h5 class="fs-20 text-black font-w600">Main Balance</h5>
										<span class="text-num text-black font-w600">₦ <?php echo number_format($data_account['account_balance'], 2) ?> </span>
									</div>
								</div>
								<!-- <div class="mr-3 mb-3">
									<a class="btn btn-outline-primary rounded d-block btn-md" data-toggle="modal" data-target="#newspends"> My Balances</a>
									<div class="modal fade" id="newspends">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title">Modal title</h5>
													<button type="button" class="close" data-dismiss="modal"><span>&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-danger light" data-dismiss="modal">Close</button>
													<button type="button" class="btn btn-primary">Save changes</button>
												</div>
											</div>
										</div>
									</div>
								</div> -->
								<div class="mr-3 mb-3">
									<p class="fs-14 mb-1">Account No</p>
									<span class="text-black"><?php echo $data_account['account_number'] ?></span>
								</div>
								<div class="mr-3 mb-3">
									<p class="fs-14 mb-1">BVN</p>
									<?php
									if ($data_account['bvn'] != Null) {
									?>
										<span class="text-black"><?php echo $data_account['bvn'] ?></span>
									<?php
									} else {
									?>
										<span class="text-danger">N/A</span>
									<?php
									}
									?>
								</div>
								<!-- <span class="fs-20 text-black font-w500 mr-3 mb-3"> </span> -->
								<div class="dropdown mb-auto">
									<div class="btn-link" role="button" data-toggle="dropdown" aria-expanded="false">
										<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M10 11.9999C10 13.1045 10.8954 13.9999 12 13.9999C13.1046 13.9999 14 13.1045 14 11.9999C14 10.8954 13.1046 9.99994 12 9.99994C10.8954 9.99994 10 10.8954 10 11.9999Z" fill="black" />
											<path d="M10 4.00006C10 5.10463 10.8954 6.00006 12 6.00006C13.1046 6.00006 14 5.10463 14 4.00006C14 2.89549 13.1046 2.00006 12 2.00006C10.8954 2.00006 10 2.89549 10 4.00006Z" fill="black" />
											<path d="M10 20C10 21.1046 10.8954 22 12 22C13.1046 22 14 21.1046 14 20C14 18.8954 13.1046 18 12 18C10.8954 18 10 18.8954 10 20Z" fill="black" />
										</svg>
									</div>
									<!-- <div class="dropdown-menu dropdown-menu-right">
												<a class="dropdown-item" href="javascript:void(0)">Delete</a>
												<a class="dropdown-item" href="javascript:void(0)">Edit</a>
											</div> -->
								</div>
							</div>
							<div class="card-body">
								<!-- <div class="progress mb-4" style="height:18px;">
											<div class="progress-bar bg-inverse progress-animated" style="width: 70%; height:18px;" role="progressbar">
												<span class="sr-only">60% Complete</span>
											</div>
										</div> -->

								<div class="row align-items-center">
									<div class="col-xl-3 mb-3 col-xxl-6 col-sm-6">
										<!-- <div class="media align-items-center bgl-secondary rounded p-2">
													<span class="bg-white rounded-circle p-3 mr-4">
														<i class="lab la-accessible-icon fa-2x"></i>
													</span>
													<div class="media-body">
														<h4 class="fs-15 text-black font-w600 mb-0">Fund</h4>
														<span class="fs-14">₦5,412</span>
													</div>
												</div> -->
										<button class="btn btn-outline-success rounded d-block btn-lg btn-block" data-toggle="modal" data-target="#fundAccount">Add Fund</button>
										<div class="modal fade" id="fundAccount">
											<div class="modal-dialog modal-dialog-centered" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title">FUND ACCOUNT</h5>
														<button type="button" class="close" data-dismiss="modal"><span>&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<form method="post">
															<div class="form-group row style-1 align-items-center">
																<label class="fs-18 col-sm-3 text-black font-w500">Amount</label>
																<div class="input-group col-sm-9">
																	<input type="number" class="form-control" name="amount" placeholder="₦">
																	<div class="input-group-append">
																		<button class="btn btn-primary btn-sm rounded" type="submit">ADD FUNDS</button>
																	</div>
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-xl-3 mb-3 col-xxl-6 col-sm-6">
										<!-- <div class="media bgl-success rounded p-2 align-items-center">
													<div class="d-inline-block mr-3 position-relative donut-chart-sale2">
														<span class="donut2" data-peity='{ "fill": ["rgb(43, 193, 85)", "rgba(255, 255, 255, 0)"],   "innerRadius": 23, "radius": 10}'>8/10</span>
														<small class="text-success">74%</small>
													</div>
													<div class="media-body">
														<h4 class="fs-15 text-black font-w600 mb-0">Request</h4>
														<span class="fs-14">₦3,784</span>
													</div>
												</div> -->
										<a class="btn btn-outline-info rounded d-block btn-lg btn-block" href="loans.php"> Loans</a>

									</div>
									<div class="col-xl-3 mb-3 col-xxl-6 col-sm-6">
										<!-- <div class="media bgl-info rounded p-2 align-items-center">
													<div class="d-inline-block mr-3 position-relative donut-chart-sale2">
														<span class="donut2" data-peity='{ "fill": ["rgb(70, 30, 231)", "rgba(255, 255, 255, 0)"],   "innerRadius": 23, "radius": 10}'>4/10</span>
														<small class="text-info">34%</small>
													</div>
													<div class="media-body">
														<h4 class="fs-15 text-black font-w600 mb-0">Transfer</h4>
														<span class="fs-14">$3,784</span>
													</div>
												</div> -->
										<a class="btn btn-outline-danger rounded d-block btn-lg btn-block" href="withdrawals.php"> Withdraw</a>
									</div>
									<div class="col-xl-3 mb-3 col-xxl-6 col-sm-6">
										<a class="btn btn-outline-warning rounded d-block btn-lg btn-block" href="savings.php"> Save</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Recent Transactions</h4>
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
                                                <th>Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Kolor Tea Shirt For Man</td>
                                                <td><span class="badge badge-primary light">Sale</span>
                                                </td>
                                                <td>January 22</td>
                                                <td class="color-primary">$21.56</td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Kolor Tea Shirt For Women</td>
                                                <td><span class="badge badge-success">Tax</span>
                                                </td>
                                                <td>January 30</td>
                                                <td class="color-success">$55.32</td>
                                            </tr>
                                            <tr>
                                                <th>3</th>
                                                <td>Blue Backpack For Baby</td>
                                                <td><span class="badge badge-danger">Extended</span>
                                                </td>
                                                <td>January 25</td>
                                                <td class="color-danger">$14.85</td>
                                            </tr>
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