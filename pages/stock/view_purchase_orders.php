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
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-topline-yellow">
                                    <div class="card-body ">
                                        <div class="content-body">    
                                            <div class="row">
                                                <form method="POST" action="">
                                                    <div class="row form-group">
                                                        <div class="col-md-3">
                                                            <label>Date From</label>
                                                            <input type="date" name="date_from" class="form-control" max="<?php echo date("Y-m-d") ?>">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label>To</label>
                                                            <input type="date" name="date_to" class="form-control" max="<?php echo date("Y-m-d") ?>">
                                                        </div>
                                                        <div class="col-md-2"><br/>
                                                            <button type="submit" name="search_purchase_orders" value="search_purchase_orders" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>List of all purchase orders</header>
                                        <div class="actions panel_actions pull-right">
                                            <a class="btn btn-primary" href="index.php?page=<?php echo $crypt->encode("new_purchase_order"); ?>"><i class="fa fa-plus"></i> Add New Purchase Order</a>
                                        </div>
                                    </div>
                                    <div class="card-body ">
                                        <?php
                                        if (Input::exists() && Input::get("approve_order") == "approve_order") {
                                            $purchase_id = Input::get("purchase_id");
                                            $item_types = serialize(Input::get("item_types"));
                                            $description = serialize(Input::get("description"));
                                            $quantity = serialize(Input::get("quantity"));
                                            $unit_price = serialize(Input::get("unit_price"));
                                            $user_id = $_SESSION['hospital_user_id'];
                                            $update = DB::getInstance()->query("UPDATE purchase_order SET Status='Approved',Approved_By='$user_id',Approved_Time=NOW(),Final_Item_Types='$item_types',Final_Description='$description',Final_Quantity='$quantity',Final_Unit_Cost='$unit_price' WHERE Purchase_Id='$purchase_id'");
                                            if ($update) {
                                                echo '<div class="alert alert-success">Order successfully approved</div>';
                                            }
                                            Redirect::go_to('index.php?page=' . $crypt->encode("view_purchase_orders"));
                                        }
                                        if (isset($_GET['action']) && $_GET['purchase_id'] != "") {
                                            $purchase_id = $crypt->decode($_GET['purchase_id']);
                                            $action = $crypt->decode($_GET['action']);
                                            if ($action == "delete_order") {
                                                $delete = DB::getInstance()->query("DELETE FROM purchase_order WHERE Purchase_Id='$purchase_id'");
                                                if ($delete) {
                                                    $log_made = $_SESSION['hospital_staff_names'] . " deleted an LPO.";
                                                    DB::getInstance()->logs($log_made);
                                                    echo '<div class="alert alert-info">Order successfully Deleted</div>';
                                                }
                                            }
                                            Redirect::go_to("");
                                        }
                                        $user_id = $_SESSION['hospital_user_id'];
                                        $condition = ($_SESSION['hospital_role'] == "Store Manager") ? " AND Ordered_By='$user_id' " : "";
                                        if (Input::exists() && Input::get("search_purchase_orders") == "search_purchase_orders") {
                                            $date_from = Input::get("date_from");
                                            $date_to = Input::get("date_to");
                                            $condition .= ($date_from != "") ? " AND substr(Date,1,10)>='$date_from'" : "";
                                            $condition .= ($date_to != "") ? " AND substr(Date,1,10)<='$date_to'" : "";
                                        }
                                        $ordersCheck = "SELECT * FROM purchase_order WHERE Purchase_Id IS NOT NULL $condition ORDER BY Date DESC LIMIT 1000";
                                        if (DB::getInstance()->checkRows($ordersCheck)) {
                                            ?>
                                            <table class="table table-striped table-bordered table-responsive" id="example1" >
                                                <thead>
                                                    <tr>
                                                        <th>LPO No.</th>
                                                        <th>Date</th>
                                                        <th>Goods Ordered</th>
                                                        <th>Total Cost</th>
                                                        <th>Goods Approved</th>
                                                        <th>Cost Approved</th>
                                                        <th>Ordered By</th>
                                                        <th>Supplier</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $order_list = DB::getInstance()->querySample($ordersCheck);
                                                    foreach ($order_list as $orders):
                                                        $total_cost = 0;
                                                        $staffCheck = "SELECT CONCAT(FName,' ',Lname)AS Names FROM user,staff,person WHERE person.Person_Id=staff.Person_Id AND user.Staff_Id=staff.Staff_Id AND user.User_Id='$orders->Ordered_By' ORDER BY Names";
                                                        $usernames = DB::getInstance()->displayTableColumnValue($staffCheck, 'Names');
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $orders->LPO_Number ?></td>
                                                            <td><?php echo english_date($orders->Date) ?></td>
                                                            <td>
                                                                <?php
                                                                $item_types = unserialize($orders->Item_Types);
                                                                $goods_ordered = unserialize($orders->Description);
                                                                $quantity_ordered = unserialize($orders->Quantity);
                                                                $unit_price_ordered = unserialize($orders->Unit_Cost);
                                                                $total_cost = 0;
                                                                if (count($goods_ordered) > 0) {
                                                                    for ($x = 0; $x < count($goods_ordered); $x++) {
                                                                        if ($goods_ordered[$x] != "") {
                                                                            $item_name = "";
                                                                            if ($item_types[$x] == "Drugs") {
                                                                                $item_name = DB::getInstance()->getName("drug_names", $goods_ordered[$x], "Drug_Name", "Drug_Id");
                                                                            }
                                                                            if ($item_types[$x] == "Sandries") {
                                                                                $item_name = DB::getInstance()->getName("sandries", $goods_ordered[$x], "Sandry_Name", "Sandry_Id");
                                                                            }
                                                                            if ($item_types[$x] == "Assets") {
                                                                                $item_name = DB::getInstance()->getName("asset", $goods_ordered[$x], "Asset_Name", "Asset_Id");
                                                                            }
                                                                            $total = ($unit_price_ordered[$x] * $quantity_ordered[$x]);
                                                                            echo '<li>' . $item_name . ':' . $quantity_ordered[$x] . '*' . $unit_price_ordered[$x] . '=' . $total . '</li>';
                                                                            $total_cost += $total;
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?php echo ugandan_shillings($total_cost); ?></td>
                                                            <td>
                                                                <?php
                                                                $item_types = unserialize($orders->Final_Item_Types);
                                                                $final_goods_ordered = unserialize($orders->Final_Description);
                                                                $final_quantity_ordered = unserialize($orders->Final_Quantity);
                                                                $final_unit_price_ordered = unserialize($orders->Final_Unit_Cost);
                                                                $final_total_cost = 0;
                                                                if (count($final_goods_ordered) > 0) {
                                                                    for ($x = 0; $x < count($final_goods_ordered); $x++) {
                                                                        if ($final_goods_ordered[$x] != "") {
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
                                                                            $total = ($final_unit_price_ordered[$x] * $final_quantity_ordered[$x]);
                                                                            echo '<li>' . $item_name . ': ' . $final_quantity_ordered[$x] . '*' . $final_unit_price_ordered[$x] . '=' . $total . '</li>';
                                                                            $final_total_cost += $total;
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><?php echo ($final_total_cost > 0) ? ugandan_shillings($final_total_cost) : ""; ?></td>
                                                            <td><?php echo $usernames ?></td>
                                                            <td><?php echo DB::getInstance()->getName("suppliers", $orders->Supplier_Id, "Supplier_Name", "Supplier_Id") ?></td>
                                                            <td>
                                                                <?php
                                                                if ($orders->Status == "Pending") {
                                                                    echo'<label class="btn btn-warning btn-xs">' . $orders->Status . '</label><br/>';
                                                                    ?>
                                                                    <a data-toggle="modal"  href="#order<?php echo $orders->Purchase_Id ?>"><i class='fa fa-check icon-xs'></i> Approve</a>
                                                                    <br/><a href="index.php?page=<?php echo $crypt->encode("view_purchase_orders") . '&action=' . $crypt->encode("delete_order") . '&purchase_id=' . $crypt->encode($orders->Purchase_Id) ?>" class="" style="color: red" onclick="return confirm('Do you really want to delete this order?')"><i class="fa fa-times"></i> Remove</a>
                                                                    <br/><?php
                                                                } else {
                                                                    echo'<label class="btn btn-success btn-xs">' . $orders->Status . '</label><br/>';
                                                                    ?><a target="_blank" href="index.php?type=downloadpurchase_orders&page=<?php echo $crypt->encode("financial_report_pdf") . '&order_id=' . $crypt->encode($orders->Purchase_Id); ?>" class="fa fa-print" style="color: blue"> Print</a>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <?php if ($orders->Status == "Pending") { ?>
                                                            <!-- General section box modal start -->
                                                        <div class="modal fade" data-backdrop="static" id="order<?php echo $orders->Purchase_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                            <div class="modal-dialog animated fadeInDown" style="width: 65%">
                                                                <div class="modal-content">
                                                                    <form action="" method="POST">
                                                                        <div class="modal-header">
                                                                            <a href="" class="close" aria-hidden="true">&times;</a>
                                                                            <h4 class="modal-title"><i class="fa fa-edit"></i> Order Approval Form</h4>
                                                                        </div>
                                                                        <div class="modal-body row">
                                                                            <input type="hidden" value="<?php echo $orders->Purchase_Id ?>" name="purchase_id" class="form-control">
                                                                            <div class="col-sm-12">
                                                                                <div class="col-sm-1">Type</div>
                                                                                <div class="col-sm-3">Description</div>
                                                                                <div class="col-sm-2">Quantity</div>
                                                                                <div class="col-sm-2">Unit Cost</div>
                                                                                <div class="col-sm-3">Total</div>
                                                                            </div>
                                                                            <?php
                                                                            $item_types = unserialize($orders->Item_Types);
                                                                            $goods_ordered = unserialize($orders->Description);
                                                                            $quantity_ordered = unserialize($orders->Quantity);
                                                                            $unit_price_ordered = unserialize($orders->Unit_Cost);
                                                                            $total_general = 0;
                                                                            for ($x = 0; $x < count($goods_ordered); $x++) {
                                                                                $item_name = "";
                                                                                if ($item_types[$x] == "Drugs") {
                                                                                    $item_name = DB::getInstance()->getName("drug_names", $goods_ordered[$x], "Drug_Name", "Drug_Id");
                                                                                }
                                                                                if ($item_types[$x] == "Sandries") {
                                                                                    $item_name = DB::getInstance()->getName("sandries", $goods_ordered[$x], "Sandry_Name", "Sandry_Id");
                                                                                }
                                                                                if ($item_types[$x] == "Assets") {
                                                                                    $item_name = DB::getInstance()->getName("asset", $goods_ordered[$x], "Asset_Name", "Asset_Id");
                                                                                }
                                                                                ?>
                                                                                <div class="col-sm-12" id="div_<?php echo $orders->Purchase_Id . "_" . $x ?>">
                                                                                    <input type="hidden" value="<?php echo $item_types[$x] ?>" name="item_types[]" class="form-control">
                                                                                    <input type="hidden" value="<?php echo $goods_ordered[$x] ?>" name="description[]" class="form-control">
                                                                                    <div class="col-sm-1"><?php echo $item_types[$x] ?></div>
                                                                                    <div class="col-sm-3">
                                                                                        <input type="text" readonly class="form-control" value="<?php echo $item_name; ?>">
                                                                                    </div>
                                                                                    <div class="col-sm-2">
                                                                                        <input type="number" min="0" id="quantity_<?php echo $orders->Purchase_Id . "_" . $x ?>" onkeyup="calculateTotal('<?php echo $orders->Purchase_Id . "','" . $x ?>');" step="0.01" class="form-control" value="<?php echo $quantity_ordered[$x] ?>" name="quantity[]" >
                                                                                    </div>
                                                                                    <div class="col-sm-2">
                                                                                        <input type="number" min="1" id="unit_price_<?php echo $orders->Purchase_Id . "_" . $x ?>" onkeyup="calculateTotal('<?php echo $orders->Purchase_Id . "','" . $x ?>');" class="form-control" value="<?php echo $unit_price_ordered[$x] ?>" name="unit_price[]" >
                                                                                    </div>
                                                                                    <div class="col-sm-4 form-inline">
                                                                                        <input type="text" readonly id="total_cost_<?php echo $orders->Purchase_Id . "_" . $x ?>" class="form-control" value="<?php echo $quantity_ordered[$x] * $unit_price_ordered[$x] ?>" name="total_cost<?php echo $orders->Purchase_Id ?>[]" >
                                                                                        <button type="button" value="" class="btn btn-danger btn-xs pull-right" onclick="delete_item('<?php echo $orders->Purchase_Id . "','" . $x ?>');"><i class ="fa fa-times"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                                <?php
                                                                                $total_general += ($unit_price_ordered[$x] * $quantity_ordered[$x]);
                                                                            }
                                                                            ?>
                                                                            <div class="col-sm-12">
                                                                                <div class="col-sm-5"></div>
                                                                                <div class="col-sm-3">TOTAL</div>
                                                                                <div class="col-sm-4">
                                                                                    <input type="text" readonly id="general_total_<?php echo $orders->Purchase_Id ?>" value="<?php echo $total_general ?>" class="form-control">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <a class="btn btn-warning" href="">Cancel</a>
                                                                            <button class="btn btn-success" name="approve_order" value="approve_order" type="submit">Save changes</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- modal end -->
                                                        <?php
                                                    }
                                                endforeach;
                                                ?>
                                                </tbody>
                                            </table>
                                            <?php
                                        } else {
                                            echo '<div class="alert alert-warning">No Purchase orders registered</div>';
                                        }
                                        ?>
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
        <script>
                                                                            function delete_item(parent_id, child_id) {
                                                                                $('#div_' + parent_id + '_' + child_id).html('');
                                                                                calculateOverallTotal(parent_id);
                                                                            }
                                                                            function calculateTotal(parent_id, child_id) {
                                                                                var quantity = document.getElementById('quantity_' + parent_id + "_" + child_id).value;
                                                                                var unit_cost = document.getElementById('unit_price_' + parent_id + "_" + child_id).value;
                                                                                quantity = (quantity) ? parseFloat(quantity) : 0;
                                                                                unit_cost = (unit_cost) ? parseFloat(unit_cost) : 0;
                                                                                var total = quantity * unit_cost;
                                                                                document.getElementById('total_cost_' + parent_id + "_" + child_id).value = total;
                                                                                calculateOverallTotal(parent_id);
                                                                            }
                                                                            function calculateOverallTotal(parent_id) {
                                                                                var overall_total = 0;
                                                                                // gets all the input tags in frm, and their number
                                                                                //var inpfields = frm.getElementsByTagName('input');
                                                                                var inpfields = document.getElementsByName('total_cost' + parent_id + '[]');
                                                                                var nr_inpfields = inpfields.length;
                                                                                // traverse the inpfields elements, and adds the value of selected (checked) checkbox in selchbox
                                                                                for (var i = 0; i < nr_inpfields; i++) {
                                                                                    if (inpfields[i].type == 'text' && inpfields[i].value != "") {
                                                                                        var total_got = parseFloat(inpfields[i].value);
                                                                                        overall_total += total_got;
                                                                                    }
                                                                                }
                                                                                document.getElementById('general_total_' + parent_id).value = overall_total;
                                                                            }
        </script>

        <!-- end js include path -->
    </body>

</html>