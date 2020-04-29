<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD -->

    <head>
        <?php include_once 'includes/header_css.php'; ?>
    </head>
    <!-- END HEAD -->
    <body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md header-blue">
        <div class="page-wrapper">
            <!-- start header -->
            <?php include_once 'includes/header_menu.php'; ?>
            <!-- end header -->
            <!-- start page container -->
            <div class="page-container">
                <!-- start sidebar menu -->
                <?php include_once 'includes/side_menu.php'; ?>
                <!-- end sidebar menu -->
                <!-- start page content -->
                <div class="page-content-wrapper">
                    <div class="page-content">
                        <div class="page-bar">
                            <div class="page-title-breadcrumb">
                                <div class=" pull-left">
                                    <div class="page-title">System users</div>
                                </div>
                                <div class="actions panel_actions pull-right">
                                    <a class="btn btn-primary" href="index.php?page=<?php echo $crypt->encode("view_users"); ?>"><i class="fa fa-eye"></i> View Users</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-8">
                                <?php
                                if (Input::exists() && Input::get("add_user") == "add_user") {
                                    $username = Input::get("username");
                                    $password = Input::get("password");
                                    $user_role = Input::get("user_role");
                                    $staff_id = Input::get("staff_id");
                                    $modules_accessed = serialize(Input::get("modules_accessed"));
                                    $queryDup = "SELECT * FROM user WHERE (Staff_Id='$staff_id' OR Username='$username') AND Status=1";
                                    if (DB::getInstance()->checkRows($queryDup)) {
                                        //Duplicate
                                        echo '<div class="alert alert-warning">User Account already exists</div>';
                                    } else {
                                        $queryInsert = DB::getInstance()->insert("user", array(
                                            'Username' => $username,
                                            'Password' => SHA1($password),
                                            'User_Role' => $user_role,
                                            "Modules_Accessed" => $modules_accessed,
                                            'Staff_Id' => $staff_id
                                        ));
                                        if ($queryInsert) {
                                            echo '<div class="alert alert-success">User Account registered successfully</div>';
                                        }
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode("add_user"));
                                }
                                ?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>User Account entry form</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="" enctype="multipart/form-data">

                                            <div class="col-md-12 col-sm-12 col-xs-12">

                                                <div class="form-group">
                                                    <label>Staff Names</label>
                                                    <div class="controls">
                                                        <select class="select2" style="width: 100%" name="staff_id" required>
                                                            <option value="">Select..</option>
                                                            <?php
                                                            $staffCheck = "SELECT * FROM staff,person WHERE person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                                            $staff_list = DB::getInstance()->query($staffCheck);
                                                            foreach ($staff_list->results() as $staff):
                                                                echo '<option value="' . $staff->Staff_Id . '">' . $staff->Title . ' ' . $staff->Fname . ' ' . $staff->Lname . '</option>';
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Username</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="username" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Password</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" value="<?php echo generatePassword(); ?>" name="password" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Position/ User Role</label>
                                                    <div class="controls">
                                                        <select class="form-control" name="user_role" required>
                                                           <option value="">Select..</option>
                                                            <option value="Admin">Admin</option>
                                                            <option value="Pharmacist">Manager</option>
                                                            <option value="Accountant">Accountant</option>
                                                            <option value="Receptionist">Receptionist</option>
                                                            <option value="Secretory">Secretory</option>
                                                            <option value="Others">Others</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Module(s) to be accessed <label class="btn btn-warning btn-xs" id="selectControl" onclick="modulesSelected()">select all</label></label>
                                                    <div class="controls">
                                                        <select class="select2" id="modules_accessed" multiple style="width: 100%" name="modules_accessed[]" required>
                                                            <?php
                                                            //Modules array in the init file
                                                            for ($x = 0; $x < count($modules_list_array); $x++) {
                                                                echo '<option value="' . $modules_list_array[$x] . '">' . $modules_list_array[$x] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="pull-right ">
                                                    <button type="submit" name="add_user" value="add_user" class="btn btn-success">Save</button>
                                                    <button type="reset" class="btn">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page content -->
            </div>
            <!-- end page container -->
            <!-- start footer -->
            <?php include_once 'includes/footer.php'; ?>
            <!-- end footer -->
        </div>
        <!-- start js include path -->
        <script>
                                                        function modulesSelected() {
                                                            var modulesAccessedElement = document.getElementById('modules_accessed');
                                                            var selectedValues = new Array();
                                                            for (var i = 0; i < modulesAccessedElement.options.length; i++) {
                                                                selectedValues.push(modulesAccessedElement.options[i].value);
                                                                modulesAccessedElement.getElementsByTagName('option')[i].selected = true;
                                                            }
                                                            selectedValues = (selectControl.innerHTML === "select all") ? selectedValues : null;
                                                            $('#modules_accessed').val(selectedValues).trigger('change');
                                                            selectControl.innerHTML = (selectControl.innerHTML === "select all") ? "unselect all" : 'select all';
                                                        }
        </script>
        <?php include_once 'includes/footer_js.php'; ?>
        <!-- end js include path -->
    </body>

</html>