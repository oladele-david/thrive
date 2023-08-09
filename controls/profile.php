<?php
   require_once('../includes/autoload.php');

   if (session_status() != PHP_SESSION_ACTIVE)
       session_start();
   $account = new Account();
   $country = new Country();
   $state = new State();
   $bank = new Bank();

    $userInSession = $_SESSION['userInSession'];
   $accountId  = (isset($_REQUEST['accountId'])) ? $_REQUEST['accountId'] : $_POST['accountId'] ;

    if(empty($_SESSION['userInSession']))
    {
        header("Location: signin.php");
        
        die("Redirecting to signin.php");
    }
    
    if(isset($_POST['updatePassword'])) {
        $password = $_POST['password'];

        $updatePassword = $account->updatePassword($accountId, $password);
        ob_clean();
        echo $updatePassword;              
        exit;
    }

    if (isset($_POST['updateProfile'])) {
       
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $otherName = $_POST['otherName'];
        $motherMaidenName = $_POST['motherMaidenName'];
        $bvn = $_POST['bvn'];
        $nin = $_POST['nin'];
        $phoneNo = $_POST['phoneNo'];
        $emailId = $_POST['emailId'];
        $gender = $_POST['gender'];
        $dateOfBirth = $_POST['dateOfBirth'];
        $address = $_POST['address'];
        $countryId = $_POST['countryId'];
        $stateId = $_POST['stateId'];
        $city = $_POST['city'];
        $defaultBankId = $_POST['defaultBankId'];
        $bankAccountNo = $_POST['bankAccountNo'];
        $bankAccountName = $_POST['bankAccountName'];
        $reeveAccountNo = $_POST['reeveAccountNo'];

        $updateAccount = $account->updateAccount($accountId, $reeveAccountNo, $lastName, $firstName, $otherName, $motherMaidenName, $bvn, $nin, $gender, $dateOfBirth, $address, $city, $stateId, $countryId, $phoneNo, $emailId, $defaultBankId, $bankAccountNo, $bankAccountName);
        ob_clean();
        echo $updateAccount;              
        exit;
        
    }
    
    $pageTitle = "Profile";

    $data_account = $account->getAccountById($accountId);
    $listCountries = $country->listCountries();
    $listStates = $state->listStates();
    $data_banks = $bank->listBanks();
    $data_countries = $listCountries['countries'];
    $data_states = $listStates['states'];
    // require_once('objects/accountRESTful.php');
    // require_once('objects/bankRESTful.php');
    // require_once('objects/countryRESTful.php');
    // require_once('objects/stateRESTful.php');

    $dateOfBirth = $data_account['date_of_birth'];
    $today = date("Y-m-d");
    $date_diff = date_diff(date_create($dateOfBirth), date_create($today));
    $currentAge = $date_diff->format('%y');
?>

