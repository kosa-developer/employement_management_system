<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta name="description" content="Responsive Admin Template" />
        <meta name="author" content="SeffyHospital" />
        <title><?php echo $title; ?> Guide</title>

        <!-- icons -->
        <link href="js/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css"/>

        <!--bootstrap -->
        <link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="js/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />

        <!-- theme style -->
        <link href="css/theme_style.css" rel="stylesheet" id="rt_style_components" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link href="css/plugins.min.css" rel="stylesheet" type="text/css" />
        <link href="css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="css/theme-color.css" rel="stylesheet" type="text/css" />

        <!-- favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.png" /> 
    </head>
    <!-- END HEAD -->
    <body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-full-width page-md header-blue">
        <div class="page-wrapper">
            <!-- start header -->
            <?php
            if (isset($_SESSION["hospital_username"])) {
                include_once 'includes/header_menu.php';
            } else {
                ?>
                <div class="page-header navbar navbar-fixed-top">
                    <div class="page-header-inner ">
                        <!-- logo start -->
                        <div class="page-logo">
                            <a href="">
                                <span class="logo-icon fa fa-hospital-o"></span>
                                <span class="logo-default" ></span>
                            </a>
                        </div>
                        <!-- logo end -->
                        <div class="hor-menu   hidden-sm hidden-xs">
                            <ul class="nav navbar-nav">
                                <li class="nav-item start ">
                                    <a href="index.php?page=<?php echo $crypt->encode("login") ?>" class="nav-link "> <i class="fa fa-backward"></i> <span class="title">Back</span></a>
                                </li>
                            </ul>
                        </div>
                        <!-- start header menu -->
                        <div class="top-menu">
                            <ul class="nav navbar-nav pull-right">
                                <li class="dropdown dropdown-quick-sidebar-toggler">
                                    <a href="javascript:;" class="dropdown-toggle">
                                        <i class="icon-logout"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php }
            ?>
            <!-- end header -->
            <!-- start page container -->
            <div class="page-container">
                <!-- start sidebar menu -->
                <div class="sidebar-container">
                    <div class="sidemenu-container navbar-collapse collapse">
                        <ul class="sidemenu  page-header-fixed" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                            <li class="sidebar-toggler-wrapper hide">
                                <div class="sidebar-toggler">
                                    <span></span>
                                </div>
                            </li>
                            <li>
                                <div class="user-panel">
                                    <div class="pull-left image">
                                        <img src="img/dp.svg" class="img-circle user-img-circle" alt="User Image" />
                                    </div>
                                    <div class="pull-left info">
                                        <p> Dr.Bansi Patel</p>
                                        <a href="#"><i class="fa fa-circle user-online"></i><span class="txtOnline"> Online</span></a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end sidebar menu --> 
            <!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title">System user mannual (guide)</div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="text-align: justify">
                        <!-- end stock management//start Hospital staff -->
                        <div class="col-md-6">
                            <div class="card card-topline-lightblue">
                                <div class="card-head">
                                    <header>STAFF</header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body" id="line-parent">
                                    <div class="row">
                                        <!--Add Staff member start-->
                                        <div class="panel-group accordion" id="accordion3">
                                            <div class="panel panel-default">
                                                <div class="panel-heading panel-heading-gray">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#Hospital_staff" href="#add_staff_member"> Add Staff member</a>
                                                    </h4>
                                                </div>
                                                <div id="add_staff_member" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>Under Hospital staff,you cn register hospital workers,view registered workers,edit workers' information,print staff list and monitor staff attendance.<br>
                                                        <h4>Add Hospital staff</h4>
                                                        To aregister new staff member,click <em>Hospital stafft</em> from the Menu.<br>
                                                        Click Add staff member and a registration form will be displayed.</p>
                                                        Click in the tittle field and enter the worker's tittle say Dr.<br>
                                                        Click in the First Name field type the worker's name.Do the same for Last name <br>
                                                        Click on the Date of Birth  button amd enter the date of birth for the worker you are registering,the date should be in the <b>month/date/year</b> format otherwise click on the drop down(arrow showing down) <select name="" disabled="disabled"></select>and select the date.
                                                        Click on the Gender button and select the worker's Gender.
                                                        Click in the National ID field and enter the worker's National identification number.
                                                        Click in the Residence field and type the worker's place of Residence.
                                                        Click in the Village field and type the worker's village name.Click in the Sub county field and enter the worker's sub county name.
                                                        Click in the District field and enter the worker's District name.
                                                        Under allocation,click on the Department button and select the department to which the worker you are registering belongs.
                                                        Click on the position field and select the position that worker holds in that department.
                                                        To add a worker's profile picture,click on the choose file button and browse to the file directory where you previously saved the picture,select the workers picture and click open.
                                                        Click in the email address field and type a valid email address of the worker you are registering.
                                                        Click in the biography field and type words/a short description of the worker you are registering.
                                                        In the education fields,you are required to enter the workers recent study information i.e year,award and the institution from which the award was obtained.
                                                        Click in the year field to input the year,click in the award field and type the award obtained,click in the institution field and type the institution.Click the add(green button) to add more fields add the cancel red button beside any un used fields to remove them.
                                                        In the experience fields,enter the worker's recent experience.In the year fields,click in the first field and enter the start year of work and the end year of work in the next field.Click in the tittle field and enter the tittle of the worker while at that work place.
                                                        Click in the organisation field and type the organisation where they worked.
                                                        Click the add(green button) to add more fields add the cancel red button beside any un used fields to remove them.
                                                        Click in the accomplishments field and type any projects the worker has previously accomplished.
                                                        Click  in the social media name field and type the worker's social media and enter its link in the following link field.
                                                        Click in the date of enrollment field and type date of enrollment for that worker or click the drop down button and select the date.
                                                        Click the <b class="btn btn-success btn-xs">SAVE</b>to save the entered information or the cancel butoon to discard the entered information.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="panel panel-default">
                                                <div class="panel-heading panel-heading-gray">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#Hospital_staff" href="#all_staff_members"> All Staff members</a>
                                                    </h4>
                                                </div>
                                                <div id="all_staff_members" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>This is the page where all registered staff members can be viewed.<br>
                                                        <h4>All Hospital staff</h4>
                                                        To view reegistered staff members,click <em>Hospital stafft</em> from the Menu.<br>
                                                        Click All staff members.</p>
                                                        There is a <b>search filter</b> on the page from where you can specify the category of staff you want to view.<br>
                                                        Under staff serch or filter,select categories according to your search choice<br>
                                                        <!--Type the age range of the staff you want to view.
                                                        Click on the gender button and select the gender you want to view <br>
                                                        Click on the department button and select the department you want to view<br>-->
                                                        <h4>Edit staff members</h4>
                                                        To edit a staff member,On the All staff members page,click the  <b class="btn-success btn-xs>EDIT</b> next to the name of the staff member you want to edit. 
                                                                                                                           A page containing the staff member information will come up.You can then delete the information you want to edit and type new information
                                                                                                                           Click the <b class="btn btn-success btn-xs">SAVE CHANGES</b>to save the entered information or the <b class="btn-success btn-xs">CLOSE</b> button to discard the entered information.
                                                        <h4>Delete staff members</h4>
                                                        To delete a staff member,from the <b>All staff members</b> page,click the  <b class="btn-success btn-xs"> DELETE</b> button below the name of the staff member you want to delete. 
                                                        A message will come up readingm <b>Do you really want to delete that person from the system?</b>
                                                        Click <b class="btn-success btn-xs ">Yes</b> to confirm or the <b class="btn-success btn">cancel</b> to cancel.
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Staff attendace-->
                                            <div class="panel panel-default">
                                                <div class="panel-heading panel-heading-gray">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#Hospital_staff" href="#staff_attendance">Staff attendance</a>
                                                    </h4>
                                                </div>
                                                <div id="staff_attendance" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>This is the page from where you can keep track of staff attendance.<br>
                                                            Clck Staff Attendance under Hospital staff on the Menu.<br>
                                                            Click and select the month for which you want to capture attendance from the button beow list of staff and click search</p>
                                                        Every staff member listed has a row of check boxes from where you can click to mark attendance for that day<br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-topline-lightblue">
                                <div class="card-head">
                                    <header>Staff Payroll</header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;"></a>
                                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body" id="line-parent">
                                    <div class="row">
                                        <!--collapse start-->
                                        <div class="panel-group accordion" id="staff_payroll_module">
                                            <div class="panel panel-default">
                                                <div class="panel-heading panel-heading-gray">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#staff_payroll_module" href="#payroll_salary_scale"> Setting Salary Scale </a>
                                                    </h4>
                                                </div>
                                                <div id="payroll_salary_scale" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>
                                                            Every hospital staff member (employee) capable of being paid (Salary or advance). MUST be having a salary scale of which would have been agreed upon during the recruitment time.<br/>
                                                            In order to give any staff salary (Using the system) their salary scale can be set from this very page. This also caters for salary scale changes (In case the emploeyee is promoted or demoted).<br/>
                                                            To do so, just on the left hand side menu, Click on the Payroll link and thereafter select on Add salary scale, which takes you to a page to set employee's salary scale.<br/>
                                                            On the page, select the employee (Staff) names, specify date (MM/DD/YYYY) FORMAT from when the salary scale starts from and then enter the salary amount agreed on.<br/>
                                                            Click on <b class="btn btn-success btn-xs">Save</b> button to save entered data.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading panel-heading-gray">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#staff_payroll_module" href="#payroll_add_staff_payment"> Staff Payments registration </a>
                                                    </h4>
                                                </div>
                                                <div id="payroll_add_staff_payment" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>
                                                            All the epmloyees' payments say Advance or Monthly salary can be registered or captured using this form, of which accessed from the Left hand side menu under "Payroll" module and then click on Add Staff payment.<br/>
                                                            On the page displayed, Enter payment date (MM/DD/YYYY), select the staff (Employee) to be paid, Select the paid as option of which differentiates if it is advance or Monthly Salary, and enter the amount paid.<br/>
                                                            <i style="color: red">
                                                                <b>NOTE:</b> Basing on the employee's salary scale there is restriction i.e mothly salary payment You can only pay them the maximum of months worked for multipied by the monthly salary scale.
                                                                AND for Advance an increament of extra days in the current month can also be added on their salary.
                                                            </i><br/>
                                                            Thereafter, click on the <b class="btn btn-success btn-xs">save</b> button to save the employee's payments made.<br/>
                                                            On the same page you can directly access the <b class="btn btn-primary btn-xs">view staff payments</b> link from the right hand upper corner.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel panel-default">
                                                <div class="panel-heading panel-heading-gray">
                                                    <h4 class="panel-title">
                                                        <a class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#staff_payroll_module" href="#payroll_view_staff_payments"> Viewing staff Payments (Salary and Advances) </a>
                                                    </h4>
                                                </div>
                                                <div id="payroll_view_staff_payments" class="panel-collapse collapse">
                                                    <div class="panel-body">
                                                        <p>
                                                            All the employee's payments entered/registered into the system can be retrieved from this page. This can ba accessed directly from Payroll module, and then select <a>View staff payments</a>.<br/>
                                                            On this page you can filter and display basing on date range or view salaries only or even view all payments given to a single staff, and to do so:-<br/>
                                                            Select what you want to filter basing on, from the top search form. Say Date from  <?php echo (date("d") - 1) . date("/m/Y") . " to " . date("d/m/Y") ?> and click on <b class="btn btn-success btn-xs">Search staff</b>.<br/>
                                                            The filtered payments are displayed in a tabular form, and can be printed out, JUST Click on the <b class="btn btn-primary btn-xs">print pdf</b> button of which opens the printer options page from where you can select the printer and print.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--collapse end-->
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
        <!-- start js include path -->
        <script src="js/jquery.min.js" type="text/javascript"></script>
        <script src="js/jquery.blockui.min.js" type="text/javascript"></script>
        <!-- bootstrap -->
        <script src="js/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="js/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <script src="js/jquery.slimscroll.js"></script>
        <script src="js/app.js" type="text/javascript"></script>
        <script src="js/layout.js" type="text/javascript"></script>
        <script src="js/theme-color.js" type="text/javascript"></script>


        <!-- end js include path -->
    </body>
</html>