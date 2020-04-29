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
                                    <div class="page-title">CUSTOMER INVOICING</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">

                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>customer invoicing</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <table class="table table-striped table-bordered table-responsive" id="example1" >
                                                    <thead>
                                                        <tr>
                                                            <th>customer/organization</th>
                                                            <th>Rate</th>
                                                            <th >Total bill</th>
                                                            <th>Months</th>
                                                            <th colspan="2">Guards</th>

                                                        </tr>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th >Days</th>
                                                            <th >Amount</th>


                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $customerCheck = "SELECT * FROM billing,customer where billing.Customer_Id=customer.Customer_Id AND status=1 group by billing.Billing_Id";
                                                        $customer_list = DB::getInstance()->query($customerCheck);
                                                        $opentr = "<tr>";
                                                        $closetr = "</tr>";
                                                        foreach ($customer_list->results() as $customer):
                                                            $rowspan_client = $customer->No_of_guards * $customer->Period;
                                                            $rowspan_month = $customer->No_of_guards;
                                                            $rowspan_rate = $customer->No_of_guards * $customer->Period;
                                                            $rowspan_total = $customer->No_of_guards * $customer->Period;
                                                            $daily_amount=unserialize(DB::getInstance()->displayTableColumnValue("select Amount_Perdays from invoicing where Billing_Id='$customer->Billing_Id'", 'Amount_Perdays'));
                                                           $no_of_working_days=unserialize(DB::getInstance()->displayTableColumnValue("select Working_Days from invoicing where Billing_Id='$customer->Billing_Id'", 'Working_Days'));
                                                            echo $opentr;
                                                            ?>
                                                        <td rowspan="<?php echo $rowspan_client; ?>"><?php echo $customer->Customer_Names ?></td>
                                                        <td rowspan="<?php echo $rowspan_rate; ?>"><?php echo $customer->Rate ?></td>
                                                        <td rowspan="<?php echo $rowspan_total; ?>" ><div id="overall_total_<?php echo $customer->Billing_Id ?>"><?php echo DB::getInstance()->displayTableColumnValue("select Total_Amount from invoicing where Billing_Id='$customer->Billing_Id'", 'Total_Amount') ?></div><input type="hidden"id="input_total_<?php echo $customer->Billing_Id ?>" name="overall_total[]"></td>
                                                        <?php
                                                        $amountarry=0;
                                                        for ($nmonth = 0; $nmonth < $customer->Period; $nmonth++) {
                                                            
                                                            if ($nmonth > 0) {
                                                                echo $opentr;
                                                            }
                                                            $testmonth = substr($customer->Start_Month, 5, 2) + $nmonth;

                                                            $testmonth = ($testmonth > 12 && $testmonth == 13) ? 1 : (($testmonth > 12 && $testmonth == 14) ? 2 : (($testmonth > 12 && $testmonth == 15) ? 3 : (($testmonth > 12 && $testmonth == 16) ? 4 : (($testmonth > 12 && $testmonth == 17) ? 5 : (($testmonth > 12 && $testmonth == 18) ? 6 : (($testmonth > 12 && $testmonth == 19) ? 7 : (($testmonth > 12 && $testmonth == 20) ? 9 : (($testmonth > 12 && $testmonth == 21) ? 10 : (($testmonth > 12 && $testmonth == 21) ? 11 : (($testmonth > 12 && $testmonth == 22) ? 12 : $testmonth))))))))));
                                                            $zero = ($testmonth < 10) ? "0" : "";
                                                            $display_month = $zero . $testmonth;
                                                            $display_year = ((substr($customer->Start_Month, 5, 2) + $nmonth) > 12) ? substr($customer->Start_Month, 0, 4) + 1 : substr($customer->Start_Month, 0, 4);

                                                            $final_month = "02-" . $display_month . "-" . $display_year;
                                                            ?>

                                                            <td rowspan="<?php echo $rowspan_month; ?>"><?php echo english_months_year($final_month) ?> 
                                                                <input type="hidden" name="month<?php echo $customer->Billing_Id; ?>[]" value="<?php echo substr($final_month, 3, 7); ?>"></td>
                                                            <?php
                                                            for ($nguards = 0; $nguards < $customer->No_of_guards; $nguards++) {
                                                                
                                                                if ($nguards > 0) {
                                                                    echo $opentr;
                                                                }
                                                                ?>
                                                                <td >full_month<br/><label>yes<input type="radio" name="days<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>" value="yes" id="days_<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>" onchange="callcalculate('<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>', '<?php echo $customer->Rate ?>', this.value, '<?php echo $customer->Billing_Id; ?>');"></label>
                                                                    <label>No<input type="radio" name="days<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>" value="no" id="days_<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>" onchange="callcalculate('<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>', '<?php echo $customer->Rate ?>', this.value, '<?php echo $customer->Billing_Id; ?>');"></label>
                                                                    <div style="color:red"id="enteramount_<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>"><?php echo $daysno=($no_of_working_days[$amountarry]==30)?"1 Month":(($no_of_working_days[$amountarry]<30&&$no_of_working_days[$amountarry]!='')?$no_of_working_days[$amountarry]."days":"");?></div></td>
                                                                <td ><div style="color:blue" id="success_<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>"></div><div id="returmamount_<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>"><?php echo $daily_amount[$amountarry]?></div><input type="hidden" name="amount_printed<?php echo $customer->Billing_Id; ?>[]" id="inputamount_<?php echo $customer->Billing_Id . "_" . $customer->Start_Month . "_" . $customer->No_of_guards . "_" . $nguards . "" . $nmonth; ?>"></td>
                                                                <?php
                                                                if ($nguards < $customer->No_of_guards) {
                                                                    echo $closetr;
                                                                }
                                                                $amountarry++;
                                                            }
                                                           
                                                        }
                                                        ?>


                                                        <?php
                                                        echo $closetr;
                                                    endforeach;
                                                    ?>
                                                    </tbody>
                                                </table>
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
                function callcalculate(customerid, rate, value, id) {
                    var days = document.getElementById("days_" + customerid).value;

                    var number_of_days;
                    var totalvalue;
                    var rateamount = parseFloat(rate);
                    if (value === 'yes') {
                        number_of_days = 30;
                        totalvalue = rateamount;
                        $("#returmamount_" + customerid).html(totalvalue);
                        document.getElementById("inputamount_" + customerid).value = totalvalue;

                        $("#enteramount_" + customerid).html("<input type='hidden'  class='form-control' name='daysworked" + id + "[]' value='" + number_of_days + "'  >");
                        var overall_total = 0;
                        var amount_entered = document.getElementsByName('amount_printed' + id + '[]');
                        var days_worked = document.getElementsByName('daysworked' + id + '[]');
                        var month_worked = document.getElementsByName('month' + id + '[]');
                        var amount_per_day = [];
                        var period = [];
                        var working_days = [];
                        for (var i = 0; i < amount_entered.length; i++) {
                            if (amount_entered[i].type === "hidden" && amount_entered[i].value !== "") {
                                var total_got = parseFloat(amount_entered[i].value);
                                overall_total += total_got;
                                amount_per_day.push(total_got);
                            }
                        }

                        for (var k = 0; k < month_worked.length; k++) {
                            if (month_worked[k].type === "hidden" && month_worked[k].value !== "") {
                                var months = month_worked[k].value;
                                period.push(months);
                            }
                        }

                        for (var r = 0; r < days_worked.length; r++) {
                            if (days_worked[r].type === "hidden" || days_worked[r].type === "number" && days_worked[r].value !== "") {
                                var work_days = days_worked[r].value;
                                working_days.push(work_days);
                            }
                        }
                        $("#overall_total_" + id).html(overall_total);
                        document.getElementById("input_total_" + id).value = overall_total;

                        $.ajax({
                            type: 'POST',
                            url: 'index.php?page=<?php echo $crypt->encode("ajax_data") ?>',
                            data: {invoicing: "invoicing", working_days: working_days, billing_id: id, rate: rate, total_bill: overall_total, amount_per_day: amount_per_day, period: period},
                            success: function (html) {
                              
                                 $("#success_" + customerid).html(html);
                            }
                        });
                    } else if (value === 'no') {
                        $("#returmamount_" + customerid).html('');
                        $("#enteramount_" + customerid).html('<input type="number" class="form-control" max="29" min="0" name="daysworked' + id + '[]"  oninput="inputcalculate(this.value,' + rate + ',\'' + customerid + '\',' + id + ')">');
                        document.getElementById("inputamount_" + customerid).value = "";
                        $("#overall_total_" + id).html("");
                        document.getElementById("input_total_" + id).value = "";

                    }


                }
                function inputcalculate(value, rate, customerid, id) {
                    var totalvalue;
                    var rateamount = parseFloat(rate);
                    var number_of_days = parseFloat(value);
                    var totalvalue = Math.round((rateamount / 30) * number_of_days);
                    if (number_of_days < 30) {
                        $("#returmamount_" + customerid).html(totalvalue);
                        document.getElementById("inputamount_" + customerid).value = totalvalue;

                        var overall_total = 0;
                        var amount_entered = document.getElementsByName('amount_printed' + id + '[]');
                        var days_worked = document.getElementsByName('daysworked' + id + '[]');
                        var month_worked = document.getElementsByName('month' + id + '[]');
                        var amount_per_day = [];
                        var period = [];
                        var working_days = [];
                        for (var i = 0; i < amount_entered.length; i++) {
                            if (amount_entered[i].type === "hidden" && amount_entered[i].value !== "") {
                                var total_got = parseFloat(amount_entered[i].value);
                                overall_total += total_got;
                                amount_per_day.push(total_got);
                            }
                        }

                        for (var k = 0; k < month_worked.length; k++) {
                            if (month_worked[k].type === "hidden" && month_worked[k].value !== "") {
                                var months = month_worked[k].value;
                                period.push(months);
                            }
                        }

                        for (var r = 0; r < days_worked.length; r++) {
                            if (days_worked[r].type === "hidden" || days_worked[r].type === "number" && days_worked[r].value !== "") {
                                var work_days = days_worked[r].value;
                                working_days.push(work_days);
                            }
                        }

                        $("#overall_total_" + id).html(overall_total);
                        document.getElementById("input_total_" + id).value = overall_total;
                        
                          $.ajax({
                            type: 'POST',
                            url: 'index.php?page=<?php echo $crypt->encode("ajax_data") ?>',
                            data: {invoicing: "invoicing", working_days: working_days, billing_id: id, rate: rate, total_bill: overall_total, amount_per_day: amount_per_day, period: period},
                            success: function (html) {
                                 $("#success_" + customerid).html(html);
                            }
                        });
                    } else {
                        alert("value must be less than 30 days: thanks");
                        $("#returmamount_" + customerid).html("");
                        $("#overall_total_" + id).html("");
                        document.getElementById("input_total_" + id).value = "";
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