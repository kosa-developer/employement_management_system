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
                                    <div class="page-title">Add Banks</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="row">
                                    <?php
                                    //deletng the procedure
                                    if (isset($_GET['action']) && $_GET['action'] == $crypt->encode("remove_bank") && $_GET['Bank_Id'] != "") {
                                        $Bank_Id = $crypt->decode($_GET['Bank_Id']);
                                        $deletebank = DB::getInstance()->query("delete from bank where Bank_Id='$Bank_Id'");
                                        if ($deletebank) {
                                            echo '<div class="alert alert-success"> Bank deleted successfully</div>';
                                        }
                                        Redirect::go_to("index.php?page=" . $crypt->encode("add_bank"));
                                    }
                                    if (Input::exists()) {
//                                    Editing procedure names
                                        if (Input::get("edit_bank") == "edit_bank") {
                                            $Bank_Id = Input::get("Bank_Id");
                                            $bank_name = Input::get("Bank_Name");
                                            
                                            $bankUpdate = DB::getInstance()->update("bank", $Bank_Id, array(
                                                "Bank_Name" => $bank_name
                                                    ), "Bank_Id");
                                            if ($bankUpdate) {
                                                echo '<div class="alert alert-success"> bank details updated successfully</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("add_bank"));
                                        }
//                                    adding the new procedure
                                        if ((Input::get("submit_bank"))) {
                                            $bank_name = Input::get("bank_names");
                                            $bank_added = 0;
                                            $duplicates = 0;
                                            if (!empty($bank_name)) {
                                                for ($x = 0; $x < count($bank_name); $x++) {
                                                    $queryDup = DB::getInstance()->checkRows("SELECT * FROM bank WHERE Bank_Name='$bank_name[$x]' ");
                                                    if ($queryDup) {
                                                        $duplicates++;
                                                    } else {
                                                         $bankInsert = DB::getInstance()->insert("bank", array(
                                                            "Bank_Name" => $bank_name[$x]));
                                                        if ($bankInsert) {
                                                            $bank_added++;
                                                        }
                                                    }
                                                }
                                            }
                                            if ($bank_added != 0) {
                                                echo '<div class="alert alert-success col-sm-6">' . $bank_added . ' bank successfully registered</div>';
                                            } if ($duplicates != 0) {
                                                echo '<div class="alert alert-warning col-sm-6">' . $duplicates . ' Duplicates could not be registered</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("add_bank"));
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body " id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#tab_new" data-toggle="tab">
                                                    <i class="fa fa-pencil"></i> Add New Bank
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_viewprocedures" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Added Banks
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
                                                                            <th>Bank Name<button type="button" class="btn btn-success btn-xs pull-right" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="bank_names[]" required> 
                                                                            </td>
                                                                            
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_procedure" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_bank" value="submit_bank">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade" id="tab_viewprocedures">
                                                <?php
                                                $querybank = "SELECT * FROM bank  ORDER BY Bank_Id DESC";
                                                if (DB::getInstance()->checkRows($querybank)) {
                                                    ?>                                                    
                                                    <table id="example1" class="table table-striped table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Bank Name</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $bank_list = DB::getInstance()->querySample($querybank);
                                                            $no = 0;
                                                            foreach ($bank_list as $bank) {
                                                                $no++;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $no ?></td>
                                                                    <td><?php echo $bank->Bank_Name; ?></td>
                                                                    <td> 
                                                                        <a data-toggle="modal"  href="#edit_<?php echo $bank->Bank_Id ?>">
                                                                            <i class="fa fa-pencil"></i> Edit</a>&nbsp&nbsp&nbsp;
                                                                        <a href="index.php?page=<?php echo $crypt->encode('add_bank') . '&action=' . $crypt->encode('remove_bank') . '&Bank_Id=' . $crypt->encode($bank->Bank_Id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Do you really want to Delete this bank?');"><i class="fa fa-trash-o"></i> Delete</a> 
                                                                    </td>
                                                            <div class="modal fade" id="edit_<?php echo $bank->Bank_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                                <form action="" method="post">
                                                                    <div class="modal-dialog animated fadeInDown">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                <h4 class="modal-title">Edit <?php echo $bank->Bank_Name; ?>'s&nbsp;Information</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Bank Name</label>
                                                                                    <input type="hidden" name="Bank_Id" value="<?php echo $bank->Bank_Id ?>">
                                                                                    <input type="text" class="form-control" name="Bank_Name" value="<?php echo $bank->Bank_Name ?>">
                                                                                </div>
                                                                                
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                                <button type="submit" name="edit_bank" value="edit_bank"class="btn btn-success" type="button">Save changes</button>
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
                                                    echo '<div class="alert alert-warning">No bank registered</div>';
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
                        <input name="bank_names[]" class="form-control" type="text" required><button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button> </tr>');

                                                                            }
                                                                            function delete_item(element_id) {
                                                                                $('#' + element_id).html('');
                                                                            }
    </script>
    </body>

</html>