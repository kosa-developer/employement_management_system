<?php
if (isset($_POST["invoicing"]) && $_POST['invoicing'] == "invoicing") {
   
    $billing_total= $_POST['total_bill'] ;
    $billing_id=$_POST['billing_id'] ;
   $rate=$_POST['rate'] ;
   $amount_per_day=serialize($_POST['amount_per_day']) ;
    $period=serialize($_POST['period']);
   $working_days=serialize($_POST['working_days']) ;
         if (DB::getInstance()->checkRows("SELECT * FROM invoicing WHERE Billing_Id='$billing_id'")) {
                                                 $dataUpdate = DB::getInstance()->query("UPDATE invoicing SET Period='$period',Working_Days='$working_days',Rate='$rate',Amount_Perdays='$amount_per_day',Total_Amount='$billing_total' WHERE Billing_Id='$billing_id'");
                                                  $action_made = "Data updated";
                                             } else {
                                                $dataUpdate = DB::getInstance()->insert("invoicing", array(
                                                    "Billing_Id" => $billing_id,
                                                    "Period" => $period,
                                                    "Working_Days" => $working_days,
                                                    "Rate" =>  $rate,
                                                    "Amount_Perdays" => $amount_per_day,
                                                    "Total_Amount" => $billing_total
                                                ));
                                               
                                                $action_made = "Data submitted";
                                          
                                        }
                                        if($dataUpdate){
                                           echo $action_made; 
                                        }
 }


