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
                                    <div class="page-title">Receive drug in pharmacy</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <?php
                                    if(Input::exists()&&Input::get("submit_received")=="submit_received"){
                                        $stocking_ids= Input::get("stocking_id");
                                        $boxes_received= Input::get("boxes_received");
                                        $strips_received= Input::get("strips_received");
                                        $other_quantity_received= Input::get("other_quantity_received");
                                        for($i=0;$i<count($stocking_ids);$i++){
                                            if($boxes_received[$i]>0||$strips_received[$i]>0||$other_quantity_received[$i]>0){
                                                DB::getInstance()->update("drugs_in_pharmacy",$stocking_ids[$i],array(
                                                    "Boxes_Received"=>($boxes_received[$i]>0)?$boxes_received[$i]:NULL,
                                                    "Strips_Received"=>($strips_received[$i]>0)?$strips_received[$i]:NULL,
                                                    "Other_Quantity_Received"=>($other_quantity_received[$i]>0)?$other_quantity_received[$i]:NULL,
                                                    "Date_Received"=>$date_today,
                                                    "Confirmed_By"=>$_SESSION["hospital_user_id"]
                                                ),"Stocking_Id");
                                            }
                                        }
                                        echo '<div class="alert alert-success">Drug received approved successfully</div>';
                                        Redirect::go_to("");
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-head"><header>Entry form</header></div>
                                    <div class="card-body">
                                        <form action="" method="POST">
                                            <?php
                                            $condition = "";
                                            $condition .= ($pharmacy_name != "") ? " AND drugs_in_pharmacy.Pharmacy_Name = '$pharmacy_name'" : "";
                                            $pharmacyDrugQuery = "select drugs_in_pharmacy.Pharmacy_Name,drug_names.Drug_Name,drugs_in_store.Drug_Type,drugs_in_store.Description,drugs_in_store.Date_Of_manufacture,drugs_in_pharmacy.Date_Sent,drugs_in_store.Expiry_Date,drugs_in_pharmacy.Stocking_Id,drugs_in_pharmacy.Other_Quantity_Sent,drugs_in_pharmacy.Boxes_Sent,drugs_in_pharmacy.Strips_Sent,drugs_in_pharmacy.Other_Quantity_Received,drugs_in_pharmacy.Boxes_Received,drugs_in_pharmacy.Strips_Received,drugs_in_store.Selling_Price from drug_names,drugs_in_store,drugs_in_pharmacy WHERE drugs_in_store.Drug_Id=drug_names.Drug_Id AND drugs_in_store.Stock_Id=drugs_in_pharmacy.Stock_Id AND Expiry_Date>='$date_today' $condition ORDER BY drugs_in_pharmacy.Date_Received DESC";
                                            if (DB::getInstance()->checkRows($pharmacyDrugQuery)) {
                                                ?>
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Pharmacy (Dept)</th>
                                                            <th>Drug name and description</th>
                                                            <th>Batch number</th>
                                                            <th>Manufacture Date</th>
                                                            <th>Date Sent</th>
                                                            <th>Expiry Date</th>
                                                            <th>Sent</th>
                                                            <th>Received</th>
                                                            <th>Receive remaining</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $drug_data_list = DB::getInstance()->querySample($pharmacyDrugQuery);
                                                        foreach ($drug_data_list as $drug_data) {
                                                            $queryTaken = "SELECT * FROM patient_drug_taken WHERE Stocking_Id='$drug_data->Stocking_Id' ";
                                                            $QuantityTaken = DB::getInstance()->calculateSum($queryTaken, "Quantity_Taken");
                                                            $drug_sent = "";
                                                            $other_quantity_left = $drug_data->Other_Quantity_Sent - $drug_data->Other_Quantity_Received;
                                                            $boxes_left = $drug_data->Boxes_Sent-$drug_data->Boxes_Received;
                                                            $strips_left = $drug_data->Strips_Sent-$drug_data->Strips_Received;
                                                            $drug_sent = ($drug_data->Other_Quantity_Sent > 0) ? $drug_data->Other_Quantity_Sent : "";
                                                            $drug_sent = ($drug_data->Boxes_Sent > 0) ? $drug_data->Boxes_Sent . "(Boxes) " : $drug_sent;
                                                            $drug_sent .= ($drug_data->Strips_Sent > 0) ? $drug_data->Strips_Sent . "(Strips)" : "";
                                                            
                                                            $drug_received = ($drug_data->Other_Quantity_Received > 0) ? $drug_data->Other_Quantity_Received : "";
                                                            $drug_received = ($drug_data->Boxes_Received > 0) ? $drug_data->Boxes_Received . "(Boxes) " : $drug_received;
                                                            $drug_received .= ($drug_data->Strips_Received > 0) ? $drug_data->Strips_Received . "(Strips)" : "";
                                                            
                                                            $boxes_left_hidden = ($drug_data->Boxes_Sent > 0) ? "" : " hidden";
                                                            $strips_left_hidden = ($drug_data->Strips_Sent > 0) ? "" : " hidden";
                                                            $others_left_hidden = ($drug_data->Other_Quantity_Sent > 0) ? "" : " hidden";
                                                            if($other_quantity_left>0||$boxes_left>0||$strips_left>0){
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="stocking_id[]" value="<?php echo $drug_data->Stocking_Id?>">
                                                                    <?php echo $drug_data->Pharmacy_Name; ?></td>
                                                                <td><?php echo "<b style='color:blue'>" . $drug_data->Drug_Name . "</b> " . $drug_data->Drug_Type . '<br/>' . $drug_data->Description; ?></td>
                                                                <td><?php echo $drug_data->Batch_Number; ?></td>
                                                                <td><?php echo english_date($drug_data->Date_Of_manufacture); ?></td>
                                                                <td><?php echo english_date($drug_data->Date_Received); ?></td>
                                                                <td><?php echo english_date($drug_data->Expiry_Date); ?></td>
                                                                <td><?php echo $drug_sent; ?></td>
                                                                <td><?php echo $drug_received; ?></td>
                                                                <td>
                                                                    <input class="form-control <?php echo $boxes_left_hidden ?>" type="number" min="0" max="<?php echo $boxes_left ?>" name="boxes_received[]" placeholder="Box..." oninput="enableOrDisableButton();">
                                                                    <input class="form-control <?php echo $strips_left_hidden ?>" type="number" min="0" max="<?php echo $strips_left ?>" name="strips_received[]" placeholder="Strips..." oninput="enableOrDisableButton();">
                                                                    <input class="form-control <?php echo $others_left_hidden ?>" type="number" min="0" max="<?php echo $other_quantity_left ?>" name="other_quantity_received[]" oninput="enableOrDisableButton();">
                                                                </td>
                                                            </tr>
                                                            <?php
                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <b style="color:red" id="testLabel">Please enter the exact drug received and click submit</b>
                                                <button class="btn btn-success pull-right" disabled id="submit_button" type="submit" name="submit_received" value="submit_received">SUBMIT</button>
                                                <?php
                                            } else {
                                                echo '<div class="alert alert-danger">No search results found</div>';
                                            }
                                            ?>
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
        <script>
            function enableOrDisableButton() {
                var overall_total = 0;
                var boxes_data = document.getElementsByName('boxes_received[]');
                var strips_data = document.getElementsByName('strips_received[]');
                var others_data = document.getElementsByName('other_quantity_received[]');
                for (var i = 0; i < boxes_data.length; i++) {
                    if (boxes_data[i].type === "number" && boxes_data[i].value !== "") {
                        var total_got = parseFloat(boxes_data[i].value);
                        overall_total += total_got;
                    }
                }
                for (var i = 0; i < strips_data.length; i++) {
                    if (strips_data[i].type === "number" && strips_data[i].value !== "") {
                        var total_got = parseFloat(strips_data[i].value);
                        overall_total += total_got;
                    }
                }
                for (var i = 0; i < others_data.length; i++) {
                    if (others_data[i].type === "number" && others_data[i].value !== "") {
                        var total_got = parseFloat(others_data[i].value);
                        overall_total += total_got;
                    }
                }
                var buttonDisabled = (overall_total > 0) ? false : true;
                var testLabelData = (overall_total > 0) ? "" : "Cannot submit empty drugs received";
                $("#testLabel").html(testLabelData);
                $("#submit_button").attr({"disabled": buttonDisabled});
            }
        </script>
    </body>
</html>