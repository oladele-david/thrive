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

    $data_account = $account->getAccountById($userInSession);
    
    if(isset($_POST['updateTransactionPin'])) {
        $transactionPin = $_POST['transactionPin'];
        $updatePin = $account->updatePin($userInSession, $transactionPin);
        ob_clean();
        echo $updatePin;              
        exit;
    }

    if(isset($_POST['updateSecurity'])) {
        $securityQuestion = $_POST['securityQuestion'];
        $securityAnswer = $_POST['securityAnswer'];

        $updateQuestion = $account->updateSecurityQuestion($userInSession, $securityQuestion, $securityAnswer);

        ob_clean();
        echo $updateQuestion;              
        exit;
    }

   
    $pageTitle = "Security Settings";


?>

<?php include('includes/header.php') ?>
<?php include('includes/sidebar.php') ?>

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <div class="page-titles">
					<h4>Security</h4>
					<ol class="breadcrumb">
						<!-- <li class="breadcrumb-item"><a href="javascript:void(0)">App</a></li> -->
						<li class="breadcrumb-item active"><a href="javascript:void(0)">Setup</a></li>
					</ol>
                </div>
                <!-- row -->
                <?php include('alerts.php') ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="profile card card-body px-3 pt-3 pb-0">
                            <div class="profile-head">
                                <div class="profile-info">
									<div class="profile-photo">
										<img src="images/profile/profile.png" class="img-fluid rounded-circle" alt="">
									</div>
									<div class="profile-details">
										<div class="profile-name px-3 pt-2">
											<h4 class="text-primary mb-0"><?php echo ucfirst($lastName) . " " . ucfirst($firstName) ?></h4>
											<p>User</p>
										</div>
										<div class="profile-email px-2 pt-2">
											<h4 class="text-muted mb-0"><?php echo $emailId ?></h4>
											<p>Email</p>
										</div>
										<div class="dropdown ml-auto">
											<a href="javascript:void(0)" class="btn btn-primary light sharp" data-toggle="dropdown" aria-expanded="true"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg></a>
											<ul class="dropdown-menu dropdown-menu-right">
												<li class="dropdown-item"><i class="fa fa-user-circle text-primary mr-2"></i> View profile</li>
												<li class="dropdown-item"><i class="fa fa-users text-primary mr-2"></i> Add to close friends</li>
												<li class="dropdown-item"><i class="fa fa-plus text-primary mr-2"></i> Add to group</li>
												<li class="dropdown-item"><i class="fa fa-ban text-primary mr-2"></i> Block</li>
											</ul>
										</div>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="profile-tab">
                                    <div class="custom-tab-1">
                                        <ul class="nav nav-tabs">
                                            <li class="nav-item"><a href="#transaction-pin" data-toggle="tab" class="nav-link active show">Transaction Pin</a></li>
                                            <li class="nav-item"><a href="#security-question" data-toggle="tab" class="nav-link">Security Question</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="transaction-pin" class="tab-pane fade active show">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <h4 class="text-primary">Transaction Pin</h4>
                                                        <form id="transactionPinUpdate" method="POST" onsubmit="updateTransactionPin(); return false;">
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Pin</label>
                                                                    <input type="password"  id="transactionPin" class="form-control" maxlength="4" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Confirm Pin</label>
                                                                    <input type="password"  id="confirmTransactionPin" class="form-control" maxlength="4" required>
                                                                </div>
                                                            </div>
                                                            
                                                            <button class="btn btn-primary" id="buttonSavePin" type="submit">Reset</button>
                                                            <button class="btn btn-primary loading_spinners" type="button" style="display: none;"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Saving</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                
                                            <div id="security-question" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <h4 class="text-primary">Security Question</h4>
                                                        <form  method="POST" onsubmit="updateSecurityQA(); return false;">
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Secret Question</label>
                                                                    <input type="text"  id="securityQuestion" class="form-control" maxlength="50" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Secret Answer <small class="text-danger">*answers are case sensitive</small></label>
                                                                    <input type="text"  id="securityAnswer" maxlength="50" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            
                                                            <button class="btn btn-primary" id="buttonSaveQuestion" type="submit">Reset</button>
                                                            <button class="btn btn-primary loading_spinners" type="button" style="display: none;"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Saving</button>
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
		
	<!-- Dashboard 1 -->
	<script src="js/dashboard/my-wallet.js"></script>
	
    <!-- Datatable -->

</body>

</html>