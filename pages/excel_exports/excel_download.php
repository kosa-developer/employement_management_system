<?php
//Recoveries download
if (isset($_GET['download_type']) && $_GET['download_type'] == "download_expenditure_report" && $_GET['data_sent'] != "") {
    $array_data = unserialize($crypt->decode($_GET['data_sent']));
    $reportName = $array_data[0];
    $current_month_and_year = $array_data[1];
    $month_name = $array_data[2];
    $year_name = $array_data[3];
    $heading = "MILLENIUM SECURITY LIMITED";
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename= " . $reportName . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "<center><h2>" . $heading . "<br> " . $reportName . "</h2></center>";
    ?>
    <h3 style="color:blue">(1) BANK W/D & A/C PAYEE ONLY:</h3>
    <table style="font-size: 15px; width: 50%" border="1">
        <thead>
            <tr style="color:blue;height: 40px; weight:bold;">
                <th>#</th>
                <th>EXPENSE</th>
                <th>AMOUNT</th>
                <th>ACCOUNT/NO</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_salary = 0;
            $no = 0;
            $nadd = 1;
            $banksquery = "SELECT * FROM bank,expenses WHERE bank.Bank_Id=expenses.Bank_Id group by bank.Bank_Id";
            if (DB::getInstance()->checkRows($banksquery)) {

                $bankList = DB::getInstance()->querySample($banksquery);

                foreach ($bankList as $bank) {
                    $nadd += $no;
                    $no++;
                    $salary = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE Bank_Id='$bank->Bank_Id' AND Expense_Type='Salary' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
                    $total_salary += $salary;
                    ?>

                    <tr>
                        <td><?php echo $no; ?></td>
                        <td>Salary & wages <?php echo $bank->Bank_Name; ?></td> 
                        <td><?php echo number_format($salary); ?></td> 
                        <td style="color: blue;"><?php echo DB::getInstance()->displayTableColumnValue("SELECT * FROM expenses WHERE Bank_Id='$bank->Bank_Id' AND Expense_Type='Salary' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ORDER BY Expenses_Id LIMIT 1", "Bank_Reference"); ?></td>
                    </tr> 

                    <?php
                }
            }

            $total_expense = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE   substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");

            $firearms = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE  Expense_Type='Firearms' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
            $tcash = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE  Expense_Type='Cash Payment' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
            $tcheque = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE  Expense_Type='Cheque Payment' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
            $tvat = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE  Expense_Type='VAT' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");

            $overalltotal_expense = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE   substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
            ?>
            <tr>
                <td><?php echo $nadd + 1; ?></td> 
                <td>Firearms rental </td> 
                <td><?php echo number_format($firearms); ?></td> 
                <td style="color: blue;"><?php echo DB::getInstance()->displayTableColumnValue("SELECT * FROM expenses WHERE  Expense_Type='Firearms' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ORDER BY Expenses_Id LIMIT 1", "Bank_Reference"); ?></td>
            </tr> 

            <tr>
                <td><?php echo $nadd + 2; ?></td> 
                <td>General requirements in cash </td> 
                <td><?php echo number_format($tcash); ?></td> 
                <td style="color: blue;"></td>
            </tr> 

            <tr>
                <td><?php echo $nadd + 3; ?></td> 
                <td>Total chqs.out </td> 
                <td><?php echo number_format($tcheque); ?></td> 
                <td style="color: blue;"></td>
            </tr>
        </tbody> 
        <tfoot>
        <th></th>
        <th>TOTAL</th>
        <th style="color:#FF00FF"><?php echo number_format($total_expense); ?></th>
        <th style="color:#FF00FF"><?php echo number_format($total_expense); ?></th> 
    </tfoot>  

    </table>

    <?php
    $chequequery = "SELECT * FROM expenses WHERE  Expense_Type='Cheque Payment' AND  substr(Date_Submitted,1,7)='$current_month_and_year'";
    if (DB::getInstance()->checkRows($chequequery)) {
        ?>
        <h3 style="color:blue">(2) GENERAL DETAILED CHEQUE PAYMENTS:</h3>
        <table style="font-size: 15px; width: 100px" border="1">
            <thead>
                <tr>
                    <th>#</th>
                    <th>EXPENSE NAME</th>
                    <th>AMOUNT</th>
                    <th>ACCOUNT/NO</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $chequeList = DB::getInstance()->querySample($chequequery);
                $chequeno = 1;
                $total_quque_amount = 0;
                foreach ($chequeList as $cheque) {
                    ?>
                    <tr>
                        <td><?php echo $chequeno; ?></td>
                        <td><?php echo $cheque->Description; ?></td>
                        <td><?php echo number_format($cheque->Amount); ?></td>
                        <td style="color:blue"><?php echo $cheque->Bank_Reference; ?></td>
                        <td style="color:#FF00FF"><?php echo $cheque->Status; ?></td>
                    </tr>
                    <?php
                    $total_quque_amount += $cheque->Amount;
                    $chequeno++;
                }
                ?>
            </tbody>
            <tfoot>
            <th></th>
            <th>TOTAL</th>
            <th style="color:#00000"><?php echo number_format($total_quque_amount); ?></th>
            <th style="color:#FF6600"><?php echo number_format($total_quque_amount); ?></th> 
            <th></th> 

        </tfoot> 
        </table>
        <?php
    }
    $branchquery = "SELECT * FROM branch,expenses where expenses.Branch_Id=branch.Branch_Id group by branch.Branch_Id";
    if (DB::getInstance()->checkRows($branchquery)) {
        ?>
        <h3 style="color:blue">(3) DETAILED CASH PAYMENTS:</h3>
        <?php
        $branchList = DB::getInstance()->querySample($branchquery);
        foreach ($branchList as $branch) {

            $chash_list = DB::getInstance()->querySample("SELECT * FROM expenses WHERE Branch_Id='$branch->Branch_Id' AND Expense_Type='Cash Payment' AND  substr(Date_Submitted,1,7)='$current_month_and_year'");
            ?>
            <h4><?php echo $branch->Branch_Name ?>  </h4>
            <table style="font-size: 15px; width: 100px" border="1">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>EXPENSE NAME</th>
                        <th>AMOUNT</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalcashobtained = 0;
                    $k = 1;
                    foreach ($chash_list as $cash) {
                        ?>
                        <tr>
                            <td><?php echo $k; ?></td>
                            <td><?php echo $cash->Description; ?></td>
                            <td><?php echo number_format($cash->Amount) ?></td>
                            <td></td>
                        </tr>
                        <?php
                        $k++;
                        $totalcashobtained += $cash->Amount;
                    }
                    ?>

                </tbody>
                <tfoot>
                <th></th>
                <th>TOTAL</th>
                <th style="color:blue"><?php echo number_format($totalcashobtained); ?></th>
                <th style="color:#FF0000"><?php echo number_format($totalcashobtained); ?></th>

            </tfoot> 
            </table>
            <?php
        }
        ?>
        <h4></h4>
        <table style="font-size: 15px; width: 100px" border="1">
            <thead
                >
                <tr>
                    <th>DETAILS TOTAL BREAKDOWN (CASH)</th>
                    <th></th>
                    <th ></th>
                    <th style="color:#FF0000"><?php echo number_format($tcash); ?></th>
                </tr>
            </thead>

        </table>
        <h4></h4>
        <table style="font-size: 15px; width: 100px" border="1">
            <thead>
                <tr>
                    <th>VAT FOR <?php echo $month_name . " " . $year_name; ?></th>
                    <th></th>
                    <th ></th>
                    <th style="color:#FF0000"><?php echo number_format($tvat); ?></th>
                </tr>
            </thead>

        </table>
        <h4></h4>
        <table style="font-size: 15px; width: 100px" border="1">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th ></th>

                    <th ></th>
                    <th >TOTAL</th>
                    <th style="color:#FF0000"><?php echo number_format($overalltotal_expense); ?></th>
                </tr>
            </thead>

        </table> 
        <h4>APPROVED BY :</h4>
        <?php
        $querysignatories = "SELECT * FROM signatories  ORDER BY Signatory_Id DESC";
        if (DB::getInstance()->checkRows($querysignatories)) {
            ?>
            <table style="font-size: 15px; width: 100px" border="1">
                <tr>
                    <th>NAMES</th>
                    <th>DESIGNATION</th>
                    <th >SIGNATURE</th>
                    <th >DATE</th>
                </tr>
                <tr>
                    <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person,signatories WHERE staff.Staff_Id=signatories.Staff_Id AND signatories.Role='Managing Director' AND staff.Person_Id=person.Person_Id ", "Fname") . " " . DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person,signatories WHERE staff.Staff_Id=signatories.Staff_Id AND signatories.Role='Managing Director' AND staff.Person_Id=person.Person_Id ", "Lname"); ?></td>
                    <td>Managing Director</td>
                    <td>........................................................................</td>
                    <td>........................................................................</td>
                </tr> 
                <tr>
                    <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person,signatories WHERE staff.Staff_Id=signatories.Staff_Id AND signatories.Role='Human Resource' AND staff.Person_Id=person.Person_Id ", "Fname") . " " . DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person,signatories WHERE staff.Staff_Id=signatories.Staff_Id AND signatories.Role='Human Resource' AND staff.Person_Id=person.Person_Id ", "Lname"); ?></td>
                    <td>Human Resource M.</td>
                    <td>........................................................................</td>
                    <td>........................................................................</td>
                </tr> 
                <tr>
                    <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person,signatories WHERE staff.Staff_Id=signatories.Staff_Id AND signatories.Role='Accountant' AND staff.Person_Id=person.Person_Id ", "Fname") . " " . DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff,person,signatories WHERE staff.Staff_Id=signatories.Staff_Id AND signatories.Role='Accountant' AND staff.Person_Id=person.Person_Id ", "Lname"); ?></td>
                    <td>Accountant</td>
                    <td>........................................................................</td>
                    <td>........................................................................</td>
                </tr> 


            </table> 
            <?php
        }
    }
} else if (isset($_GET['download_type']) && $_GET['download_type'] == "download_bank_salary_report" && $_GET['data_sent'] != "") {
    $array_data = unserialize($crypt->decode($_GET['data_sent']));
    $queryguard = $array_data[0];
    $queryofficer = $array_data[1];
    $reportName = $array_data[2];
    $current_month_and_year = $array_data[3];
    $heading = "MILLENIUM SECURITY LIMITED";
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename= " . $reportName . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "<center><h3>" . $heading . "<br> " . $reportName . "</h3></center>";
    ?>
    <table style="font-size: 15px; width: 100px" border="1">
        <thead>
            <tr style="color:blue;height: 40px;weight:bold;">
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
            $totalNetPay = 0;
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
                $totalNetPay += $netpay;
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
            $totalNetPay = 0;
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
                $totalNetPay += $netpay;
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

    </table>
    <?php
} else if (isset($_GET['download_type']) && $_GET['download_type'] == "download_gaurd_payroll_report" && $_GET['data_sent'] != "") {
    $array_data = unserialize($crypt->decode($_GET['data_sent']));
    $querystaff = $array_data[0];
    $reportName = $array_data[1];
    $current_month_and_year = $array_data[2];
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename= " . $reportName . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "<center><h2>" . $reportName . "</h2></center>";
    ?>
    <table style="font-size: 15px;" border="1">
        <thead>
            <tr style="color:blue;height: 40px;weight:bold;">
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
    <?php
} else if (isset($_GET['download_type']) && $_GET['download_type'] == "download_officer_payroll_report" && $_GET['data_sent'] != "") {
    $array_data = unserialize($crypt->decode($_GET['data_sent']));
    $querystaff = $array_data[0];
    $reportName = $array_data[1];
    $current_month_and_year = $array_data[2];
    header("Content-Type: application/xls");
    header("Content-Disposition: attachment; filename= " . $reportName . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "<center><h2>" . $reportName . "</h2></center>";
    ?>
    <table style="font-size: 15px;" border="1">
        <thead>
            <tr style="color:blue;height: 40px; width: 100px;weight:bold;">
                <th>S/No</th>
                <th>Staff Name</th>
                <th>Basic Pay</th>
                <th>House Allowance</th>
                <th>Meals Allowance</th>
                <th>Medical Allowance</th>
                <th>Transport Allowance</th>
                <th>Gross Pay</th>
                <th>PF 2.5%</th>
                <th>NSSF.(5%)</th>
                <th>PAYE</th>
                <th>Loan/Advance</th>
                <th>Loan/OT</th>
                <th>Net Payment</th>
                <th>Others</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $staffList = DB::getInstance()->querySample($querystaff);
            $totalNetPay = 0;
            $tloan_earn = 0;
            $tLloan = 0;
            $tadvance = 0;
            $tpf_tax = 0;
            $tnssf = 0;
            $tpaye = 0;
            $thouse = 0;
            $tmeal = 0;
            $tmedical = 0;
            $ttransport = 0;
            $tnetpay = 0;
            $tgrosspay = 0;
            foreach ($staffList as $staff) {
                $basic_pay = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff->Staff_Id' AND substr(Date_From,1,7)<='$current_month_and_year' order by Salary_Scale_Id DESC limit 1", "Salary_Scale");
                $loan_earn = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM loan WHERE Staff_Id='$staff->Staff_Id' AND substr(Loan_Date,1,7)='$current_month_and_year' AND Loan_Type='Loan/OT'", "Amount_Paid");
                $Lloan = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM loan WHERE Staff_Id='$staff->Staff_Id' AND substr(Loan_Date,1,7)='$current_month_and_year' AND Loan_Type='L/Loan'", "Amount_Paid");
                $advance = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_payments WHERE Staff_Id='$staff->Staff_Id' AND substr(Payment_Date,1,7)='$current_month_and_year' AND Payment_Type='Advance'", "Amount_Paid");

                $house = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id'  AND substr(Date_From,1,7)<='$current_month_and_year' order by Allawence_Id DESC limit 1", "House");
                $meal = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id'  AND substr(Date_From,1,7)<='$current_month_and_year' order by Allawence_Id DESC limit 1", "Meal");
                $medical = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id'  AND substr(Date_From,1,7)<='$current_month_and_year' order by Allawence_Id DESC limit 1", "Medical");
                $transport = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM allowance WHERE Staff_Id='$staff->Staff_Id'  AND substr(Date_From,1,7)<='$current_month_and_year' order by Allawence_Id DESC limit 1", "Transport");

                $thouse += $house;
                $tmeal += $meal;
                $tmedical += $medical;
                $ttransport += $transport;

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
                $totalNetPay += $netpay;

                $tloan_earn += $loan_earn;
                $tLloan += $Lloan;
                $tadvance += $advance;
                $tpf_tax += $pf_tax;
                $tnssf += $nssf;
                $tpaye += $paye;
                $tnetpay += $netpay;
                $tgrosspay += $grosspay;
                ?>
                <tr>
                    <td><?php echo $staff->Serial_Number ?></td>
                    <td><?php echo $staff->Fname . ' ' . $staff->Lname ?></td>
                    <td><?php echo number_format($basic_pay); ?></td>
                    <td><?php echo number_format($house); ?></td>
                    <td><?php echo number_format($meal); ?></td>
                    <td><?php echo number_format($medical); ?></td>
                    <td><?php echo number_format($transport); ?></td>
                    <td><?php echo number_format($grosspay); ?></td>
                    <td><?php echo number_format($pf_tax); ?></td>
                    <td><?php echo number_format($nssf); ?></td>
                    <td><?php echo number_format($paye); ?></td>
                    <td style="color: red"><?php echo number_format($Lloan + $advance); ?></td>
                    <td style="color: green"><?php echo number_format($loan_earn); ?></td>
                    <td><?php echo number_format($netpay); ?></td>
                    <td></td>

                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th>TOTAL</th>
                <th></th>
                <th></th>
                <th style="color:blue"><?php echo number_format($thouse); ?></th>
                <th style="color:blue"><?php echo number_format($tmeal); ?></th>
                <th style="color:blue"><?php echo number_format($tmedical); ?></th>
                <th style="color:blue"><?php echo number_format($ttransport); ?></th>
                <th style="color:blue"><?php echo number_format($tgrosspay); ?></th>
                <th style="color:blue"><?php echo number_format($tpf_tax); ?></th>
                <th style="color:blue"><?php echo number_format($tnssf); ?></th>
                <th style="color:blue"><?php echo number_format($tpaye); ?></th>
                <th><?php echo number_format($tLloan + $tadvance); ?></th>
                <th style="color:blue"><?php echo number_format($tloan_earn); ?></th>
                <th><?php echo number_format($totalNetPay) ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    <?php
} else {
    echo 'No data already registered';
}
?>