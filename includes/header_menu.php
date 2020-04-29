<?php
if ((empty($_SESSION['security_user_id'])) && (empty($_SESSION['security_immergencepassword']))) {
    Redirect::to('index.php?page=' . $crypt->encode("logout"));
}
$all = 0;
//assets
$previous_page = $_SESSION["PREVIOUS_URL"];
?>

<div class="page-header navbar navbar-fixed-top" onload="openModel()">
    <div class="page-header-inner ">
        <!-- logo start -->
        <div class="page-logo">
            <a href="index.php?page=<?php echo $crypt->encode("dashboard") ?>">
                <span class="logo-icon fa fa-hospital-o fa-rotate-left"></span>
                <span class="logo-default" >Millenium </span> </a>
        </div>
        <!-- logo end -->
        <ul class="nav navbar-nav navbar-left in">
            <li><a href="javascript:void(0)" class="menu-toggler sidebar-toggler"><i class="icon-menu"></i></a></li>
        </ul>
        
        <!-- start mobile menu -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>
        <!-- end mobile menu -->
        <!-- start header menu -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
           <!-- start notification dropdown -->
              
                <!-- end notification dropdown -->
                <!-- start manage user dropdown -->
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" class="img-circle " src="images/staff/<?php echo $_SESSION['security_profile_picture'] ?>" />
                        <span class="username username-hide-on-mobile"><?php echo $_SESSION['security_staff_names']; ?></span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <?php if (!isset($_SESSION['security_immergencepassword'])) { ?>
                            <li>
                                <a href="index.php?page=<?php echo $crypt->encode('staff_profile') . '&staff_id=' . $crypt->encode($_SESSION['security_staff_id']); ?>"><i class="fa fa-user"></i>Profile</a>
                            </li>
                            <li>
                                <a data-toggle="modal" href="#account_settings"><i class="fa fa-gear"></i>Account Update</a>
                            </li>
                        <?php } ?>
                        <li class="divider"> </li>
                        <li>
                            <a href="index.php?page=<?php echo $crypt->encode("logout"); ?>">
                                <i class="fa fa-lock"></i> Log Out </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php
if (Input::exists() && Input::get("edit_my_account") == "edit_my_account") {
    $user_id = $_SESSION['security_user_id'];
    $user = $_SESSION['security_username'];
    $username = Input::get("username");
    $old_password = sha1(Input::get('old_password'));
    $new_password = sha1(Input::get('new_password'));
    $queryUpdateUser = DB::getInstance()->checkRows("select * from user where  Username='$user' AND Password='$old_password'");
    if ($queryUpdateUser) {
        $queryUpdateCheck = DB::getInstance()->checkRows("select * from user where User_Id!='$user_id' AND Username='$username'");
        if ($queryUpdateCheck) {
            $message = "The username entered is already used by another person";
        } else {
            $queryUpdate = DB::getInstance()->query("update  user set Password='$new_password',Username='$username' where  Username='$user'");
            if ($queryUpdate) {
                $message = "Your account user credentials have been changed successfully";
                $_SESSION['security_username'] = $username;
            } else {
                $message = "Could not update your user credentials, please try again later";
            }
        }
    } else {
        $message = "Incorrect old password";
    }
    redirect($message, "");
}
?>
<div class="modal fade" data-backdrop="static" id="account_settings" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
    <div class="modal-dialog animated fadeInDown">
        <div class="modal-content animated">
            <form action="" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title">Account Details for <b style="color: blue"><?php echo $_SESSION['security_staff_names'] ?></b></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="hidden" value="<?php echo $_SESSION['security_user_id']; ?>" name="user_id" class="form-control">
                                <input type="text" value="<?php echo $_SESSION['security_username']; ?>" name="username" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Old Password</label> 
                                <input type="password" name="old_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>New Password:</label>
                                <input type="password" name="new_password" id="new_password" pattern="[a-zA-Z0-9]{8,}" title="Password must be a minimum of 8 letters" onkeyup="emptyConfirm();" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Re-Enter password:</label>
                                <input type="password" id="confirm_password" required onkeyup="compare_password(this.value);" class="form-control" pattern="[a-zA-Z0-9]{8,}" title="Password must be a minimum of 8 letters">
                            </div>
                            <strong id="check_password" style="color: red">Passwords enetered must match</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" name="edit_my_account" id="edit_my_account_button" disabled value="edit_my_account" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
if (Input::exists() && Input::get("set_recovery_name") == "set_recovery_name") {
    $recovery_name = $crypt->encode(Input::get("recovery_name"));
    DB::getInstance()->update("user", $_SESSION["security_user_id"], array("Recovery_Option" => $recovery_name), "User_Id");
    redirect("Recovery option set successfully", "");
}
if (isset($_SESSION["security_user_id"])) {
    $recovery_option = DB::getInstance()->getName("user", $_SESSION["security_user_id"], "Recovery_Option", "User_Id");
    if ($recovery_option == "" || $recovery_option == "NA") {
        ?>
        <div class="modal fade bs-modal-sm" id="set_recovery_name_div" data-backdrop="static" tabindex="-1" role="dialog" data-show="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content animated">
                    <form action="" method="POST">
                        <div class="modal-header">
                            <h4 class="modal-title">Set password recovery option</h4>
                            <p>This may be a simple sentense or word you cannot forget</p>
                        </div>
                        <div class="modal-body">
                            <input type="text" class="form-control" name="recovery_name" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="set_recovery_name" value="set_recovery_name" class="btn btn-success pull-right">OK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}
?>

<script>
    function compare_password(confirm) {
        if (confirm !== "") {
            var old_password = document.getElementById("new_password").value;
            if (confirm === old_password) {
                document.getElementById("edit_my_account_button").disabled = false;
                document.getElementById("check_password").style.display = "none";
            } else {
                document.getElementById("edit_my_account_button").disabled = true;
                document.getElementById("check_password").style.display = "block";
            }

        } else {
            document.getElementById("edit_my_account_button").disabled = true;
            document.getElementById("check_password").style.display = "block";
        }
    }
    function emptyConfirm() {
        document.getElementById("confirm_password").value = "";
        document.getElementById("edit_my_account_button").disabled = true;
    }
</script>