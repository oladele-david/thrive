<?php
    require_once('../includes/autoload.php');

	if (session_status() != PHP_SESSION_ACTIVE)
		session_start();
	$admin = new Admin();
	
	$userInSession = $_SESSION['userInSession'];
	$lastName = $_SESSION['lastName'];
	$firstName = $_SESSION['firstName'];
	$emailId = $_SESSION['emailId'];
	$phoneNo = $_SESSION['phoneNo'];
	
	if(empty($_SESSION['userInSession']))
	{
		header("Location: ../signin.php");
	
		die("Redirecting to signin.php");
	}
	$pageTitle = "Home";
	
	// require_once('objects/accountRESTful.php');
	$data_admin = $admin->getAdminById($userInSession);
	

    if(!$data_admin){
        header("location: ../signin.php");
        //echo "<iframe src='authorize.php' width='100%' height='100%' frameborder='0' noresize='noresize'></iframe>";
    }
    else {
        header("location: dashboard.php");
        //echo "<iframe src='webtop.php' width='100%' height='100%' frameborder='0' noresize='noresize'></iframe>";
    }
?>