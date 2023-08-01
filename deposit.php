<?php
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
    $pageTitle = "Deposit";


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
            <div class="container-fluid">
                <div class="page-titles">
					<h4>Fund Account</h4>
					<ol class="breadcrumb">
						<!-- <li class="breadcrumb-item"><a href="javascript:void(0)">App</a></li> -->
						<li class="breadcrumb-item active"><a href="javascript:void(0)">Deposit</a></li>
					</ol>
                </div>
                <!-- row -->
                <?php include('alerts.php') ?>

                
                <div class="row  justify-content-center">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="profile-tab">
                                    <div class="custom-tab-1">
                                        <ul class="nav nav-tabs">
                                            <li class="nav-item"><a href="#transaction-pin" data-toggle="tab" class="nav-link active show">Deposit Funds</a></li>
                                            <!-- <li class="nav-item"><a href="#security-question" data-toggle="tab" class="nav-link">Security Question</a></li> -->
                                        </ul>
                                        <div class="tab-content">
                                            <div id="transaction-pin" class="tab-pane fade active show">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <!-- <h4 class="text-primary">Amount</h4> -->
                                                        <form  method="POST" >
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Amount</label>
                                                                    <input type="number"  name="amount" class="form-control"  required>
                                                                </div>
                                                            </div>
                                                            
                                                            <button class="btn btn-primary" name="submit" type="submit">Deposit</button>
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

</body>

</html>