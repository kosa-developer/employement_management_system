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
                                <div class="title page-title">Expenditure Report</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Search</header>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" action="" class="form-inline">

                                            <div class="form-group">
                                                <label>Year</label>
                                                <div class="controls">
                                                    <select class="form-control" name="year" required>
                                                        <option value=""  >Choose...</option>
                                                        <?php
                                                        $year = date('Y');
                                                        $month = date('m');
                                                        for ($i = 2013; $i <= date('Y'); $i++) {
                                                            $selected = ($i == $year) ? "selected" : "";
                                                            ?>
                                                            <option value="<?php echo $i ?>" <?php echo $selected ?> ><?php echo $i ?></option>
                                                        <?php } ?></select></div>
                                            </div>
                                            <div class="form-group">
                                                <label>Months</label> 
                                                <div class="controls">
                                                    <select class="form-control" name="month" required>
                                                        <option value="">Select Month</option>
                                                        <?php
                                                        for ($i = 1; $i <= 12; $i++) {
                                                            $month_ = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                            $selected = ($i == $month) ? " selected" : "";
                                                            echo '<option value="' . $month_ . '" ' . $selected . '>' . $month_ . '</option>';
                                                        }
                                                        ?>
                                                    </select></div>
                                            </div>
                                            <div class="form-group"><br/>
                                                <button type="submit" name="search_date_btn" value="search_date_btn" class="btn btn-success"><i class="fa fa-search"></i> Search </button>
                                            </div>
                                        </form>
                                    </div>
                                </div><?php
                                $current_month_and_year = date("Y-m");
                                $month_name = english_months($month);
                                $header = $month_name . " " . $year_name . " Expenditure";
                                $year_name = $year;
                                $condition = "";
                                $heading = "MILLENIUM SECURITY LIMITED";
                                $reportName = "SALARY & OTHER REQUIREMENTS FOR " . $month_name . " " . $year_name;
                                if (Input::exists() && Input::get("search_date_btn") == "search_date_btn") {
                                    $month = Input::get("month");
                                    $year = Input::get("year");
                                    $month_name = english_months($month);
                                    $year_name = date($year);
                                    $current_month_and_year = $year . '-' . $month;
                                    $reportName = " SALARY & OTHER REQUIREMENTS FOR  " . $month_name . " " . $year_name . "  " . DB::getInstance()->DisplayTableColumnValue("SELECT * FROM bank WHERE Bank_Id='$bank_id'", "Bank_Name");
                                    $header = $month_name . " " . $year_name . " Expenditure";
                                }

                                $reportName = strtoupper($reportName);
                                 $data_sent = serialize(array($reportName, $current_month_and_year,$month_name,$year_name));
                                   
                                ?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header><?php echo $heading . " " . $reportName ?></header>
                                        <div class="actions panel_actions pull-right">
                                            <a href="index.php?page=<?php echo $crypt->encode("excel_download") . "&download_type=download_expenditure_report&data_sent=" . $crypt->encode($data_sent) ?>" class="btn btn-primary btn-xs"><i class="fa fa-print"></i> download excel</a>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-head">
                                                <header style="color:blue">(1) BANK W/D & A/C PAYEE ONLY:</header>
                                            </div>


                                            <table id="example1" class="table table-striped table-responsive display table-bordered" cellspacing="1" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>EXPENSE</th>
                                                        <th>AMOUNT</th>
                                                        <th>ACCOUNT/NO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $total_salary = 0;
                                                    $no = 0;
                                                    $nadd = 1;
                                                    $banksquery = "SELECT * FROM bank,expenses WHERE bank.Bank_Id=expenses.Bank_Id group by bank.Bank_Id";
                                                    if (DB::getInstance()->checkRows($banksquery)) {

                                                        $bankList = DB::getInstance()->querySample($banksquery);

                                                        foreach ($bankList as $bank) {
                                                            $nadd += $no;
                                                            $no++;
                                                            $salary = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE Bank_Id='$bank->Bank_Id' AND Expense_Type='Salary' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
                                                            $total_salary += $salary;
                                                            ?>

                                                            <tr>
                                                                <td><?php echo $no; ?></td>
                                                                <td>Salary & wages <?php echo $bank->Bank_Name; ?></td> 
                                                                <td><?php echo number_format($salary); ?></td> 
                                                                <td style="color: blue;"><?php echo DB::getInstance()->displayTableColumnValue("SELECT * FROM expenses WHERE Bank_Id='$bank->Bank_Id' AND Expense_Type='Salary' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ORDER BY Expenses_Id LIMIT 1", "Bank_Reference"); ?></td>
                                                            </tr> 

                                                            <?php
                                                        }
                                                    }

                                                    $total_expense = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE   substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");

                                                    $firearms = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE  Expense_Type='Firearms' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
                                                    $tcash = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE  Expense_Type='Cash Payment' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
                                                    $tcheque = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE  Expense_Type='Cheque Payment' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
                                                    $tvat = DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE  Expense_Type='VAT' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
                                                   
                                                    $overalltotal_expense=DB::getInstance()->calculateSum("SELECT * FROM expenses WHERE   substr(Date_Submitted,1,7)='$current_month_and_year' ", "Amount");
                                                   
                                                            ?>
                                                    <tr>
                                                        <td><?php echo $nadd + 1; ?></td> 
                                                        <td>Firearms rental </td> 
                                                        <td><?php echo number_format($firearms); ?></td> 
                                                        <td style="color: blue;"><?php echo DB::getInstance()->displayTableColumnValue("SELECT * FROM expenses WHERE  Expense_Type='Firearms' AND  substr(Date_Submitted,1,7)='$current_month_and_year' ORDER BY Expenses_Id LIMIT 1", "Bank_Reference"); ?></td>
                                                    </tr> 

                                                    <tr>
                                                        <td><?php echo $nadd + 2; ?></td> 
                                                        <td>General requirements in cash </td> 
                                                        <td><?php echo number_format($tcash); ?></td> 
                                                        <td style="color: blue;"></td>
                                                    </tr> 

                                                    <tr>
                                                        <td><?php echo $nadd + 3; ?></td> 
                                                        <td>Total chqs.out </td> 
                                                        <td><?php echo number_format($tcheque); ?></td> 
                                                        <td style="color: blue;"></td>
                                                    </tr>
                                                </tbody> 
                                                <tfoot>
                                                <th></th>
                                                <th>TOTAL</th>
                                                <th style="color:#FF00FF"><?php echo number_format($total_expense); ?></th>
                                                <th style="color:#FF00FF"><?php echo number_format($total_expense); ?></th> 
                                                </tfoot>                                            
                                            </table>
                                            <?php
                                            $chequequery = "SELECT * FROM expenses WHERE  Expense_Type='Cheque Payment' AND  substr(Date_Submitted,1,7)='$current_month_and_year'";
                                            if (DB::getInstance()->checkRows($chequequery)) {
                                                ?>
                                                <div class="card-head">
                                                    <header style="color:blue">(2) GENERAL DETAILED CHEQUE PAYMENTS:</header>
                                                </div>
                                                <div class="col-md-12 col-sm-12">
                                                    <table id="example1" class="table table-striped table-responsive display table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>EXPENSE NAME</th>
                                                                <th>AMOUNT</th>
                                                                <th>ACCOUNT/NO</th>
                                                                <th>STATUS</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $chequeList = DB::getInstance()->querySample($chequequery);
                                                            $chequeno = 1;
                                                            $total_quque_amount = 0;
                                                            foreach ($chequeList as $cheque) {
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $chequeno; ?></td>
                                                                    <td><?php echo $cheque->Description; ?></td>
                                                                    <td><?php echo number_format($cheque->Amount); ?></td>
                                                                    <td style="color:blue"><?php echo $cheque->Bank_Reference; ?></td>
                                                                    <td style="color:#FF00FF"><?php echo $cheque->Status; ?></td>
                                                                </tr>
                                                                <?php
                                                                $total_quque_amount += $cheque->Amount;
                                                                $chequeno++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                        <th></th>
                                                        <th>TOTAL</th>
                                                        <th style="color:#00000"><?php echo number_format($total_quque_amount); ?></th>
                                                        <th style="color:#FF6600"><?php echo number_format($total_quque_amount); ?></th> 
                                                        <th></th> 

                                                        </tfoot> 
                                                    </table>
                                                </div>


                                                <?php
                                            }
                                            $branchquery= "SELECT * FROM branch,expenses where expenses.Branch_Id=branch.Branch_Id group by branch.Branch_Id";
                                            if (DB::getInstance()->checkRows($branchquery)) {
                                                ?>
                                                <div class="card-head">
                                                    <header style="color:blue">(3) DETAILED CASH PAYMENTS:</header>
                                                </div>
                                            <div class="col-md-12 col-sm-12">
                                                 <?php
                                                        $branchList = DB::getInstance()->querySample($branchquery);
                                                        foreach ($branchList as $branch) {
                                                           
                                                             $chash_list=DB::getInstance()->querySample("SELECT * FROM expenses WHERE Branch_Id='$branch->Branch_Id' AND Expense_Type='Cash Payment' AND  substr(Date_Submitted,1,7)='$current_month_and_year'");
                                                            ?>
                                                 <div class="card-head">
                                                    <header ><?php echo $branch->Branch_Name?></header>
                                                </div>
                                                 <table id="example1" class="table table-striped table-responsive display table-bordered" cellspacing="1" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>EXPENSE NAME</th>
                                                            <th>AMOUNT</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                       <?php 
                                                        $totalcashobtained=0;
                                                        $k=1;
                                                       foreach ($chash_list as $cash) {?>
                                                            <tr>
                                                                <td><?php echo $k; ?></td>
                                                                <td><?php echo $cash->Description;?></td>
                                                                <td><?php echo number_format($cash->Amount)?></td>
                                                                <td></td>
                                                            </tr>
                                                            <?php 
                                                            $k++;
                                                            $totalcashobtained+=$cash->Amount;
                                                            
                                                       }?>
                                                          
                                                    </tbody>
                                                    <tfoot>
                                                    <th></th>
                                                    <th>TOTAL</th>
                                                    <th style="color:blue"><?php echo number_format($totalcashobtained);?></th>
                                                    <th style="color:#FF0000"><?php echo number_format($totalcashobtained);?></th>
                                                   
                                                    </tfoot> 
                                                </table>
                                                  <?php
                                                           
                                                        }
                                                        ?>
                                                <table id="example1" class="table table-responsive display table-bordered" cellspacing="1" width="100%">
                                                    <thead>
                                                    <tr>
                                                    <th></th>
                                                    <th>DETAILS TOTAL BREAKDOWN (CASH)</th>
                                                    <th ></th>
                                                    <th style="color:#FF0000"><?php echo number_format($tcash);?></th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                                <table id="example1" class="table table-responsive display table-bordered" cellspacing="1" width="100%">
                                                    <thead>
                                                    <tr>
                                                    <th></th>
                                                    <th>VAT FOR <?php echo $month_name . " " . $year_name; ?></th>
                                                    <th ></th>
                                                    <th style="color:#FF0000"><?php echo number_format($tvat);?></th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                                
                                                 <table id="example1" class="table table-responsive display table-bordered" cellspacing="1" width="100%">
                                                    <thead>
                                                    <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th ></th>
                                                    
                                                    <th ></th>
                                                    <th >TOTAL</th>
                                                    <th style="color:#FF0000"><?php echo number_format($overalltotal_expense);?></th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                               
                                            <?php } ?>

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