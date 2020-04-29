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
                                <div class="title page-title">Payroll Report For Guards</div>
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
                                $reportName = "GUARDS PAYROLL REPORT FOR " . $month_name . " " . $year_name;
                                if (Input::exists() && Input::get("search_date_btn") == "search_date_btn") {
                                    $month = Input::get("month");
                                    $year = Input::get("year");
                                    $month_name = english_months($month);
                                    $year_name = date($year);
                                    $current_month_and_year = $year . '-' . $month;
                                    $reportName = "GUARDS PAYROLL REPORT FOR " . $month_name . " " . $year_name;
                                }

                                $reportName = strtoupper($reportName);
                                $querystaff = "SELECT * FROM staff,person WHERE staff.Position='Guard' AND person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                if (DB::getInstance()->checkRows($querystaff)) {
                                    $data_sent = serialize(array($querystaff, $reportName, $current_month_and_year));
                                    ?> 
                                    <div class="card card-topline-yellow">
                                        <div class="card-head">
                                            <header><?php echo $reportName ?></header>
                                            <div class="actions panel_actions pull-right">
                                                <a  href="index.php?page=<?php echo $crypt->encode("excel_download") . "&download_type=download_gaurd_payroll_report&data_sent=" . $crypt->encode($data_sent) ?>" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> download excel</a>
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <table id="example1" class="table table-striped table-responsive display table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Rank</th>
                                                                <th>Staff Name</th>
                                                                <th>Basic Pay</th>
                                                                <th>Daily Rate</th>
                                                                <th>Days</th>
                                                                <th>Overtime</th>
                                                                <th>Overtime Rate</th>
                                                                <th>Loan Earn</th>
                                                                <th>NSSF 5%</th>
                                                                <th>PROV.(2.5%)</th>
                                                                <th>Advance</th>
                                                                <th>L/loan</th>
                                                                <th>Net Payment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $staffList = DB::getInstance()->querySample($querystaff);
                                                            $totalNetPay = 0;
                                                            $tnssf = 0;
                                                            $tprovtax = 0;
                                                            $tloan_earn = 0;
                                                            $tLloan = 0;
                                                            $tdail_days = 0;
                                                            $tovertime_days = 0;
                                                            $tadvance = 0;
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
                                                                $tnssf += $nssf;
                                                                $tprovtax += $provtax;
                                                                $tloan_earn += $loan_earn;
                                                                $tLloan += $Lloan;
                                                                $tdail_days += $dail_days;
                                                                $tovertime_days += $overtime_days;
                                                                $tadvance += $advance;

                                                                $netpay = ($daily_rate * $dail_days) + ($overtime_rate * $overtime_days) + $loan_earn - ($nssf + $provtax + $Lloan + $advance);
                                                                $netpay = ($netpay >= 0) ? $netpay : 0;
                                                                $totalNetPay += $netpay;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $staff->Rank ?></td>
                                                                    <td><?php echo $staff->Fname . ' ' . $staff->Lname ?></td>
                                                                    <td><?php echo number_format($basic_pay); ?></td>
                                                                    <td><?php echo number_format($daily_rate) ?></td>
                                                                    <td><?php echo number_format($dail_days); ?></td>
                                                                    <td><?php echo number_format($overtime_days); ?></td>
                                                                    <td><?php echo number_format($overtime_rate); ?></td>
                                                                    <td style="color:green;"><?php echo number_format($loan_earn); ?></td>
                                                                    <td><?php echo number_format($nssf); ?></td>
                                                                    <td><?php echo number_format($provtax); ?></td>
                                                                    <td><?php echo number_format($advance); ?></td>
                                                                    <td style="color:red;"><?php echo $Lloan; ?></td>
                                                                    <td><?php echo number_format($netpay); ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>TOTAL</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th style="color:green"><?php echo number_format($tdail_days); ?></th>
                                                                <th style="color:green"><?php echo number_format($tovertime_days); ?></th>
                                                                <th></th>
                                                                <th style="color:green"><?php echo number_format($tloan_earn); ?></th>
                                                                <th ><?php echo number_format($tnssf); ?></th>
                                                                <th style="color:green"><?php echo number_format($tprovtax); ?></th>
                                                                <th style="color:red"><?php echo number_format($tadvance); ?></th>
                                                                <th style="color:red"><?php echo $tLloan; ?></th>
                                                                <th><?php echo number_format($totalNetPay) ?></th>
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