<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once 'includes/header_css.php'; ?>
    </head>
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
                                    <div class="page-title">Dashboard</div>
                                </div>
                            </div>
                        </div>
                        <!-- start widget -->
                        <div class="row">
                            <div class="card-body">
                                  </div>
                           
                            <div class="state-overview hidden" style="font-size:12px">
                               
                            </div>
                        </div>
                        <!-- end widget -->
                        <!-- chart start -->
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="borderBox light bordered">
                                   
                                    <div class="borderBox-title tabbable-line hidden">
                                        <div class="caption">
                                            <span class="caption-subject font-dark bold uppercase">Graphical Reports</span>
                                        </div>
                                        <ul class="nav nav-tabs">
                                            <li class="<?php echo $annual_patients_tab_class ?>">
                                                <a href="#borderBox_annaul_patients_graph" data-toggle="tab"> Annual Patients report (<?php echo $year ?>)</a>
                                            </li>
                                            <li class="<?php echo $monthly_patients_tab_class ?>">
                                                <a href="#borderBox_monthly_patients_graph" data-toggle="tab">Monthly Patients Report (<?php echo $month."/".$year ?>)</a>
                                            </li>
                                            <li class="<?php echo $annual_death_tab_active ?>">
                                                <a href="#borderBox_annaul_death_graph" data-toggle="tab"> Annual Death report (<?php echo $year ?>)</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="borderBox-body">
                                        <div class="tab-content">
                                            <div class="tab-pane <?php echo $annual_dead_patients_tab_class ?>" id="borderBox_annaul_death_graph">
                                                <div class="card-head">
                                                    <form action="index.php?page=<?php echo $_GET['page'] . "&tab=" . $crypt->encode("annual_death") ?>" method="POST" class="col-md-3" id="patientsDeathGraphSearchForm">
                                                        <select class="form-control" name="searchDeadPatientYear" onchange="ReloadPage('patientsDeathGraphSearchForm');">
                                                            <option value="">Select Year</option>
                                                            <?php
                                                            $distinctYears = DB::getInstance()->querySample("SELECT SUBSTR(Date_Of_VISIT,1,4) AS Year_Of_Admission FROM patient GROUP BY Year_Of_Admission ORDER BY Year_Of_Admission");
                                                            foreach ($distinctYears AS $years) {
                                                                $selected = ($years->Year_Of_Admission == $year) ? " selected" : "";
                                                                echo '<option value="' . $years->Year_Of_Admission . '" ' . $selected . '>' . $years->Year_Of_Admission . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </form>
                                                    <header>ANNUAL DEATHS FOR THE YEAR <?php echo $year ?></header>
                                                </div>
                                                <div class="card-body no-padding height-9">
                                                    <div class="row">
                                                        <canvas id="death_report_graph"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane <?php echo $annual_patients_tab_class ?>" id="borderBox_annaul_patients_graph">
                                                <div class="card-head">
                                                    <form action="index.php?page=<?php echo $_GET['page'] ?>" method="POST" class="col-md-3" id="patientsMonthlyGraphSearchForm">
                                                        <select class="form-control" name="searchActivePatientYear" onchange="ReloadPage('patientsMonthlyGraphSearchForm');">
                                                            <option value="">Select Year</option>
                                                            <?php
                                                            $distinctYears = DB::getInstance()->querySample("SELECT SUBSTR(Date_Of_VISIT,1,4) AS Year_Of_Admission FROM patient GROUP BY Year_Of_Admission ORDER BY Year_Of_Admission");
                                                            foreach ($distinctYears AS $years) {
                                                                $selected = ($years->Year_Of_Admission == $year) ? " selected" : "";
                                                                echo '<option value="' . $years->Year_Of_Admission . '" ' . $selected . '>' . $years->Year_Of_Admission . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </form>
                                                    <header>PATIENTS MONTHLY GRAPH FOR THE YEAR <?php echo $year ?></header>
                                                </div>
                                                <div class="card-body no-padding height-9">
                                                    <div class="row">
                                                        <canvas id="monthly_graph_report"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane <?php echo $monthly_patients_tab_class ?>" id="borderBox_monthly_patients_graph">
                                                <div class="card-head">
                                                    <?php
                                                    $day_to = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                                                    $daily_patients_array = array();
                                                    for ($i = 1; $i <= $day_to; $i++) {
                                                        $date = $year . "-" . $month . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);
                                                        $totalPatients = DB::getInstance()->countElements("SELECT Patient_Id FROM patient WHERE SUBSTR(Date_Of_Visit,1,10)='$date'");
                                                        $daily_patients_array[] = [
                                                            "label" => str_pad($i, 2, "0", STR_PAD_LEFT),
                                                            "total" => $totalPatients
                                                        ];
                                                    }
                                                    ?>
                                                    <form action="index.php?page=<?php echo $_GET['page'] . "&tab=" . $crypt->encode("monthly_patients") ?>" method="POST">
                                                        <div class="col-md-2">
                                                            <select class="form-control" name="month" required>
                                                                <option value="">Select Month</option>
                                                                <?php
                                                                for ($i = 1; $i <= 12; $i++) {
                                                                    $month_ = str_pad($i, 2, '0', STR_PAD_LEFT);
                                                                    $selected = ($i == $month) ? " selected" : "";
                                                                    echo '<option value="' . $month_ . '" ' . $selected . '>' . $month_ . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <select class="form-control" name="year" required>
                                                                <option value="">Select Year</option>
                                                                <?php
                                                                $distinctYears = DB::getInstance()->querySample("SELECT SUBSTR(Date_Of_VISIT,1,4) AS Year_Of_Admission FROM patient GROUP BY Year_Of_Admission ORDER BY Year_Of_Admission");
                                                                foreach ($distinctYears AS $years) {
                                                                    $selected = ($years->Year_Of_Admission == $year) ? " selected" : "";
                                                                    echo '<option value="' . $years->Year_Of_Admission . '" ' . $selected . '>' . $years->Year_Of_Admission . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <button class="btn btn-success" name="searchMonthlyGraph" value="searchMonthlyGraph">Search</button>
                                                    </form>
                                                    <header>PATIENTS GRAPH FOR THE MONTH <?php echo $month . " " . $year ?></header>
                                                </div>
                                                <div class="card-body no-padding height-9">
                                                    <div class="row">
                                                        <canvas id="daily_graph_report"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 hidden">
                                <div class="card card-topline-aqua">
                                    <div class="card-head">
                                        <header>USER LOGS</header>
                                    </div>
                                    <div class="card-body no-padding height-auto">
                                        <div class="row" style="height:400px;overflow:scroll;">
                                            <ul class="docListWindow">
                                                <?php
                                                $systemLogs = DB::getInstance()->query("SELECT * FROM system_logs ORDER BY Time DESC LIMIT 100");
                                                foreach ($systemLogs->results() as $log):
                                                    $status = "clsAvailable";
                                                    $status = (strstr($log->Log, "change") || strstr($log->Log, "edit")) ? "clsOnLeave" : $status;
                                                    $status = (strstr($log->Log, "delet")) ? "clsNotAvailable" : $status;
                                                    ?>
                                                    <li>
                                                        <div class="details">
                                                            <div class="title">
                                                                <a href="#"><?php echo english_date_time($log->Time); ?></a>
                                                            </div>
                                                            <div>
                                                                <span class="<?php echo $status; ?>"><?php echo $log->Log; ?></span>
                                                            </div>
                                                        </div>
                                                    </li> 
                                                <?php endforeach;
                                                ?>
                                            </ul>
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
        <script>
            function ReloadPage(form_id) {
                $("#" + form_id).submit();
            }
            var DailypatientsArrayData = <?php echo json_encode($daily_patients_array); ?>;
            var MonthlypatientsArrayData = <?php echo json_encode($monthly_patients_array); ?>;
            var DeathOccuranceArrayData = <?php echo json_encode($death_occurance_array); ?>;
        </script>
        <?php include_once 'includes/footer_js.php'; ?>
        
        <script src="js/counterup/jquery.waypoints.min.js" type="text/javascript"></script>
        <script src="js/counterup/jquery.counterup.min.js" type="text/javascript"></script>
     
        <script src="js/chart-js/Chart.bundle.js" type="text/javascript"></script>
        <script src="js/chart-js/utils.js" type="text/javascript"></script>
        <script src="js/chart-js/chartjs-data.js" type="text/javascript"></script>
    </body>

</html>