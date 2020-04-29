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
                                    <div class="page-title">Income statement</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-topline-yellow">
                                    <div class="card-body ">
                                        <div class="content-body">    
                                            <div class="row">
                                                <form method="POST" action="index.php?page=<?php echo $crypt->encode("view_income_statement"); ?>" class="form-inline">
                                                    <div class="form-group">
                                                        <label>Report Type</label>
                                                        <div class="controls">
                                                            <label><input type="radio" value="All" name="report_type" checked required onchange="returnReportType(this.value);">All</label>
                                                            <label><input type="radio" value="Annually" name="report_type" required onchange="returnReportType(this.value);">Annually</label>
                                                        </div>
                                                    </div>
                                                    <div id="date_from_div" class="form-group hidden">
                                                        <label>Date From</label>
                                                        <div class="controls">
                                                            <input id="date_from_txt" type="date" class="form-control" name="date_from" max="<?php echo date("Y-m-d") ?>">
                                                        </div>
                                                    </div>
                                                    <div id="date_to_div" class="form-group hidden">
                                                        <label>To</label>
                                                        <div class="controls">
                                                            <input id="date_to_txt" type="date" class="form-control" name="date_to" max="<?php echo date("Y-m-d") ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group hidden" id="year_div">
                                                        <label>Year</label>
                                                        <div class="controls">
                                                            <select id="year_txt" class="form-control" name="year">
                                                                <option value="">Choose</option>
                                                                <?php
                                                                for ($x = 2014; $x <= date("Y"); $x++) {
                                                                    echo '<option value="' . $x . '">' . $x . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group"><br/>
                                                        <button type="submit" name="search" value="search" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-topline-yellow">
                                    <?php
                                    $headingTitle = "Statement of comprehensive income and other incomes for year ended ";
                                    $patientPaymentsCondition="";
                                    $incomeCondition = "";
                                    $feesCondition = "";
                                    $purchasesCondition = "";
                                    $expensesCondition = "";
                                    $depreciationCondition = "";
                                    $stockingYear = date("Y");
                                    if (Input::exists() && Input::get("search") == "search") {
                                        $report_type = Input::get("report_type");
                                        $year = Input::get("year");
                                        $year = ($year != "") ? $year : $stockingYear;
                                        $stockingYear = ($year != "") ? $year : $stockingYear;
                                        $patientPaymentsCondition .= ($year != "") ? " AND substr(payments.Time,1,4)='$year' " : "";
                                        $incomeCondition .= ($year != "") ? " AND substr(income.Date_Time,1,4)='$year' " : "";

                                        $headingTitle .= ($year != "") ? " IN " . $year : "";
                                        $headingTitle .= ($date_from != "") ? " FROM " . english_date($date_from) : "";
                                        $headingTitle .= ($date_to != "") ? " TO " . english_date($date_to) : "";
                                    } else {
                                        $headingTitle = "Statement of comprehensive incomes and other income for year ended " . $stockingYear;
                                    }
                                    $purchasesCondition .= " AND substr(purchases.Date_Time,1,4)='$stockingYear' ";
                                    $expensesCondition .= " AND substr(expenses.Date_Submitted,1,4)='$stockingYear' ";
                                    $headingTitle = strtoupper($headingTitle);
                                    $patientPaymentsQuery="SELECT Amount_Paid FROM payments WHERE Payment_Id IS NOT NULL $patientPaymentsCondition";
                                    $incomeQuery = "SELECT * FROM income,source_items WHERE income.Item_Id=source_items.Item_Id $incomeCondition GROUP BY source_items.Item_Id ORDER BY Income_Id desc";
                                    $purchasesQuery = "SELECT (Qty*Price) AS Price FROM purchases,source_items WHERE purchases.Item_Id=source_items.Item_Id $purchasesCondition";
                                    $expensesQuery = "select * from expenses,source_items WHERE expenses.Item_Id=source_items.Item_Id $expensesCondition  GROUP BY source_items.Item_Id";
                                    $operatingExpensesQuery = "select * from expenses,source_items WHERE expenses.Item_Id=source_items.Item_Id AND expenses.Expense_Type='Operating Expenses' $expensesCondition  GROUP BY source_items.Item_Id";
                                    //$depreciationQuery = "SELECT * FROM asset_and_liability,source_items,depreciation_rate WHERE depreciation_rate.Asset_And_Liability_Id=asset_and_liability.Asset_And_Liability_Id AND source_items.Item_Id=asset_and_liability.Item_Id AND asset_and_liability.Type='Fixed Asset' $depreciationCondition";
                                    $depreciationQuery = "SELECT * FROM asset,stock_assets WHERE stock_assets.Asset_Id=asset.Asset_Id $depreciationCondition";
                                    if (DB::getInstance()->checkRows($incomeQuery) ||DB::getInstance()->checkRows($patientPaymentsQuery)|| DB::getInstance()->checkRows($expensesQuery)) {
                                        $totalSales = 0;
                                        $return_inwards = 0;
                                        $totalSales += $patient_payments = DB::getInstance()->calculateSum($patientPaymentsQuery,"Amount_Paid");
                                        $openingStock = 0;
                                        $closingStock = 0;
                                        $purchasesAmount = 0;//Needs to be defined from registration

                                        $return_outwards = 0;//Get all the return outwards
                                        $cost_of_sales = $openingStock + $purchasesAmount;
                                        $gross_profit = $totalSales - ($return_inwards + $cost_of_sales - ($return_outwards + $closingStock));
                                        $bad_debts = 0;//Needs to handle it
                                        $wages_and_salaries = DB::getInstance()->calculateSum("SELECT Amount_Paid FROM staff_payments WHERE Staff_Id IS NOT NULL AND SUBSTR(Payment_Date,1,4)='$stockingYear'", "Amount_Paid");
                                        $array_sent = $crypt->encode(serialize(array($incomeQuery, $patient_payments, $bad_debts, $purchasesAmount, $operatingExpensesQuery
                                            , $openingStock, $closingStock, $return_outwards, $cost_of_sales, $gross_profit, $wages_and_salaries,
                                            $headingTitle, $incomeCondition, $expensesCondition, $depreciationQuery)));
                                        ?>

                                        <div class="card-head">
                                            <header><?php echo $headingTitle ?></header>
                                            <div class="actions panel_actions pull-right">
                                                <form target="_blank" action="index.php?page=<?php echo $crypt->encode("financial_report_pdf") . "&type=" . $crypt->encode("download_income_statement"); ?>" method="POST">
                                                    <input type="hidden" name="data_sent" value="<?php echo $array_sent ?>">
                                                    <button class="btn btn-info btn-xs"><i class="fa fa-download"></i> Download pdf</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="card-body ">
                                            <table class="table table-bordered" >
                                                <thead>
                                                    <tr>
                                                        <th>Particular (Details/ Items)</th>
                                                        <th>Amount</th>
                                                        <th>Amount</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Sales</td>
                                                        <td></td>
                                                        <td><?php echo number_format($totalSales) ?></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Less return inwards</td>
                                                        <td></td>
                                                        <td><?php echo $return_inwards; ?></td>
                                                        <td><?php echo ($totalSales - $return_inwards < 0) ? "(" . number_format(abs($totalSales - $return_inwards)) . ")" : number_format($totalSales - $return_inwards) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Cost of Sales</th>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Opening Stock</td>
                                                        <td><?php echo number_format($openingStock) ?></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Purchases</td>
                                                        <td><?php echo number_format($purchasesAmount) ?></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Less return outwards</td>
                                                        <td><?php echo $return_outwards ?></td>
                                                        <td><?php echo number_format($cost_of_sales - $return_outwards) ?></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Less Closing stock</td>
                                                        <td></td>
                                                        <td><?php echo number_format($closingStock) ?></td>
                                                        <td><?php echo ($cost_of_sales - ($return_inwards + $closingStock) < 0) ? "(" . number_format(abs($cost_of_sales - ($return_inwards + $closingStock))) . ")" : number_format($cost_of_sales - ($return_inwards + $closingStock)) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Gross profit</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th><?php echo ($gross_profit < 0) ? "(" . number_format(abs($gross_profit)) . ")" : number_format($gross_profit) ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th>Add other incomes</th>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <?php
                                                    $totalIncomes = 0;
                                                    $incomeList = DB::getInstance()->query($incomeQuery);
                                                    $x = 0;
                                                    foreach ($incomeList->results() as $income_list) {
                                                        $totalIncomes += $incomeAmount = DB::getInstance()->calculateSum("SELECT Amount FROM income WHERE Item_Id='$income_list->Item_Id' $incomeCondition", "Amount");
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $income_list->Item_Name ?></td>
                                                            <td></td>
                                                            <td><?php echo number_format($incomeAmount) ?></td>
                                                            <td><?php echo ($x == count($incomeList)) ? number_format($totalIncomes) : "" ?></td>
                                                        </tr>
                                                        <?php
                                                        $x++;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <th>Gross Income</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th><?php echo ($gross_profit + $totalIncomes < 0) ? "(" . number_format(abs($gross_profit + $totalIncomes)) . ")" : number_format($gross_profit + $totalIncomes); ?></th>
                                                    </tr>
                                                    <tr>
                                                        <th>Less Operating Expenses</th>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Bad debts</td>
                                                        <td></td>
                                                        <td><?php echo $bad_debts ?></td>
                                                        <td></td>
                                                    </tr>
                                                    <?php
                                                    $operatingExpensesList = DB::getInstance()->querySample($operatingExpensesQuery);
                                                    foreach ($operatingExpensesList as $expenses_list) {
                                                        $totalOperatingExpenses += $expensesAmount = DB::getInstance()->calculateSum("SELECT Amount FROM expenses WHERE Item_Id='$expenses_list->Item_Id' AND Expense_Type='Operating Expenses' $expensesCondition", "Amount");
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $expenses_list->Item_Name ?></td>
                                                            <td><?php echo number_format($expensesAmount) ?></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>Wages and salaries</td>
                                                        <td><?php echo number_format($wages_and_salaries) ?></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Depreciation</th>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <?php
                                                    $totalDepreciation = 0;

                                                    $depreciationList = DB::getInstance()->querySample($depreciationQuery);
                                                    $x = 0;
                                                    foreach ($depreciationList AS $deplist) {
                                                        $unit_price=0+$deplist->Unit_Price;
                                                        $x++;
                                                        $expiry_date = ($deplist->Is_Removed == 1) ? $deplist->Removed_On : $date_today;
                                                        $yearsEllapsed = round(calculateDateDifference($deplist->Date_Received, $expiry_date, "years"),2);
                                                        $depreciation = round(($deplist->Depreciation_Rate * $unit_price * $yearsEllapsed) / 100,2);
                                                        $depreciation_display = ($depreciation > $unit_price) ? "Depreciation Exceeded " . $unit_price : "(" . ($deplist->Depreciation_Rate . "/100)" . "x" . $unit_price) . "x" . $yearsEllapsed . "=" . $depreciation;
                                                        $totalDepreciation += $depreciation = ($depreciation > $unit_price) ? $unit_price : $depreciation;
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $deplist->Asset_Name . "(" . english_date($deplist->Date_Received). ")" ?></td>
                                                            <td><?php echo $depreciation_display ?></td>
                                                            <td><?php echo number_format($depreciation) ?></td>
                                                            <th><?php echo ($x == count($depreciationList)) ? number_format($totalDepreciation) : "" ?></th>
                                                        </tr>
                                                        <?php
                                                    }
                                                    $totalOperatingExpenses += $bad_debts + $wages_and_salaries + $totalDepreciation;
                                                    ?>

                                                    <tr>
                                                        <td>Total expenses</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td><?php echo number_format($totalOperatingExpenses) ?></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Net profit</th>
                                                        <th></th>
                                                        <th></th>
                                                        <th><?php echo ($gross_profit + $totalIncomes - $totalOperatingExpenses < 0) ? "(" . number_format(abs($gross_profit + $totalIncomes - $totalOperatingExpenses)) . ")" : number_format($gross_profit + $totalIncomes - $totalOperatingExpenses) ?></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <?php
                                    } else {
                                        echo '<div class="alert alert-danger">NO ' . $headingTitle . '</div>';
                                    }
                                    ?>
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
        <script type="text/javascript">
                                                    function returnReportType(value) {
                                                        document.getElementById("date_from_txt").value = "";
                                                        document.getElementById("date_to_txt").value = "";
                                                        document.getElementById("year_txt").value = "";
                                                        if (value === "Annually") {
                                                            $('#year_div').attr({"class": "form-group"});
                                                            $('#date_from_div').attr({"class": "form-group hidden"});
                                                            $('#date_to_div').attr({"class": "form-group hidden"});

                                                            $('#year_txt').attr({"required": true});
                                                            $('#date_from_txt').attr({"required": false});
                                                            $('#date_to_txt').attr({"required": false});
                                                        } else if (value === "All") {
                                                            $('#year_div').attr({"class": "form-group hidden"});
                                                            $('#date_from_div').attr({"class": "form-group hidden"});
                                                            $('#date_to_div').attr({"class": "form-group hidden"});

                                                            $('#year_txt').attr({"required": false});
                                                            $('#date_from_txt').attr({"required": false});
                                                            $('#date_to_txt').attr({"required": false});
                                                        }
                                                    }
        </script>

        <!-- end js include path -->
    </body>

</html>