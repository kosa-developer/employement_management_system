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
                                    <div class="page-title">Purchase Orders</div>
                                </div>
                                <div class="actions panel_actions pull-right">
                                    <a class="btn btn-primary" href="index.php?page=<?php echo $crypt->encode("view_purchase_orders"); ?>"><i class="fa fa-eye"></i> View Pending Orders</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if (Input::exists() && Input::get("add_purchase_order") == "add_purchase_order") {
                                    $supplier = (Input::get("supplier") != "") ? Input::get("supplier") : NULL;
                                    $user_id = $_SESSION['hospital_user_id'];
                                    $date = Input::get("date");
                                    $lpo_number = Input::get("lpo_number");
                                    $item_types = Input::get("item_types");
                                    $stock_name = Input::get("stock_name");
                                    $unit_cost = Input::get("unit_cost");
                                    $quantity = Input::get("quantity");
                                    $queryCheck = "SELECT * FROM purchase_order WHERE LPO_Number='$lpo_number'";
                                    if (!DB::getInstance()->checkRows($queryCheck)) {
                                        $insertPurchaseOrder = DB::getInstance()->insert("purchase_order", array(
                                            'LPO_Number' => $lpo_number,
                                            'Date' => $date,
                                            'Item_Types' => serialize($item_types),
                                            'Description' => serialize($stock_name),
                                            'Quantity' => serialize($quantity),
                                            'Unit_Cost' => serialize($unit_cost),
                                            'Supplier_Id' => $supplier,
                                            'Ordered_By' => $user_id
                                        ));
                                        if ($insertPurchaseOrder) {
                                            echo'<div class="alert alert-success">Purchase order submitted successfully</div>';
                                        }
                                    } else {
                                        echo'<div class="alert alert-warning">Error, LPO Number already submitted</div>';
                                    }
                                    Redirect::go_to("");
                                }
                                ?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Entry form</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="">
                                            <div class="row form-group">
                                                <div class="col-md-2">
                                                    <label>LPO No:</label>
                                                    <input type="text" class="form-control" name="lpo_number" required/>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>Date:</label>
                                                    <input type="date" class="form-control" name="date" value="<?php echo $date_today ?>" max="<?php echo $date_today ?>" required/>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>To Supplier</label>
                                                    <select  class="select2" style="width: 100%" name="supplier">
                                                        <?php echo DB::getInstance()->dropDowns('suppliers', 'Supplier_Id', 'Supplier_Name'); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row col-sm-12">
                                                <button type="button" class="btn btn-success btn-xs hidden" onclick="add_element('Drugs');"><i class="fa fa-plus-circle"></i> Add Drugs</button>
                                                <button type="button" class="btn btn-success btn-xs hidden" onclick="add_element('Sandries');"><i class="fa fa-plus-circle"></i> Add Sandries</button>
                                                <button type="button" class="btn btn-success btn-xs" onclick="add_element('Assets');"><i class="fa fa-plus-circle"></i> Add Asset</button>
                                            </div>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>Goods Description</th>
                                                        <th>Total Quantity</th>
                                                        <th>Unit Cost</th>
                                                        <th>Total Cost</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody  id="add_element">
                                                    <tr id="tr_1">
                                                        <td>Assets <input type="hidden" name="item_types[]" value="Assets"></td>
                                                        <td>
                                                            <select class="form-control select2" name="stock_name[]" required>
                                                                <?php echo DB::getInstance()->dropDowns("asset", "Asset_Id", "Asset_Name"); ?>
                                                            </select>
                                                        </td>
                                                        <td><input type="number" id="quantity_1" onkeyup="calculateTotal(1);" min="0" step="0.1" class="form-control" name="quantity[]" required></td>
                                                        <td><input type="number" id="unit_price_1" onkeyup="calculateTotal(1);" min="0" class="form-control" name="unit_cost[]" required></td>
                                                        <td><input type="text" id="total_cost_1" class="form-control" name="total_cost[]" readonly></td>
                                                        <td>
                                                            <button type="button" value="1" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr><th></th><th></th><th></th><th>TOTAL:</th><th><input type="text" readonly class="form-control" id="general_total" value=""></th><th></th></tr>
                                                </tfoot>
                                            </table>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="pull-right ">
                                                    <button type="submit" name="add_purchase_order" value="add_purchase_order" class="btn btn-success">Save</button>
                                                    <button type="reset" class="btn">Cancel</button>
                                                </div>
                                            </div>
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
            <?php include_once 'includes/footer.php';
            ?>
            <!-- end footer -->
        </div>
        <!-- start js include path -->
        <?php include_once 'includes/footer_js.php'; ?>

        <script>
                                                                function initializeSelect2(selectElementObj) {
                                                                    selectElementObj.select2({
                                                                        width: "100%",
                                                                        allowClear: true
                                                                    });
                                                                }
                                                                function add_element(element_type) {
                                                                    var row_ids = Math.round(Math.random( ) * 300000000), data, table_name, id_column, other_column;
                                                                    if (element_type === "Assets") {
                                                                        table_name = "asset", id_column = "Asset_Id", other_column = "Asset_Name";
                                                                    } else if (element_type === "Drugs") {
                                                                        table_name = "drug_names", id_column = "Drug_Id", other_column = "Drug_Name";
                                                                    } else if (element_type === "Sandries") {
                                                                        table_name = "sandries", id_column = "Sandry_Id", other_column = "Sandry_Name";
                                                                    }
                                                                    document.getElementById('add_element').insertAdjacentHTML('beforeend',
                                                                            '<tr id="tr_' + row_ids + '"><td>' + element_type + '<input type="hidden" name="item_types[]" value="' + element_type + '"></td>\n\
                                                    <td><select class="form-control select2" id="item_' + row_ids + '"  name="stock_name[]" style="width:100%;" tabindex="1" required></select></td>\n\
                                                    <td><input type="number" id="quantity_' + row_ids + '" onkeyup="calculateTotal(' + row_ids + ');" min="0" step="0.1" class="form-control" name="quantity[]" required></td>\n\
                                                    <td><input type="number" min="0" id="unit_price_' + row_ids + '" onkeyup="calculateTotal(' + row_ids + ');" class="form-control" name="unit_cost[]" required></td>\n\
                                                    <td><input type="text" id="total_cost_' + row_ids + '" class="form-control" name="total_cost[]" readonly></td>\n\
                                                    <td><button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>\n\
                                                    </td></tr>');

                                                                    $.ajax({
                                                                        type: 'POST',
                                                                        url: 'index.php?page=<?php echo $crypt->encode("ajax_data") ?>',
                                                                        data: {display_selects: "display_selects", table_name: table_name, id_column: id_column, other_column: other_column},
                                                                        success: function (html) {
                                                                            $('#item_' + row_ids).html(html);
                                                                        }
                                                                    });
                                                                    $(".select2").each(function () {
                                                                        initializeSelect2($(this));
                                                                    });
                                                                }
                                                                function delete_item(element_id) {
                                                                    $('#tr_' + element_id).html('');
                                                                    calculateOverallTotal();
                                                                }
                                                                function calculateTotal(tr_id) {
                                                                    var quantity = document.getElementById('quantity_' + tr_id).value;
                                                                    var unit_cost = document.getElementById('unit_price_' + tr_id).value;
                                                                    quantity = (quantity) ? parseFloat(quantity) : 0;
                                                                    unit_cost = (unit_cost) ? parseFloat(unit_cost) : 0;
                                                                    var total = quantity * unit_cost;
                                                                    document.getElementById('total_cost_' + tr_id).value = total;
                                                                    calculateOverallTotal();
                                                                }
                                                                function calculateOverallTotal() {
                                                                    var overall_total = 0;
                                                                    // gets all the input tags in frm, and their number
                                                                    //var inpfields = frm.getElementsByTagName('input');
                                                                    var inpfields = document.getElementsByName('total_cost[]');
                                                                    var nr_inpfields = inpfields.length;
                                                                    // traverse the inpfields elements, and adds the value of selected (checked) checkbox in selchbox
                                                                    for (var i = 0; i < nr_inpfields; i++) {
                                                                        if (inpfields[i].type === 'text' && inpfields[i].value !== "") {
                                                                            var total_got = parseFloat(inpfields[i].value);
                                                                            overall_total += total_got;
                                                                        }
                                                                    }
                                                                    document.getElementById('general_total').value = overall_total;
                                                                }
        </script>
    </body>

</html>