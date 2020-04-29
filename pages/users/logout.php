<?php
session_start();
unset($_SESSION['security_role']);
unset($_SESSION['security_username']);
unset($_SESSION['security_immergencepassword']);
unset($_SESSION['security_user_id']);
unset($_SESSION['security_staff_id']);
unset($_SESSION['security_staff_names']);
unset($_SESSION['security_profile_picture']);
unset($_SESSION["PREVIOUS_URL"]);
$user=new User();
$user->logout();
Redirect::to('index.php?page='.$crypt->encode("login"));
?>