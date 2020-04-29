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
                                    <div class="page-title">Staff Loan</div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-8">
                                <?php
                                  if (isset($_GET['action']) && $_GET['action'] == $crypt->encode("remove_loan") && $_GET['Loan_Id'] != "") {
                                        $Loan_Id = $crypt->decode($_GET['Loan_Id']);
                                        $deleteloan = DB::getInstance()->query("delete from loan where Loan_Id='$Loan_Id'");
                                        if ($deleteloan) {
                                            echo '<div class="alert alert-success"> loan deleted successfully</div>';
                                        }
                                        Redirect::go_to("index.php?page=" . $crypt->encode("loan"));
                                    }
                                if (Input::exists() && Input::get("add_loan") == "add_loan") {
                                    $staff_id = Input::get("staff_id");
                                    $Loan_date = Input::get("Loan_date");
                                    $Loan_type = Input::get("Loan_type");
                                    $amount_paid = Input::get("amount_paid");
                                    if ($amount_paid > 0) {
                                        DB::getInstance()->insert("loan", array(
                                            "Staff_Id" => $staff_id,
                                            "Loan_Date" => $Loan_date,
                                            "Loan_Type" => $Loan_type,
                                            "AMount_Paid" => $amount_paid,
                                            "Registered_By" => $_SESSION['security_user_id']
                                        ));
                                        echo '<div class="alert alert-success">Staff loan uploaded successfully</div>';
                                    } else {
                                        echo '<div class="alert alert-danger">Could not upload loan less or equal to zero</div>';
                                    }
                                Redirect::go_to("");
                                
                                    }
                           if (Input::get("edit_loan") == "edit_loan") {
                                    $Loan_date = Input::get("Loan_date");
                                    $Loan_type = Input::get("Loan_type");
                                    $amount_paid = Input::get("amount_paid");
                                    $Loan_Id = Input::get("Loan_Id");
                                    $loanUpdate = DB::getInstance()->update("loan", $Loan_Id, array(
                                        "Loan_Date" => $Loan_date,
                                        "Loan_Type" => $Loan_type,
                                        "AMount_Paid" => $amount_paid,
                                        "Registered_By" => $_SESSION['security_user_id']
                                            ), "Loan_Id");
                                    if ($loanUpdate) {
                                        echo '<div class="alert alert-success"> loan details updated successfully</div>';
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode("loan"));
                                }
                                ?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Staff Loan Entry</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input type="date" class="form-control" name="Loan_date" value="<?php echo $date_today ?>" id="Loan_date" max="<?php echo $date_today ?>" required oninput="returnMaximumPayment();">
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
                                                    <label>Loan Type</label>
                                                    <select class="form-control" name="Loan_type" id="Loan_type" required onchange="returnMaximumPayment();">
                                                        <option value="">Choose..</option>
                                                        <option value="L/Loan">L/Loan</option>
                                                        <option value="Loan/OT">Loan/OT</option>
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
                                                    <button type="submit" name="add_loan" value="add_loan" class="btn btn-success">Save</button>
                                                    <button type="reset" class="btn">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9 col-sm-8">

                                <?php
                                $condition = "";
                                $reportName = "Loans REPORT ";
                                if (Input::exists() && Input::get("searchloan_btn") == "searchloan_btn") {
                                    $staff_id = Input::get("staff_id");
                                    $Loan_type = Input::get("Loan_type");
                                    $date_from = Input::get("date_from");
                                    $date_to = Input::get("date_to");
                                    $condition .= ($staff_id != "") ? " AND loan.Staff_Id='$staff_id' " : "";
                                    $condition .= ($Loan_type != "") ? " AND loan.Loan_Type='$Loan_type' " : "";
                                    $condition .= ($date_from != "") ? " AND substr(loan.Loan_Date,1,10)>='$date_from' " : "";
                                    $condition .= ($date_to != "") ? " AND substr(loan.Loan_Date,1,10)<='$date_to' " : "";
                                    $reportName .= ($date_from != "") ? " FROM " . strtoupper(english_date($date_from)) : "";
                                    $reportName .= ($date_to != "") ? " TO " . strtoupper(english_date($date_to)) : "";
                                }
                                $reportName = strtoupper($reportName);
                                $queryPayments = "SELECT person.*,loan.* FROM staff,person,loan WHERE loan.Staff_Id=staff.Staff_Id AND person.Person_Id=staff.Person_Id $condition ORDER BY Loan_Date";
                                if (DB::getInstance()->checkRows($queryPayments)) {
                                    $data_sent = serialize(array($queryPayments, $reportName));
                                    ?> 
                                    <div class="card card-topline-yellow">
                                        <div class="card-head">
                                            <header><?php echo $reportName ?></header>
                                            <div class="actions panel_actions pull-right hidden">
                                                <a target="_blank" href="index.php?page=<?php echo $crypt->encode("financial_report_pdf") . "&type=download_loan_pdf&data_sent=" . $crypt->encode($data_sent) ?>" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> Print pdf</a>
                                            </div>

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
                                                    <label>Loan Type</label>
                                                    <div class="controls">
                                                        <select class="form-control" name="Loan_type">
                                                            <option value="">Choose..</option>
                                                            <option value="L/Loan">L/Loan</option>
                                                            <option value="Loan/OT">Loan/OT</option>
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
                                                    <button type="submit" name="searchloan_btn" value="searchloan_btn" class="btn btn-success"><i class="fa fa-search"></i> Search </button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <table id="example1" class="table table-striped table-responsive display table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Staff Name</th>
                                                                <th>Date</th>
                                                                <th>Loan Type</th>
                                                                <th>Loan Amount</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $paymentsList = DB::getInstance()->querySample($queryPayments);
                                                            $totalPayments = 0;
                                                            foreach ($paymentsList as $loan) {
                                                                $totalPayments += $loan->Amount_Paid;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $loan->Fname . " " . $loan->Lname; ?></td>
                                                                    <td><?php echo english_date($loan->Loan_Date); ?></td>
                                                                    <td><?php echo $loan->Loan_Type ?></td>
                                                                    <td><?php echo number_format($loan->Amount_Paid); ?></td>
                                                                    <td> <a data-toggle="modal"  href="#edit_<?php echo $loan->Loan_Id ?>">
                                                                            <i class="fa fa-pencil"></i> Edit</a>&nbsp&nbsp&nbsp;
                                                                        <a href="index.php?page=<?php echo $crypt->encode('loan') . '&action=' . $crypt->encode('remove_loan') . '&Loan_Id=' . $crypt->encode($loan->Loan_Id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Do you really want to Delete this loan?');"><i class="fa fa-trash-o"></i> Delete</a> 
                                                                    </td>
                                                            <div class="modal fade" id="edit_<?php echo $loan->Loan_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">

                                                                <div class="modal-dialog animated fadeInDown">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                            <h4 class="modal-title">Edit <?php echo $loan->Fname . " " . $loan->Lname; ?>'s&nbsp;Loan</h4>
                                                                        </div>
                                                                        <form action="" method="post">
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Date</label>
                                                                                    <input type="hidden" name="Loan_Id" value="<?php echo $loan->Loan_Id ?>">

                                                                                    <input type="date" class="form-control" name="Loan_date" value="<?php echo $loan->Loan_Date ?>" id="Loan_date" max="<?php echo $date_today ?>" required oninput="returnMaximumPayment();">
                                                                                </div>

                                                                                <div class="form-group">
                                                                                    <label>Loan Type</label>
                                                                                    <select class="form-control" name="Loan_type" id="Loan_type" required >
                                                                                        <option value="">Choose..</option>
                                                                                        <option value="L/Loan" <?php echo $selected = ($loan->Loan_Type == "L/Loan") ? "selected" : ""; ?>>L/Loan</option>
                                                                                        <option value="Loan/OT" <?php echo $selected = ($loan->Loan_Type == "Loan/OT") ? "selected" : ""; ?>>Loan/OT</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Amount Paid</label>
                                                                                    <div class="controls">
                                                                                        <input type="number" min="0" class="form-control" value="<?php echo $loan->Amount_Paid; ?>" name="amount_paid" id="amount_paid" required>
                                                                                    </div>
                                                                                </div></div>
                                                                            <div class="modal-footer">
                                                                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                                <button type="submit" name="edit_loan" value="edit_loan"class="btn btn-success" type="button">Save changes</button>
                                                                            </div>

                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>TOTAL</th><th></th><th></th><th><?php echo number_format($totalPayments) ?></th>
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
                                ?></div>
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

        </script>
    </body>

</html>