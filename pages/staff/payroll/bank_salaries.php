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
                <?php require_once 'includes/side_menu.php'; ?>
                <!-- end sidebar menu -->
                <!-- start page content -->
                <div class="page-content-wrapper">
                    <div class="page-content">
                        <div class="page-bar">
                            <div class="page-title-breadcrumb">
                                <div class="title page-title">Staff Salary Report</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Search</header>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="" class="form-inline">
                                            <div class="form-group">
                                                <label class="form-label">Banks</label>
                                                <select class="select2" style="width:100%" name="bank_id" required>
                                                    <option value="">Choose...</option>
                                                    <?php echo DB::getInstance()->dropDowns("bank", "Bank_Id", "Bank_Name"); ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Year</label>
                                                <div class="controls">
                                                    <select class="form-control" name="year" required>
                                                        <option value=""  >Choose...</option>
                                                        <?php
                                                        $year = date('Y');
                                                        $month = date('m');
                                                        for ($i = 2013; $i <= date('Y'); $i++) {
                                                            $selected = ($i == $year) ? "selected" : "";
                                                            ?>
                                                            <option value="<?php echo $i ?>" <?php echo $selected ?> ><?php echo $i ?></option>
                                                        <?php } ?></select></div>
                                            </div>
                                            <div class="form-group">
                                                <label>Months</label> 
                                                <div class="controls">
                                                    <select class="form-control" name="month" required>
                                                        <option value="">Select Month</option>
                                                        <?php
                                                        for ($i = 1; $i <= 12; $i++) {
                                                            $month_ = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                            $selected = ($i == $month) ? " selected" : "";
                                                            echo '<option value="' . $month_ . '" ' . $selected . '>' . $month_ . '</option>';
                                                        }
                                                        ?>
                                                    </select></div>
                                            </div>
                                            <div class="form-group"><br/>
                                                <button type="submit" name="search_date_btn" value="search_date_btn" class="btn btn-success"><i class="fa fa-search"></i> Search </button>
                                            </div>
                                        </form>
                                    </div>
                                </div><?php
                                $current_month_and_year = date("Y-m");
                                $month_name = english_months($month);
                                $year_name = $year;
                                $condition = "";
                                $heading = "MILLENIUM SECURITY LIMITED";
                                $reportName = "SALARY REPORT FOR " . $month_name . " " . $year_name;
                                if (Input::exists() && Input::get("search_date_btn") == "search_date_btn") {
                                    $month = Input::get("month");
                                    $year = Input::get("year");
                                    $bank_id = Input::get("bank_id");
                                    $condition .= "AND staff.Bank_Id='$bank_id'";
                                    $month_name = english_months($month);
                                    $year_name = date($year);
                                    $current_month_and_year = $year . '-' . $month;
                                    $reportName = " SALARY REPORT FOR  " . $month_name . " " . $year_name . "  " . DB::getInstance()->DisplayTableColumnValue("SELECT * FROM bank WHERE Bank_Id='$bank_id'", "Bank_Name") . " BANK";
                                }

                                $reportName = strtoupper($reportName);
                                $queryguard = "SELECT * FROM staff,person WHERE staff.Position='Guard' $condition AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                $queryofficer = "SELECT * FROM staff,person WHERE staff.Position!='Guard' AND staff.Position!='Director' $condition AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                if (DB::getInstance()->checkRows($queryguard) || DB::getInstance()->checkRows($queryofficer)) {
                                    $data_sent = serialize(array($queryguard, $queryofficer, $reportName, $current_month_and_year));
                                    ?> 
                                    <div class="card card-topline-yellow">
                                        <div class="card-head">
                                            <header><?php echo $heading . " " . $reportName ?></header>
                                            <div class="actions panel_actions pull-right">
                                                <a target="_blank"  href="index.php?page=<?php echo $crypt->encode("financial_report_pdf") . "&download_type=download_bank_salary_report&data_sent=" . $crypt->encode($data_sent) ?>" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> print</a>
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <table id="example1" class="table table-striped table-responsive display table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>S/NO</th>
                                                                <th>SVC NO.</th>
                                                                <th>ACCOUNT NAME</th>
                                                                <th>ACCOUNT/NO</th>
                                                                <th>AMOUNT(UGX)</th>
                                                                <th>BRANCH</th>
                                                                <th>REMARKS</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $staffList = DB::getInstance()->querySample($queryguard);
                                                            $guardtotalNetPay = 0;
                                                            foreach ($staffList as $staff) {
                                                                $basic_pay = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' AND substr(Date_From,1,7)<='$current_month_and_year' order by Salary_Scale_Id DESC limit 1", "Salary_Scale");
                                                                $daily_rate = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM daily_rates WHERE Staff_Id='$staff->Staff_Id' AND substr(Date_From,1,7)<='$current_month_and_year' order by Rate_Id DESC limit 1", "Daily_Rate");
                                                                $overtime_rate = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM daily_rates WHERE Staff_Id='$staff->Staff_Id' AND substr(Date_From,1,7)<='$current_month_and_year' order by Rate_Id DESC limit 1", "Overtime_Rate");
                                                                $nssf = (DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' AND substr(Date_From,1,7)<='$current_month_and_year' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") != '') ? DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") * 0.05 : 0;
                                                                $provtax = (DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' AND substr(Date_From,1,7)<='$current_month_and_year' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") != '') ? DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' order by Salary_Scale_Id DESC limit 1", "Salary_Scale") * 0.025 : 0;
                                                                $loan_earn = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM loan WHERE Staff_Id='$staff->Staff_Id' AND substr(Loan_Date,1,7)='$current_month_and_year' AND Loan_Type='Loan/OT'", "Amount_Paid");
                                                                $Lloan = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM loan WHERE Staff_Id='$staff->Staff_Id' AND substr(Loan_Date,1,7)='$current_month_and_year' AND Loan_Type='L/Loan'", "Amount_Paid");
                                                                $dail_days = DB::getInstance()->countElements("SELECT * FROM staff_attendance where Staff_Id='$staff->Staff_Id' AND substr(Date,1,7)='$current_month_and_year' and Is_Present='1'");
                                                                $overtime_days = DB::getInstance()->countElements("SELECT * FROM staff_overtime_attendency where Staff_Id='$staff->Staff_Id' AND substr(Date,1,7)='$current_month_and_year' and Is_Present='1'");
                                                                $advance = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_payments WHERE Staff_Id='$staff->Staff_Id' AND substr(Payment_Date,1,7)='$current_month_and_year' AND Payment_Type='Advance'", "Amount_Paid");

                                                                $basic_pay = ($basic_pay != '') ? $basic_pay : 0;
                                                                $daily_rate = ($daily_rate != '') ? $daily_rate : 0;
                                                                $overtime_rate = ($overtime_rate != '') ? $overtime_rate : 0;
                                                                $nssf = ($nssf != '') ? $nssf : 0;
                                                                $provtax = ($provtax != '') ? $provtax : 0;
                                                                $loan_earn = ($loan_earn != '') ? $loan_earn : 0;
                                                                $Lloan = ($Lloan != '') ? $Lloan : 0;
                                                                $dail_days = ($dail_days != '') ? $dail_days : 0;
                                                                $overtime_days = ($overtime_days != '') ? $overtime_days : 0;
                                                                $advance = ($advance != '') ? $advance : 0;

                                                                $netpay = ($daily_rate * $dail_days) + ($overtime_rate * $overtime_days) + $loan_earn - ($nssf + $provtax + $Lloan);
                                                                $netpay = ($netpay >= 0) ? $netpay : 0;
                                                                $guardtotalNetPay += $netpay;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $staff->Serial_Number ?></td>
                                                                    <td><?php echo $staff->Service_Number ?></td>
                                                                    <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM bank WHERE Bank_Id='$staff->Bank_Id'", "Bank_Name") ?></td>
                                                                    <td><?php echo $staff->Account_Number ?></td>
                                                                    <td><?php echo number_format($netpay); ?></td>
                                                                    <td><?php echo $staff->Branch ?></td>
                                                                    <td></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            $officerList = DB::getInstance()->querySample($queryofficer);
                                                            $officertotalNetPay = 0;
                                                            foreach ($officerList as $officer) {
                                                                $basic_pay = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$officer->Staff_Id' AND substr(Date_From,1,7)<='$current_month_and_year' order by Salary_Scale_Id DESC limit 1", "Salary_Scale");
                                                                $loan_earn = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM loan WHERE Staff_Id='$officer->Staff_Id' AND substr(Loan_Date,1,7)='$current_month_and_year' AND Loan_Type='Loan/OT'", "Amount_Paid");
                                                                $Lloan = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM loan WHERE Staff_Id='$officer->Staff_Id' AND substr(Loan_Date,1,7)='$current_month_and_year' AND Loan_Type='L/Loan'", "Amount_Paid");
                                                                $advance = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_payments WHERE Staff_Id='$officer->Staff_Id' AND substr(Payment_Date,1,7)='$current_month_and_year' AND Payment_Type='Advance'", "Amount_Paid");

                                                                $house = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$officer->Staff_Id'  AND substr(Date_From,1,7)<='$current_month_and_year' order by Allawence_Id DESC limit 1", "House");
                                                                $meal = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$officer->Staff_Id'  AND substr(Date_From,1,7)<='$current_month_and_year' order by Allawence_Id DESC limit 1", "Meal");
                                                                $medical = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$officer->Staff_Id'  AND substr(Date_From,1,7)<='$current_month_and_year' order by Allawence_Id DESC limit 1", "Medical");
                                                                $transport = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$officer->Staff_Id'  AND substr(Date_From,1,7)<='$current_month_and_year' order by Allawence_Id DESC limit 1", "Transport");



                                                                $basic_pay = ($basic_pay != '') ? $basic_pay : 0;

                                                                $loan_earn = ($loan_earn != '') ? $loan_earn : 0;
                                                                $Lloan = ($Lloan != '') ? $Lloan : 0;
                                                                $advance = ($advance != '') ? $advance : 0;

                                                                $grosspay = $basic_pay + $house + $meal + $medical + $transport;
                                                                $paye_nssf = calculateEmployeeTax($grosspay);
                                                                $pf_tax = $grosspay * 0.025;
                                                                $nssf = $paye_nssf['nssf_5percent'];
                                                                $paye = $paye_nssf['paye'];


                                                                $netpay = $grosspay - $nssf - $pf_tax - $Lloan - $advance - $paye + $loan_earn;
                                                                $netpay = ($netpay >= 0) ? $netpay : 0;
                                                                $officertotalNetPay += $netpay;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $officer->Serial_Number ?></td>
                                                                    <td><?php echo $officer->Service_Number ?></td>
                                                                    <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM bank WHERE Bank_Id='$officer->Bank_Id'", "Bank_Name") ?></td>
                                                                    <td><?php echo $officer->Account_Number ?></td>
                                                                    <td><?php echo number_format($netpay); ?></td>
                                                                    <td><?php echo $officer->Branch ?></td>
                                                                    <td></td>

                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                             <tr>
                                                                <th>Total</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th><?php echo number_format($officertotalNetPay+$guardtotalNetPay); ?></th>
                                                                <th></th>
                                                                <th></th>
                                                            </tr>
                                                        </tfoot>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo '<h3 style="color:red;">NO ' . $reportName . '</h3>';
                                }
                                ?>
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