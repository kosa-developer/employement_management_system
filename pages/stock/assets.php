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
                                    <div class="page-title">Stock Equipments</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <?php
                                    if (Input::exists()) {
                                        if (Input::get("edit_asset_button") == "edit_asset_button") {
                                            $asset_id = Input::get("asset_id");
                                            $Stock_Asset_Id = Input::get("Stock_Asset_Id");
                                            $description = Input::get("description");
                                            $total_number = Input::get("total_number");
                                            $unit_price = Input::get("unit_price");
                                            $date_received = Input::get("date_received");
                                            $depreciation_rate = Input::get("depreciation_rate");
                                            $assetUpdate = DB::getInstance()->update("stock_assets", $Stock_Asset_Id, array(
                                                "Asset_id" => $asset_id,
                                                "Description" => $description,
                                                "Total_Number" => $total_number,
                                                "Unit_Price" => $unit_price,
                                                "Date_Received" => $date_received,
                                                "Depreciation_Rate" => $depreciation_rate
                                                    ), "Stock_Asset_Id");
                                            if ($assetUpdate) {
                                                echo '<div class="alert alert-success col-sm-6"> Asset details updated successfully</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("assets"));
                                        }

                                        if (Input::get("edit_allocated_asset_button") == "edit_allocated_asset_button") {
                                            $asset_id = Input::get("asset_id");
                                            $allocated_id = Input::get("allocated_id");
                                            $date_received = Input::get("date_received");
                                            $received_by = Input::get("received_by");
                                            // $total_number = Input::get("total_number");
                                            $department_name = Input::get("department_name");
                                            $assetUpdate = DB::getInstance()->update("asset_allocation", $allocated_id, array(
                                                "Department_Name" => $department_name,
                                                "Asset_Id" => $asset_id,
                                                "Received_Date" => $date_received,
                                                "Qty" => $total_number,
                                                "Received_by" => $received_by
                                                    ), "Asset_Allocation_Id");
                                            if ($assetUpdate) {
                                                echo '<div class="alert alert-success col-sm-6"> Allocated Asset details updated successfully</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("assets"));
                                        }

                                        if (Input::get("submit_new_asset") == 'submit_new_asset') {
                                            $asset_id = Input::get("asset_id");
                                            $description = Input::get("description");
                                            $total_number = Input::get("total_number");
                                            $serial_number = Input::get("serial_number");
                                            $unit_price = Input::get("unit_price");
                                            $date_received = Input::get("date_received");
                                            $depreciation_rate = Input::get("depreciation_rate");
                                            $assets_added = 0;
                                            $duplicates = 0;
                                            if (!empty($asset_id)) {
                                                $s = 0;
                                                $p = 0;
                                                for ($x = 0; $x < count($asset_id); $x++) {
                                                    $p = $total_number[$x];
                                                    while ($p > 0) {
                                                        $queryDup = DB::getInstance()->checkRows("SELECT * FROM stock_assets WHERE Asset_id='$asset_id[$x]' AND Serial_Number='$serial_number[$s]' AND Date_Received='$date_received[$x]' AND Description='$description[$x]'");
                                                        if ($queryDup) {
                                                            DB::getInstance()->query("UPDATE stock_assets SET Total_Number=(Total_Number+$total_number[$x]), Unit_Price='$unit_price[$x]',Depreciation_Rate='$depreciation_rate[$x]' WHERE Asset_id='$asset_id[$x]' AND Date_Received='$date_received[$x]' AND Description='$description[$x]'AND Serial_Number='$serial_number[$s]'");
                                                            $duplicates++;
                                                        } else {
                                                            $assetInsert = DB::getInstance()->insert("stock_assets", array(
                                                                "Asset_Id" => $asset_id[$x],
                                                                "Description" => $description[$x],
                                                                "Total_Number" => $total_number[$x],
                                                                "Serial_Number" => $serial_number[$s],
                                                                "Unit_Price" => $unit_price[$x],
                                                                "Date_Received" => $date_received[$x],
                                                                "Depreciation_Rate" => $depreciation_rate[$x]
                                                            ));
                                                            if ($assetInsert) {
                                                                $assets_added++;
                                                            }
                                                        }
                                                        $s++;
                                                        $p--;
                                                    }
                                                }
                                            }
                                            if ($assets_added != 0) {
                                                echo '<div class="alert alert-success col-sm-12">' . $assets_added . ' Assets successfully registered</div>';
                                            } if ($duplicates != 0) {
                                                echo '<div class="alert alert-warning col-sm-12">' . $duplicates . ' Assets duplicates could not be re registered, were updated</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("assets"));
                                        }
                                        if (Token::check(Input::get("token_new_asset_locate"))) {
                                            $asset_id = Input::get("asset_name");
                                            $date_received = Input::get("date_received");
                                            $department_name = Input::get("department_name");
                                            $serial_number = Input::get('serial_number');
                                            $received_by = Input::get("received_by");
                                            $total_number = Input::get("total_quantity");
                                            $selected_ids = Input::get("selected_ids");
                                            $assigned_by = $_SESSION['hospital_staff_id'];
                                            $assets_added = 0;
                                            $duplicates = 0;
                                            if (!empty($asset_id)) {
                                                for ($x = 0; $x < count($asset_id); $x++) {
                                                    $serials = explode(",", trim($selected_ids[$x], ","));
                                                    for ($i = 0; $i < count($serials); $i++) {
                                                        $queryDup = DB::getInstance()->checkRows("SELECT * FROM asset_allocation WHERE Stock_Asset_Id='$serials[$i]' AND Received_Date='$date_received[$x]' AND Assigned_by='$assigned_by' AND Received_by='$received_by[$x]' AND Department_Name='$department_name[$x]'");
                                                        if ($queryDup) {
                                                            $duplicates++;
                                                        } else {
                                                            $assetInsert = DB::getInstance()->insert("asset_allocation", array(
                                                                "Department_Name" => $department_name[$x],
                                                                "Stock_Asset_Id" => $serials[$i],
                                                                "Received_Date" => $date_received[$x],
                                                                "Assigned_by" => $assigned_by,
                                                                "Received_by" => $received_by[$x],
                                                                "Qty" => 1
                                                            ));
                                                            if ($assetInsert) {
                                                                $assets_added++;
                                                            }
                                                        }
                                                    }
                                                }
                                                if ($assets_added != 0) {
                                                    echo '<div class="alert alert-success col-sm-6">' . $assets_added . ' Assets successfully allocated</div>';
                                                } if ($duplicates != 0) {
                                                    echo '<div class="alert alert-warning col-sm-6">' . $duplicates . ' Assets duplicates could not be re allocted, were updated</div>';
                                                }
                                            } else {
                                                echo '<div class="alert alert-warning">Empty form could not be uploaded</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("assets"));
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body" id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#tab_new" data-toggle="tab">
                                                    <i class="fa fa-pencil"></i> Add new Equipment to stock
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_view" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Equipments in stock
                                                </a>
                                            </li>
                                            <li >
                                                <a href="#tab_allocate" data-toggle="tab">
                                                    <i class="fa fa-compass"></i> Allocate Equipments 
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_view_allocated" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Allocated Equipments
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content primary">
                                            <div class="tab-pane fade in active" id="tab_new">

                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-md-12">
                                                            <div class="form-group">

                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr><th>Equipment Name</th>
                                                                            <th>Description</th>
                                                                            <th style="width:10%">Units</th>
                                                                            <th style="width:20%">Serial Number(s)</th>                                                                            
                                                                            <th style="width:10%">Unit Price (UGx)</th>
                                                                            <th>Delivery Date</th>
                                                                            <th>Depreciation Rate (%age)<button type="button" class="btn btn-success btn-xs pull-right" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <select class="select2" style="width:100%" name="asset_id[]" required>
                                                                                    <option value="">Select....</option>
                                                                                    <?php
                                                                                    $queryward_select = 'select * from  asset order by Asset_Id desc';
                                                                                    $fetchward_select = DB::getInstance()->querySample($queryward_select);
                                                                                    foreach ($fetchward_select as $wardtdata_select) {
                                                                                        ?>
                                                                                        <option value="<?php echo $wardtdata_select->Asset_Id; ?>"><?php echo $wardtdata_select->Asset_Name; ?></option>
                                                                                    <?php } ?>
                                                                                </select>  
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control" name="description[]" rows="2" required> </textarea> 
                                                                            </td>
                                                                            <td >
                                                                                <input type="number" value="1" class="form-control" name="total_number[]" oninput="add_serial_fields(this.value,1)" required>  
                                                                            </td>
                                                                            <td >
                                                                                <div class="form-group" id="serial_number_add1">
                                                                                    <input type="text" class="form-control" name="serial_number[]" required>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" min="1" class="form-control" name="unit_price[]">  
                                                                            </td>
                                                                            <td>
                                                                                <input type="date" class="form-control" name="date_received[]" max="<?php echo date("Y-m-d") ?>" required>  
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" min="0" step="0.01" max="100" class="form-control" name="depreciation_rate[]">  
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_asset" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_new_asset" value="submit_new_asset">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade in " id="tab_allocate">

                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-md-12">
                                                            <div class="form-group">

                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr><td>Equipment Name</td>
                                                                            <td style="width:20%">Serial Numbers</td>
                                                                            <td>Received Date</td>
                                                                            <td>Received By</td>
                                                                            <td>Department<button type="button" class="btn btn-success btn-xs pull-right" id="add_more_allocate[]" onclick="add_element_allocate();">Add more</button></td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element_allocate">
                                                                        <tr id="row_1">
                                                                            <td>
                                                                                <select class="select2" style="width:100%" id="asset_allocate_1" name="asset_name[]" onchange="serialNumberSelection(this.value, 1);" required>
                                                                                    <option value="">Select....</option>
                                                                                    <?php
                                                                                    $queryasset_select = 'select DISTINCT asset.Asset_Name,asset.Asset_Id from  asset,stock_assets WHERE stock_assets.Asset_Id=asset.Asset_Id order by asset.Asset_Id desc';
                                                                                    $fetchasset_select = DB::getInstance()->querySample($queryasset_select);
                                                                                    foreach ($fetchasset_select as $assetdata_select) {
                                                                                        ?>
                                                                                        <option value="<?php echo $assetdata_select->Asset_Id; ?>"><?php echo $assetdata_select->Asset_Name; ?></option>
                                                                                    <?php } ?>
                                                                                </select>  
                                                                            </td>
                                                                            <td style="width:20%" id="serial_numbers1">

                                                                            </td>
                                                                            <td>
                                                                                <input type="date" class="form-control" name="date_received[]" max="<?php echo date("Y-m-d") ?>" required>  
                                                                            </td>
                                                                            <td>
                                                                                <select class="select2" style="width:100%" name="received_by[]"  required>
                                                                                    <option value="">Select....</option>
                                                                                    <?php
                                                                                    $queryasset_select = 'select * from  staff  order by Staff_Id desc';
                                                                                    $fetchasset_select = DB::getInstance()->querySample($queryasset_select);
                                                                                    foreach ($fetchasset_select as $assetdata_select) {
                                                                                        ?>
                                                                                        <option value="<?php echo $assetdata_select->Staff_Id; ?>"><?php echo DB::getInstance()->getName('person', $assetdata_select->Person_Id, 'Fname', 'Person_Id'); ?>&nbsp;<?php echo DB::getInstance()->getName('person', $assetdata_select->Person_Id, 'Lname', 'Person_Id'); ?></option>
                                                                                    <?php } ?>
                                                                                </select>  <input type="hidden" name="selected_ids[]" id="selected_id_1">
                                                                            </td>

                                                                            <td>
                                                                                <select class="select2" style="width:100%" name="department_name[]" required>
                                                                                    <option value="">Select....</option>
                                                                                    <?php
                                                                                    //Department array in the init file
                                                                                    for ($x = 0; $x < count($department_list_array); $x++) {
                                                                                        echo '<option value="' . $department_list_array[$x] . '">' . $department_list_array[$x] . '</option>';
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                                <button type="button" value="row_1" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_asset_locate" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit" value="submit">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab_view">
                                                <?php
                                                $queryAssets = "SELECT * FROM stock_assets,asset WHERE stock_assets.Asset_Id=asset.Asset_Id ORDER BY stock_assets.Date_Received DESC";
                                                if (DB::getInstance()->checkRows($queryAssets)) {
                                                    ?>                                                    
                                                    <table id="example1" class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Equipment Name</th>
                                                                <th>Description</th>
                                                                <th>Serial Number</th>
                                                                <th>Unit Price</th>
                                                                <th>Delivery Date</th>
                                                                <th>Depreciation Rate</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $asset_list = DB::getInstance()->querySample($queryAssets);
                                                            foreach ($asset_list as $assets) {
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $assets->Asset_Name; ?></td>
                                                                    <td><?php echo $assets->Description; ?></td>
                                                                    <td><?php echo $assets->Serial_Number; ?></td>
                                                                    <td><?php echo ($assets->Unit_Price != "") ? ugandan_shillings($assets->Unit_Price) : ""; ?></td>
                                                                    <td><?php echo ($assets->Date_Received != "") ? english_date($assets->Date_Received) : ""; ?></td>
                                                                    <td><?php echo $assets->Depreciation_Rate; ?>%</td>
                                                                    <td> 
                                                                        <a data-toggle="modal"  href="#edit_new_<?php echo $assets->Asset_Id ?>"><i class="fa fa-pencil"></i> Edit</a>
                                                                    </td>
                                                            <div class="modal fade" id="edit_new_<?php echo $assets->Asset_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                                <form action="" method="post">
                                                                    <div class="modal-dialog animated fadeInDown">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                <h4 class="modal-title">Edit <?php echo $assets->Asset_Name; ?>'s&nbsp;Information</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Equipment Name</label>
                                                                                    <input type="hidden" name="Stock_Asset_Id" value="<?php echo $assets->Stock_Asset_Id ?>">
                                                                                    <select class="select2" style="width:100%" name="asset_id" required>
                                                                                        <option value="">Select....</option>
                                                                                        <?php
                                                                                        $queryward_select = 'select * from  asset order by Asset_Id desc';
                                                                                        $fetchward_select = DB::getInstance()->querySample($queryward_select);
                                                                                        foreach ($fetchward_select as $wardtdata_select) {
                                                                                            ?>
                                                                                            <option  value="<?php echo $wardtdata_select->Asset_Id; ?>" <?php echo ($assets->Asset_Id == $wardtdata_select->Asset_Id) ? "selected" : ""; ?>><?php echo $wardtdata_select->Asset_Name; ?></option>
                                                                                        <?php } ?>
                                                                                    </select> 
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Description</label>
                                                                                    <textarea class="form-control" name="description" rows="2" required><?php echo $assets->Description ?></textarea> 
                                                                                </div>
                                                                                <!--                                                                                <div class="form-group">
                                                                                                                                                                    <label>Units</label>
                                                                                                                                                                    <input type="number" min="1" class="form-control" name="total_number" value="<?php echo $assets->Total_Number ?>" required>  
                                                                                                                                                                </div>-->
                                                                                <div class="form-group">
                                                                                    <label>Unit Price (UGx)</label>
                                                                                    <input type="number" min="1" class="form-control" value="<?php echo $assets->Unit_Price ?>" name="unit_price">  
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Date received</label>
                                                                                    <input type="date" class="form-control" name="date_received" value="<?php echo $assets->Date_Received ?>" max="<?php echo date("Y-m-d") ?>" required>  
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Depreciation Rate (%age)</label>
                                                                                    <input type="number" min="0" step="0.01" max="100" class="form-control"value="<?php echo $assets->Depreciation_Rate ?>" name="depreciation_rate">  
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                                <button type="submit" name="edit_asset_button" value="edit_asset_button"class="btn btn-success" type="button">Save changes</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                    <?php
                                                } else {
                                                    echo '<div class="alert alert-warning">No Assets registered</div>';
                                                }
                                                ?>
                                            </div>
                                            <div class="tab-pane fade" id="tab_view_allocated">
                                                <?php
                                                $queryAssets = "SELECT * FROM asset_allocation,stock_assets WHERE asset_allocation.Stock_Asset_Id=stock_assets.Stock_Asset_Id ORDER BY Asset_Allocation_Id DESC";
                                                if (DB::getInstance()->checkRows($queryAssets)) {
                                                    ?>                                                    
                                                    <table id="example4" class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Equipment Name</th>
                                                                <th>Serial number</th>
                                                                <th>Department</th>
                                                                <th>Received Date</th>
                                                                <th>Issued By</th>
                                                                <th>Received By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $asset_list = DB::getInstance()->querySample($queryAssets);
                                                            foreach ($asset_list as $assets) {
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo DB::getInstance()->getName('asset', $assets->Asset_Id, 'Asset_Name', 'Asset_Id'); ?></td>
                                                                    <td><?php echo $assets->Serial_Number; ?></td>
                                                                    <td><?php echo $assets->Department_Name; ?></td>
                                                                    <td><?php echo $assets->Received_Date; ?></td>
                                                                    <td><?php echo DB::getInstance()->getName('person', DB::getInstance()->getName('staff', $assets->Assigned_by, 'Person_Id', 'Staff_Id'), 'Fname', 'Person_Id'); ?>&nbsp;<?php echo DB::getInstance()->getName('person', DB::getInstance()->getName('staff', $assets->Assigned_by, 'Person_Id', 'Staff_Id'), 'Lname', 'Person_Id'); ?></td>
                                                                    <td><?php echo DB::getInstance()->getName('person', DB::getInstance()->getName('staff', $assets->Received_by, 'Person_Id', 'Staff_Id'), 'Fname', 'Person_Id'); ?>&nbsp;<?php echo DB::getInstance()->getName('person', DB::getInstance()->getName('staff', $assets->Received_by, 'Person_Id', 'Staff_Id'), 'Lname', 'Person_Id'); ?></td>
                                                                    <td> 
                                                                        <!--<a data-toggle="modal"  href="#edit_allocated_<?php echo $assets->Asset_Allocation_Id ?>"><i class="fa fa-pencil"></i> Edit</a>-->
                                                                    </td>
                                                            <div class="modal fade" id="edit_allocated_<?php echo $assets->Asset_Allocation_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                                <form action="" method="post">
                                                                    <div class="modal-dialog animated fadeInDown">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                <h4 class="modal-title">Edit Allocated <?php echo DB::getInstance()->getName('asset', $assets->Asset_Id, 'Asset_Name', 'Asset_Id'); ?>&nbsp;Information</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Equipment Name</label>
                                                                                    <input type="hidden" name="allocated_id" value="<?php echo $assets->Asset_Allocation_Id ?>">
                                                                                    <select class="select2" style="width:100%" name="asset_id" required>
                                                                                        <option value="">Select....</option>
                                                                                        <?php
                                                                                        $queryasset_select = 'select DISTINCT asset.Asset_Name,asset.Asset_Id from  asset,stock_assets WHERE stock_assets.Asset_Id=asset.Asset_Id order by asset.Asset_Id desc';
                                                                                        $fetchasset_select = DB::getInstance()->querySample($queryasset_select);
                                                                                        foreach ($fetchasset_select as $assetdata_select) {
                                                                                            ?>
                                                                                            <option value="<?php echo $assetdata_select->Asset_Id; ?>" <?php echo ($assets->Asset_Id == $assetdata_select->Asset_Id) ? "selected" : ""; ?>><?php echo $assetdata_select->Asset_Name; ?></option>
                                                                                        <?php } ?>
                                                                                    </select> </div>
                                                                                <div class="form-group">
                                                                                    <label>Received Date</label>
                                                                                    <input type="date" value="<?php echo $assets->Received_Date; ?>"class="form-control" name="date_received" max="<?php echo date("Y-m-d") ?>" required> 
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Received by</label>
                                                                                    <select class="select2" style="width:100%" name="received_by" required>
                                                                                        <option value="">Select....</option>
                                                                                        <?php
                                                                                        $queryasset_select = 'select * from  staff  order by Staff_Id desc';
                                                                                        $fetchasset_select = DB::getInstance()->querySample($queryasset_select);
                                                                                        foreach ($fetchasset_select as $assetdata_select) {
                                                                                            ?>
                                                                                            <option value="<?php echo $assetdata_select->Staff_Id; ?>" <?php echo ($assets->Received_by == $assetdata_select->Staff_Id) ? "selected" : ""; ?>><?php echo DB::getInstance()->getName('person', $assetdata_select->Person_Id, 'Fname', 'Person_Id'); ?>&nbsp;<?php echo DB::getInstance()->getName('person', $assetdata_select->Person_Id, 'Lname', 'Person_Id'); ?></option>
                                                                                        <?php } ?>
                                                                                    </select> 
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Qty</label>
                                                                                    <input type="number" value="<?php echo $assets->Qty; ?>" max="<?php echo $assets->Qty; ?>" min="1" class="form-control" name="total_number" required> 
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Department</label>

                                                                                    <select class="select2" style="width:100%" name="department_name" required>
                                                                                        <option value="">Select....</option>
                                                                                        <?php
                                                                                        //Department array in the init file
                                                                                        for ($x = 0; $x < count($department_list_array); $x++) {
                                                                                            $selected = ($assets->Department_Name == $department_list_array[$x]) ? " selected" : "";
                                                                                            echo '<option value="' . $department_list_array[$x] . '" ' . $selected . '>' . $department_list_array[$x] . '</option>';
                                                                                        }
                                                                                        ?>
                                                                                    </select> 
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                                <button type="submit" name="edit_allocated_asset_button" value="edit_allocated_asset_button"class="btn btn-success" type="button">Save changes</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                    <?php
                                                } else {
                                                    echo '<div class="alert alert-warning">No Assets allocated</div>';
                                                }
                                                ?>
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
        <script>
                                                                                    function initializeSelect2(selectElementObj) {
                                                                                        selectElementObj.select2({
                                                                                            width: "100%",
                                                                                            allowClear: true
                                                                                        });
                                                                                    }
                                                                                    function add_element() {
                                                                                        var row_ids = Math.round(Math.random() * 300000000);
                                                                                        document.getElementById('add_element').insertAdjacentHTML('beforeend',
                                                                                                '<tr id="' + row_ids + '">\n\
                    <td> <select class="select2" style="width:100%" name="asset_id[]" required>\n\
                    <option value="">Select....</option> <?php
            $queryward_select = 'select * from  asset order by Asset_Id desc';
            $fetchward_select = DB::getInstance()->querySample($queryward_select);
            foreach ($fetchward_select as $wardtdata_select) {
                ?> <option value="<?php echo $wardtdata_select->Asset_Id; ?>">\n\
    <?php echo $wardtdata_select->Asset_Name; ?></option>\n\
<?php } ?></td>\n\
\n\
                    <td><textarea class="form-control" name="description[]" rows="2" required> </textarea></td>\n\
                    <td><input type="number" value="1" class="form-control" name="total_number[]" oninput="add_serial_fields(this.value,' + row_ids + ')" required></td>\n\
             <td >\n\
            <div class="form-group" id="serial_number_add' + row_ids + '">\n\
                <input type="text" class="form-control" name="serial_number[]" required>\n\
            </div>\n\
            </td>\n\
                        <td><input min="1" name="unit_price[]" class="form-control" type="number"></td>\n\
                        <td><input name="date_received[]" class="form-control" type="date" max="<?php echo date("Y-m-d") ?>" required></td>\n\
                           <td class="form-inline">\n\
                               <input min="0" name="depreciation_rate[]" class="form-control" type="number" step="0.01" max="100" style="width:80%">\n\
                               <button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>\n\
                                    </td></tr>');
                                                                                        $(".select2").each(function () {
                                                                                            initializeSelect2($(this));
                                                                                        });

                                                                                    }
                                                                                    function delete_item(element_id) {
                                                                                        $('#' + element_id).html('');

                                                                                    }
                                                                                    function add_element_allocate() {
                                                                                        var row_idsv = Math.round(Math.random() * 300000000);
                                                                                        document.getElementById('add_element_allocate').insertAdjacentHTML('beforeend',
                                                                                                '<tr id="' + row_idsv + '">\n\
                    <td> <select class="select2" style="width:100%" id="asset_allocate_' + row_idsv + '" name="asset_name[]" onchange="serialNumberSelection(this.value,' + row_idsv + ' );" required>\n\
                    <option value="">Select....</option> <?php
$queryasset_select = 'select DISTINCT asset.Asset_Name,asset.Asset_Id from  asset,stock_assets WHERE stock_assets.Asset_Id=asset.Asset_Id order by asset.Asset_Id desc';
$fetchasset_select = DB::getInstance()->querySample($queryasset_select);
foreach ($fetchasset_select as $assetdata_select) {
    ?>  <option value="<?php echo $assetdata_select->Asset_Id; ?>"><?php echo $assetdata_select->Asset_Name; ?></option>\n\
<?php } ?></select></td>\n\
                                                                            <td style="width:20%" id="serial_numbers' + row_idsv + '">\n\
\n\
                                                                            </td>\n\
                    <td> <input type="date" class="form-control" name="date_received[]" max="<?php echo date("Y-m-d") ?>" required>  </td>\n\
                    <td> <select class="select2" style="width:100%" name="received_by[]" required>\n\
                    <option value="">Select....</option> <?php
$queryasset_select = 'select * from  staff  order by Staff_Id desc';
$fetchasset_select = DB::getInstance()->querySample($queryasset_select);
foreach ($fetchasset_select as $assetdata_select) {
    ?> <option value="<?php echo $assetdata_select->Staff_Id; ?>"><?php echo DB::getInstance()->getName('person', $assetdata_select->Person_Id, 'Fname', 'Person_Id'); ?>&nbsp;<?php echo DB::getInstance()->getName('person', $assetdata_select->Person_Id, 'Lname', 'Person_Id'); ?></option>\n\
<?php } ?></select>\n\
<input type="hidden" name="selected_ids[]" id="selected_id_' + row_idsv + '"></td>\n\
                    <td> <select class="select2" style="width:100%" name="department_name[]" required> \n\
                    <option value="">Select....</option>\n\
<?php
//Department array in the init file
for ($x = 0; $x < count($department_list_array); $x++) {
    echo '<option value="' . $department_list_array[$x] . '">' . $department_list_array[$x] . '</option>';
}
?> </select><button type="button" value="' + row_idsv + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>\n\
                    </td></tr>');
                                                                                        $(".select2").each(function () {
                                                                                            initializeSelect2($(this));
                                                                                        });
                                                                                    }
                                                                                    function delete_item(element_id) {
                                                                                        $('#' + element_id).html('');
                                                                                    }
                                                                                    function  serialNumberSelection(asset_id, tr_id) {
                                                                                        var asset_id = asset_id;
                                                                                        var value = document.getElementById('asset_allocate_' + tr_id).value;
                                                                                        if (value) {
                                                                                            $.ajax({
                                                                                                type: 'POST',
                                                                                                url: 'index.php?page=<?php echo $crypt->encode("ajax_data"); ?>',
                                                                                                data: {populate_asset_serial_numbers: 'populate_asset_serial_numbers', asset_id: asset_id, tr_id: tr_id},
                                                                                                success: function (html) {
                                                                                                    $('#serial_numbers' + tr_id).html(html);
                                                                                                    $(".select2").each(function () {
                                                                                                        initializeSelect2($(this));
                                                                                                    });
                                                                                                }
                                                                                            });
                                                                                        }
                                                                                    }
                                                                                    function calculateSelectedNumbers(element, tr_id) {
                                                                                        var serialsData = "";
                                                                                        for (var i = 0; i < element.options.length; i++) {
                                                                                            var item = element.getElementsByTagName('option')[i].value;
                                                                                            if (element.getElementsByTagName('option')[i].selected === true) {
                                                                                                serialsData += item + ",";
                                                                                            }
                                                                                        }
                                                                                        document.getElementById("selected_id_" + tr_id).value = serialsData;
                                                                                    }
                                                                                    function add_serial_fields(serial_no, tr_id) {
                                                                                        $('#serial_number_add' + tr_id).html('');
                                                                                        for (var s = 0; s < serial_no; s++) {
                                                                                            document.getElementById('serial_number_add' + tr_id).insertAdjacentHTML('beforeend', '<input type="text" class="form-control" name="serial_number[]" required> <br>');
                                                                                        }
                                                                                    }
        </script>
    </body>

</html>