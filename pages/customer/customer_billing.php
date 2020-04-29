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
                                    <div class="page-title">CUSTOMER BILLING</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <?php
                                if (Input::exists() && Input::get("add_billing") == "add_billing") {
                                    $customer_id = Input::get("Customer_id");
                                    $start_month = Input::get("start_month");
                                    $guards = Input::get("no_of_guards");
                                    $rate = Input::get("rate");
                                     $period = Input::get("period");

                                    $submited = 0;
                                    for ($i = 0; $i < sizeof($customer_id); $i++) {
                                        $month_and_year = substr($date_from[$i], 0, 7);
                                        if (DB::getInstance()->checkRows("SELECT * FROM billing WHERE Customer_Id='$customer_id[$i]' AND Start_Month='$start_month[$i]'")) {
                                            if ($guards[$i] != "" && $rate[$i] != "") {
                                                $dataUpdate = DB::getInstance()->query("UPDATE billing SET No_of_guards='$guards[$i]',Period='$period[$i]',Rate='$rate[$i]' WHERE Customer_Id='$customer_id[$i]' AND Start_Month='$start_month[$i]'");
                                                $submited++;
                                                $action_made = "uploaded";
                                            }
                                        } else {
                                            if ($guards[$i] != "" && $rate[$i] != "") {
                                                $dataUpdate = DB::getInstance()->insert("billing", array(
                                                    "Customer_Id" => $customer_id[$i],
                                                    "Start_Month" => $start_month[$i],
                                                    "Period" => $period[$i],
                                                    "Rate" => ($rate[$i] != "") ? $rate[$i] : NULL,
                                                    "No_of_guards" => ($guards[$i] != "") ? $guards[$i] : NULL
                                                ));
                                                $submited++;
                                                $action_made = "uploaded";
                                            }
                                        }
                                    }
                                    if ($dataUpdate) {
                                        echo '<div class="alert alert-success">' . $submited . ' customers have been billed successfully</div>';
                                    }
                                    Redirect::go_to("");
                                }
                                ?>


                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>customer billing</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <table class="table table-striped table-bordered table-responsive" id="example1" >
                                                    <thead>
                                                        <tr>
                                                            <th>customer/organization</th>
                                                            <th>No.guards</th>
                                                            <th>Rate</th>
                                                            <th>Period</th>
                                                            <th>Starting Month</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $customerCheck = "SELECT * FROM customer where status=1";
                                                        $customer_list = DB::getInstance()->query($customerCheck);
                                                        foreach ($customer_list->results() as $customer):
                                                            $no_of_guards = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM billing WHERE Customer_Id='$customer->Customer_Id' order by Billing_Id DESC limit 1", "No_of_guards");
                                                            $rate = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM billing WHERE Customer_Id='$customer->Customer_Id' order by Billing_Id DESC limit 1", "Rate");
                                                            $Period = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM billing WHERE Customer_Id='$customer->Customer_Id' order by Billing_Id DESC limit 1", "Period");
                                                           $months_from = DB::getInstance()->DisplayTableColumnValue("SELECT * FROM billing WHERE Customer_Id='$customer->Customer_Id' order by Billing_Id DESC limit 1", "Start_Month");
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $customer->Customer_Names ?> 
                                                                    <input type="hidden" class="form-control" name="Customer_id[]" value="<?php echo $customer->Customer_Id ?>"  ></td>
                                                                <td style="width:10%;"><input type="number" min="0" class="form-control" oninput="calculate('<?php echo $customer->Customer_Id ?>');" name="no_of_guards[]" id="<?php echo $customer->Customer_Id ?>guards" value="<?php echo $no_of_guards; ?>"></td>
                                                                <td style="width:20%;"><input type="number" min="0" class="form-control" name="rate[]" oninput="calculate('<?php echo $customer->Customer_Id ?>');" id="<?php echo $customer->Customer_Id ?>rate" value="<?php echo $rate; ?>"></td>
                                                                <td style="width:20%;"><input style="width:50%;" type="number" min="0" class="form-control pull-left" name="period[]" oninput="calculate('<?php echo $customer->Customer_Id ?>');" id="<?php echo $customer->Customer_Id ?>period" value="<?php echo $Period; ?>">MONTHS</td>
                                                                <td style="width:20%;">
                                                                    <select class="select2" data-placeholder="Month..." style="width: 100%;" onchange="calculate('<?php echo $customer->Customer_Id ?>');"  id="<?php echo $customer->Customer_Id ?>start_month" name="start_month[]">
                                                                        <option value="">Choose....</option>
                                                                        <?php
                                                                        $month_from = substr(DB::getInstance()->DisplayTableColumnValue("SELECT MIN(Date) AS Min_Date FROM customer", "Min_Date"), 0, 7);
                                                                        $month_to = date("Y-12");

                                                                        $begin = new DateTime($month_from);
                                                                        $end = new DateTime($month_to);
                                                                        $end = $end->modify('+1 month');
                                                                        $interval = new DateInterval('P1M');
                                                                        $daterange = new DatePeriod($begin, $interval, $end);
                                                                        foreach ($daterange AS $date) {
                                                                            $selected=($months_from==$date->format('Y-m'))?"selected":"";
                                                                            echo '<option '.$selected.' value="' . $date->format('Y-m') . '">' . $date->format('m/Y') . '</option>';
                                                                        }
                                                                        ?>
                                                                    </select></td>
                                                                <td style="width:25%;"><input type="number" min="0" class="form-control"  id="<?php echo $customer->Customer_Id ?>total" readonly value="<?php echo $total = ($no_of_guards != "" && $rate != "") ? $no_of_guards * $rate*$Period : "" ?>"></td>
                                                            </tr>

                                                            <?php
                                                        endforeach;
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="pull-right">
                                                    <button type="submit" name="add_billing" id="add_billing" value="add_billing" class="btn btn-success hidden">Submit</button>
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
            <script>
                function calculate($customerid) {
                    var guards = document.getElementById($customerid + "guards").value;
                    var rate = document.getElementById($customerid + "rate").value;
                    var period = document.getElementById($customerid + "period").value;
                    var start_month=document.getElementById($customerid + "start_month").value;
                   
                    var total;
                    guards = (guards !== "") ? guards : 0;

                    rate = (rate !== "") ? rate : 0;
                    period = (period !== "") ? period : 0;

                    total = guards * rate * period;
                    if (total !== 0) {
                        document.getElementById($customerid + "total").value = total;
                        if(start_month!==''){
                          $('#add_billing').attr({"class": 'btn btn-success'});
                      }else{
                          $('#add_billing').attr({"class": 'hidden'}); 
                      }
                        
                    } else {
                        document.getElementById($customerid + "total").value = "";
                         $('#add_billing').attr({"class": 'hidden'});
                    }

                }
            </script>
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