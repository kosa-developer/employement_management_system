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
                                    <div class="page-title">Staff Payroll (Salary Daily Rates) for Guards</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10 col-sm-10">
                                <?php
                                if (Input::exists() && Input::get("add_daily_rates") == "add_daily_rates") {
                                    $staff_id = Input::get("staff_id");
                                    $date_from = Input::get("date_from");
                                    $daily_salary_rate = Input::get("daily_salary_rate");
                                    
                                    $overtime_salary_rate=Input::get("overtime_salary_rate");
                                    
                                    $submited=0;
                                    $ratedata="";
                                    for($i=0;$i<sizeof($staff_id);$i++){
                                        $month_and_year = substr($date_from[$i], 0, 7);
                                        if($daily_salary_rate[$i]!=''&&$overtime_salary_rate[$i]==''){
                                            $added_query="Daily_Rate='$daily_salary_rate[$i]'";
                                        }else if($overtime_salary_rate[$i]!=''&&$daily_salary_rate[$i]==''){
                                           $added_query ="Overtime_Rate='$overtime_salary_rate[$i]'";
                                        }else if($overtime_salary_rate[$i]!=''&&$daily_salary_rate[$i]!=''){
                                           $added_query ="Daily_Rate='$daily_salary_rate[$i]',Overtime_Rate='$overtime_salary_rate[$i]'";
                                        }else{
                                            $added_query="";
                                        }
                                         
                                      
                                    if ($added_query!=""&& DB::getInstance()->checkRows("SELECT * FROM daily_rates WHERE Staff_Id='$staff_id[$i]' AND SUBSTR(Date_From,1,7)='$month_and_year'")) {
                                         $dataUpdate = DB::getInstance()->query("UPDATE daily_rates SET $added_query WHERE Staff_Id='$staff_id[$i]' AND SUBSTR(Date_From,1,7)='$month_and_year'");
                                       if($dataUpdate) {$submited++;}
                                         $action_made = "uploaded";
                                    } else {
                                        $dataUpdate = DB::getInstance()->insert("daily_rates", array(
                                            "Staff_Id" => $staff_id[$i],
                                            "Date_From" => $date_from[$i],
                                            "Daily_Rate" => ($daily_salary_rate[$i]!='')?$daily_salary_rate[$i]:NULL,
                                            "Overtime_Rate"=>($overtime_salary_rate[$i]!='')?$overtime_salary_rate[$i]:NULL,
                                            "Registered_By" => $_SESSION['security_user_id']
                                        ));
                                       $submited++;
                                        $action_made = "uploaded";
                                    }
                                     }
                                     echo $ratedata;
                                    if ($dataUpdate) {
                                        echo '<div class="alert alert-success">'.$submited.' Rates ' . $action_made . ' successfully</div>';
                                    }
                                 //   Redirect::go_to("");
                                }
                                ?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Daily salary rate Entry</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                              <table class="table table-striped table-bordered table-responsive" id="example1" >
                                                <thead>
                                                    <tr>
                                                        <th>Staff Names</th>
                                                        <th>Date from</th>
                                                        <th>Daily Rate</th>
                                                        <th>Overtime Rate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     <?php
                                                $staffCheck = "SELECT * FROM staff,person WHERE staff.Position='Guard' AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                                $staff_list = DB::getInstance()->query($staffCheck);
                                                foreach ($staff_list->results() as $staff):
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $staff->Fname . ' ' . $staff->Lname ?> <input type="hidden" class="form-control" name="staff_id[]" value="<?php echo $staff->Staff_Id?>"  required></td>
                                                        <td> <input type="date" class="form-control" name="date_from[]" value="<?php echo $date_today ?>" max="<?php echo $date_today ?>" required> </td>
                                               <td><input type="number" min="0" class="form-control" name="daily_salary_rate[]" value="<?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM daily_rates WHERE Staff_Id='$staff->Staff_Id' order by Rate_Id DESC limit 1","Daily_Rate") ?>"></td>
                                               <td><input type="number" min="0" class="form-control" name="overtime_salary_rate[]" value="<?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM daily_rates WHERE Staff_Id='$staff->Staff_Id' order by Rate_Id DESC limit 1","Overtime_Rate") ?>"></td>
                                                    </tr>
                                                    
                                                    <?php
                                                          endforeach;
                                                ?>
                                                </tbody>
                                              </table>
                                            </div>
                                               
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="pull-right">
                                                    <button type="submit" name="add_daily_rates" value="add_daily_rates" class="btn btn-success">Save</button>
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