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
                                    <div class="page-title">Register Signatories</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="row">
                                    <?php
                                    //deletng the procedure
                                    if (isset($_GET['action']) && $_GET['action'] == $crypt->encode("remove_signatory") && $_GET['Signatory_Id'] != "") {
                                        $signatory_Id = $crypt->decode($_GET['Signatory_Id']);
                                        $deletesignatories = DB::getInstance()->query("delete from signatories where Signatory_Id='$signatory_Id'");
                                        if ($deletesignatories) {
                                            echo '<div class="alert alert-success"> signatories deleted successfully</div>';
                                        }
                                        Redirect::go_to("index.php?page=" . $crypt->encode("signatories"));
                                    }
                                    if (Input::exists()) {
//                                    Editing procedure names
                                        if (Input::get("edit_signatory") == "edit_signatory") {
                                            $signatory_Id = Input::get("Signatory_Id");
                                            $signatory_name = Input::get("signatory_names");
                                            $role= Input::get("role");
                                            
                                            $signatoryUpdate = DB::getInstance()->update("signatories", $signatory_Id, array(
                                                "Staff_Id" => $signatory_name,
                                                "Role"=>$role
                                                    ), "Signatory_Id");
                                            if ($signatoryUpdate) {
                                                echo '<div class="alert alert-success"> signatories details updated successfully</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("signatories"));
                                        }
//                                    adding the new procedure
                                        if ((Input::get("submit_signatories"))) { 
                                            
                                            $signatory_name = Input::get("signatory_names");
                                            $role= Input::get("role");
                                            $signatory_added = 0;
                                            $duplicates = 0;
                                            if (!empty($signatory_name)) {
                                                for ($x = 0; $x < count($signatory_name); $x++) {
                                                    $queryDup = DB::getInstance()->checkRows("SELECT * FROM signatories WHERE Staff_Id='$signatory_name[$x]' ");
                                                    if ($queryDup) {
                                                        $duplicates++;
                                                    } else {
                                                         $signatoryInsert = DB::getInstance()->insert("signatories", array(
                                                            "Staff_Id" => $signatory_name[$x],
                                                             "Role"=>$role[$x]));
                                                        if ($signatoryInsert) {
                                                            $signatory_added++;
                                                        }
                                                    }
                                                }
                                            }
                                            if ($signatory_added != 0) {
                                                echo '<div class="alert alert-success col-sm-6">' . $signatory_added . ' signatories successfully registered</div>';
                                            } if ($duplicates != 0) {
                                                echo '<div class="alert alert-warning col-sm-6">' . $duplicates . ' Duplicates could not be registered</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("signatories"));
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body " id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#tab_new" data-toggle="tab">
                                                    <i class="fa fa-pencil"></i> Add New  Signatory
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_viewprocedures" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Added Signatories
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
                                                                            <th>Name</th>
                                                                            <td>Role<button type="button" class="btn btn-success btn-xs pull-right" id="add_more[]" onclick="add_element();">Add more</button></td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                
                                                                                <select class="select2" style="width:100%" name="signatory_names[]" required >
                                                                                        <option value="">Choose...</option>
                                                                                        <?php
                                                                                        $staffCheck = "SELECT * FROM staff,person where staff.Person_Id=person.Person_Id  GROUP BY person.Person_Id";
                                                                                        $staff_list = DB::getInstance()->query($staffCheck);
                                                                                        foreach ($staff_list->results() as $staff):
                                                                                               echo '<option value="' . $staff->Staff_Id . '">' . $staff->Fname . ' '. $staff->Lname . '</option>';
                                                                                        endforeach;
                                                                                        ?>
                                                                                    </select>
                                                                               </td>
                                                                            <td>
                                                                                <select class="select2" style="width:100%" name="role[]" required>
                                                                                   <option value="">Choose...</option>
                                                                                   <option value="Managing Director">Managing Director</option>
                                                                                     <option value="Human Resource">Human Resource</option>
                                                                                      <option value="Accountant">Accountant</option>
                                                                                </select>
                                                                            </td>
                                                                            
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_procedure" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_signatories" value="submit_signatories">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade" id="tab_viewprocedures">
                                                <?php
                                                $querysignatories = "SELECT * FROM signatories  ORDER BY Signatory_Id DESC";
                                                if (DB::getInstance()->checkRows($querysignatories)) {
                                                    ?>                                                    
                                                    <table id="example1" class="table table-striped table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>signatories Name</th>
                                                                <th>Roles</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $signatory_list = DB::getInstance()->querySample($querysignatories);
                                                            $no = 0;
                                                            foreach ($signatory_list as $signatory) {
                                                                $no++;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $no ?></td>
                                                                    <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person WHERE staff.Person_Id=person.Person_Id and staff.Staff_Id='$signatory->Staff_Id' ", "Fname")." ".DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person WHERE staff.Person_Id=person.Person_Id AND staff.Staff_Id='$signatory->Staff_Id' ", "Lname"); ?></td>
                                                                    <td><?php echo $signatory->Role; ?></td>
                                                                    <td> 
                                                                        <a data-toggle="modal"  href="#edit_<?php echo $signatory->Signatory_Id ?>">
                                                                            <i class="fa fa-pencil"></i> Edit</a>&nbsp&nbsp&nbsp;
                                                                        <a href="index.php?page=<?php echo $crypt->encode('signatories') . '&action=' . $crypt->encode('remove_signatory') . '&Signatory_Id=' . $crypt->encode($signatory->Signatory_Id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Do you really want to Delete this signatories?');"><i class="fa fa-trash-o"></i> Delete</a> 
                                                                    </td>
                                                            <div class="modal fade" id="edit_<?php echo $signatory->Signatory_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                                <form action="" method="post">
                                                                    <div class="modal-dialog animated fadeInDown">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                <h4 class="modal-title">Edit <?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person WHERE staff.Person_Id=person.Person_Id and staff.Staff_Id='$signatory->Staff_Id' ", "Fname")." ".DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person WHERE staff.Person_Id=person.Person_Id AND staff.Staff_Id='$signatory->Staff_Id' ", "Lname"); ?>'s&nbsp;Information</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Name</label>
                                                                                    <input type="hidden" name="Signatory_Id" value="<?php echo $signatory->Signatory_Id ?>">
                                                                                    <select class="select2" style="width:100%" name="signatory_names" required >
                                                                                        <option value="">Choose...</option>
                                                                                        <?php
                                                                                        $staffCheck = "SELECT * FROM staff,person where staff.Person_Id=person.Person_Id  GROUP BY person.Person_Id";
                                                                                        $staff_list = DB::getInstance()->query($staffCheck);
                                                                                        foreach ($staff_list->results() as $staff):
                                                                                            $selected=($staff->Staff_Id==$signatory->Staff_Id)?"selected":"";
                                                                                               echo '<option value="' . $staff->Staff_Id . '"'.$selected.'>' . $staff->Fname . ' '. $staff->Lname . '</option>';
                                                                                        endforeach;
                                                                                        ?>
                                                                                    </select> </div>
                                                                                <div class="form-group">
                                                                                    <label>Role</label>
                                                                                 <select class="select2" style="width:100%" name="role" required>
                                                                                   <option value="">Choose...</option>
                                                                                   <option value="Managing Director" <?php echo  $selected=($signatory->Role=="Managing Director")?"selected":""; ?> >Managing Director</option>
                                                                                     <option value="Human Resource" <?php echo  $selected=($signatory->Role=="Human Resource")?"selected":""; ?>>Human Resource</option>
                                                                                      <option value="Accountant" <?php echo  $selected=($signatory->Role=="Accountant")?"selected":""; ?>>Accountant</option>
                                                                                </select>
                                                                                </div>
                                                                                
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                                <button type="submit" name="edit_signatory" value="edit_signatory"class="btn btn-success" type="button">Save changes</button>
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
                                                    echo '<div class="alert alert-warning">No signatories registered</div>';
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
             <select class="form-control" name="signatory_names[]" required >\n\
            <option value="">Choose...</option><?php $staffCheck = "SELECT * FROM staff,person where staff.Person_Id=person.Person_Id  GROUP BY person.Person_Id";$staff_list = DB::getInstance()->query($staffCheck);foreach ($staff_list->results() as $staff): echo '<option value="' . $staff->Staff_Id . '">' . $staff->Fname . ' '. $staff->Lname . '</option>'; endforeach;?>\n\
        </select></td>\n\
            <td><select class="form-control" name="role[]" required>\n\
           <option value="">Choose...</option>\n\
           <option value="Managing Director">Managing Director</option>\n\
             <option value="Human Resource">Human Resource</option>\n\
              <option value="Accountant">Accountant</option>\n\
        </select>\n\
        <button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button> </tr>');

    }
    function delete_item(element_id) {
        $('#' + element_id).html('');
    }
    </script>
    </body>

</html>