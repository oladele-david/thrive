<?php
    // Initialize the session.
    // If you are using session_name("something"), don't forget it now!
    if(session_status() != PHP_SESSION_ACTIVE)
        session_start();

    session_destroy();
    header("Location: signin.php");
?>