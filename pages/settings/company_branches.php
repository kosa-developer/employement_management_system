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
                                    <div class="page-title">Add Company Branches</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="row">
                                    <?php
                                    //deletng the procedure
                                    if (isset($_GET['action']) && $_GET['action'] == $crypt->encode("remove_branch") && $_GET['Branch_Id'] != "") {
                                        $Branch_Id = $crypt->decode($_GET['Branch_Id']);
                                        $deletebranch = DB::getInstance()->query("delete from branch where Branch_Id='$Branch_Id'");
                                        if ($deletebranch) {
                                            echo '<div class="alert alert-success"> branch deleted successfully</div>';
                                        }
                                        Redirect::go_to("index.php?page=" . $crypt->encode("company_branches"));
                                    }
                                    if (Input::exists()) {
//                                    Editing procedure names
                                        if (Input::get("edit_branch") == "edit_branch") {
                                            $Branch_Id = Input::get("Branch_Id");
                                            $branch_name = Input::get("Branch_Name");
                                            
                                            $branchUpdate = DB::getInstance()->update("branch", $Branch_Id, array(
                                                "Branch_Name" => $branch_name
                                                    ), "Branch_Id");
                                            if ($branchUpdate) {
                                                echo '<div class="alert alert-success"> branch details updated successfully</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("company_branches"));
                                        }
//                                    adding the new procedure
                                        if ((Input::get("submit_branch"))) {
                                            $branch_name = Input::get("Branch_Names");
                                            $branch_added = 0;
                                            $duplicates = 0;
                                            if (!empty($branch_name)) {
                                                for ($x = 0; $x < count($branch_name); $x++) {
                                                    $queryDup = DB::getInstance()->checkRows("SELECT * FROM branch WHERE Branch_Name='$branch_name[$x]' ");
                                                    if ($queryDup) {
                                                        $duplicates++;
                                                    } else {
                                                         $branchInsert = DB::getInstance()->insert("branch", array(
                                                            "Branch_Name" => $branch_name[$x]));
                                                        if ($branchInsert) {
                                                            $branch_added++;
                                                        }
                                                    }
                                                }
                                            }
                                            if ($branch_added != 0) {
                                                echo '<div class="alert alert-success col-sm-6">' . $branch_added . ' branch successfully registered</div>';
                                            } if ($duplicates != 0) {
                                                echo '<div class="alert alert-warning col-sm-6">' . $duplicates . ' Duplicates could not be registered</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("company_branches"));
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body " id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#tab_new" data-toggle="tab">
                                                    <i class="fa fa-pencil"></i> Add New branch
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_viewprocedures" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Added branchs
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content primary">
                                            <div class="tab-pane fade in active" id="tab_new">
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Branch Name<button type="button" class="btn btn-success btn-xs pull-right" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="Branch_Names[]" required> 
                                                                            </td>
                                                                            
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_procedure" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_branch" value="submit_branch">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade" id="tab_viewprocedures">
                                                <?php
                                                $querybranch = "SELECT * FROM branch  ORDER BY Branch_Id DESC";
                                                if (DB::getInstance()->checkRows($querybranch)) {
                                                    ?>                                                    
                                                    <table id="example1" class="table table-striped table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>branch Name</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $branch_list = DB::getInstance()->querySample($querybranch);
                                                            $no = 0;
                                                            foreach ($branch_list as $branch) {
                                                                $no++;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $no ?></td>
                                                                    <td><?php echo $branch->Branch_Name; ?></td>
                                                                    <td> 
                                                                        <a data-toggle="modal"  href="#edit_<?php echo $branch->Branch_Id ?>">
                                                                            <i class="fa fa-pencil"></i> Edit</a>&nbsp&nbsp&nbsp;
                                                                        <a href="index.php?page=<?php echo $crypt->encode('company_branches') . '&action=' . $crypt->encode('remove_branch') . '&Branch_Id=' . $crypt->encode($branch->Branch_Id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Do you really want to Delete this branch?');"><i class="fa fa-trash-o"></i> Delete</a> 
                                                                    </td>
                                                            <div class="modal fade" id="edit_<?php echo $branch->Branch_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                                <form action="" method="post">
                                                                    <div class="modal-dialog animated fadeInDown">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                <h4 class="modal-title">Edit <?php echo $branch->Branch_Name; ?>'s&nbsp;Information</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Branch Name</label>
                                                                                    <input type="hidden" name="Branch_Id" value="<?php echo $branch->Branch_Id ?>">
                                                                                    <input type="text" class="form-control" name="Branch_Name" value="<?php echo $branch->Branch_Name ?>">
                                                                                </div>
                                                                                
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                                <button type="submit" name="edit_branch" value="edit_branch"class="btn btn-success" type="button">Save changes</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>


                                                    <?php
                                                } else {
                                                    echo '<div class="alert alert-warning">No branch registered</div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
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
            <?php include_once 'includes/footer.php';?>
            <!-- end footer -->
        </div>
        <!-- start js include path -->
        <?php include_once 'includes/footer_js.php'; ?>
        <!-- end js include path -->
        <script>
                                                                            function add_element() {
                                                                                var row_ids = Math.round(Math.random( ) * 300000000);
                                                                                document.getElementById('add_element').insertAdjacentHTML('beforeend',
                                                                                        '<tr id="' + row_ids + '">\n\
                    <td>\n\
                        <input name="Branch_Names[]" class="form-control" type="text" required><button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button> </tr>');

                                                                            }
                                                                            function delete_item(element_id) {
                                                                                $('#' + element_id).html('');
                                                                            }
    </script>
    </body>

</html>