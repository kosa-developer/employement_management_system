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
                                    <div class="page-title">Staff Payroll (Salary Scale)</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10 col-sm-10">
                                <?php
                                if (Input::exists() && Input::get("add_staff_salary_scale") == "add_staff_salary_scale") {
                                    $staff_id = Input::get("staff_id");
                                    $date_from = Input::get("date_from");
                                    $salary_amount = Input::get("salary_amount");

                                    $submited = 0;
                                    for ($i = 0; $i < sizeof($staff_id); $i++) {
                                        $month_and_year = substr($date_from[$i], 0, 7);
                                        if (DB::getInstance()->checkRows("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff_id[$i]' AND SUBSTR(Date_From,1,7)='$month_and_year'")) {
                                            if($salary_amount[$i]!=""){
                                                $dataUpdate = DB::getInstance()->query("UPDATE staff_salary_scale SET Salary_Scale='$salary_amount[$i]' WHERE Staff_Id='$staff_id[$i]' AND SUBSTR(Date_From,1,7)='$month_and_year'");
                                            $submited++;
                                            $action_made = "uploaded";}
                                        } else {
                                            $dataUpdate = DB::getInstance()->insert("staff_salary_scale", array(
                                                "Staff_Id" => $staff_id[$i],
                                                "Date_From" => $date_from[$i],
                                                "Salary_Scale" =>($salary_amount[$i]!="")?$salary_amount[$i]:NULL,
                                                "Registered_By" => $_SESSION['security_user_id']
                                            ));
                                            $submited++;
                                            $action_made = "uploaded";
                                        }
                                    }
                                    if ($dataUpdate) {
                                        echo '<div class="alert alert-success">' . $submited . ' Salary Scales ' . $action_made . ' successfully</div>';
                                    }
                                    //   Redirect::go_to("");
                                }
                                ?>

                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Search</header>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="" class="form-inline">
                                            <div class="form-group">
                                                <label>Position</label>
                                                <div class="form-group">
                                                    <select class="select2" style="width: 100%" name="position" required>
                                                        <option value="">Choose....</option>
                                                        <?php
//Position array declared in the init file
                                                        for ($x = 0; $x < count($position_list_array); $x++) {
                                                            echo '<option value="' . $position_list_array[$x] . '">' . $position_list_array[$x] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group"><br/>
                                                <button type="submit" name="search_date_btn" value="search_date_btn" class="btn btn-success"><i class="fa fa-search"></i> Search </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <?php   $condition = "";
                                
                                $reportName = "Salary Scale Entry";
                                if (Input::exists() && Input::get("search_date_btn") == "search_date_btn") {
                                    $position = Input::get("position");
                                   
                                    $condition .= " AND staff.Position='$position'";
                                    $reportName = " Salary Scale Entry For " . $position."s";
                                }?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header><?php echo $reportName; ?></header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <table class="table table-striped table-bordered table-responsive" id="example1" >
                                                    <thead>
                                                        <tr>
                                                            <th>Staff Names</th>
                                                            <th>Date from</th>
                                                            <th>Basic pay</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $staffCheck = "SELECT * FROM staff,person WHERE staff.Position!='Director' $condition AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                                        $staff_list = DB::getInstance()->query($staffCheck);
                                                        foreach ($staff_list->results() as $staff):
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $staff->Fname . ' ' . $staff->Lname ?> <input type="hidden" class="form-control" name="staff_id[]" value="<?php echo $staff->Staff_Id ?>"  ></td>
                                                                <td> <input type="date" class="form-control" name="date_from[]" value="<?php echo $date_today ?>" max="<?php echo $date_today ?>" > </td>
                                                                <td><input type="number" min="0" class="form-control" name="salary_amount[]" value="<?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") ?>"></td>
                                                            </tr>

                                                            <?php
                                                        endforeach;
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="pull-right">
                                                    <button type="submit" name="add_staff_salary_scale" value="add_staff_salary_scale" class="btn btn-success">Save</button>
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