if (isset($_POST["returnStaffExpectedPayment"]) && $_POST['staff_id'] != "") {
    $staff_id = Input::get('staff_id');
    $payment_date = Input::get("payment_date");
    $payment_type = Input::get("payment_type");

    $total_salaries_paid = DB::getInstance()->calculateSum("SELECT Amount_Paid FROM staff_payments WHERE Staff_Id='$staff_id'", "Amount_Paid");
    //dummy expected
    $total_salary_expected = 0;
    $start_date = DB::getInstance()->displayTableColumnValue("SELECT Enrollment_Date FROM staff WHERE Staff_Id='$staff_id'", "Enrollment_Date");
    $end_date = $payment_date;
    $begin = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = new DateInterval('P1M');
    $daterange = new DatePeriod($begin, $interval, $end);

    foreach ($daterange as $date) {
        $actual_date = $date->format("Y-m-d");
        $current_month_and_year = $date->format("Y-m");
        $monthly_salary = DB::getInstance()->displayTableColumnValue("SELECT Salary_Scale FROM staff_salary_scale WHERE Staff_id='$staff_id' AND substr(Date_From,1,7)<='$current_month_and_year' ORDER BY Date_From DESC LIMIT 1", "Salary_Scale");
        $daily_rate = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM daily_rates WHERE Staff_Id='$staff_id' AND substr(Date_From,1,7)<='$current_month_and_year' ORDER BY Date_From DESC LIMIT 1", "Daily_Rate");
        $overtime_rate = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM daily_rates WHERE Staff_Id='$staff_id'AND substr(Date_From,1,7)<='$current_month_and_year'  ORDER BY Date_From DESC LIMIT 1", "Overtime_Rate");
        $nssf = (DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff_id' AND substr(Date_From,1,7)<='$current_month_and_year' ORDER BY Date_From DESC LIMIT 1", "Salary_Scale") != '') ? DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff_id' AND substr(Date_From,1,7)<='$current_month_and_year' ORDER BY Date_From DESC LIMIT 1", "Salary_Scale") * 0.05 : 0;
        $provtax = (DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff_id' AND substr(Date_From,1,7)<='$current_month_and_year' ORDER BY Date_From DESC LIMIT 1", "Salary_Scale") != '') ? DB::getInstance()->DisplayTableColumnValue("SELECT * FROM staff_salary_scale WHERE Staff_Id='$staff_id' AND substr(Date_From,1,7)<='$current_month_and_year' ORDER BY Date_From DESC LIMIT 1", "Salary_Scale") * 0.025 : 0;
        $loan_earn = DB::getInstance()->calculateSum("SELECT * FROM loan WHERE Staff_Id='$staff_id' AND Loan_Type='Loan/OT' AND substr(Loan_Date,1,7)='$current_month_and_year'", "Amount_Paid");
        $Lloan = DB::getInstance()->calculateSum("SELECT * FROM loan WHERE Staff_Id='$staff_id' AND Loan_Type='L/Loan' AND substr(Loan_Date,1,7)='$current_month_and_year'", "Amount_Paid");
        $dail_days = DB::getInstance()->countElements("SELECT * FROM staff_attendance where Staff_Id='$staff_id' AND substr(Date,1,7)='$current_month_and_year' and Is_Present='1'");
        $overtime_days = DB::getInstance()->countElements("SELECT * FROM staff_overtime_attendency where Staff_Id='$staff_id' AND substr(Date,1,7)='$current_month_and_year' and Is_Present='1'");
        $monthly_salary = ($monthly_salary != "") ? $monthly_salary : 0;
        $total_salary_expected1 += $monthly_salary;
        $total_daily_rates += $daily_rate;
        $total_overtime_rates += $overtime_rate;
        $total_nssf += $nssf;
        $total_provtax += $provtax;
        $total_loan_earn += $loan_earn;
        $total_Lloan += $Lloan;
        $total_daily_days += $dail_days;
        $total_overtime_days += $overtime_days;
    }


    $total_salary_expected = $total_salary_expected1;
   
    $total_daily_rates = ($total_daily_rates != '') ? $total_daily_rates : 0;
    $total_overtime_rates = ($total_overtime_rates != '') ? $total_overtime_rates : 0;
    $total_nssf = ($total_nssf != '') ? $total_nssf : 0;
    $total_provtax = ($total_provtax != '') ? $total_provtax : 0;
    $total_loan_earn = ($total_loan_earn != '') ? $total_loan_earn : 0;
    $total_Lloan = ($total_Lloan != '') ? $total_Lloan : 0;
    $total_daily_days = ($total_daily_days != '') ? $total_daily_days : 0;
    $total_overtime_days = ($total_overtime_days != '') ? $total_overtime_days : 0;
$total_salaries_not_paid = ($total_daily_rates*$total_daily_days)+($total_overtime_rates*$total_overtime_days)+$total_loan_earn- ($total_salaries_paid+$total_nssf+$total_provtax+$total_Lloan);
   
    if ($payment_type == "Advance") {
        $date_diff = calculateDateDifference($actual_date, $payment_date);
        $date_diff = ($date_diff == 31) ? 30 : $date_diff;
        $monthly_salary = DB::getInstance()->displayTableColumnValue("SELECT Salary_Scale FROM staff_salary_scale WHERE Staff_Id='$staff_id' AND substr(Date_From,1,10)<='$payment_date' ORDER BY Date_From DESC LIMIT 1", "Salary_Scale");
        $total_salary_expected += round(($monthly_salary / 30) * $date_diff);
        $total_salary_expected = $total_salary_expected1;
        $total_salaries_not_paid=$total_salary_expected;
    }
     $total_salary_expected = ($total_salary_expected != '') ? $total_salary_expected : 0;
    //$total_salaries_not_paid = $total_salary_expected - ($total_salaries_paid);
      
    $total_salaries_not_paid = ($total_salaries_not_paid > 0) ? $total_salaries_not_paid : 0;
    echo $total_salary_expected . '#' . $total_salaries_paid . '#' . $total_salaries_not_paid . '#' . $total_daily_rates . '#' . $total_overtime_rates . '#' . $total_nssf . '#' . $total_provtax . '#' . $total_loan_earn . '#' . $total_Lloan . '#' . $total_daily_days . '#' . $total_overtime_days;
}

if (isset($_POST['updateOvertimeStaffAttendance']) && $_POST['staff_id'] != "") {
    $date = Input::get("date");
    $staff_id = Input::get("staff_id");
    $is_present = Input::get("is_present");
    $user_id = Input::get("user_id");
    //Register the status
    if (DB::getInstance()->checkRows("SELECT * FROM staff_overtime_attendency WHERE Staff_Id='$staff_id' AND Date='$date'")) {
        DB::getInstance()->query("UPDATE staff_overtime_attendency SET Is_Present='$is_present' WHERE Staff_Id='$staff_id' AND Date='$date'");
    } else {
        DB::getInstance()->insert("staff_overtime_attendency", array(
            "Date" => $date,
            "Staff_Id" => $staff_id,
            "Registered_By" => $user_id,
            "Is_Present" => 1
        ));
    }
}if (isset($_POST['updateStaffAttendance']) && $_POST['staff_id'] != "") {
    $date = Input::get("date");
    $staff_id = Input::get("staff_id");
    $is_present = Input::get("is_present");
    $user_id = Input::get("user_id");
    //Register the status
    if (DB::getInstance()->checkRows("SELECT * FROM staff_attendance WHERE Staff_Id='$staff_id' AND Date='$date'")) {
        DB::getInstance()->query("UPDATE staff_attendance SET Is_Present='$is_present' WHERE Staff_Id='$staff_id' AND Date='$date'");
    } else {
        DB::getInstance()->insert("staff_attendance", array(
            "Date" => $date,
            "Staff_Id" => $staff_id,
            "Registered_By" => $user_id,
            "Is_Present" => 1
        ));
    }
}
//submit add_icu_patient
if (isset($_POST['add_icu_patient']) && $_POST['admission_id'] != "") {
    $admission_id = Input::get("admission_id");
    $icu_patient_time = $_POST['icu_patient_date'] . ' ' . $_POST['icu_patient_time'] . ":" . date("s");
    if (!DB::getInstance()->checkRows("select * from icu_patient where Admission_Id='$admission_id' AND Status=1 ")) {
        $submit_in_icu = DB::getInstance()->insert('icu_patient', array(
            "Admission_Id" => $admission_id,
            "Date" => $icu_patient_time
        ));
        echo '<i>Patient sent to ICU successfully</i>';
    } else {
        echo '<i style="color:red">Patient already sent to ICU before</i>';
    }
}

if (isset($_POST['updatePatientStatus']) && $_POST['admission_id'] != "" && $_POST["status_value"] != "") {
    $admission_id = Input::get("admission_id");
    $status_value = Input::get("status_value");
    $status_time = Input::get("status_date") . " " . Input::get("status_time");
    $staff_id = Input::get("staff_id");
    $user_id = Input::get("user_id");
    //Register the status
    DB::getInstance()->insert("patient_status_details", array(
        "Admission_Id" => $admission_id,
        "Patient_Status" => $status_value,
        "Status_Time" => $status_time . ":" . date("s"),
        "Staff_Id" => $staff_id,
        "User_Id" => $user_id
    ));
    if ($status_value == "Critical in ICU") {
        if (!DB::getInstance()->checkRows("select * from icu_patient where Admission_Id='$admission_id' AND Status=1 ")) {
            DB::getInstance()->insert('icu_patient', array(
                "Admission_Id" => $admission_id,
                "Date" => $status_time . ":" . date("s")
            ));
        }
    } else {
        //DB::getInstance()->update('icu_patient',$admission_id, array("Status" => 0 ),'Admission_Id');
    }
    $statusQuery = "SELECT * FROM patient_status_details WHERE Admission_Id='$admission_id' ORDER BY Status_Time DESC LIMIT 1";
    $patient_status = DB::getInstance()->DisplayTableColumnValue($statusQuery, "Patient_Status");
    $statusTimeLatest = substr(DB::getInstance()->DisplayTableColumnValue($statusQuery, "Status_Time"), 0, 10);
    $patient_status_class = ($patient_status == "Stable") ? "success" : (($patient_status == "Unstable") ? "warning" : "danger");
    echo '<label class="label label-' . $patient_status_class . '">' . $patient_status . '</label>##' . $statusTimeLatest . "##";
}
//calculate_total_drug_price: 'calculate_total_drug_price', drug_id: drug_id, quantity: entered_quantity
if (isset($_POST['calculate_total_drug_price']) && $_POST['drug_id'] != "" && $_POST["quantity"] != "") {
    $stocking_id = Input::get("drug_id");
    $quantity = Input::get("quantity");
    $stock_id = DB::getInstance()->getName("drugs_in_pharmacy", $stocking_id, "Stock_Id", "Stocking_Id");
    echo $drug_price = $quantity * DB::getInstance()->displayTableColumnValue("SELECT * FROM drugs_in_store WHERE Stock_Id=$stock_id", "Selling_Price");
}
//drugs limit in the pharmacy,
if (isset($_POST['calculate_pharmacy_drug_limit']) && $_POST['calculate_pharmacy_drug_limit'] == "calculate_pharmacy_drug_limit" && $_POST["tr_id"] != "" && $_POST['stocking_id'] != "" & $_POST["pharmacy_name"] != "") {
    $pharmacy_name = Input::get("pharmacy_name");
    $stocking_id = Input::get("stocking_id");
    $tr_id = Input::get("tr_id");
    $stock_id = DB::getInstance()->getName("drugs_in_pharmacy", $stocking_id, "Stock_Id", "Stocking_Id");
    $drug_type = DB::getInstance()->getName("drugs_in_store", $stock_id, "Drug_Type", "Stock_Id");
    $drug_id = DB::getInstance()->getName("drugs_in_store", $stock_id, "Drug_Id", "Stock_Id");
    $quantityTaken = 0;
    $quantityAll = 0;
    $boxesAll = 0;
    $stripsAll = 0;
    $tabletsAll = 0;
    $queryGeneral = "SELECT drugs_in_store.Stock_Id,drugs_in_pharmacy.Stocking_Id,Tablets,Strips,Boxes  FROM drugs_in_store,drugs_in_pharmacy WHERE drugs_in_pharmacy.Stock_Id=drugs_in_store.Stock_Id AND Drug_Type='$drug_type' AND Drug_Id=$drug_id AND Expiry_Date>=CURDATE() ORDER BY Expiry_Date ASC";
    $stockList = DB::getInstance()->query($queryGeneral);
    foreach ($stockList->results() as $stocks_data) {
        $queryAll = "SELECT SUM(Other_Quantity_Received) AS Other_Quantity, SUM(Boxes_Received) AS Boxes,SUM(Strips_Received) AS Strips  FROM drugs_in_pharmacy WHERE Pharmacy_Name='$pharmacy_name' AND Stock_Id=$stocks_data->Stock_Id ORDER BY Time DESC";
        //Calculate taken
        $quantityAll += DB::getInstance()->DisplayTableColumnValue($queryAll, "Other_Quantity");
        $boxesAll += DB::getInstance()->DisplayTableColumnValue($queryAll, "Boxes");
        $stripsGot = DB::getInstance()->DisplayTableColumnValue($queryAll, "Strips");
        $stripsAll += $stripsGot;
        $tabletsAll += ($stocks_data->Boxes * $stocks_data->Strips * $stocks_data->Tablets) + ($stripsGot * $stocks_data->Tablets);
        $queryAllTaken = "SELECT Quantity_Taken  FROM patient_drug_taken WHERE Stocking_Id=$stocks_data->Stocking_Id ORDER BY Time_Submitted DESC";
        //Calculate taken
        $quantityTaken += DB::getInstance()->calculateSum($queryAllTaken, "Quantity_Taken");
    }
    $quantityAll = ($quantityAll >= $quantityTaken) ? $quantityAll - $quantityTaken : 0;
    $tabletsAll = ($tabletsAll >= $quantityTaken) ? $tabletsAll - $quantityTaken : 0;

    if ($strips_taken <= 0 && $boxesAll <= 0) {
        $stripsAll = 0;
    }
    if ($stripsAll <= 0 && $boxesAll <= 0) {
        $tabletsAll = 0;
    }
    if ($drug_type == "tablets" || $drug_type == "pesalies" || $drug_type == "suppostories") {
        ?>
        <label class="badge"><?php echo $boxesAll . " Boxes and " . $stripsAll . " Strips Left"; ?></label>
        <label class="badge">==<?php echo $tabletsAll; ?> Tablets Left</label>
        <input class="form-control" id="quantity_id_<?php echo $tr_id; ?>" type="number" max="<?php echo $tabletsAll; ?>" placeholder="Enter number of tablets taken" name="units[]" min="1" onkeyup="calculateDrugPrice(<?php echo $tr_id; ?>);" required>
        <!--<input id="quantities_strips_left_<?php echo $tr_id; ?>" name="quantity[]" placeholder="Enter number of tablets taken" min="1" max="<?php echo $tabletsAll; ?>" class="form-control" type="number">-->
    <?php } else if ($drug_type == "injectable" || $drug_type == "creams" || $drug_type == "drops" || $drug_type == "orals") { ?>
        <label class="badge"><?php echo $quantityAll; ?> Left</label>
        <input class="form-control" id="quantity_id_<?php echo $tr_id; ?>" type="number" max="<?php echo $quantityAll; ?>" placeholder="Enter quantity taken" name="units[]" min="1" onkeyup="calculateDrugPrice(<?php echo $tr_id; ?>);" required>
        <!--<input id="quantities_left_<?php echo $tr_id; ?>" name="quantity[]" min="1" max="<?php echo $quantityAll; ?>" class="form-control" type="number" required>-->
        <?php
    }
}

if (isset($_POST['type']) && $_POST["type"] == "view_drug_prescriptions") {
    $foreign_key_column = Input::get("key");
    $value = Input::get("value");
    $prescriptionsQuery = "SELECT * FROM drug_prescription WHERE $foreign_key_column='$value' ORDER BY Time DESC";
    $dataList = DB::getInstance()->querySample($prescriptionsQuery);
    foreach ($dataList AS $list) {
        echo '<option value="' . $list->Prescription_Id . '">' . english_date_time($list->Time) . '</option>';
    }
}
//pick assements
if (isset($_POST['patient_id']) && $_POST["type"] == "view_assessments") {

    $patient_id = $_POST['patient_id'];
    $selectassements = "select * from patient,clinical_assessment WHERE clinical_assessment.Patient_Id = patient.Patient_Id AND patient.Patient_Id='$patient_id' order by Date";
    if (!DB::getInstance()->checkRows($selectassements)) {
        echo '<h4 style="color:red">No Clinical assements for that patient</h4>';
    } else {
        $query2 = "select * from clinical_assessment,patient WHERE clinical_assessment.Patient_Id = patient.Patient_Id AND patient.Patient_Id='$patient_id' AND Examination!='' order by Date DESC";
        $assessments_list = DB::getInstance()->query($query2);
        foreach ($assessments_list->results() as $assessments) {
            ?>
            <option value='<?php echo $assessments->Assessment_Id; ?>'><?php
                if ($assessments->Date != "") {
                    echo english_date_time($assessments->Date);
                } else {
                    echo 'No Clinical Assement';
                }
                ?>
            </option>
            <?php
        }
    }
}
// end of assements_done
if (isset($_POST['ward_id']) && $_POST['ward_id'] != "") {
    $ward_id = Input::get("ward_id");
    $queryBeds = "select * from  bed WHERE Ward_Id=$ward_id AND Status='Free'";
    echo '<option value="">Choose</option>';
    $fetchBeds = DB::getInstance()->querySample($queryBeds);
    foreach ($fetchBeds as $beds) {
        echo '<option value="' . $beds->Bed_Id . '">' . $beds->Bed_Number . '</option>';
    }
}

//'display_department':'display_department',department_id: parent_id
if (isset($_POST['display_department']) && $_POST['display_department'] == "display_department" && $_POST['department_id'] != "") {
    $department_id = Input::get("department_id");
    ?>
    <label>Department Name</label>
    <select class="form-control" name="department_name" required>
        <option value="">Choose...</option>
        <?php
        $department_list = DB::getInstance()->query("SELECT * FROM department ORDER BY Department_Name");
        foreach ($department_list->results() as $departments) {
            $selected = ($departments->Department_Id == $department_id) ? "selected" : "";
            echo '<option value="' . $departments->Department_Id . '" ' . $selected . '>' . $departments->Department_Name . '</option>';
        }
        ?>
    </select>
    <?php
}
//populate_asset_serial_numbers: 'populate_asset_serial_numbers', asset_id: value},
if (isset($_POST['populate_asset_serial_numbers']) && $_POST['populate_asset_serial_numbers'] == "populate_asset_serial_numbers" && $_POST['asset_id'] != "") {
    $asset_id = Input::get("asset_id");
    $tr_id = Input::get("tr_id");
    $query2 = "select * from stock_assets WHERE Asset_Id='$asset_id' ";
    $serial_numbers = DB::getInstance()->query($query2);
    echo'<select class = "form-control select2" multiple name = "serial_number[]" style = "width:100%;" required onchange="calculateSelectedNumbers(this,' . $tr_id . ')">';
    foreach ($serial_numbers->results() as $number) {
        if ($number->Serial_Number != "") {
            if (!DB::getInstance()->checkRows("SELECT Stock_Asset_Id FROM asset_allocation WHERE Stock_Asset_Id='$number->Stock_Asset_Id'")) {
                echo '<option value="' . $number->Stock_Asset_Id . '">' . $number->Serial_Number . '</option>';
            }
        }
    }
    echo '</select>';
}
//Display all data in the select
if (isset($_POST['display_selects']) && $_POST['display_selects'] == "display_selects" && $_POST['id_column'] != "") {
    $table_name = Input::get("table_name");
    $id_column = Input::get("id_column");
    $other_column = Input::get("other_column");
    echo DB::getInstance()->dropDowns($table_name, $id_column, $other_column);
}
//calculate_asset_limit: 'calculate_asset_limit', asset_id: value},
if (isset($_POST['calculate_drug_limit']) && $_POST['calculate_drug_limit'] == "calculate_drug_limit" && $_POST["tr_id"] != "" && $_POST['drug_id'] != "") {
    $drug_id = Input::get("drug_id");
    $tr_id = Input::get("tr_id");
    $drug_type = DB::getInstance()->getName("drugs_in_store", $drug_id, "Drug_Type", "Stock_Id");
    $drug_id = DB::getInstance()->getName("drugs_in_store", $drug_id, "Drug_Id", "Stock_Id");
    $quantity_taken = 0;
    $boxes_taken = 0;
    $strips_taken = 0;
    $query = "SELECT Stock_Id,Total_Number AS Quantity, Boxes,(Boxes*Strips) AS Strips  FROM drugs_in_store WHERE Drug_Type='$drug_type' AND Drug_Id=$drug_id AND Expiry_Date>=CURDATE() ORDER BY Expiry_Date ASC";
    $stockList = DB::getInstance()->query($query);
    foreach ($stockList->results() as $stocks_data) {
        $stock_id = $stocks_data->Stock_Id;
        $queryTaken = "SELECT SUM(Other_Quantity_Sent) AS Other_Quantity, SUM(Boxes_Sent) AS Boxes,SUM(Strips_Sent) AS Strips  FROM drugs_in_pharmacy WHERE Stock_Id=$stocks_data->Stock_Id ORDER BY Time DESC";
        //Calculate taken
        $quantity_taken += DB::getInstance()->DisplayTableColumnValue($queryTaken, "Other_Quantity");
        $boxes_taken += DB::getInstance()->DisplayTableColumnValue($queryTaken, "Boxes");
        $strips_taken += DB::getInstance()->DisplayTableColumnValue($queryTaken, "Strips");
    }
    $query = "SELECT * FROM drugs_in_store WHERE Drug_Type='$drug_type' AND Drug_Id=$drug_id AND Expiry_Date>=CURDATE()";
    $quantityAll = DB::getInstance()->calculateSum($query, "Total_Number") - $quantity_taken;
    $boxesAll = DB::getInstance()->calculateSum($query, "Boxes") - $boxes_taken;
    $stripsAll = DB::getInstance()->calculateSum("SELECT (Boxes*Strips) AS Strips FROM drugs_in_store WHERE Drug_Type='$drug_type' AND Drug_Id=$drug_id AND Expiry_Date>=CURDATE()", "Strips") - $strips_taken;
    if ($strips_taken <= 0 && $boxesAll <= 0) {
        $stripsAll = 0;
    }
    if ($drug_type == "tablets" || $drug_type == "pesalies" || $drug_type == "suppostories") {
        ?>
        <label class="badge"><?php echo $boxesAll; ?> Boxes Left</label>
        <input type="hidden" name="selected_ids[]" value="<?php echo $drug_id; ?>" id="selected_id_<?php echo $tr_id; ?>">
        <input id="quantities_boxes_left_<?php echo $tr_id; ?>" name="total_boxes[]" min="1" max="<?php echo $boxesAll; ?>" class="form-control" type="number">
        <!--<br/>-->
        <label class="badge"><?php echo $stripsAll; ?> Strips Left</label>
        <input type="hidden" name="selected_ids[]" value="<?php echo $drug_id; ?>" id="selected_id_<?php echo $tr_id; ?>">
        <input id="quantities_strips_left_<?php echo $tr_id; ?>" name="total_strips[]" min="1" max="<?php echo $stripsAll; ?>" class="form-control" type="number">
        <input id="quantities_left_<?php echo $tr_id; ?>" name="other_quantity[]" value="" class="form-control" type="hidden">
    <?php } else if ($drug_type == "injectable" || $drug_type == "creams" || $drug_type == "drops" || $drug_type == "orals") { ?>
        <label class="badge"><?php echo $quantityAll; ?> Left</label>
        <input type="hidden" name="selected_ids[]" value="<?php echo $drug_id; ?>" id="selected_id_<?php echo $tr_id; ?>">
        <input id="quantities_boxes_left_<?php echo $tr_id; ?>" name="total_boxes[]" value="" class="form-control" type="hidden">
        <input id="quantities_strips_left_<?php echo $tr_id; ?>" name="total_strips[]" value="" class="form-control" type="hidden">
        <input id="quantities_left_<?php echo $tr_id; ?>" name="other_quantity[]" min="1" max="<?php echo $quantityAll; ?>" class="form-control" type="number" required>
        <?php
    }
}
if (isset($_POST["update_assessment_service_status"]) && $_POST["update_assessment_service_status"] == "update_assessment_service_status") {
    $patient_id = Input::get("patient_id");
    $assessment_id = Input::get("assessment_id");
    $value_got = Input::get("value");
    $service_id = Input::get("service_id");
    $service_column = Input::get("service_column");
    $payment_mode_column = Input::get("payment_mode_column");
    $dataQuery = "SELECT * FROM clinical_assessment WHERE Patient_Id=$patient_id AND Assessment_Id=$assessment_id  LIMIT 1";
    $lab_tests = unserialize(DB::getInstance()->DisplayTableColumnValue($dataQuery, $service_column));
    $payment_mode = unserialize(DB::getInstance()->DisplayTableColumnValue($dataQuery, $payment_mode_column));
    for ($x = 0; $x < count($lab_tests); $x++) {
        $payment_mode[$x] = ($service_id == $lab_tests[$x]) ? $value_got : $payment_mode[$x];
    }
    DB::getInstance()->query("UPDATE clinical_assessment SET $payment_mode_column='" . serialize($payment_mode) . "' WHERE Patient_Id=$patient_id AND Assessment_Id=$assessment_id");
}
if (isset($_POST["searched_data"]) && $_POST["searched_data"] != "") {
    $search_data = trim($_POST["searched_data"], " ");
    $patientsCheck = "SELECT * FROM patient,person WHERE person.Person_Id=patient.Person_Id and (person.Person_Number='$search_data' or CONCAT(person.Fname,' ',person.Lname) like '%$search_data%'or patient.Ward_Assigned='$search_data' or person.Identity_Card='$search_data' or person.DOB='$search_data' or person.Phone_Number='$search_data' or person.Identity_Card='$search_data' or person.Department='$search_data') group by person.Person_Id ORDER BY person.Fname ";
    $patients_list = DB::getInstance()->querySample($patientsCheck);
    foreach ($patients_list as $patients):
        ?>
        <tr>
            <td><input type="radio" name="person_id" value="<?php echo $patients->Person_Id ?>" requiredg>
            </td><td><?php echo $patients->Person_Number ?></td><td><?php echo $patients->Identity_Card ?></td><td><?php echo $patients->Fname . " " . $patients->Lname ?></td><td><?php echo calculateAge($patients->DOB, $date_today) ?></td><td><?php echo $patients->Phone_Number ?></td>
        </tr><?php
    endforeach;
}
?>

