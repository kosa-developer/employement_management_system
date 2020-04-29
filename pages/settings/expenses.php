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
                                    <div class="page-title">Add Expenses</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <?php
                                    //deletng the procedure
                                    if (isset($_GET['action']) && $_GET['action'] == $crypt->encode("remove_expense") && $_GET['Expenses_Id'] != "") {
                                        $Expenses_Id = $crypt->decode($_GET['Expenses_Id']);
                                        $deleteexpenses = DB::getInstance()->query("delete from expenses where Expenses_Id='$Expenses_Id'");
                                        if ($deleteexpenses) {
                                            echo '<div class="alert alert-success"> expenses deleted successfully</div>';
                                        }
                                        Redirect::go_to("index.php?page=" . $crypt->encode("expenses"));
                                    }
                                    if (Input::exists()) {
//                                    Editing procedure names
                                        if (Input::get("edit_expenses") == "edit_expenses") {
                                            $Expenses_Id = Input::get("Expenses_Id");
                                            $expense_date = (Input::get("expense_date") != "") ? Input::get("expense_date") : NULL;
                                            $expense_description = (Input::get("expense_description") != "") ? Input::get("expense_description") : NULL;
                                            $expense_amount = (Input::get("expense_amount") != "") ? Input::get("expense_amount") : NULL;
                                            $expenses_reference = (Input::get("expenses_reference") != "") ? Input::get("expenses_reference") : NULL;
                                            $branch_id = (Input::get("branch_id") != "") ? Input::get("branch_id") : NULL;
                                            $status = (Input::get("status") != "") ? Input::get("status") : NULL;
                                            $bank_id = (Input::get('bank_id') != "") ? Input::get('bank_id') : NULL;

                                            $expensesUpdate = DB::getInstance()->update("expenses", $Expenses_Id, array(
                                                "Description" => $expense_description,
                                                "Date_Submitted" => $expense_date,
                                                "Amount" => $expense_amount,
                                                "Bank_Id" => $bank_id,
                                                "Added_By" => $_SESSION['security_user_id'],
                                                "Bank_Reference" => $expenses_reference,
                                                "Status" => $status,
                                                "Branch_Id" => $branch_id
                                                    ), "Expenses_Id");
                                            if ($expensesUpdate) {
                                                echo '<div class="alert alert-success"> expenses details updated successfully</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("expenses"));
                                        }
//                                    adding the new procedure
                                        if ((Input::get("submit_expenses"))) {
                                            $expense_type = (Input::get("expense_type") != "") ? Input::get("expense_type") : NULL;
                                            $expense_date = (Input::get("expense_date") != "") ? Input::get("expense_date") : NULL;
                                            $expense_description = (Input::get("expense_description") != "") ? Input::get("expense_description") : NULL;
                                            $expense_amount = (Input::get("expense_amount") != "") ? Input::get("expense_amount") : NULL;
                                            $expenses_reference = (Input::get("expenses_reference") != "") ? Input::get("expenses_reference") : NULL;
                                            $branch_id = (Input::get("branch_id") != "") ? Input::get("branch_id") : NULL;
                                            $status = (Input::get("status") != "") ? Input::get("status") : NULL;
                                            $bank_id = (Input::get('bank_id') != "") ? Input::get('bank_id') : NULL;
                                            if (!empty($expense_date)) {
                                                $queryDup = DB::getInstance()->checkRows("SELECT * FROM expenses WHERE Description='$expense_description' AND Date_Submitted='$expense_date' ");
                                                if ($queryDup) {
                                                    $duplicates++;
                                                } else {
                                                    $expensesInsert = DB::getInstance()->insert("expenses", array(
                                                        "Description" => $expense_description,
                                                        "Date_Submitted" => $expense_date,
                                                        "Expense_Type" => $expense_type,
                                                        "Amount" => $expense_amount,
                                                        "Added_By" => $_SESSION['security_user_id'],
                                                        "Bank_Reference" => $expenses_reference,
                                                        "Status" => $status,
                                                        "Bank_Id" => $bank_id,
                                                        "Branch_Id" => $branch_id));
                                                }
                                            }
                                            if ($expensesInsert) {
                                                echo '<div class="alert alert-success col-sm-6"> expenses successfully registered</div>';
                                            } else {
                                                echo '<div class="alert alert-warning col-sm-6">Duplicates could not be registered</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("expenses"));
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body " id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#salary" data-toggle="tab">
                                                    <i class="fa fa-fire-extinguisher"></i> Salary & wages
                                                </a>
                                            </li>
                                            <li >
                                                <a href="#Firearms" data-toggle="tab">
                                                    <i class="fa fa-fire-extinguisher"></i> Firearms Rental Payments
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#Check_payments" data-toggle="tab">
                                                    <i class="fa fa-expenses"></i> Cheque Payments
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#Cash_payments" data-toggle="tab">
                                                    <i class="fa fa-money"></i> Cash Payments
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#vat_payment" data-toggle="tab">
                                                    <i class="fa fa-money"></i> VAT Payments
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#View_payments" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Expenses
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content primary">
                                            <div class="tab-pane fade in active" id="salary">
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Description</th>
                                                                            <th>Bank name</th>
                                                                            <th>Bank reference</th>
                                                                            <th>Amount<button type="button" class="btn btn-success btn-xs pull-right hidden" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="hidden" class="form-control" value="Salary" name="expense_type" > 

                                                                                <input type="date" class="form-control" name="expense_date" required> 
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control" name="expense_description" required> </textarea>
                                                                            </td>
                                                                            <td>
                                                                                <select class="select2" style="width:100%" name="bank_id" required>
                                                                                    <option value="">Choose...</option>
                                                                                    <?php echo DB::getInstance()->dropDowns("bank", "Bank_Id", "Bank_Name"); ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="expenses_reference" > 
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" class="form-control" name="expense_amount" required> 
                                                                            </td>

                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_procedure" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_expenses" value="submit_expenses">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade " id="Firearms">
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Expense Description/name</th>
                                                                            <th>Bank reference</th>
                                                                            <th>Amount<button type="button" class="btn btn-success btn-xs pull-right hidden" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="hidden" class="form-control" value="Firearms" name="expense_type" > 

                                                                                <input type="date" class="form-control" name="expense_date" required> 
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control" name="expense_description" required> </textarea>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="expenses_reference" > 
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" class="form-control" name="expense_amount" required> 
                                                                            </td>

                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_procedure" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_expenses" value="submit_expenses">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade " id="Check_payments">
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Expense Description/name</th>
                                                                            <th>Bank reference</th>
                                                                            <th>Amount</th>
                                                                            <th>Status<button type="button" class="btn btn-success btn-xs pull-right hidden" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="hidden" class="form-control" value="Cheque Payment" name="expense_type" > 

                                                                                <input type="date" class="form-control" name="expense_date" required> 
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control" name="expense_description" required> </textarea>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="expenses_reference" > 
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" class="form-control" name="expense_amount" required> 
                                                                            </td>
                                                                            <td>                   
                                                                                <div class="form-group">
                                                                                    <label > <input type="radio" value="Paid" name="status" > Paid</label>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label > <input type="radio" value="Not Paid" name="status" > Not Paid</label>
                                                                                </div>
                                                                            </td>

                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_procedure" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_expenses" value="submit_expenses">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                            
                                            <div class="tab-pane fade" id="Cash_payments">
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Expense Description/name</th>
                                                                            <th>Branch Name</th>
                                                                            <th>Amount<button type="button" class="btn btn-success btn-xs pull-right hidden" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="hidden" class="form-control" value="Cash Payment" name="expense_type" > 

                                                                                <input type="date" class="form-control" name="expense_date" required> 
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control" name="expense_description" required> </textarea>
                                                                            </td>
                                                                            <td>
                                                                                <select class="select2" style="width:100%" name="branch_id" required>
                                                                                    <option value="">Choose...</option>
                                                                                    <?php echo DB::getInstance()->dropDowns("branch", "Branch_Id", "Branch_Name"); ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" class="form-control" name="expense_amount" required> 
                                                                            </td>


                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_procedure" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_expenses" value="submit_expenses">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
<div class="tab-pane fade" id="vat_payment">
                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Amount<button type="button" class="btn btn-success btn-xs pull-right hidden" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="hidden" class="form-control" value="VAT" name="expense_type" > 

                                                                                <input type="date" class="form-control" name="expense_date" required> 
                                                                            </td>
                                                                           
                                                                            
                                                                            <td>
                                                                                <input type="number" class="form-control" name="expense_amount" required> 
                                                                            </td>


                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_procedure" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit_expenses" value="submit_expenses">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>

                                            <div class="tab-pane fade" id="View_payments">
                                                <?php
                                                $queryexpenses = "SELECT * FROM expenses  ORDER BY Expenses_Id DESC";
                                                if (DB::getInstance()->checkRows($queryexpenses)) {
                                                    ?>                                                    
                                                    <table id="example1" class="table table-striped table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Date</th>
                                                                <th>Expenses Type</th>
                                                                <th>Expenses Name</th>
                                                                 <th>Bankm Name</th>
                                                                <th>Amount</th>
                                                                <th>Bank refference/Bank Account</th>
                                                                <th>Branch Name</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $expenses_list = DB::getInstance()->querySample($queryexpenses);
                                                            $no = 0;
                                                            foreach ($expenses_list as $expenses) {
                                                                $no++;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $no ?></td>
                                                                    <td><?php echo english_date(substr($expenses->Date_Submitted, 0, 10)); ?></td>
                                                                    <td><?php echo $expenses->Expense_Type; ?></td>
                                                                    <td><?php echo $expenses->Description; ?></td>
                                                                    <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM bank WHERE Bank_Id='$expenses->Bank_Id'", "Bank_Name"); ?></td>
                                                                    <td><?php echo number_format($expenses->Amount); ?></td>
                                                                    <td><?php echo $expenses->Bank_Reference; ?></td>
                                                                    <td><?php echo DB::getInstance()->DisplayTableColumnValue("SELECT * FROM branch WHERE Branch_Id='$expenses->Branch_Id'", "Branch_Name"); ?></td>
                                                                    <td><?php echo $expenses->Status; ?></td>
                                                                    <td> 
                                                                        <a data-toggle="modal"  href="#edit_<?php echo $expenses->Expenses_Id ?>">
                                                                            <i class="fa fa-pencil"></i> Edit</a>&nbsp&nbsp&nbsp;
                                                                        <a href="index.php?page=<?php echo $crypt->encode('expenses') . '&action=' . $crypt->encode('remove_expense') . '&Expenses_Id=' . $crypt->encode($expenses->Expenses_Id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Do you really want to Delete this expenses?');"><i class="fa fa-trash-o"></i> Delete</a> 
                                                                    </td>
                                                            <div class="modal fade" id="edit_<?php echo $expenses->Expenses_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">

                                                                <div class="modal-dialog animated fadeInDown">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                            <h4 class="modal-title">Edit <?php echo $expenses->Description; ?>'s&nbsp;Information</h4>
                                                                        </div>
                                                                        <form action="" method="post">
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Expense Date</label>
                                                                                    <input type="date" class="form-control" name="expense_date" value="<?php echo substr($expenses->Date_Submitted, 0, 10) ?>">
                                                                                </div>
                                                                                 <?php
                                                                                $branch_hidden = ($expenses->Expense_Type == "Cash Payment") ? "" : "hidden";
                                                                                $bankre_hidden = ($expenses->Expense_Type == "Cheque Payment" || $expenses->Expense_Type == "Firearms") ? "" : "hidden";
                                                                                $status_hidden = ($expenses->Expense_Type == "Cheque Payment") ? "" : "hidden";
                                                                                $bank_hidden=($expenses->Expense_Type=="Salary")?"":"hidden";
                                                                               $discription_hidden=($expenses->Expense_Type=="VAT")?"hidden":"";
                                                                               ?>
                                                                                <div class="form-group <?php echo $discription_hidden;?>">
                                                                                    <label>Expense Name/Description</label>
                                                                                    <input type="hidden" name="Expenses_Id" value="<?php echo $expenses->Expenses_Id ?>">
                                                                                    <textarea class="form-control" name="expense_description" ><?php echo $expenses->Description ?> </textarea>
                                                                                </div>
                                                                              

                                                                                <div class="form-group">
                                                                                    <label>Amount</label>
                                                                                    <input type="number" class="form-control" name="expense_amount" value="<?php echo $expenses->Amount ?>">
                                                                                </div>
                                                                               

                                                                                <div class="form-group <?php echo $bankre_hidden ?>">
                                                                                    <label>Bank reference</label>
                                                                                    <input type="text" class="form-control" name="expenses_reference" value="<?php echo $expenses->Bank_Reference ?>">
                                                                                </div>
                                                                                <div class="form-group <?php echo $branch_hidden ?>">
                                                                                    <label>Branch Name</label>
                                                                                    <select class="select2" style="width:100%" name="branch_id" >
                                                                                        <option value="">Choose...</option>
                                                                                        <?php
                                                                                        $branchCheck = "SELECT * FROM branch  ORDER BY Branch_Name";
                                                                                        $branch_list = DB::getInstance()->query($branchCheck);
                                                                                        foreach ($branch_list->results() as $branch):
                                                                                            $selected = ($branch->Branch_Id == $expenses->Branch_Id) ? "selected" : "";
                                                                                            echo '<option value="' . $branch->Branch_Id . '"' . $selected . '>' . $branch->Branch_Name . '</option>';
                                                                                        endforeach;
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                                
                                                                                  <div class="form-group <?php echo $bank_hidden;?>">
                                                                            <label class="form-label ">Banks</label>
                                                                            
                                                                            <select class="select2" style="width:100%" name="bank_id" >
                                                                                <option value="">Choose...</option>
                                                                                <?php
                                                                                $bankCheck = "SELECT * FROM bank  ORDER BY Bank_Name";
                                                                                $bank_list = DB::getInstance()->query($bankCheck);
                                                                                foreach ($bank_list->results() as $bank):
                                                                                    $selected = ($bank->Bank_Id == $expenses->Bank_Id) ? "selected" : "";
                                                                                    echo '<option value="' . $bank->Bank_Id . '"'.$selected.'>' . $bank->Bank_Name . '</option>';
                                                                                endforeach;
                                                                                ?>
                                                                            </select>
                                                                        </div>

                                                                                <div class="form-group <?php echo $status_hidden ?>">

                                                                                    <label>Status</label>
                                                                                    <div class="form-group">
                                                                                        <?php
                                                                                        $checked1 = ($expenses->Status == 'Paid') ? "checked" : "";
                                                                                        $checked2 = ($expenses->Status == 'Not Paid') ? "checked" : "";
                                                                                        ?>
                                                                                        <label > <input   type="radio" <?php echo $checked1; ?> value="Paid" name="status" > Paid</label>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label > <input type="radio"   <?php echo $checked2; ?> value="Not Paid" name="status" >Not Paid</label>
                                                                                    </div> 
                                                                                </div>

                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                                <button type="submit" name="edit_expenses" value="edit_expenses"class="btn btn-success" type="button">Save changes</button>
                                                                            </div>

                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>


                                                    <?php
                                                } else {
                                                    echo '<div class="alert alert-warning">No expenses registered</div>';
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

    </body>

</html>