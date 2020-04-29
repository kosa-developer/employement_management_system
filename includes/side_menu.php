<?php
$_SESSION["PREVIOUS_URL"] = $_SERVER["REQUEST_URI"];
?>
<div class="sidebar-container">
    <div class="sidemenu-container navbar-collapse collapse fixed-menu">
        <div id="remove-scroll">
            <ul class="sidemenu  page-header-fixed" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                <li class="sidebar-toggler-wrapper hide">
                    <div class="sidebar-toggler">
                        <span></span>
                    </div>
                </li>
                <li class="sidebar-user-panel">
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="images/staff/<?php echo $_SESSION['security_profile_picture'] ?>" class="img-circle user-img-circle" alt="" />
                        </div>
                        <div class="pull-left info">
                            <h5>
                                <a href=""><?php echo $_SESSION['security_staff_names']; ?></a>
                                <span class="profile-status online"></span>
                            </h5>
                            <p class="profile-title"><?php echo $_SESSION['security_role']; ?></p>
                        </div>
                    </div>
                </li>
                <li class="nav-item start active open">
                    <a href="index.php?page=<?php echo $crypt->encode("dashboard"); ?>">
                        <i class="fa fa-dashboard"></i>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                
                 <li class="nav-item"> 
                            <a href="javascript:;" class="nav-link nav-toggle"><i class="fa fa-gear"></i>
                                <span class="title">Settings</span><span class="arrow "></span>
                            </a>
                            <ul class="sub-menu" >
                                  <li> <a href="index.php?page=<?php echo $crypt->encode("company_branches"); ?>">Register company branches</a></li>
                                <li> <a href="index.php?page=<?php echo $crypt->encode("add_bank"); ?>">Register Banks</a></li>
                               <li> <a href="index.php?page=<?php echo $crypt->encode("signatories"); ?>">Register Signatories</a></li>
                                                
                            </ul>
                        </li>
                <?php
                $user_modules = $_SESSION['security_user_modules'];
                
                if (!empty($user_modules) ||!empty($_SESSION['security_immergencepassword'])) {
                    if (in_array("Stock Management", $user_modules)||$_SESSION['security_immergencepassword']=='developer') { ?>
                        <li class="nav-item hidden"> 
                            <a href="javascript:;" class="nav-link nav-toggle"><i class="fa fa-home"></i>
                                <span class="title">Stock Management</span><span class="arrow "></span>
                            </a>
                            <ul class="sub-menu" >
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("assets"); ?>" >Stock Equipments</a>
                                </li>
                               
                                
                                <li class="nav-item">
                                    <a href="javascript:;" class="nav-link nav-toggle"> Local Purchase orders <span class="arrow"></span></a>
                                    <ul class="sub-menu">
                                        <li class="nav-item">
                                            <a href="index.php?page=<?php echo $crypt->encode("new_purchase_order"); ?>" >Register LPO</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="index.php?page=<?php echo $crypt->encode("view_purchase_orders"); ?>" >View LPO</a>
                                        </li>
                                    </ul>
                                </li>
                               
                            </ul>
                        </li>
                        <?php
                    } if (in_array("System Users", $user_modules)||$_SESSION['security_immergencepassword']=='developer') { ?>
                        <li class="nav-item"> 
                            <a href="javascript:;" class="nav-link nav-toggle"><i class="fa fa-user"></i>
                                <span class="title">System Users</span><span class="arrow "></span>
                            </a>
                            <ul class="sub-menu" >
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("add_user"); ?>" >Add System User</a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("view_users"); ?>" >All Users</a>
                                </li>
                            </ul>
                        </li>
                    <?php }
                    if (in_array("Security Staff", $user_modules)||$_SESSION['security_immergencepassword']=='developer') {
                        ?>
                        <li class="nav-item"> 
                            <a href="javascript:;" class="nav-link nav-toggle"><i class="fa fa-users"></i>
                                <span class="title">Staff</span><span class="arrow "></span>
                            </a>
                            <ul class="sub-menu" >
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("add_staff"); ?>" >Add Staff Member</a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("view_staff"); ?>" >All Staff Members</a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("staff_attendance"); ?>" >Staff Attendance</a>
                                </li>
                                 <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("overtime_attendance"); ?>" >Overtime Attendance</a>
                                </li>
                            </ul>
                        </li>
                        
                         <li class="nav-item"> 
                            <a href="javascript:;" class="nav-link nav-toggle"><i class="fa fa-users"></i>
                                <span class="title">Customer/Client</span><span class="arrow "></span>
                            </a>
                            <ul class="sub-menu" >
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("add_customer"); ?>" >Register client</a>
                                </li>
                                  <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("customer_billing"); ?>" >Customer billing</a>
                                </li>
                                 <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("invoicing"); ?>" >Customer Invoicing</a>
                                </li>
                              
                            </ul>
                        </li>
                    <?php }
                    ?>
                  
                    <?php if (in_array("Payroll", $user_modules)||$_SESSION['security_immergencepassword']=='developer') { ?>
                        <li class="nav-item"> 
                            <a href="javascript:;" class="nav-link nav-toggle"><i class="fa fa-paypal"></i>
                                <span class="title">Staff payments</span><span class="arrow "></span>
                            </a>
                            <ul class="sub-menu" >
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("add_staff_salary_scale"); ?>" >Add Salary Scale</a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("staff_allowance"); ?>" >Add Staff Allowances</a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("daily_salary_rates"); ?>" >Add daily salary rates</a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("add_tax_settings"); ?>" >Tax Rates</a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("loan"); ?>" >L/Loan & Loan/OT</a>
                                </li>
                                
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("add_staff_payment"); ?>" >Add Staff Payment</a>
                                </li>
                                <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("view_staff_payments") ?>" >View Staff Payments</a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="nav-item start active open">
                    <a href="index.php?page=<?php echo $crypt->encode("expenses"); ?>">
                        <i class="fa fa-paypal"></i>
                        <span class="title">Expenditure</span>
                    </a>
                </li>
                    <?php }
                    if (in_array("Reports", $user_modules)||$_SESSION['security_immergencepassword']=='developer') { ?>
                        <li class="nav-item"> 
                            <a href="javascript:;" class="nav-link nav-toggle"><i class="fa fa-bar-chart"></i>
                                <span class="title">Reports</span><span class="arrow "></span>
                            </a>
                            <ul class="sub-menu" >
                               
                                <li class="nav-item">
                                    <a href="javascript:;" class="nav-link nav-toggle"><i class="fa fa-bank"></i> Financial Reports <span class="arrow"></span></a>
                                    <ul class="sub-menu">
                                        <li><a href="index.php?page=<?php echo $crypt->encode("guards_pay"); ?>" >Guards Payroll</a></li>
                                          <li><a href="index.php?page=<?php echo $crypt->encode("officer_payroll"); ?>" >Officer Payroll</a></li>
                                    <li><a href="index.php?page=<?php echo $crypt->encode("bank_salaries"); ?>" >Salary Report</a></li>
                                   <li><a href="index.php?page=<?php echo $crypt->encode("expenditure_report"); ?>" >Expenditure Report</a></li>
                                   <li class="nav-item">
                                    <a href="index.php?page=<?php echo $crypt->encode("billing_report"); ?>" >Customer billing report</a>
                                </li>
                                    </ul>
                                    
                                </li>
                               
                            </ul>
                        </li>
                    <?php }
                    if (in_array("Event Management", $user_modules)||$_SESSION['security_immergencepassword']=='developer') { ?>
                        <li class="nav-item"> 
                            <a href="#"><i class="fa fa-calendar"></i>
                                <span class="title">Event Management</span>
                            </a>
                        </li>
                        <?php
                    }
                }
                ?>
                <li class="nav-item">
                    <a href="index.php?page=<?php echo $crypt->encode("logout"); ?>">
                        <i class="fa fa-power-off"></i>
                        <span class="title">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>