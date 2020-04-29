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
                                    <div class="page-title">Staff attendance</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>List of staff</header>
                                        <form action="" method="POST">
                                            <div class="col-sm-2">
                                                <label>Month</label>
                                                <div class="form-group">
                                                <select class="select2" data-placeholder="Month..." style="width: 100%" name="month">
                                                    <option value="">Choose....</option>
                                                    <?php
                                                    $month_from = substr(DB::getInstance()->DisplayTableColumnValue("SELECT MIN(Enrollment_Date) AS Min_Date FROM staff", "Min_Date"), 0, 7);
                                                    $month_to = date("Y-m");

                                                    $begin = new DateTime($month_from);
                                                    $end = new DateTime($month_to);
                                                    $end = $end->modify('+1 month');
                                                    $interval = new DateInterval('P1M');
                                                    $daterange = new DatePeriod($begin, $interval, $end);
                                                    foreach ($daterange AS $date) {
                                                        echo '<option value="' . $date->format('Y-m') . '">' . $date->format('m/Y') . '</option>';
                                                    }
                                                    ?>
                                                </select></div>
                                            </div>
                                            <div class="col-sm-2">
                                                <label>Position</label>
                                                <div class="form-group">
                                                    <select class="select2" style="width: 100%" name="position" >
                                                        <option value="">Choose....</option>
                                                        <?php
//Position array declared in the init file
                                                        for ($x = 0; $x < count($position_list_array); $x++) {
                                                            echo '<option value="' . $position_list_array[$x] . '">' . $position_list_array[$x] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div><br>
                                            <button class="btn btn-success" name="search_attendance" value="search_attendance"><i class="fa fa-search"></i> Search</button>
                                        </form>
                                    </div>
                                    <div class="card-body ">

                                        <?php
                                        $date_from = date("Y-m") . "-01";
                                        $date_to = $date_today;
                                        $condition = "";
                                        if (Input::exists() && Input::get("search_attendance") == "search_attendance") {
                                            $month = Input::get("month");
                                            $date_from = $month . "-01";
                                            $date_to = $month . "-31";
                                            $position = Input::get("position");

                                            $condition .= " AND staff.Position='$position'";
                                        }
                                        $staffCheck = "SELECT * FROM staff,person WHERE person.Person_Id=staff.Person_Id $condition AND staff.Staff_Status=1 ORDER BY person.Fname";
                                        if (DB::getInstance()->checkRows($staffCheck)) {
                                            $begin = new DateTime($date_from);
                                            $end = new DateTime($date_to);
                                            $end = $end->modify('+1 day');
                                            $interval = new DateInterval('P1D');
                                            $daterange = new DatePeriod($begin, $interval, $end);
                                            ?>
                                            <table class="table table-striped table-bordered table-responsive" id="example1" >
                                                <thead>
                                                    <tr>
                                                        <th>Staff Names</th>
                                                        <?php
                                                        foreach ($daterange as $date) {
                                                            echo "<th>" . english_date($date->format("Y-m-d")) . "</th>";
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $number = 0;
                                                    $staff_list = DB::getInstance()->query($staffCheck);
                                                    foreach ($staff_list->results() as $staff):
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $staff->Title . '.  ' . $staff->Fname . ' ' . $staff->Lname ?></td>
                                                            <?php
                                                            foreach ($daterange as $date) {
                                                                $number++;
                                                                $cur_date = $date->format("Y-m-d");
                                                                $checked = (DB::getInstance()->checkRows("SELECT * FROM staff_attendance WHERE Staff_Id='$staff->Staff_Id' AND Date='$cur_date' AND Is_Present=1")) ? " checked" : "";
                                                                $had_enrolled = (strtotime($cur_date) >= strtotime($staff->Enrollment_Date)) ? TRUE : FALSE;
                                                                ?>

                                                                <?php if ($had_enrolled) { ?>
                                                                    <td id="td_<?php echo $staff->Staff_Id . "_" . $number ?>" <?php echo($checked == " checked") ? 'style="background-color:green"' : "" ?>>
                                                                        <div class="checkbox checkbox-aqua">
                                                                            <input type="checkbox" <?php echo $checked ?> id="checkbox_<?php echo $staff->Staff_Id . "_" . $number ?>" onchange="submitStaffAttendance(this, '<?php echo $staff->Staff_Id . "','" . $cur_date . "','" . $number ?>')">
                                                                            <label for="checkbox_<?php echo $staff->Staff_Id . "_" . $number ?>"></label>
                                                                        </div>
                                                                    </td>
                                                                    <?php
                                                                } else {
                                                                    echo '<td style="background-color:orange"></td>';
                                                                }
                                                                ?>  
                                                            <?php }
                                                            ?>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>

                                            <?php
                                        } else {
                                            echo '<div class="alert alert-warning">No Staff registered</div>';
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
            function submitStaffAttendance(element, staff_id, date, entry_number) {

                var isPresent = (document.getElementById(element.id).checked) ? 1 : 0;
                var bg_color = (isPresent === 1) ? "green" : "white";
                document.getElementById("td_" + staff_id + "_" + entry_number).style.backgroundColor = bg_color;//.attr({"background-color": bg_color});
                $.ajax({
                    type: 'POST',
                    url: 'index.php?page=<?php echo $crypt->encode("ajax_data"); ?>',
                    data: {updateStaffAttendance: 'updateStaffAttendance', is_present: isPresent, date: date, staff_id: staff_id, user_id: <?php echo $_SESSION['security_user_id'] ?>},
                    success: function (html) {

                    }
                });
            }
        </script>
        <!-- end js include path -->
    </body>

</html>