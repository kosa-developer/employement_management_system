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
                                    <div class="page-title">Staff Payroll</div>
                                </div>
                                <div class="actions panel_actions pull-right">
                                    <a class="btn btn-primary" href="index.php?page=<?php echo $crypt->encode("view_staff_payments"); ?>"><i class="fa fa-eye"></i> View Staff Payments</a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-8">
                                <?php
                                if (Input::exists() && Input::get("add_staff_payment") == "add_staff_payment") {
                                    $staff_id = Input::get("staff_id");
                                    $payment_date = Input::get("payment_date");
                                    $payment_type = Input::get("payment_type");
                                    $amount_paid = Input::get("amount_paid");
                                    if ($amount_paid > 0) {
                                        DB::getInstance()->insert("staff_payments", array(
                                            "Staff_Id" => $staff_id,
                                            "Payment_Date" => $payment_date,
                                            "Payment_Type" => $payment_type,
                                            "AMount_Paid" => $amount_paid,
                                            "Registered_By" => $_SESSION['hospital_user_id']
                                        ));
                                        echo '<div class="alert alert-success">Staff payment uploaded successfully</div>';
                                    } else {
                                        echo '<div class="alert alert-danger">Could not upload payment less or equal to zero</div>';
                                    }
                                    Redirect::go_to("");
                                }
                                ?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Staff Payment Entry</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input type="date" class="form-control" name="payment_date" value="<?php echo $date_today ?>" id="payment_date" max="<?php echo $date_today ?>" required oninput="returnMaximumPayment();">
                                                </div>
                                              
                                                <div class="form-group">
                                                    <label>Staff</label>
                                                    <div class="controls">
                                                        <select class="select2" style="width: 100%" name="staff_id" id="staff_id" required onchange="returnMaximumPayment();">
                                                            <option value="">Select..</option>
                                                            <?php
                                                            $staffCheck = "SELECT * FROM staff,person WHERE person.Person_Id=staff.Person_Id AND staff.Is_Approved=1 AND staff.Staff_Status=1  ORDER BY person.Fname";
                                                            $staff_list = DB::getInstance()->query($staffCheck);
                                                            foreach ($staff_list->results() as $staff):
                                                                echo '<option value="' . $staff->Staff_Id . '">' . $staff->Title . ' ' . $staff->Fname . ' ' . $staff->Lname . '</option>';
                                                            endforeach;
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Paid as</label>
                                                    <select class="form-control" name="payment_type" id="payment_type" required onchange="returnMaximumPayment();">
                                                        <option value="">Choose..</option>
                                                        <option value="Advance">Advance</option>
                                                        <option value="Salary">Salary</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Amount Paid</label>
                                                    <div class="controls">
                                                        <input type="number" min="0" class="form-control" name="amount_paid" id="amount_paid" required>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="pull-right">
                                                    <button type="submit" name="add_staff_payment" value="add_staff_payment" class="btn btn-success">Save</button>
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
            <?php include_once 'includes/footer.php'; ?>
            <!-- end footer -->
        </div>
        <!-- start js include path -->
        <?php include_once 'includes/footer_js.php'; ?>
        <!-- end js include path -->
        <script>
                                                        function returnMaximumPayment() {
                                                            var staff_id = document.getElementById("staff_id").value;
                                                            var payment_type = document.getElementById("payment_type").value;
                                                            var payment_date = document.getElementById("payment_date").value;
                                                            if (staff_id && payment_type && payment_date) {
                                                                $.ajax({
                                                                    type: 'POST',
                                                                    url: 'index.php?page=<?php echo $crypt->encode("ajax_data") ?>',
                                                                    data: {returnStaffExpectedPayment: "returnStaffExpectedPayment", payment_date: payment_date, payment_type: payment_type, staff_id: staff_id},
                                                                    success: function (html) {
                                                                        var data = html.split('#');
                                                                        var total_salary_expected = parseFloat(data[0]);
                                                                        var total_salary_paid = parseFloat(data[1]);
                                                                        var total_salary_not_paid = parseFloat(data[2]);
                                                                        var total_daily_rates =parseFloat(data[3]);
                                                                        var total_overtime_rates =parseFloat(data[4]);
                                                                        var total_nssf=parseFloat(data[5]);
                                                                        var total_provtax=parseFloat(data[6]);
                                                                        var total_loan_earn=parseFloat(data[7]);
                                                                        var total_Lloan=parseFloat(data[8]);
                                                                        var total_daily_days=parseFloat(data[9]);
                                                                        var total_overtime_days=parseFloat(data[10]);
                                                                       
                                                                        
                 // alert("total_salary_expected= "+total_salary_expected+", total_salary_paid="+total_salary_paid+", total_salary_not_paid="+total_salary_not_paid+", total_daily_rates="+total_daily_rates+",total_overtime_rates="+total_overtime_rates+", total_nssf="+total_nssf+", total_provtax="+total_provtax+", total_loan_earn="+total_loan_earn+", total_Lloan="+total_Lloan+", total_daily_days="+total_daily_days+", total_overtime_days="+total_overtime_days);
                                                                        $('#amount_paid').attr({"max": total_salary_not_paid});
                                                                        //document.getElementById("paye").value = paye;
                                                                        //document.getElementById("nssf").value = nssf;
                                                                        //document.getElementById("lst").value = lst;
                                                                    }
                                                                });
                                                            }
                                                        }
        </script>
    </body>

</html>