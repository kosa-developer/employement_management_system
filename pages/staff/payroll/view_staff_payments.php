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
                <?php require_once 'includes/side_menu.php'; ?>
                <!-- end sidebar menu -->
                <!-- start page content -->
                <div class="page-content-wrapper">
                    <div class="page-content">
                        <div class="page-bar">
                            <div class="page-title-breadcrumb">
                                <div class="title page-title">Payroll (All Staff Payments)</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Staff Search</header>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="" class="form-inline">
                                            <div class="form-group">
                                                <label>Staff</label>
                                                <select class="select2" style="width: 100%" name="staff_id">
                                                    <option value="">Select..</option>
                                                    <?php
                                                    $staffCheck = "SELECT * FROM staff,person WHERE person.Person_Id=staff.Person_Id AND staff.Is_Approved=1  ORDER BY person.Fname";
                                                    $staff_list = DB::getInstance()->query($staffCheck);
                                                    foreach ($staff_list->results() as $staff):
                                                        echo '<option value="' . $staff->Staff_Id . '">' . $staff->Title . ' ' . $staff->Fname . ' ' . $staff->Lname . '</option>';
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Paid as</label>
                                                <div class="controls">
                                                    <select class="form-control" name="payment_type">
                                                        <option value="">Choose..</option>
                                                        <option value="Advance">Advance</option>
                                                        <option value="Salary">Salary</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Date From</label>
                                                <div class="controls">
                                                    <input type="date" class="form-control" name="date_from" max="<?php echo date("Y-m-d") ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>To</label>
                                                <div class="controls">
                                                    <input type="date" class="form-control" name="date_to" max="<?php echo date("Y-m-d") ?>">
                                                </div>
                                            </div>
                                            <div class="form-group"><br/>
                                                <button type="submit" name="search_payment_btn" value="search_payment_btn" class="btn btn-success"><i class="fa fa-search"></i> Search Staff</button>
                                            </div>
                                        </form>
                                    </div>
                                </div><?php
                                $condition = "";
                                $reportName = "Staff Payments REPORT ";
                                if (Input::exists() && Input::get("search_payment_btn") == "search_payment_btn") {
                                    $staff_id = Input::get("staff_id");
                                    $payment_type = Input::get("payment_type");
                                    $date_from = Input::get("date_from");
                                    $date_to = Input::get("date_to");
                                    $condition .= ($staff_id != "") ? " AND staff_payments.Staff_Id='$staff_id' " : "";
                                    $condition .= ($payment_type != "") ? " AND staff_payments.Payment_Type='$payment_type' " : "";
                                    $condition .= ($date_from != "") ? " AND substr(staff_payments.Payment_Date,1,10)>='$date_from' " : "";
                                    $condition .= ($date_to != "") ? " AND substr(staff_payments.Payment_Date,1,10)<='$date_to' " : "";
                                    $reportName .= ($date_from != "") ? " FROM " . strtoupper(english_date($date_from)) : "";
                                    $reportName .= ($date_to != "") ? " TO " . strtoupper(english_date($date_to)) : "";
                                }
                                $reportName = strtoupper($reportName);
                                $queryPayments = "SELECT person.*,staff_payments.* FROM staff,person,staff_payments WHERE staff_payments.Staff_Id=staff.Staff_Id AND person.Person_Id=staff.Person_Id $condition ORDER BY Payment_Date";
                                if (DB::getInstance()->checkRows($queryPayments)) {
                                    $data_sent = serialize(array($queryPayments, $reportName));
                                    ?> 
                                    <div class="card card-topline-yellow">
                                        <div class="card-head">
                                            <header><?php echo $reportName ?></header>
                                            <div class="actions panel_actions pull-right">
                                                <a target="_blank" href="index.php?page=<?php echo $crypt->encode("financial_report_pdf") . "&type=download_staff_payments_pdf&data_sent=" . $crypt->encode($data_sent) ?>" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Print pdf</a>
                                            </div>

                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <table id="example1" class="table table-striped table-responsive display table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Staff Name</th>
                                                                <th>Payment Date</th>
                                                                <th>Amount paid</th>
                                                                <th>Paid as</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $paymentsList = DB::getInstance()->querySample($queryPayments);
                                                            $totalPayments = 0;
                                                            foreach ($paymentsList as $staff_payments) {
                                                                $totalPayments += $staff_payments->Amount_Paid;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $staff_payments->Fname . " " . $staff_payments->Lname; ?></td>
                                                                    <td><?php echo english_date($staff_payments->Payment_Date); ?></td>
                                                                    <td><?php echo number_format($staff_payments->Amount_Paid); ?></td>
                                                                    <td><?php echo $staff_payments->Payment_Type ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>TOTAL</th><th></th><th><?php echo number_format($totalPayments) ?></th><th></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo '<h3 style="color:red;">NO ' . $reportName . '</h3>';
                                }
                                ?>
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
    </body>

</html>