<?php
    require_once('utilities/appServer.php');
    if(session_status() != PHP_SESSION_ACTIVE)
    session_start();

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
    $pageTitle = "Template";

    require_once('objects/accountRESTful.php');

?>

<?php include('includes/header.php') ?>
<?php include('includes/sidebar.php') ?>

<?php include('includes/alerts.php') ?>

<?php include('includes/footer.php') ?>
