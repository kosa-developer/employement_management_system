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
                                    <div class="page-title">System Users</div>
                                </div>
                                <div class="actions panel_actions pull-right">
                                    <a class="btn btn-primary" href="index.php?page=<?php echo $crypt->encode("add_user"); ?>"><i class="fa fa-plus"></i> Add User</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>List of all registered users</header>
                                    </div>
                                    <div class="card-body ">
                                        <?php
                                        if (Input::exists() && Input::get("edit_user_modules") == "edit_user_modules") {
                                            $user_id = Input::get("user_id");
                                            $modules_accessed = serialize(Input::get("modules_accessed"));
                                            DB::getInstance()->update('user', $user_id, array('Modules_Accessed' => $modules_accessed), 'User_Id');
                                            echo '<div class="alert alert-success">User Modules accessed updated successfully</div>';
                                            Redirect::go_to('index.php?page=' . $crypt->encode("view_users"));
                                        }
                                        if (isset($_GET['action']) && $_GET['action'] == "delete_user" && $_GET['user_id'] != "") {
                                            $user_id = $crypt->decode($_GET['user_id']);
                                            $updateUser = DB::getInstance()->update('user', $user_id, array('Status' => 0), 'User_Id');
                                            echo '<div class="alert alert-warning">User successfully deleted</div>';
                                            Redirect::go_to('index.php?page=' . $crypt->encode("view_users"));
                                        }
                                        $usersCheck = "SELECT * FROM user,staff,person WHERE person.Person_Id=staff.Person_Id AND user.Staff_Id=staff.Staff_Id AND user.Status=1 ORDER BY person.Fname";
                                        if (DB::getInstance()->checkRows($usersCheck)) {
                                            ?>
                                            <table class="table table-striped table-bordered table-responsive" id="example1" >
                                                <thead>
                                                    <tr>
                                                        <th>Staff Names</th>
                                                        <th>Email Address</th>
                                                        <th>Username</th>
                                                        <th>Role</th>
                                                        <th>Modules Accessed</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $users_list = DB::getInstance()->query($usersCheck);
                                                    foreach ($users_list->results() as $users):
                                                        $modules_accessed = "";
                                                        $modules_accessed_list = unserialize($users->Modules_Accessed);
                                                        for ($i = 0; $i < count($modules_accessed_list); $i++) {
                                                            $modules_accessed .= ($i != count($modules_accessed_list) - 1) ? $modules_accessed_list[$i] . ", " : $modules_accessed_list[$i];
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $users->Title . '.  ' . $users->Fname . ' ' . $users->Lname ?></td>
                                                            <td><?php echo $users->Email ?></td>
                                                            <td><?php echo $users->Username ?></td>
                                                            <td><?php echo $users->User_Role ?></td>
                                                            <td><?php echo $modules_accessed ?>

                                                                <div class="panel-group primary" id="accordion-<?php echo $users->User_Id ?>" role="tablist" aria-multiselectable="true">
                                                                    <div class="" role="tab" id="headingThree<?php echo $users->User_Id ?>">
                                                                        <a class="btn btn-warning btn-xs collapsed" data-toggle="collapse" data-parent="#accordion-<?php echo $users->User_Id ?>" href="#collapseThree-<?php echo $users->User_Id ?>" aria-expanded="false" aria-controls="collapseThree-<?php echo $users->User_Id ?>">
                                                                            <i class='fa fa-pencil'></i> Edit modules accessed
                                                                        </a>
                                                                    </div>
                                                                    <div id="collapseThree-<?php echo $users->User_Id ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree<?php echo $users->User_Id ?>">
                                                                        <div class="panel-body">
                                                                            <form action="" method="POST">
                                                                                <input type="hidden" name="user_id" value="<?php echo $users->User_Id ?>">
                                                                                <div class="form-group" style="width: 100%">
                                                                                    <label>Module(s) to be accessed <label class="btn btn-primary btn-xs" id="selectControl<?php echo $users->User_Id ?>" onclick="modulesSelected('<?php echo $users->User_Id ?>',this)">select all</label></label>
                                                                                    <div class="controls">
                                                                                        <select class="select2" id="modules_accessed<?php echo $users->User_Id ?>" multiple style="width: 100%" name="modules_accessed[]" required>
                                                                                            <?php
                                                                                            //Modules array in the init file
                                                                                            for ($x = 0; $x < count($modules_list_array); $x++) {
                                                                                                $selected = (in_array($modules_list_array[$x], $modules_accessed_list)) ? " selected" : "";
                                                                                                echo '<option value="' . $modules_list_array[$x] . '" ' . $selected . '>' . $modules_list_array[$x] . '</option>';
                                                                                            }
                                                                                            ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <button type="submit" class="btn btn-success btn-xs" name="edit_user_modules" value="edit_user_modules">Save changes</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </td>
                                                            <td>
                                                                <?php
                                                                if (($users->User_Role != "Admin" && $users->User_Role != "Super Admin")) {
                                                                    ?>
                                                                    <a href="index.php?page=<?php echo $crypt->encode("view_users") . '&action=delete_user&user_id=' . $crypt->encode($users->User_Id) ?>" class="label label-danger" style="" onclick="return confirm('Do you really want to remove this user from the system')"><i class="fa fa-times"></i> Remove</a>
                                                                <?php }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>

                                            <?php
                                        } else {
                                            echo '<div class="alert alert-warning">No User Details registered</div>';
                                        }
                                        ?>
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
        <?php include_once 'includes/footer_js.php'; ?>
        <script>
                                                        function modulesSelected(user_id,selectControlId) {
                                                            var modulesAccessedElement = document.getElementById('modules_accessed'+user_id);
                                                                var selectedValues=new Array();
                                                                for (var i = 0; i < modulesAccessedElement.options.length; i++) {
                                                                    selectedValues.push(modulesAccessedElement.options[i].value);
                                                                    modulesAccessedElement.getElementsByTagName('option')[i].selected = true;
                                                                }
                                                                selectedValues=(selectControlId.innerHTML === "select all")?selectedValues:null;
                                                                $('#modules_accessed'+user_id).val(selectedValues).trigger('change');
                                                            selectControlId.innerHTML = (selectControlId.innerHTML === "select all") ? "unselect all" : 'select all';
                                                        }
        </script>
        <!-- end js include path -->
    </body>

</html>