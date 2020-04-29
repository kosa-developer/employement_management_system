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
                                    <div class="page-title">Tax Settings</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">

                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body " id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#tab_paye" data-toggle="tab">
                                                    <i class="fa fa-plus-circle"></i> National Social Security Fund (NSSF 5%)
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_nssf" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> Pay As You Earn (PAYE)
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_lst" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> PF (2.5%)
                                                </a>
                                            </li>

                                        </ul>
                                        <div class="tab-content primary">
                                            <div class="tab-pane fade in active" id="tab_paye">
                                                <div class="row">

                                                </div>
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 form-group hidden">
                                                                <label class=""><b>Select Date</b></label>
                                                                <input type="date" class="form-control" name="date_" value="<?php echo date('Y-m-d'); ?>" id="date" >
                                                            </div>
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width:50%;">Staff Names</th>
                                                                            <th>Basic Pay</th>
                                                                            <th>Tax(5%)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <?php
                                                                        $staffCheck = "SELECT * FROM staff,person WHERE staff.Position!='Director' AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                                                        $staff_list = DB::getInstance()->query($staffCheck);
                                                                        foreach ($staff_list->results() as $staff):
                                                                            ?>
                                                                            <tr>
                                                                                <td  > <?php echo $staff->Fname . ' ' . $staff->Lname ?> </td>
                                                                                <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") ?></td>
                                                                                <td><?php echo $tax = (DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") != '') ? DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") * 0.05 : 0; ?></td>

                                                                            </tr>
                                                                            <?php
                                                                        endforeach;
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>


                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade in" id="tab_nssf">
                                                <div class="row">

                                                </div>
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 form-group hidden">
                                                                <label class=""><b>Select Date</b></label>
                                                                <input type="date" class="form-control" name="date_" value="<?php echo date('Y-m-d'); ?>" id="date" >
                                                            </div>
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width:50%;">Staff Names</th>
                                                                            <th> Basic Pay</th>
                                                                            <th> House Allowance</th>
                                                                            <th> Meals Allowance</th>
                                                                            <th> Medical Allowance</th>
                                                                            <th> Transport Allowance</th>
                                                                            <th> Gross pay</th>
                                                                            <th>PAYE</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element1">
                                                                        <?php
                                                                        $staffCheck = "SELECT * FROM staff,person WHERE staff.Position='Officer' AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                                                        $staff_list = DB::getInstance()->query($staffCheck);
                                                                        foreach ($staff_list->results() as $staff):
                                                                            $house = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id' order by Allawence_Id DESC limit 1", "House");
                                                                            $meal = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id' order by Allawence_Id DESC limit 1", "Meal");
                                                                            $medical = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id' order by Allawence_Id DESC limit 1", "Medical");
                                                                            $transport = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id' order by Allawence_Id DESC limit 1", "Transport");
                                                                            $basic=DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale");
                                                                           
                                                                            $grosspay=$basic+$house+$meal+$medical+$transport;
                                                                           $paye= calculateEmployeeTax($grosspay);
                                                                                    ?>
                                                                            <tr>
                                                                                <td ><?php echo $staff->Fname . ' ' . $staff->Lname ?></td>
                                                                                <td><?php echo number_format($basic); ?> </td>
                                                                                <td><?php echo number_format($house); ?> </td>
                                                                                <td><?php echo number_format($meal); ?> </td>
                                                                                <td><?php echo number_format($medical); ?> </td>
                                                                                <td><?php echo number_format($transport); ?> </td>
                                                                                <td><?php echo number_format($grosspay);?></td>
                                                                                <td><?php echo $paye['paye'];?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>


                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade in" id="tab_lst">
                                                <div class="row">

                                                </div>
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                                                            <div class="col-md-4 col-sm-4 col-xs-4 col-lg-4 form-group hidden">
                                                                <label class=""><b>Select Date</b></label>
                                                                <input type="date" class="form-control" name="date_" value="<?php echo date('Y-m-d'); ?>" id="date" >
                                                            </div>
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width:40%;">Staff Names</th>
                                                                            <th> Basic Pay</th>
                                                                            <th>PF(2.5%)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element2">
                                                                        <?php
                                                                        $staffCheck = "SELECT * FROM staff,person WHERE staff.Position!='Director' AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                                                        $staff_list = DB::getInstance()->query($staffCheck);
                                                                        foreach ($staff_list->results() as $staff):
                                                                            ?>
                                                                            <tr>
                                                                                <td  ><?php echo $staff->Fname . ' ' . $staff->Lname ?> </td>
                                                                                <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") ?></td>
                                                                                <td><?php echo $tax = (DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") != '') ? DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") * 0.025 : 0; ?></td>

                                                                            </tr> 
                                                                            <?php
                                                                        endforeach;
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>


                                                        </div>
                                                    </form>
                                                </div>
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
            <?php
            include_once 'includes/footer.php';
            ?>
            <!-- end footer -->
        </div>
        <!-- start js include path -->
        <?php include_once 'includes/footer_js.php'; ?>
        <!-- end js include path -->
        <script>
        </script>
    </body>

</html>