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
                                    <div class="page-title">Staff Allowances</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <?php
                                if (Input::exists() && Input::get("add_allowance") == "add_allowance") {
                                    $staff_id = Input::get("staff_id");
                                    $date_from = Input::get("date_from");
                                    $house_allowance = Input::get("house_allowance");
                                    $meals_allowance = Input::get("meals_allowance");
                                    $medical_allowance = Input::get("medical_allowance");
                                    $transport_allowance = Input::get("transport_allowance");
                                    
                                    $submited=0;
                                    for($i=0;$i<sizeof($staff_id);$i++){
                                        $month_and_year = substr($date_from[$i], 0, 7);
                                    if (DB::getInstance()->checkRows("SELECT * FROM allowance WHERE Staff_Id='$staff_id[$i]' AND SUBSTR(Date_From,1,7)='$month_and_year'")) {
                                        $dataUpdate = DB::getInstance()->query("UPDATE allowance SET House='$house_allowance[$i]',Meal='$meals_allowance[$i]',Medical='$medical_allowance[$i]',Transport='$transport_allowance[$i]' WHERE Staff_Id='$staff_id[$i]' AND SUBSTR(Date_From,1,7)='$month_and_year'");
                                        $submited++;
                                         $action_made = "uploaded";
                                    } else {
                                        $dataUpdate = DB::getInstance()->insert("allowance", array(
                                            "Staff_Id" => $staff_id[$i],
                                            "Date_From" => $date_from[$i],
                                            "House" => $house_allowance[$i],
                                            "Meal" => $meals_allowance[$i],
                                            "Medical" => $medical_allowance[$i],
                                            "Transport" => $transport_allowance[$i],
                                            "Registered_By" => $_SESSION['security_user_id']
                                        ));
                                       $submited++;
                                        $action_made = "uploaded";
                                    }
                                     }
                                    if ($dataUpdate) {
                                        echo '<div class="alert alert-success">'.$submited.' Staff Allowances ' . $action_made . ' successfully</div>';
                                    }
                                 //   Redirect::go_to("");
                                }
                                ?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Staff Allowance Entry</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                              <table class="table table-striped table-bordered table-responsive" id="example1" >
                                                <thead>
                                                    <tr>
                                                        <th>Staff Names</th>
                                                        <th>Date from</th>
                                                        <th>House Allowance</th>
                                                        <th>Meals Allowance</th>
                                                        <th>Medical Allowance</th>
                                                        <th>Transport Allowance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     <?php
                                                $staffCheck = "SELECT * FROM staff,person WHERE staff.Position='Officer' AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                                $staff_list = DB::getInstance()->query($staffCheck);
                                                foreach ($staff_list->results() as $staff):
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $staff->Fname . ' ' . $staff->Lname ?> <input type="hidden" class="form-control" name="staff_id[]" value="<?php echo $staff->Staff_Id?>"  required></td>
                                                        <td> <input type="date" class="form-control" name="date_from[]" value="<?php echo $date_today ?>" max="<?php echo $date_today ?>" required> </td>
                                               <td><input type="number" min="0" class="form-control" name="house_allowance[]" value="<?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id' order by Allawence_Id DESC limit 1","House") ?>"required></td>
                                               <td><input type="number" min="0" class="form-control" name="meals_allowance[]" value="<?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id' order by Allawence_Id DESC limit 1","Meal") ?>"required></td>
                                               <td><input type="number" min="0" class="form-control" name="medical_allowance[]" value="<?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id' order by Allawence_Id DESC limit 1","Medical") ?>"required></td>
                                               <td><input type="number" min="0" class="form-control" name="transport_allowance[]" value="<?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id' order by Allawence_Id DESC limit 1","Transport") ?>"required></td>
                                                    </tr>
                                                    
                                                    <?php
                                                          endforeach;
                                                ?>
                                                </tbody>
                                              </table>
                                            </div>
                                               
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="pull-right">
                                                    <button type="submit" name="add_allowance" value="add_allowance" class="btn btn-success">Save</button>
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
        <?php include_once 'includes/footer_js.php'; ?>
        <!-- end js include path -->
    </body>

</html>