<?php include('includes/header.php') ?>
<?php include('includes/sidebar.php') ?>

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <div class="container-fluid">
                <div class="page-titles">
					<h4>Profile</h4>
					<ol class="breadcrumb">
						<li class="breadcrumb-item active"><a href="users.php">Users</a></li>
						<li class="breadcrumb-item "><a href="javascript:void(0)">User Profile</a></li>
					</ol>
                </div>
                <!-- row -->
                <?php // include('includes/alerts.php') ?>

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
											<h4 class="text-primary mb-0"><?php echo ucfirst($data_account['last_name']) . " " . ucfirst($data_account['first_name']) ?></h4>
											<p>User</p>
										</div>
										<div class="profile-email px-2 pt-2">
											<h4 class="text-muted mb-0"><?php echo $data_account['email_id']?></h4>
											<p>Email</p>
										</div>
										<!-- <div class="dropdown ml-auto">
											<a href="#" class="btn btn-primary light sharp" data-toggle="dropdown" aria-expanded="true"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg></a>
											<ul class="dropdown-menu dropdown-menu-right">
												<li class="dropdown-item"><i class="fa fa-user-circle text-primary mr-2"></i> View profile</li>
												<li class="dropdown-item"><i class="fa fa-users text-primary mr-2"></i> Add to close friends</li>
												<li class="dropdown-item"><i class="fa fa-plus text-primary mr-2"></i> Add to group</li>
												<li class="dropdown-item"><i class="fa fa-ban text-primary mr-2"></i> Block</li>
											</ul>
										</div> -->
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
                                            <li class="nav-item"><a href="#about-me" data-toggle="tab" class="nav-link active show">About Me</a></li>
                                            <li class="nav-item"><a href="#profile-settings" data-toggle="tab" class="nav-link">Settings</a></li>
                                            <li class="nav-item"><a href="#change-password" data-toggle="tab" class="nav-link">Change Password</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="about-me" class="tab-pane fade active show">
                                                <br>
                                                <div class="profile-personal-info">
                                                    <h4 class="text-primary mb-4">Personal Information</h4>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">Name <span class="pull-right d-none d-sm-block">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo ucwords($data_account['last_name'] ." ". $data_account['first_name'] ." ". $data_account['other_name'])?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">Email <span class="pull-right d-none d-sm-block">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo $data_account['email_id']?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">Phone No. <span class="pull-right d-none d-sm-block">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo $data_account['phone_no']?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">Age <span class="pull-right d-none d-sm-block">:</span>
                                                            </h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo $currentAge ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">Location <span class="pull-right d-none d-sm-block">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo ucfirst($data_account['address']) ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">Gender <span class="pull-right d-none d-sm-block">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo ucwords($data_account['gender'])?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">Thrive Account No <span class="pull-right d-none d-sm-block">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo $data_account['account_number']?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">BVN <span class="pull-right d-none d-sm-block">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo $data_account['bvn']?></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-sm-2">
                                                        <div class="col-sm-4">
                                                            <h5 class="f-w-500">NIN <span class="pull-right d-none d-sm-block">:</span></h5>
                                                        </div>
                                                        <div class="col-sm-8"><span><?php echo $data_account['nin']?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="profile-settings" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <h4 class="text-primary">Account Settings</h4>
                                                        <form method="POST" onsubmit="updateProfile(); return false;">
                                                            <input type="hidden" id="accountId" class="form-control" value="<?php echo $data_account['id']?>">

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>First Name</label>
                                                                    <input type="text" required id="firstName" class="form-control" value="<?php echo $data_account['first_name']?>">
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Last Name</label>
                                                                    <input type="text" required id="lastName" class="form-control" value="<?php echo $data_account['last_name']?>">
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Other Name</label>
                                                                    <input type="text" required id="otherName" class="form-control" value="<?php echo $data_account['other_name']?>">
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Mother Maiden Name</label>
                                                                    <input type="text" required id="motherMaidenName" class="form-control" value="<?php echo $data_account['mother_maiden_name']?>">
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>BVN</label>
                                                                    <input type="text" required id="bvn" class="form-control" value="<?php echo $data_account['bvn']?>">
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>NIN</label>
                                                                    <input type="text" required id="nin" class="form-control" value="<?php echo $data_account['nin']?>">
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Phone No</label>
                                                                    <input type="text" required id="phoneNo" class="form-control"value="<?php echo $data_account['phone_no']?>" readonly>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Email</label>
                                                                    <input type="email" required id="emailId" class="form-control" value="<?php echo $data_account['email_id']?>" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Gender</label>
                                                                    <select class="form-control default-select" id="gender" required>
                                                                        <option selected="">Choose...</option>
                                                                        <option <?php if ($data_account['gender'] == "female") { echo "selected" ; } ?> value="female">Female</option>
                                                                        <option <?php if ($data_account['gender'] == "male") { echo "selected" ; } ?> value="male">Male</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Date Of Birth</label>
                                                                    <input type="date" required id="dateOfBirth" class="form-control" value="<?php echo $data_account['date_of_birth']?>">
                                                                </div>
                                                            </div>
                                                        
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Country</label>
                                                                    <select class="form-control default-select" id="countryId" disabled>
                                                                        <option value="">Choose...</option>
                                                                        <?php
                                                                            foreach($data_countries as $key => $val) {
                                                                        ?>
                                                                            <option <?php if("00001" == $val['id']) {echo "selected";} ?> value="<?php echo $val['id'] ?>"><?php echo $val['country_name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-md-4">
                                                                    <label>State</label>
                                                                    <select class="form-control default-select" id="stateId" required>
                                                                        <option value="">Choose...</option>
                                                                        <?php
                                                                            foreach($data_states as $key => $val) {
                                                                        ?>
                                                                            <option <?php if($data_account['state_id'] == $val['id']) {echo "selected";} ?> value="<?php echo $val['id'] ?>"><?php echo $val['state_name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-md-2">
                                                                    <label>City</label>
                                                                    <input type="text" id="city" required class="form-control" value="<?php echo $data_account['city']?>">
                                                                </div>
                                                            </div>

                                                            <div class="form-group">
                                                                <label>Address</label>
                                                                <input type="text" required id="address" class="form-control" value="<?php echo $data_account['address']?>">
                                                            </div>

                                                            <div class="form-row">
                                                                <div class="form-group col-md-4">
                                                                    <label>Default Bank</label>
                                                                    <select class="form-control default-select" id="defaultBankId" required>
                                                                        <option value="">Choose...</option>
                                                                        <?php
                                                                            foreach($data_banks['banks'] as $key => $val) {
                                                                        ?>
                                                                            <option <?php if($data_account['default_bank_id'] == $val['id']) {echo "selected";} ?> value="<?php echo $val['id'] ?>"><?php echo $val['bank_name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-md-5">
                                                                    <label>Account Name</label>
                                                                    <input type="text" id="bankAccountName" required class="form-control" value="<?php echo $data_account['bank_account_name']?>">
                                                                </div>
                                                                <div class="form-group col-md-3">
                                                                    <label>Account No</label>
                                                                    <input type="text" id="bankAccountNo" required class="form-control" value="<?php echo $data_account['bank_account_no']?>">
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                                <div class="form-group col-md-3">
                                                                    <label>Thrive Account No</label>
                                                                    <input type="text" value="<?php echo $data_account['account_number']  ?>" id="reeveAccountNo" class="form-control" readonly>
                                                                </div>
                                                            </div>

                                                            <!-- <div class="form-group">
                                                                <div class="custom-control custom-checkbox">
																	<input type="checkbox" class="custom-control-input" id="gridCheck">
																	<label class="custom-control-label" for="gridCheck"> Check me out</label>
																</div>
                                                            </div> -->
                                                            <button class="btn btn-primary" id="buttonSave" type="submit">Save</button>
                                                            <button class="btn btn-primary" id="loading_spinner" type="submit" style="display: none;"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Saving</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="change-password" class="tab-pane fade">
                                                <div class="pt-3">
                                                    <div class="settings-form">
                                                        <h4 class="text-primary">Change Password</h4>
                                                        <form id="passwordUpdate" method="POST" onsubmit="updatePassword(); return false;">
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label>Password</label>
                                                                    <input type="password"  id="password" class="form-control" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label>Confirm Password</label>
                                                                    <input type="password"  id="confirmPassword" class="form-control" required>
                                                                </div>
                                                            </div>
                                                            
                                                            <button class="btn btn-primary" id="buttonSavePassword" type="submit">Reset</button>
                                                            <button class="btn btn-primary" id="loading_spinners" type="submit" style="display: none;"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Saving</button>
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
   
	<script src="js/ajax-scripts.js?<?php echo time() ?>"></script>
		
	
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