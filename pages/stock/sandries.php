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
                                    <div class="page-title">Sandries</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <?php
                                    if (Input::exists()) {
                                        if (Input::get("edit_asset_button") == "edit_asset_button") {
                                            $sandry_id = Input::get("sandry_id");
                                            $sandry_name = Input::get("sandry_name");
                                            $sandry_type = Input::get("sandry_type");
                                            $sandry_qty = Input::get("sandry_qty");
                                            $unit_price = Input::get("unit_price");
                                            $batch_number = Input::get("batch_number");
                                            $manufacture_date = Input::get("manufacture_date");
                                            if (empty($unit_price)) {
                                                $unit_price = NULL;
                                            }
                                            $date_received = Input::get("date_received");
                                            $assetUpdate = DB::getInstance()->update("sandries", $sandry_id, array(
                                                "Sandry_Name" => $sandry_name,
                                                "Batch_Number" => $batch_number,
                                                "Sandry_Type" => $sandry_type,
                                                "Qty" => $sandry_qty,
                                                "Price" => $unit_price,
                                                "Manufacture_Date" => $manufacture_date,
                                                "Date_Received" => $date_received
                                                    ), "Sandry_Id");
                                            if ($assetUpdate) {
                                                echo '<div class="alert alert-success col-sm-6"> Sandries details updated successfully</div>';
                                            }
                                            Redirect::go_to("");
                                        }
                                        if (Token::check(Input::get("token_new_asset"))) {
                                            $sandry = Input::get("sandry");
                                            $batch_number = Input::get("batch_number");
                                            $qty = Input::get("qty");
                                            $price = Input::get("price");
                                            $type = Input::get("type");
                                            $manufacture_date = Input::get("manufacture_date");
                                            $date_received = Input::get("date_received");
                                            $sandry_added = 0;
                                            for ($x = 0; $x < count($sandry); $x++) {
                                                if (empty($price[$x])) {
                                                    $price[$x] = NULL;
                                                }
                                                $sandryInsert = DB::getInstance()->insert("sandries", array(
                                                    "Sandry_Name" => $sandry[$x],
                                                    "Batch_Number" => $batch_number[$x],
                                                    "Sandry_Type" => $type[$x],
                                                    "Qty" => $qty[$x],
                                                    "Price" => $price[$x],
                                                    "Manufacture_Date" => $manufacture_date[$x],
                                                    "Date_Received" => $date_received[$x]
                                                ));
                                                if ($sandryInsert) {
                                                    $sandry_added++;
                                                }
                                            }
                                            if ($sandry_added != 0) {
                                                echo '<div class="alert alert-success">Sandries successfully registered</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("sandries"));
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body" id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#tab_new" data-toggle="tab">
                                                    <i class="fa fa-pencil"></i> Register new Sandries
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_view" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Sandries 
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
                                                                        <tr>
                                                                            <th>Sandries Name</th>
                                                                            <th>Batch number</th>
                                                                            <th>Type</th>
                                                                            <th>Qty</th>
                                                                            <th>Manufacture date</th>
                                                                            <th>Date Received</th>
                                                                            <th>Price @ Unit<button type="button" class="btn btn-success btn-xs pull-right" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="sandry[]" required> 
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="batch_number[]" required>  
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="type[]" required>  
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" min="0" class="form-control" name="qty[]" required>  
                                                                            </td>
                                                                            <td>
                                                                                <input type="date" class="form-control" name="manufacture_date[]" max="<?php echo date("Y-m-d") ?>" required>  
                                                                            </td>
                                                                            <td>
                                                                                <input type="date" class="form-control" name="date_received[]" max="<?php echo date("Y-m-d") ?>" required>  
                                                                            </td>
                                                                            <td class="form-inline">
                                                                                <input type="number" min="0" class="form-control" name="price[]">  
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_asset" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit" value="submit">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab_view">

                                                <?php
                                                $querySandries = "SELECT * FROM sandries ORDER BY Date_Received DESC";
                                                if (DB::getInstance()->checkRows($querySandries)) {
                                                    ?>                                                    
                                                    <table id="example1" class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Sandry Name</th>
                                                                <th>Batch number</th>
                                                                <th>Type</th>
                                                                <th>Qty</th>
                                                                <th>Manufacture date</th>
                                                                <th>Price @ Unit</th>
                                                                <th>Recieved Date</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sandries_list = DB::getInstance()->querySample($querySandries);
                                                            foreach ($sandries_list as $sandries) {
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $sandries->Sandry_Name; ?></td>
                                                                    <td><?php echo $sandries->Batch_Number; ?></td>
                                                                    <td><?php echo $sandries->Sandry_Type; ?></td>
                                                                    <td><?php echo $sandries->Qty; ?></td>
                                                                    <td><?php echo $sandries->Manufacture_Date; ?></td>
                                                                    <td><?php echo ($sandries->Price != "") ? ugandan_shillings($sandries->Price) : ""; ?></td>
                                                                    <td><?php echo $sandries->Date_Received; ?></td>
                                                                    <td> 
                                                                        <a data-toggle="modal"  href="#edit_<?php echo $sandries->Sandry_Id ?>"><i class="fa fa-pencil"></i> Edit</a>
                                                                    </td>
                                                            <div class="modal fade" id="edit_<?php echo $sandries->Sandry_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                                <form action="" method="post">
                                                                    <div class="modal-dialog animated fadeInDown">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                <h4 class="modal-title">Edit <?php echo $sandries->Sandry_Name; ?>'s&nbsp;Information</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Sandry Name</label>
                                                                                    <input type="hidden" name="sandry_id" value="<?php echo $sandries->Sandry_Id ?>">
                                                                                    <input type="text" class="form-control" name="sandry_name" value="<?php echo $sandries->Sandry_Name ?>">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Batch Number</label>
                                                                                    <input class="form-control" name="batch_number"  value="<?php echo $sandries->Batch_Number ?>" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Sandry Type</label>
                                                                                    <input class="form-control" name="sandry_type"  value="<?php echo $sandries->Sandry_Type ?>" required>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Qty</label>
                                                                                    <input type="number" min="1" class="form-control" name="sandry_qty" value="<?php echo $sandries->Qty ?>" required>  
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Manufacture Date</label>
                                                                                    <input type="date" class="form-control" name="manufacture_date" value="<?php echo $sandries->Manufacture_Date ?>" max="<?php echo date("Y-m-d") ?>" required>  
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Unit Price (UGx)</label>
                                                                                    <input type="number" min="1" class="form-control" value="<?php echo $sandries->Price ?>" name="unit_price">  
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Date received</label>
                                                                                    <input type="date" class="form-control" name="date_received" value="<?php echo $sandries->Date_Received ?>" max="<?php echo date("Y-m-d") ?>" required>  
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
                                                    echo '<div class="alert alert-warning">No Sandries registered</div>';
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
                                                                                function add_element() {
                                                                                    var row_ids = Math.round(Math.random( ) * 300000000);
                                                                                    document.getElementById('add_element').insertAdjacentHTML('beforeend',
                                                                                            '<tr id="' + row_ids + '">\n\
                                                                                        <td><input name="sandry[]" class="form-control" type="text" required></td>\n\
                                                                                        <td><input type="text" class="form-control" name="batch_number[]" required>\n\
                                                                                        <td><input  name="type[]" class="form-control" type="text" required></td>\n\
                                                                                        <td><input  name="qty[]" class="form-control" type="number" required></td> \n\
                                                                                        <td><input name="manufacture_date[]" class="form-control" type="date" max="<?php echo date("Y-m-d") ?>" required></td>\n\
                                                                                        <td><input name="date_received[]" class="form-control" type="date" max="<?php echo date("Y-m-d") ?>" required></td>\n\
                                                                                        <td class="form-inline"><input min="1" name="price[]" class="form-control" type="number" style="width:80%">\n\
                                                                                        <button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>\n\
                                                                                        </td></tr>');
                                                                                }
                                                                                function delete_item(element_id) {
                                                                                    $('#' + element_id).html('');
                                                                                }
        </script>
    </body>

</html>