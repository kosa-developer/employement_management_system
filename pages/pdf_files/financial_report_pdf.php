<?php

require ('pdf_header.php');
$pdf = new PDF();
$pdf->AliasNbPages();
if (isset($_GET['download_type']) && $_GET['download_type'] == "download_bank_salary_report" && $_GET['data_sent'] != "") {
    $array_data = unserialize($crypt->decode($_GET['data_sent']));
    $queryguard = $array_data[0];
    $queryofficer = $array_data[1];
    $reportName = $array_data[2];
    $current_month_and_year = $array_data[3];
    $heading = "MILLENIUM SECURITY LIMITED";
    $main_title = $heading . " " . $reportName;
    $pdf->AddPage();
    $pdf->SetTextColor(180, 0, 16);
    $pdf->createHeader($heading, 185);
    $pdf->Cell(0, 5, "$reportName", 0, 1, "C");
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(10, 7, "S/NO", 1, 0, "L");
    $pdf->Cell(30, 7, "SVC NO", 1, 0, "L");
    $pdf->Cell(40, 7, "ACCOUNT NAME", 1, 0, "L");
    $pdf->Cell(40, 7, "ACCOUNT/NO", 1, 0, "L");
    $pdf->Cell(25, 7, "AMOUNT(UGX)", 1, 0, "L");
    $pdf->Cell(25, 7, "BRANCH", 1, 0, "L");
    $pdf->Cell(25, 7, "REMARKS", 1, 1, "L");

    $pdf->SetFont("Arial", "", 8);
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
                                                               
        $pdf->Cell(10, 7, "$staff->Serial_Number", 1, 0, "L");
        $pdf->Cell(30, 7, "$staff->Service_Number", 1, 0, "L");
        $pdf->Cell(40, 7, DB::getInstance()->DisplayTableColumnValue("SELECT * FROM bank WHERE Bank_Id='$staff->Bank_Id'", "Bank_Name"), 1, 0, "L");
        $pdf->Cell(40, 7, $staff->Account_Number, 1, 0, "L");
        $pdf->Cell(25, 7, number_format($netpay), 1, 0, "L");
        $pdf->Cell(25, 7, $staff->Branch, 1, 0, "L");
        $pdf->Cell(25, 7, "", 1, 1, "L");
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
        $pdf->Cell(10, 7, $officer->Serial_Number, 1, 0, "L");
        $pdf->Cell(30, 7, $officer->Service_Number, 1, 0, "L");
        $pdf->Cell(40, 7, DB::getInstance()->DisplayTableColumnValue("SELECT * FROM bank WHERE Bank_Id='$officer->Bank_Id'", "Bank_Name"), 1, 0, "L");
        $pdf->Cell(40, 7, $officer->Account_Number, 1, 0, "L");
        $pdf->Cell(25, 7, number_format($netpay), 1, 0, "L");
        $pdf->Cell(25, 7, $officer->Branch, 1, 0, "L");
        $pdf->Cell(25, 7, "", 1, 1, "L");
         
    }
     $pdf->Cell(10, 7, "TOTAL", 1, 0, "L");
    $pdf->Cell(30, 7, "", 1, 0, "L");
    $pdf->Cell(40, 7, "", 1, 0, "L");
    $pdf->Cell(40, 7, "", 1, 0, "L");
    $pdf->Cell(25, 7, number_format($officertotalNetPay+$guardtotalNetPay), 1, 0, "L");
    $code_data=number_format($officertotalNetPay+$guardtotalNetPay);
    $pdf->Cell(25, 7, "", 1, 0, "L");
    $pdf->Cell(25, 7, "", 1, 1, "L");
    $pdf->Image("http://127.0.0.1/Millenium%20Security%20Limited/index.php?page=x9QyL2C9h6ONVY0h5vYBfhPjK2Shnm8mp23xPBelBac&code=$code_data",5,273,25,25,"png");
 
    $pdf->AutoPrint();
    $pdf->Output();
} else if (($_GET['type']) && ($_GET['type'] == "download_staff_payments_pdf")) {
    $data_sent = unserialize($crypt->decode($_GET["data_sent"]));
    $queryPayments = $data_sent[0];
    $fileHeading = $data_sent[1];
    $pdf->AddPage();
    $pdf->SetTextColor(180, 0, 16);
    $pdf->createHeader($hospital_main_title, 185);
    $pdf->Cell(0, 5, "$fileHeading", 0, 1, "L");
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 4, "Staff Name", 1, 0, "L");
    $pdf->Cell(40, 4, "Payment Date", 1, 0, "L");
    $pdf->Cell(40, 4, "Amount Paid", 1, 0, "L");
    $pdf->Cell(40, 4, "Paid as", 1, 1, "L");
    $paymentsList = DB::getInstance()->querySample($queryPayments);
    $totalPayments = 0;
    foreach ($paymentsList as $list) {
        $totalPayments += $list->Amount_Paid;
        $pdf->SetFont("Arial", "", 8);
        $pdf->Cell(60, 4, $list->Fname . " " . $list->Lname, 1, 0, "L");
        $pdf->Cell(40, 4, english_date($list->Payment_Date), 1, 0, "L");
        $pdf->Cell(40, 4, number_format($list->Amount_Paid), 1, 0, "L");
        $pdf->Cell(40, 4, $list->Payment_Type, 1, 1, "L");
    }
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 4, "", 1, 0, "L");
    $pdf->Cell(40, 4, "TOTAL", 1, 0, "L");
    $pdf->Cell(40, 4, number_format($totalPayments), 1, 0, "L");
    $pdf->Cell(40, 4, "", 1, 1, "L");

    $pdf->AutoPrint();
    $pdf->Output();
} else if (($_GET['type']) && ($_GET['type'] == "download_patient_payments_pdf")) {
    $data_sent = unserialize($crypt->decode($_GET["data_sent"]));
    $queryPayments = $data_sent[0];
    $fileHeading = $data_sent[1];
    $pdf->AddPage();
    $pdf->SetTextColor(180, 0, 16);
    $pdf->createHeader($hospital_main_title, 185);
    $pdf->Cell(0, 5, "$fileHeading", 0, 1, "L");
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 4, "Patient", 1, 0, "L");
    $pdf->Cell(40, 4, "Contact", 1, 0, "L");
    $pdf->Cell(40, 4, "Amount Paid", 1, 1, "L");
    $paymentsList = DB::getInstance()->querySample($queryPayments);
    $totalPayments = 0;
    foreach ($paymentsList as $list) {
        $totalPayments += $list->Amount_Paid;
        $pdf->SetFont("Arial", "", 8);
        $pdf->Cell(60, 4, $list->Fname . " " . $list->Lname, 1, 0, "L");
        $pdf->Cell(40, 4, "$list->Phone_Number", 1, 0, "L");
        $pdf->Cell(40, 4, number_format($list->Amount_Paid), 1, 1, "L");
    }
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 4, "", 1, 0, "L");
    $pdf->Cell(40, 4, "TOTAL", 1, 0, "L");
    $pdf->Cell(40, 4, number_format($totalPayments), 1, 1, "L");

    $pdf->AutoPrint();
    $pdf->Output();
} else if (($_GET['type']) && ($_GET['type'] == "print_patient_receipt") && $_GET["receipt_data"] != "") {
    $receipt_data = unserialize($crypt->decode($_GET['receipt_data']));
    $patient_id = $receipt_data[0];
    $prescription_data = $receipt_data[1];
    $service_data = $receipt_data[2];
    $accomodation_data = $receipt_data[3];
    $patientQuery = "SELECT CONCAT(Fname,' ',Lname) AS Full_Names,Person_Number FROM patient,person WHERE person.Person_Id=patient.Person_Id AND patient.Patient_Id='$patient_id'";
    $full_names = DB::getInstance()->DisplayTableColumnValue($patientQuery, "Full_Names");
    $person_number = DB::getInstance()->DisplayTableColumnValue($patientQuery, "Person_Number");
    $pdf->AddPage();
    $pdf->SetTextColor(180, 0, 16);
    $pdf->createHeader($hospital_main_title, 185);
    $pdf->Cell(0, 5, "Name: $full_names             IPD No. $person_number                    Date: " . english_date($date_today) . "", 0, 1, "L");
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(20, 4, "#", 1, 0, "L");
    $pdf->Cell(60, 4, "Payment of", 1, 0, "L");
    $pdf->Cell(60, 4, "Amount paid", 1, 1, "L");
    $pdf->SetFont("Arial", "", 8);
    $number = 0;
    $total_amount = 0;
    if (!empty($prescription_data)) {
        for ($i = 0; $i < count($prescription_data); $i++) {
            $prescription_data_array = explode("_", $prescription_data[$i]);
            $number++;
            $total_amount += $prescription_data_array[1];
            $pdf->Cell(20, 4, "$number", 1, 0, "L");
            $pdf->Cell(60, 4, "$prescription_data_array[2]", 1, 0, "L");
            $pdf->Cell(60, 4, number_format($prescription_data_array[1]), 1, 1, "R");
        }
    }if (!empty($service_data)) {
        for ($i = 0; $i < count($service_data); $i++) {
            $service_data_array = explode("_", $service_data[$i]);
            $service_price = DB::getInstance()->getName("service", $service_data_array[0], "Price", "Service_Id");
            $number++;
            $total_amount += $service_data_array[1];
            $pdf->Cell(20, 4, "$number", 1, 0, "L");
            $pdf->Cell(60, 4, "$service_data_array[2]", 1, 0, "L");
            $pdf->Cell(60, 4, number_format($service_data_array[1]), 1, 1, "R");
        }
    }
    if (!empty($accomodation_data)) {
        for ($i = 0; $i < count($accomodation_data); $i++) {
            $accomodation_data_array = explode("_", $accomodation_data[$i]);
            $number++;
            $total_amount += $accomodation_data_array[1];
            $pdf->Cell(20, 4, "$number", 1, 0, "L");
            $pdf->Cell(60, 4, "$accomodation_data_array[2]", 1, 0, "L");
            $pdf->Cell(60, 4, number_format($accomodation_data_array[1]), 1, 1, "R");
        }
    }

    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(20, 4, "TOTAL", 1, 0, "L");
    $pdf->Cell(120, 4, number_format($total_amount), 1, 1, "R");
    $pdf->Cell(0, 5, "", 0, 1, "L");
    $pdf->Write(5, "Amount in words:  ");
    $pdf->SetFont("Arial", "U", 8);
    $pdf->Write(5, NumberToWord::getInstance()->toText($total_amount) . " Shillings only.");
    $pdf->Code39(5, 270, $total_amount, $person_number, 0.5, 20);
    $pdf->AutoPrint();
    $pdf->Output();
} else if (($_GET['type']) && ($_GET['type'] == "download_patient_invoice_pdf")) {
    $data_sent = unserialize($crypt->decode($_GET["data_sent"]));
    $queryPayments = $data_sent[0];
    $fileHeading = $data_sent[1];
    $pdf->AddPage();
    $pdf->SetTextColor(180, 0, 16);
    $pdf->createHeader($hospital_main_title, 185);
    $pdf->Cell(0, 5, "$fileHeading", 0, 1, "L");
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(30, 4, "Payment date", 1, 0, "L");
    $pdf->Cell(80, 4, "Payment of", 1, 0, "L");
    $pdf->Cell(40, 4, "Amount Paid", 1, 1, "L");
    $paymentsList = DB::getInstance()->querySample($queryPayments);
    $totalPayments = 0;
    foreach ($paymentsList as $payments) {
        $column_height = 4;
        $service_name = "";
        if ($payments->Payment_Type == "Other Services" || $payments->Payment_Type == "Lab Tests") {
            $service_name = ' (' . DB::getInstance()->getName("service", $payments->Service_Id, "Service_Name", "Service_Id") . ')';
        } else if ($payments->Payment_Type == "Drugs") {
            $drugsQuery = "SELECT * FROM patient_drug_taken WHERE Prescription_Id='$payments->Prescription_Id'";
            $countTotals = DB::getInstance()->countElements($drugsQuery);
            $column_height = ($countTotals > 0) ? ($countTotals * $column_height) + $column_height : $column_height;
        }
        $totalPayments += $payments->Amount_Paid;
        $pdf->SetFont("Arial", "", 8);
        $pdf->Cell(30, $column_height, english_date($payments->Time), 1, 0, "L");
        $pdf->Cell(1, 4, "$payments->Payment_Type $service_name", 0, 0, "");
        $pdf->Cell(-1, 4, "", 0, 0, "");
        $pdf->Cell(80, $column_height, "", 1, 0, "L");
        $pdf->Cell(40, $column_height, number_format($payments->Amount_Paid), 1, 1, "L");
        if ($payments->Payment_Type == "Drugs") {
            $countTotals = DB::getInstance()->countElements($drugsQuery);
            $drugList = DB::getInstance()->querySample($drugsQuery);
            foreach ($drugList AS $drugs) {
                $drug_name = DB::getInstance()->DisplayTableColumnValue("SELECT Drug_Name FROM drugs_in_pharmacy,drugs_in_store,drug_names WHERE drugs_in_pharmacy.Stock_Id=drugs_in_store.Stock_Id AND drugs_in_store.Drug_Id=drug_names.Drug_Id AND drugs_in_pharmacy.Stocking_Id='$drugs->Stocking_Id'", "Drug_Name");
                $pdf->Cell(30, -4, "", 0, 0, "L");
                $pdf->Cell(80, -4, $drug_name . " Qty=" . $drugs->Quantity_Taken . " at " . $drugs->Price_Per . " each", 0, 1, "L");
            }
            $pdf->Cell(0, $countTotals * 4, "", 0, 1, "L");
        }
    }
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(30, 4, "", 1, 0, "L");
    $pdf->Cell(80, 4, "TOTAL", 1, 0, "L");
    $pdf->Cell(40, 4, number_format($totalPayments), 1, 1, "L");

    $pdf->Write(5, "\nAmount in words: ");
    $pdf->SetFont("Arial", "U", 8);
    $pdf->Write(5, NumberToWord::getInstance()->toText($totalPayments) . " Shillings only.");
    $pdf->AutoPrint();
    $pdf->Output();
} else if (isset($_GET['type']) && ($_GET['type'] == "downloadpurchase_orders") && $_GET['order_id'] != "") {
    $pdf->AddPage();
    $lpo_number = "";
    $order_id = $crypt->decode($_GET['order_id']);
    $ordersCheck = "SELECT * FROM purchase_order WHERE Purchase_Id='$order_id'";
    $orderList = DB::getInstance()->query($ordersCheck);
    foreach ($orderList->results() as $orders) {
        $total_cost = 0;
        $pdf->SetTextColor(180, 0, 16);
        $pdf->createHeader($hospital_main_title, 185);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 5, "LOCAL PURCHASE ORDER", 0, 1, "L");
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont("Arial", "B", 7);

        $pdf->Cell(50, 4, "NO.", 0, 0, "L");
        $pdf->Cell(50, 4, "Date", 0, 0, "L");
        $pdf->Cell(70, 4, "To Supplier", 0, 1, "L");

        $lpo_number = $orders->LPO_Number;
        $pdf->SetFont("Arial", "U", 7);
        $pdf->Cell(50, 4, $orders->LPO_Number, 0, 0, "L");
        $pdf->Cell(50, 4, english_date($orders->Date), 0, 0, "L");
        $pdf->Cell(70, 4, DB::getInstance()->getName("suppliers", $orders->Supplier_Id, "Supplier_Name", "Supplier_Id"), 0, 1, "L");
        $pdf->Line(10, 30, 205, 30);

        $pdf->SetFont("Arial", "B", 7);
        $pdf->Cell(20, 5, "Item Type", 1, 0, "L");
        $pdf->Cell(80, 5, "Goods Description", 1, 0, "L");
        $pdf->Cell(20, 5, "Quantity", 1, 0, "L");
        $pdf->Cell(25, 5, "Unit Cost", 1, 0, "L");
        $pdf->Cell(30, 5, "Total", 1, 1, "L");
        $pdf->SetFont("Arial", "", 5);

        $item_types = unserialize($orders->Final_Item_Types);
        $final_goods_ordered = unserialize($orders->Final_Description);
        $quantity_ordered = unserialize($orders->Final_Quantity);
        $unit_price_ordered = unserialize($orders->Final_Unit_Cost);
        for ($x = 0; $x < count($final_goods_ordered); $x++) {
            $item_name = "";
            if ($item_types[$x] == "Drugs") {
                $item_name = DB::getInstance()->getName("drug_names", $final_goods_ordered[$x], "Drug_Name", "Drug_Id");
            }
            if ($item_types[$x] == "Sandries") {
                $item_name = DB::getInstance()->getName("sandries", $final_goods_ordered[$x], "Sandry_Name", "Sandry_Id");
            }
            if ($item_types[$x] == "Assets") {
                $item_name = DB::getInstance()->getName("asset", $final_goods_ordered[$x], "Asset_Name", "Asset_Id");
            }
            $pdf->Cell(20, 5, "$item_types[$x]", 1, 0, "L");
            $pdf->Cell(80, 5, $item_name, 1, 0, "L");
            $pdf->Cell(20, 5, $quantity_ordered[$x], 1, 0, "L");
            $pdf->Cell(25, 5, $unit_price_ordered[$x], 1, 0, "L");
            $pdf->Cell(30, 5, $unit_price_ordered[$x] * $quantity_ordered[$x], 1, 1, "L");
            $total_cost += ($unit_price_ordered[$x] * $quantity_ordered[$x]);
        }
        $pdf->SetFont("Arial", "B", 10);
        $pdf->Cell(100, 8, "Total Cost:    " . ugandan_shillings($total_cost), 0, 1, "L");
        $pdf->Write(8, "Amount in words:   " . NumberToWord::getInstance()->toText($total_cost) . " shillings only.\n");

        $staffCheck = "SELECT CONCAT(FName,' ',Lname)AS Names FROM user,staff,person WHERE person.Person_Id=staff.Person_Id AND user.Staff_Id=staff.Staff_Id AND user.User_Id='$orders->Ordered_By' ORDER BY Names";
        $usernames = DB::getInstance()->displayTableColumnValue($staffCheck, 'Names');
        $pdf->Cell(100, 8, "Prepared By:    " . $usernames, 0, 0, "L");
        $approved_by_query = "SELECT CONCAT(FName,' ',Lname)AS Names FROM user,staff,person WHERE person.Person_Id=staff.Person_Id AND user.Staff_Id=staff.Staff_Id AND user.User_Id='$orders->Approved_By' ORDER BY Names";
        $approved_by = DB::getInstance()->displayTableColumnValue($approved_by_query, 'Names');
        //Approved by
        $pdf->Cell(100, 8, "Approved By:    $approved_by", 0, 1, "L");
    }
    $pdf->AutoPrint();
    $pdf->Output();
    //$pdf->output('D', 'Purchase order ' . $lpo_number . '.pdf');
} else if (isset($_GET["type"]) && $_GET["type"] == $crypt->encode('download_income_statement') && Input::get("data_sent") != "") {
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $array_sent = unserialize($crypt->decode(Input::get("data_sent")));
    $incomeQuery = $array_sent[0];
    $totalSales = 0;
    $return_inwards = 0;
    $totalSales += $array_sent[1];
    $bad_debts = $array_sent[2];
    $purchasesAmount = $array_sent[3];
    $operatingExpensesQuery = $array_sent[4];
    $openingStock = $array_sent[5];
    $closingStock = $array_sent[6];
    $return_outwards = $array_sent[7];
    $cost_of_sales = $array_sent[8];
    $gross_profit = $array_sent[9];
    $wages_and_salaries = $array_sent[10];
    $headingTitle = $array_sent[11];
    $incomeCondition = $array_sent[12];
    $expensesCondition = $array_sent[13];
    $depreciationQuery = $array_sent[14];

    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->createHeader($hospital_main_title, 185);
    $pdf->SetFont("Arial", "B", 10);
    $pdf->Cell(180, 5, $headingTitle, 0, 1, "L");
    $pdf->SetFont("Arial", "B", 8);

    $pdf->Cell(60, 5, "Particular (Details/ Items)", 1, 0, "L");
    $pdf->Cell(45, 5, "Amount", 1, 0, "L");
    $pdf->Cell(45, 5, "Amount", 1, 0, "L");
    $pdf->Cell(45, 5, "Amount", 1, 1, "L");
    $pdf->SetFont("Arial", "", 8);

    $pdf->Cell(60, 5, "Sales", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($totalSales), 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");

    $pdf->Cell(60, 5, "Less return inwards", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($return_inwards), 1, 0, "L");
    $pdf->Cell(45, 5, ($totalSales - $return_inwards < 0) ? "(" . number_format(abs($totalSales - $return_inwards)) . ")" : number_format($totalSales - $return_inwards), 1, 1, "L");

    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 5, "Cost of Sales", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");

    $pdf->SetFont("Arial", "", 8);
    $pdf->Cell(60, 5, "Opening stock", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($openingStock), 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");

    $pdf->Cell(60, 5, "Purchases", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($purchasesAmount), 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");

    $pdf->Cell(60, 5, "Less return outwards", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($return_outwards), 1, 0, "L");
    $pdf->Cell(45, 5, number_format($cost_of_sales - $return_outwards), 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");

    $pdf->Cell(60, 5, "Less closing stock", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($closingStock), 1, 0, "L");
    $pdf->Cell(45, 5, ($cost_of_sales - ($return_inwards + $closingStock) < 0) ? "(" . number_format(abs($cost_of_sales - ($return_inwards + $closingStock))) . ")" : number_format($cost_of_sales - ($return_inwards + $closingStock)), 1, 1, "L");

    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 5, "Gross profit", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, ($gross_profit < 0) ? "(" . number_format(abs($gross_profit)) . ")" : number_format($gross_profit), 1, 1, "L");

    $pdf->Cell(60, 5, "Add other incomes", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");

    $pdf->SetFont("Arial", "", 8);
    $totalIncomes = 0;
    $incomeList = DB::getInstance()->query($incomeQuery);
    $x = 0;
    foreach ($incomeList->results() as $income_list) {
        $totalIncomes += $incomeAmount = DB::getInstance()->calculateSum("SELECT Amount FROM income WHERE Item_Id='$income_list->Item_Id' $incomeCondition", "Amount");
        $pdf->Cell(60, 5, "$income_list->Item_Name", 1, 0, "L");
        $pdf->Cell(45, 5, "", 1, 0, "L");
        $pdf->Cell(45, 5, number_format($incomeAmount), 1, 0, "L");
        $pdf->Cell(45, 5, ($x == count($incomeList)) ? number_format($totalIncomes) : "", 1, 1, "L");
        $x++;
    }

    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 5, "Gross income", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, ($gross_profit + $totalIncomes < 0) ? "(" . number_format(abs($gross_profit + $totalIncomes)) . ")" : number_format($gross_profit + $totalIncomes), 1, 1, "L");

    $pdf->Cell(60, 5, "Less Operating Expenses", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");

    $pdf->SetFont("Arial", "", 8);
    $pdf->Cell(60, 5, "Bad debts", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($bad_debts), 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");

    $operatingExpensesList = DB::getInstance()->querySample($operatingExpensesQuery);
    foreach ($operatingExpensesList as $expenses_list) {
        $totalOperatingExpenses += $expensesAmount = DB::getInstance()->calculateSum("SELECT Amount FROM expenses WHERE Item_Id='$expenses_list->Item_Id' AND Expense_Type='Operating Expenses' $expensesCondition", "Amount");
        $pdf->Cell(60, 5, $expenses_list->Item_Name, 1, 0, "L");
        $pdf->Cell(45, 5, number_format($expensesAmount), 1, 0, "L");
        $pdf->Cell(45, 5, "", 1, 0, "L");
        $pdf->Cell(45, 5, "", 1, 1, "L");
    }

    $pdf->Cell(60, 5, "Wages and salries", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($wages_and_salaries), 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");
    $totalDepreciation = 0;
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 5, "Depreciation", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 1, "L");
    $depreciationList = DB::getInstance()->querySample($depreciationQuery);
    $x = 0;
    foreach ($depreciationList AS $deplist) {
        $unit_price = 0 + $deplist->Unit_Price;
        $x++;
        $expiry_date = ($deplist->Is_Removed == 1) ? $deplist->Removed_On : $date_today;
        $yearsEllapsed = round(calculateDateDifference($deplist->Date_Received, $expiry_date, "years"), 2);
        $depreciation = round(($deplist->Depreciation_Rate * $unit_price * $yearsEllapsed) / 100, 2);
        $depreciation_display = ($depreciation > $unit_price) ? "Depreciation Exceeded " . $unit_price : "(" . ($deplist->Depreciation_Rate . "/100)" . "x" . $unit_price) . "x" . $yearsEllapsed . "=" . $depreciation;
        $totalDepreciation += $depreciation = ($depreciation > $unit_price) ? $unit_price : $depreciation;
        $pdf->SetFont("Arial", "", 6);
        $pdf->Cell(60, 5, $deplist->Asset_Name . " (" . english_date($deplist->Date_Received) . ")", 1, 0, "L");
        $pdf->Cell(45, 5, "$depreciation_display", 1, 0, "L");
        //$pdf->SetFont("Arial", "", 8);
        $pdf->Cell(45, 5, number_format($depreciation), 1, 0, "L");
        $pdf->Cell(45, 5, ($x == count($depreciationList)) ? number_format($totalDepreciation) : "", 1, 1, "L");
    }

    $totalOperatingExpenses += $bad_debts + $wages_and_salaries + $totalDepreciation;
    $pdf->SetFont("Arial", "B", 8);
    $pdf->Cell(60, 5, "Total expenses", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, number_format($totalOperatingExpenses), 1, 1, "L");

    $pdf->Cell(60, 5, "Net profit", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, "", 1, 0, "L");
    $pdf->Cell(45, 5, ($gross_profit + $totalIncomes - $totalOperatingExpenses < 0) ? "(" . number_format(abs($gross_profit + $totalIncomes - $totalOperatingExpenses)) . ")" : number_format($gross_profit + $totalIncomes - $totalOperatingExpenses), 1, 1, "L");

    $pdf->AutoPrint();
    $pdf->Output();
} else {
    redirect("We are unable to process your request at this time", "index.php?page=" . $crypt->encode("dashboard"));
}
?>
