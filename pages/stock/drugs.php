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
                                    <div class="page-title">Drug</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <?php
                                    if (Input::exists()) {
                                        if (Input::get("new_drug_locate_button") == "new_drug_locate_button") {
                                            $drug_name = Input::get("drug_name");
                                            $date_sent = Input::get("date_sent");
                                            $received_by = Input::get("received_by");
                                            $boxes_got = Input::get("total_boxes");
                                            $strips_got = Input::get("total_strips");
                                            $other_quantity_got = Input::get("other_quantity");
                                            $pharmacy_name = Input::get("pharmacy_name");
                                            $sent_by = $_SESSION['hospital_user_id'];
                                            $captured_empty = 0;
                                            $submitted = 0;
                                            if (!empty($drug_name)) {
                                                for ($x = 0; $x < count($drug_name); $x++) {
                                                    if ($other_quantity_got[$x] > 0 || $boxes_got[$x] > 0 || $strips_got[$x] > 0) {
                                                        $other_quantity = $other_quantity_got[$x];
                                                        $boxes = $boxes_got[$x];
                                                        $strips = $strips_got[$x];
                                                        $drug_type = DB::getInstance()->getName("drugs_in_store", $drug_name[$x], "Drug_Type", "Stock_Id");
                                                        $drug_id = DB::getInstance()->getName("drugs_in_store", $drug_name[$x], "Drug_Id", "Stock_Id");
                                                        $query = "SELECT Stock_Id,Total_Number AS Quantity, Boxes,(Boxes*Strips) AS Strips  FROM drugs_in_store WHERE Drug_Type='$drug_type' AND Drug_Id=$drug_id AND Expiry_Date>=CURDATE() ORDER BY Expiry_Date ASC";
                                                        $stockList = DB::getInstance()->query($query);
                                                        $submitted_loop = 0;
                                                        $empty_loop = 0;
                                                        foreach ($stockList->results() as $stocks_data) {
                                                            $stock_id = $stocks_data->Stock_Id;
                                                            if ($other_quantity > 0 || $boxes > 0 || $strips > 0) {
                                                                $other_quantity_submitted = NULL;
                                                                $boxes_submitted = NULL;
                                                                $strips_submitted = NULL;
                                                                $queryTaken = "SELECT SUM(Other_Quantity_Sent) AS Other_Quantity, SUM(Boxes_Sent) AS Boxes,SUM(Strips_Sent) AS Strips  FROM drugs_in_pharmacy WHERE Stock_Id=$stocks_data->Stock_Id ORDER BY Time DESC";
                                                                //Calculate taken
                                                                $quantity_taken = DB::getInstance()->DisplayTableColumnValue($queryTaken, "Other_Quantity");
                                                                $boxes_taken = DB::getInstance()->DisplayTableColumnValue($queryTaken, "Boxes");
                                                                $strips_taken = DB::getInstance()->DisplayTableColumnValue($queryTaken, "Strips");
                                                                //Calculate left
                                                                $quantity_left = $stocks_data->Quantity - $quantity_taken;
                                                                $boxes_left = $stocks_data->Boxes - $boxes_taken;
                                                                $strips_left = $stocks_data->Strips - $strips_taken;
                                                                if ($drug_type == "injectable" || $drug_type == "creams" || $drug_type == "drops" || $drug_type == "orals") {
                                                                    if ($other_quantity >= $quantity_left) {
                                                                        $other_quantity_submitted = $quantity_left;
                                                                        $other_quantity -= $quantity_left;
                                                                    } else if ($other_quantity < $quantity_left) {
                                                                        $other_quantity_submitted = $other_quantity;
                                                                        $other_quantity = 0;
                                                                    }
                                                                }
                                                                if ($drug_type == "tablets" || $drug_type == "pesalies" || $drug_type == "suppostories") {
                                                                    if (($boxes >= $boxes_left)) {
                                                                        $boxes_submitted = $boxes_left;
                                                                        $boxes -= $boxes_left;
                                                                    } else if (($boxes < $boxes_left)) {
                                                                        $boxes_submitted = $boxes;
                                                                        $boxes = 0;
                                                                    }
                                                                    if ($strips >= $strips_left) {
                                                                        $strips_submitted = $strips_left;
                                                                    } else if ($strips < $strips_left) {
                                                                        $strips_submitted = $strips;
                                                                        $strips = 0;
                                                                    }
                                                                }
                                                                if ($other_quantity_submitted != NULL || $boxes_submitted != NULL || $strips_submitted != NULL) {
                                                                    $pharmacyDrugInsert = DB::getInstance()->insert("drugs_in_pharmacy", array(
                                                                        "Pharmacy_Name" => $pharmacy_name[$x],
                                                                        "Stock_Id" => $stock_id,
                                                                        "Other_Quantity_Sent" => $other_quantity_submitted,
                                                                        "Boxes_Sent" => $boxes_submitted,
                                                                        "Strips_Sent" => $strips_submitted,
                                                                        "Date_Sent" => $date_sent[$x],
                                                                        "Received_By" => $received_by[$x],
                                                                        "Sent_By" => $sent_by
                                                                    ));
                                                                    if ($pharmacyDrugInsert) {
                                                                        $submitted_loop++;
                                                                    } else {
                                                                        //$empty_loop++; 
                                                                    }
                                                                } else {
                                                                    $empty_loop++;
                                                                    //echo 'Some data missing<br/>';
                                                                }
                                                            }
                                                        }
                                                        if ($submitted_loop > 0) {
                                                            $submitted++;
                                                        }if ($empty_loop > 0) {
                                                            $captured_empty++;
                                                        }
                                                    } else {
                                                        $captured_empty++;
                                                    }
                                                }
                                                if ($submitted != 0) {
                                                    echo '<div class="alert alert-success col-md-6">' . $submitted . ' drug entries successfully submitted</div>';
                                                }
                                                if ($captured_empty != 0) {
                                                    echo '<div class="alert alert-warning col-md-6">Can not submit ' . $captured_empty . ' empty drug quantities</div>';
                                                }
                                            } else {
                                                echo '<div class="alert alert-warning">Can not submit empty form</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("drugs"));
                                        }
                                        if (Input::get("pay_all") == "pay_all") {
                                            $stock_id = Input::get("stock_id");
                                            $price = Input::get("price");
                                            $op = 0;
                                            if (!empty($stock_id)) {
                                                for ($i = 0; $i < sizeof($stock_id); $i++) {
                                                    $pick_drug_type = DB::getInstance()->getName('drugs_in_store', $stock_id[$i], 'Drug_Type', 'Stock_Id');
                                                    $pick_drug_id = DB::getInstance()->getName('drugs_in_store', $stock_id[$i], 'Drug_Id', 'Stock_Id');
                                                    $query = DB::getInstance()->query(" UPDATE drugs_in_store SET Selling_Price = '$price' WHERE Drug_Id = '$pick_drug_id' AND Drug_Type = '$pick_drug_type' ");
                                                    $op++;
                                                }
                                                if ($op > 0) {
                                                    echo '<div class="alert alert-success col-sm-12">' . $price . ' shs set for selected drug</div>';
                                                } else {
                                                    echo '<div class="alert alert-warning col-sm-12">Could not append price</div>';
                                                }
                                            } else {
                                                echo '<div class="alert alert-warning col-sm-12">No drug was selected</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("drugs"));
                                        }
                                        if (Input::get("token_new_drugs") == "token_new_drugs") {
                                            $drug_name = Input::get("drug_name");
                                            $drug_type = Input::get("drug_type");
                                            $batch_number = Input::get("batch_number");
                                            $description = Input::get("description");
                                            $unit_price_got = Input::get("unit_price");
                                            $date_received = Input::get("date_received");
                                            $delivery_number = Input::get('delivery_number');
                                            $invoice_number = Input::get('invoice_number');
                                            $expirydate = Input::get('expiry_date');
                                            $date_of_manufacture = Input::get('manufacture_date');
                                            $supplier = Input::get('supplier_name');
                                            $quantity_got = Input::get('quantity');

                                            $boxes_got = Input::get('boxes');
                                            $strips_got = Input::get('strips');
                                            $tablets_got = Input::get('tablets');
                                            $added_drugs = 0;
                                            $duplicates = 0;
                                            if (!empty($drug_name)) {
                                                for ($x = 0; $x < count($drug_name); $x++) {
                                                    $quantity = ($quantity_got[$x] != "") ? $quantity_got[$x] : NULL;
                                                    $unit_price = ($unit_price_got[$x] != "") ? $unit_price_got[$x] : NULL;
                                                    $boxes = ($boxes_got[$x] != "") ? $boxes_got[$x] : NULL;
                                                    $strips = ($strips_got[$x] != "") ? $strips_got[$x] : NULL;
                                                    $tablets = ($tablets_got[$x] != "") ? $tablets_got[$x] : NULL;
                                                    $Insertdrug = DB::getInstance()->insert("drugs_in_store", array(
                                                        "Drug_Id" => $drug_name[$x],
                                                        "Batch_Number" => $batch_number[$x],
                                                        "Drug_Type" => $drug_type[$x],
                                                        "Description" => $description[$x],
                                                        "Total_Number" => $quantity,
                                                        "Unit_Price" => $unit_price,
                                                        "Boxes" => $boxes,
                                                        "Strips" => $strips,
                                                        "Tablets" => $tablets,
                                                        "Invoice_Number" => $invoice_number,
                                                        "Delivery_number" => $delivery_number,
                                                        "Supplier_Name" => $supplier,
                                                        "Date_Received" => $date_received[$x],
                                                        "Date_Of_manufacture" => $date_of_manufacture[$x],
                                                        "Expiry_Date" => $expirydate[$x],
                                                        "User_Id" => $_SESSION['hospital_user_id']
                                                    ));
                                                    if ($Insertdrug) {
                                                        $added_drugs++;
                                                    }
                                                }
                                                if ($added_drugs != 0) {
                                                    echo '<div class="alert alert-success col-sm-12">' . $added_drugs . ' Drugs successfully registered</div>';
                                                } else {
                                                    echo '<div class="alert alert-warning col-sm-12"> Error while uploading drugs</div>';
                                                }
                                            } else {
                                                echo '<div class="alert alert-warning col-sm-12">Can not upload empty drug details</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("drugs"));
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body" id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#tab_new_stock_drug" data-toggle="tab">
                                                    <i class="fa fa-plus-circle"></i> Register new Drug
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_view_stock_drug" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Drugs in store 
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_new_pharmacy_drug" data-toggle="tab">
                                                    <i class="fa fa-plus-circle"></i>Add drug to pharmacy 
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content primary">
                                            <div class="tab-pane fade in active" id="tab_new_stock_drug">
                                                <h2 style="text-align: center;">Drug entry form</h2>
                                                <form method="post" action="" >
                                                    <div class="row">
                                                        <!--supplieir's details-->
                                                        <div class="col-md-4 col-lg-4 col-sm-4">
                                                            <div class="form-group">
                                                                <label>Suppliers' Name:</label>
                                                                <select  class="select2" style="width: 100%" name="supplier_name" required>
                                                                    <?php echo DB::getInstance()->dropDowns('suppliers', 'Supplier_Name', 'Supplier_Name'); ?>
                                                                </select> 
                                                            </div> 
                                                        </div>
                                                        <div class="col-md-4 col-lg-4 col-sm-4">
                                                            <div class="form-group">
                                                                <label>Invoice Number:</label>
                                                                <input type="text" class="form-control" name="invoice_number" max="<?php echo date("Y-m-d") ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 col-lg-4 col-sm-4">
                                                            <div class="form-group">
                                                                <label>Delivery Number</label>
                                                                <input type="text" class="form-control" name="delivery_number" max="<?php echo date("Y-m-d") ?>" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <button type="button" class="btn btn-success btn-xs" onclick="add_element('tablets');"><i class="fa fa-plus-circle"></i> Tablets</button>
                                                            <button type="button" class="btn btn-success btn-xs" onclick="add_element('injectable');"><i class="fa fa-plus-circle"></i> Injectables</button>
                                                            <button type="button" class="btn btn-success btn-xs" onclick="add_element('creams');"><i class="fa fa-plus-circle"></i> Creams</button>
                                                            <button type="button" class="btn btn-success btn-xs" onclick="add_element('drops');"><i class="fa fa-plus-circle"></i> Drops</button>
                                                            <button type="button" class="btn btn-success btn-xs" onclick="add_element('pesalies');"><i class="fa fa-plus-circle"></i> Pesalies</button>
                                                            <button type="button" class="btn btn-success btn-xs" onclick="add_element('suppostories');"><i class="fa fa-plus-circle"></i> Suppostories</button>
                                                            <button type="button" class="btn btn-success btn-xs" onclick="add_element('orals');"><i class="fa fa-plus-circle"></i> Orals</button>
                                                        </div>
                                                    </div>
                                                    <div class="" id="add_more_drug"></div>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_drugs" value="token_new_drugs">
                                                                <button type="submit" class="btn btn-success" name="submit" value="submit">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade in" id="tab_view_stock_drug">
                                                <form action="" method="POST">
                                                    <?php
                                                    $drugQuery = "select * from drug_names,drugs_in_store WHERE drugs_in_store.Drug_Id=drug_names.Drug_Id ORDER BY Date_Received DESC";
                                                    if (DB::getInstance()->checkRows($drugQuery)) {
                                                        ?> 
                                                        <h4> <a data-toggle='modal' href='#modal-form-all' class="btn btn-success pull-right"><i class="fa fa-share"></i> Append Drug Prices</a></h4>
                                                        <div class="modal fade bs-modal-sm" id="modal-form-all" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-sm">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Append prices to drugs</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label class="control-label">Unit Price:</label><br/>
                                                                            <input class="form-control" type="number" min="0" name="price" required placeholder="enter unit price">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                                                                        <button type="submit" name="pay_all" value="pay_all" class="btn btn-primary">Save changes</button>
                                                                    </div>

                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </div>
                                                            <!-- /.modal-dialog -->
                                                        </div>
                                                        <h2 style="text-align: center;">Drugs in store</h2>
                                                        <div class="">
                                                            <label>KEY:</label>
                                                            <label class="label" style="background-color: yellow">Expiring soon</label>
                                                            <label class="label" style="background-color: green">Still fresh</label>
                                                            <label class="label" style="background-color: red">Expiring in one year</label>
                                                            <label class="label" style="background-color: black">Already expired</label>
                                                        </div>
                                                        <table id="example1" class="table table-striped table-bordered" cellspacing="1" width="100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>Drug Name and type</th>
                                                                    <th>Batch number</th>
                                                                    <th>Description</th>
                                                                    <th>Quantity</th>
                                                                    <th>Unit Price</th>
                                                                    <th>Manufacture Date</th>
                                                                    <th>Date Received</th>
                                                                    <th>Expiry Date</th>
                                                                    <th>Pricing per unit</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $drug_data_list = DB::getInstance()->querySample($drugQuery);
                                                                $no = 1;
                                                                foreach ($drug_data_list as $drug_data) {
                                                                    $left = "";
                                                                    $tr_color = "";
                                                                    $days_diff = calculateDateDifference(date("Y-m-d"), $drug_data->Expiry_Date, "days");
                                                                    $years_diff = calculateDateDifference(date("Y-m-d"), $drug_data->Expiry_Date, "years");
                                                                    $tr_color = 'style="background-color:green;color:white"';
                                                                    $tr_color = ($days_diff <= 0) ? 'style="background-color:black;color:white"' : $tr_color;
                                                                    $tr_color = ($days_diff > 7 && $years_diff <= 1) ? 'style="background-color:red;color:black"' : $tr_color;
                                                                    $tr_color = ($years_diff > 0 && $days_diff <= 7) ? 'style="background-color:yellow;color:black"' : $tr_color;
                                                                    $queryTakenCheck = "SELECT *  FROM drugs_in_pharmacy WHERE Stock_Id=$drug_data->Stock_Id";
                                                                    $queryTaken = "SELECT SUM(Other_Quantity_Sent) AS Other_Quantity, SUM(Boxes_Sent) AS Boxes,SUM(Strips_Sent) AS Strips  FROM drugs_in_pharmacy WHERE Stock_Id=$drug_data->Stock_Id ORDER BY Time DESC";
                                                                    //Calculate taken
                                                                    $quantity_taken = DB::getInstance()->DisplayTableColumnValue($queryTaken, "Other_Quantity");
                                                                    $boxes_taken = DB::getInstance()->DisplayTableColumnValue($queryTaken, "Boxes");
                                                                    $strips_taken = DB::getInstance()->DisplayTableColumnValue($queryTaken, "Strips");
                                                                    $other_quantity_left = $drug_data->Total_Number - $quantity_taken;
                                                                    $boxes_left = $drug_data->Boxes - $boxes_taken;
                                                                    $strips_left = ($drug_data->Boxes * $drug_data->Strips) - $strips_taken;
                                                                    if (!DB::getInstance()->checkRows($queryTakenCheck) || $other_quantity_left != 0 || $boxes_left != 0 || $strips_left != 0) {
                                                                        $left = ($other_quantity_left > 0) ? $other_quantity_left : "";
                                                                        $left = ($boxes_left > 0) ? $boxes_left . "(Boxes) " : $left;
                                                                        $left .= ($strips_left > 0) ? $strips_left . "(Strips)" : "";
                                                                        ?>
                                                                        <tr <?php echo $tr_color; ?>>
                                                                            <td style='color:black'><?php echo " <b style='color:blue'>" . $drug_data->Drug_Name . "</b> " . $drug_data->Drug_Type; ?></td>
                                                                            <td><?php echo $drug_data->Batch_Number; ?></td>
                                                                            <td><?php echo $drug_data->Description; ?></td>
                                                                            <td><?php echo $left; ?></td>
                                                                            <td><?php echo $drug_data->Unit_Price; ?></td>
                                                                            <td><?php echo english_date($drug_data->Date_Of_manufacture); ?></td>
                                                                            <td><?php echo english_date($drug_data->Date_Received); ?></td>
                                                                            <td><?php echo english_date($drug_data->Expiry_Date); ?></td>
                                                                            <td><?php
                                                                                if ($drug_data->Selling_Price != NULL) {
                                                                                    echo ugandan_shillings($drug_data->Selling_Price);
                                                                                    if ($days_diff > 0) {
                                                                                        ?><br/><a data-toggle='modal' onclick="get_drug_id('<?php echo $drug_data->Stock_Id; ?>', '<?php echo $drug_data->Selling_Price; ?>')"  href='#modal-form-single'><i class="fa fa-edit"></i>edit</a><?php
                                                                                    }
                                                                                } else {
                                                                                    if ($days_diff > 0) {
                                                                                        ?>
                                                                                        <input type="checkbox" value="<?php echo $drug_data->Stock_Id; ?>" name="stock_id[]"><?php
                                                                                    }
                                                                                }
                                                                                ?></td>
                                                                            <td>
                                                                                <a href="#" onclick="return confirm('Do you want to remove this drug from store?');" style="color: red"><i class="fa fa-trash-o"></i> Remove</a>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </form>
                                                    <?php
                                                } else {
                                                    echo '<div class="alert alert-warning"><strong>No Drugs in store</strong></div>';
                                                }
                                                ?>
                                                <form action="" method="POST">
                                                    <div class="modal fade bs-modal-sm" id="modal-form-single" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-sm">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Edit price from here </h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="control-label">Unit Price:</label><br/>
                                                                        <input type="hidden"step="0.01" min="0" name="stock_id[]" id="drug_id">
                                                                        <input type="number"step="0.01" min="0" name="price" id="price" required placeholder="enter unit price">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                                                                    <button type="submit" name="pay_all" value="pay_all" class="btn btn-primary">Save changes</button>
                                                                </div>

                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane fade in" id="tab_new_pharmacy_drug">
                                                <h2 style="text-align: center;">Add drug to pharmacy</h2>
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-md-12">
                                                            <div class="form-group">

                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr><th style="width: 15%">Drug Name</th>
                                                                            <th style="width:10%">Units/qty</th>
                                                                            <th style="width: 15%">Pharmacy Name(Dept)</th>
                                                                            <th style="width: 15%">Sent Date</th>
                                                                            <th style="width: 15%">Received By</th>
                                                                            <th style="width: 2%"><button type="button" class="btn btn-success btn-xs pull-right" id="add_more_allocate[]" onclick="add_element_allocate();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element_allocate">
                                                                        <tr id="row_1">
                                                                            <td>
                                                                                <select class="select2" id="drug_allocate_1" name="drug_name[]" onchange="calculateTotalUnits(1);" required>
                                                                                    <option value="">Select....</option>
                                                                                    <?php
                                                                                    $querydrug_select = 'select drugs_in_store.Drug_Type, drug_names.Drug_Name, drugs_in_store.Stock_Id from drug_names,drugs_in_store WHERE drug_names.Drug_Id=drugs_in_store.Drug_Id AND drugs_in_store.Expiry_Date>=CURDATE() GROUP BY drugs_in_store.Drug_Id,drugs_in_store.Drug_Type ORDER BY drugs_in_store.Date_Received DESC';
                                                                                    $fetchDrugs = DB::getInstance()->querySample($querydrug_select);
                                                                                    foreach ($fetchDrugs as $drugdata_select) {
                                                                                        ?>
                                                                                        <option value="<?php echo $drugdata_select->Stock_Id; ?>"><?php echo $drugdata_select->Drug_Name . "  " . $drugdata_select->Drug_Type; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </td>
                                                                            <td class="form-inline" id="quantity_column_1"></td>
                                                                            <td>
                                                                                <select name="pharmacy_name[]" class="select2" required style="width: 100%">
                                                                                    <option value="">Select..............</option>
                                                                                    <option value="Main">Main</option>
                                                                                    <option value="Immunisation">Immunisation</option>
                                                                                    <option value="Family Planning">Family Planning</option>
                                                                                    <option value="Gynecology">Gynecology</option>
                                                                                    <option value="Maternity">Maternity</option>
                                                                                    <option value="Antenatal">Antenatal</option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="date" class="form-control" name="date_sent[]" max="<?php echo date("Y-m-d") ?>" required>  
                                                                            </td>
                                                                            <td>
                                                                                <select class="select2" name="received_by[]"  required style="width: 100%">
                                                                                    <option value="">Select....</option>
                                                                                    <?php
                                                                                    $staffQuery = 'SELECT * FROM staff,person WHERE staff.Person_Id=person.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1 ORDER BY Fname DESC';
                                                                                    $staffList = DB::getInstance()->querySample($staffQuery);
                                                                                    foreach ($staffList as $staff) {
                                                                                        ?>
                                                                                        <option value="<?php echo $staff->Staff_Id; ?>"><?php echo $staff->Fname . " " . $staff->Lname; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <button type="button" value="row_1" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="pull-right">
                                                                <button type="submit" class="btn btn-success" name="new_drug_locate_button" value="new_drug_locate_button">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
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
            <?php include_once 'includes/footer.php'; ?>
            <!-- end footer -->
        </div>
        <!-- start js include path -->
        <?php include_once 'includes/footer_js.php'; ?>
        <!-- end js include path -->
        <script type="text/javascript">
                                                                                    function initializeSelect2(selectElementObj) {
                                                                                        selectElementObj.select2({
                                                                                            width: "100%",
                                                                                            allowClear: true
                                                                                        });
                                                                                    }
                                                                                    $(document).ready(function () {
                                                                                        $('#drugs').on('change', function () {
                                                                                            var choosenoption = $(this).val();
                                                                                            if (choosenoption && choosenoption != "") {

                                                                                            } else {
                                                                                                $('#drugs').html('');
                                                                                            }
                                                                                        });

                                                                                    });
        </script>
        <script>
            function add_element(type) {
                var row_ids = Math.round(Math.random( ) * 300000000);
                if (type === "tablets" || type === "pesalies" || type === "suppostories") {
                    document.getElementById('add_more_drug').insertAdjacentHTML('beforeend',
                            '<div  id="' + row_ids + '"><h5>' + type + ' <button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button></h5>\n\
                <table class="table table-bordered">\n\
                <tr><td><div class="form-group"><label >Drug Name</label><input type="hidden" name="drug_type[]" value="" id="drug_type_' + row_ids + '"><select id="drug_id_' + row_ids + '" class="select2" name="drug_name[]" required></select></div></td>\n\
                <td><div class="form-group"><label>Batch number:</label><input type="text" class="form-control" name="batch_number[]" required></div></td>\n\
                <td><div class="form-group"><label>Boxes:</label><input type="number" min="1" class="form-control" name="boxes[]" required></div></td>\n\
                <td><div class="form-group"><label>Strips Per Box:</label><input type="number" min="1" class="form-control" name="strips[]" required></div></td>\n\
                <td><div class="form-group"><label>Tablets Per Strip:</label><input type="hidden" value="" class="form-control" name="description[]"><input type="hidden" value="" class="form-control" name="quantity[]">\n\
                <input type="number" min="1" class="form-control" name="tablets[]" required></div></td></tr><tr>\n\
                <td><div class="form-group"><label>Unit Price (Cost price):</label><input type="number" min="1" class="form-control" name="unit_price[]"></div></td>\n\
                <td><div class="form-group"><label>Date Received:</label><input type="date" class="form-control" name="date_received[]" max="<?php echo date("Y-m-d") ?>" required></div></td>\n\
                <td><div class="form-group"><label>Date of Manufacture</label><input type="date" class="form-control" name="manufacture_date[]" max="<?php echo date("Y-m-d") ?>" required></div></td>\n\
                <td><div class="form-group"><label>Expiry Date:</label><input type="date" class="form-control" name="expiry_date[]" min="<?php echo date("Y-m-d") ?>" required></div></td><td></td>\n\
                </tr></table></div>');
                }
                if (type === "injectable" || type === "creams" || type === "drops" || type === "orals") {
                    document.getElementById('add_more_drug').insertAdjacentHTML('beforeend',
                            '<div  id="' + row_ids + '"><h5>' + type + ' <button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button></h5>\n\
                <table class="table table-bordered">\n\
                <tr><td><div class="form-group"><label >Drug Name</label><input type="hidden" name="drug_type[]" value="" id="drug_type_' + row_ids + '"><select id="drug_id_' + row_ids + '" class="select2" name="drug_name[]" required></select></div></td>\n\
                <td><div class="form-group"><label>Batch number:</label><input type="text" class="form-control" name="batch_number[]" required></div></td>\n\
                <td><div class="form-group"><label>Quantity:</label><input type="number" min="1" class="form-control" name="quantity[]" required></div></td>\n\
                <td><div class="form-group"><label>Description:</label><input type="text" class="form-control" name="description[]" required></div></td>\n\
                <input type="hidden" class="form-control" name="boxes[]"><input type="hidden" class="form-control" name="strips[]"><input type="hidden" class="form-control" name="tablets[]"></tr> \n\
                <tr> <td><div class="form-group"><label>Unit Price (Cost price):</label><input type="number" min="1" class="form-control" name="unit_price[]"></div></td>\n\
                <td><div class="form-group"><label>Date Received:</label><input type="date" class="form-control" name="date_received[]" max="<?php echo date("Y-m-d") ?>" required></div></td>\n\
                <td><div class="form-group"><label>Date of Manufacture</label><input type="date" class="form-control" name="manufacture_date[]" max="<?php echo date("Y-m-d") ?>" required></div></td>\n\
                <td><div class="form-group"><label>Expiry Date:</label><input type="date" class="form-control" name="expiry_date[]" min="<?php echo date("Y-m-d") ?>" required></div></td>\n\
                </tr></table></div>');
                }
                document.getElementById('drug_type_' + row_ids).value = type;
                $.ajax({
                    type: 'POST',
                    url: 'index.php?page=<?php echo $crypt->encode("ajax_data") ?>',
                    data: {display_selects: "display_selects", table_name: 'drug_names', id_column: 'Drug_Id', other_column: 'Drug_Name'},
                    success: function (html) {
                        $('#drug_id_' + row_ids).html(html);
                    }
                });
                $(".select2").each(function () {
                    initializeSelect2($(this));
                });
            }
            function add_element_allocate() {
                var row_idsv = Math.round(Math.random( ) * 300000000);
                document.getElementById('add_element_allocate').insertAdjacentHTML('beforeend',
                        '<tr id="' + row_idsv + '">\n\
                        <td> <select class="select2" id="drug_allocate_' + row_idsv + '" name="drug_name[]" onchange="calculateTotalUnits(' + row_idsv + ' );" required>\n\
                        <option value="">Select....</option> <?php
            $querydrug_select = 'select drugs_in_store.Drug_Type, drug_names.Drug_Name, drugs_in_store.Stock_Id from drug_names,drugs_in_store WHERE drug_names.Drug_Id=drugs_in_store.Drug_Id AND drugs_in_store.Expiry_Date>=CURDATE() GROUP BY drugs_in_store.Drug_Id,drugs_in_store.Drug_Type ORDER BY drugs_in_store.Date_Received DESC';
            $fetchDrugs = DB::getInstance()->querySample($querydrug_select);
            foreach ($fetchDrugs as $drugdata_select) {
                ?>  <option value="<?php echo $drugdata_select->Stock_Id; ?>"><?php echo $drugdata_select->Drug_Name . ' ' . $drugdata_select->Drug_Type; ?></option>\n\
<?php } ?></select></td>\n\
    <td class="form-inline" id="quantity_column_' + row_idsv + '"></td>\n\
    <td><select name="pharmacy_name[]" class="select2" required>\n\
                        <option value="">Select..............</option>\n\
                        <option value="Main">Main</option>\n\
                        <option value="Immunisation">Immunisation</option>\n\
                        <option value="Family Planning">Family Planning</option>\n\
                        <option value="Gynecology">Gynecology</option>\n\
                        <option value="Maternity">Maternity</option>\n\
                        <option value="Antenatal">Antenatal</option>\n\
                        </select></td>\n\
                        <td> <input type="date" class="form-control" name="date_sent[]" max="<?php echo date("Y-m-d") ?>" required>  </td>\n\
                        <td> <select class="select2" name="received_by[]" required>\n\
                        <option value="">Select....</option> <?php
$staffQuery = 'SELECT * FROM staff,person WHERE staff.Person_Id=person.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1 ORDER BY Fname DESC';
$staffList = DB::getInstance()->querySample($staffQuery);
foreach ($staffList as $staff) {
    ?> <option value="<?php echo $staff->Staff_Id; ?>"><?php echo $staff->Fname . " " . $staff->Lname; ?></option>\n\
<?php } ?></select></td>\n\
                        <td><button type="button" value="' + row_idsv + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>\n\
                        </td></tr>');
                $(".select2").each(function () {
                    initializeSelect2($(this));
                });
            }

            function delete_item(element_id) {
                $('#' + element_id).html('');
            }

            function get_drug_id(drug_id, price) {
                document.getElementById("price").value = price;
                document.getElementById("drug_id").value = drug_id;
            }
            function  calculateTotalUnits(tr_id) {
                var value = document.getElementById('drug_allocate_' + tr_id).value;
                if (value) {
                    $.ajax({
                        type: 'POST',
                        url: 'index.php?page=<?php echo $crypt->encode("ajax_data"); ?>',
                        data: {calculate_drug_limit: 'calculate_drug_limit', tr_id: tr_id, drug_id: value},
                        success: function (html) {
                            $('#quantity_column_' + tr_id).html(html);
                        }
                    });
                } else {
                    $('#quantity_column_' + tr_id).html('');
                }
            }
        </script>
    </body>

</html>