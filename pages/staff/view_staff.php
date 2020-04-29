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
                        <header class="panel_header">
                            <div class="actions panel_actions pull-right">
                                <a class="btn btn-primary" href="index.php?page=<?php echo $crypt->encode("add_staff"); ?>"><i class="fa fa-plus"></i> Add Staff</a>
                            </div>
                        </header>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if (isset($_GET['action']) && $_GET["staff_id"] != "") {
                                    $action_made = $crypt->decode($_GET["action"]);
                                    $staff_id = $crypt->decode($_GET["staff_id"]);
                                    if ($action_made == "approve_staff") {
                                        DB::getInstance()->update("staff", $staff_id, array("Is_Approved" => 1), "Staff_Id");
                                        echo '<div class="alert alert-success">Staff has been added into the staff list</div>';
                                    } else if ($action_made == "delete_staff") {
                                        
                                    } else if ($action_made == "delete_permanently") {
                                        $person_id = DB::getInstance()->getName("staff", $staff_id, "Person_Id", "Staff_Id");
                                        DB::getInstance()->delete("staff", array("Staff_Id", "=", $staff_id));
                                        DB::getInstance()->delete("person", array("Person_Id", "=", $person_id));
                                        echo '<div class="alert alert-success">Staff has been permanently removed from the staff list</div>';
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode("view_staff"));
                                }
                                if (Input::exists() && Input::get("update_staff") == "update_staff") {
                                    $staff_id = Input::get("staff_id");
                                    $person_id = Input::get("person_id");
                                    $fname = strtoupper(Input::get("first_name"));
                                    $lname = strtoupper(Input::get("last_name"));
                                    $dob = Input::get("dob");
                                    $gender = Input::get("gender");
                                    $country_of_origin = Input::get("country_of_origin");
                                    $identity_card = Input::get("identity_card");
                                    $phone = Input::get("phone_number");
                                    $residence = Input::get("residence");
                                    $village = Input::get("village");
                                    $subcounty = Input::get("subcounty");
                                    $district = Input::get("district");
                                    $rank = (Input::get("rank") != '') ? Input::get("rank") : NULL;
                                    $serial_number = (Input::get("serial_number") != '') ? Input::get("serial_number") : NULL;
                                    $service_number = (Input::get("service_number") != '') ? Input::get("service_number") : NULL;
                                    $parent = (Input::get("parent") != '') ? Input::get("parent") : NULL;
                                    $parent_contact = (Input::get("parent_contact") != '') ? Input::get("parent_contact") : NULL;
                                    $bank_account = (Input::get("bank_account") != '') ? Input::get("bank_account") : NULL;
                                    $bank_id = (Input::get("bank_id") != '') ? Input::get("bank_id") : NULL;
                                    $branch = (Input::get("branch") != '') ? Input::get("branch") : NULL;


                                    $staff_title = Input::get("title");
                                    $email = Input::get("email");
                                    $education = Input::get("education");
                                    $experience = Input::get("experience");
                                    $accomplishment = Input::get("accomplishment");
                                    $position = Input::get("position");
                                    $department = Input::get("department");
                                    $enrollment_date = Input::get("enrollment_date");

                                    $queryUpdatePerson = DB::getInstance()->update("person", $person_id, array(
                                        'Fname' => $fname,
                                        'Lname' => $lname,
                                        'DOB' => $dob,
                                        'Gender' => $gender,
                                        'Country_Of_Origin' => $country_of_origin,
                                        'Identity_Card' => $identity_card,
                                        'Phone_Number' => $phone,
                                        'Residence' => $residence,
                                        'Village' => $village,
                                        'Subcounty' => $subcounty,
                                        'District' => $district
                                            ), "Person_Id");
                                    $updateStaff = DB::getInstance()->update("staff", $staff_id, array(
                                        'Staff_Department' => $department,
                                        'Position' => $position,
                                        'Serial_Number' => $serial_number,
                                        'Service_Number' => $service_number,
                                        'Parent_Name' => $parent,
                                        'Parent_contact' => $parent_contact,
                                        'Rank' => $rank,
                                        'Email' => $email,
                                        'Bank_Id' => $bank_id,
                                        'Branch' => $branch,
                                        'Account_Number' => $bank_account,
                                        'Title' => $staff_title,
                                        'Education_Background' => $education,
                                        'Experience' => $experience,
                                        'Accomplishment' => $accomplishment,
                                        'Enrollment_Date' => $enrollment_date
                                            ), "Staff_Id");
                                    if ($queryUpdatePerson && $updateStaff) {
                                        echo '<div class="alert alert-success">Staff Details updated successfully</div>';
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode("view_staff"));
                                }
                                ?>
                                <div class="card card-topline-green">
                                    <div class="card-head">
                                        <header>Search staff or filter </header>
                                    </div>
                                    <div class="card-body ">
                                        <form action="" method="POST">
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Age from</span>
                                                    <input type="number" min="0" class="form-control" name="age_from">
                                                    <span class="input-group-addon">To</span>
                                                    <input type="number" min="0" class="form-control" name="age_to">
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <!--<label>Gender</label>-->
                                                <select class="form-control" name="gender">
                                                    <option value="">Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <select class="select2" data-placeholder="Department.." style="width: 100%" name="department">
                                                    <option value="">Department...</option>
                                                    <?php
                                                    //Department array in the init file
                                                    for ($x = 0; $x < count($department_list_array); $x++) {
                                                        echo '<option value="' . $department_list_array[$x] . '">' . $department_list_array[$x] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-2">
                                                <select class="select2" data-placeholder="Position..." style="width: 100%" name="position">
                                                    <option value="">Choose....</option>
                                                    <?php
                                                    //Position array declared in the init file
                                                    for ($x = 0; $x < count($position_list_array); $x++) {
                                                        echo '<option value="' . $position_list_array[$x] . '">' . $position_list_array[$x] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <button class="btn btn-success" name="search_staff" value="search_staff"><i class="fa fa-search"></i> Search</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="card card-topline-red">
                                    <?php
                                    $staffCondition = "";
                                    $fileHeading = "Staff members";
                                    if (Input::exists() && Input::get("search_staff") == "search_staff") {
                                        $department = Input::get("department");
                                        $position = Input::get("position");
                                        $gender = Input::get("gender");
                                        $age_from = Input::get("age_from");
                                        $age_to = Input::get("age_to");
                                        $dob_from = ($age_from != "") ? increaseDateToDate(-$age_from, "year", $date_today) : "";
                                        $dob_to = ($age_to != "") ? increaseDateToDate(-$age_to, "year", $date_today) : "";
                                        $staffCondition .= ($department != "") ? " AND staff.Staff_Department='$department'" : "";
                                        $staffCondition .= ($position != "") ? " AND staff.Position='$position'" : "";
                                        $staffCondition .= ($gender != "") ? " AND person.Gender='$gender'" : "";
                                        $staffCondition .= ($dob_from != "") ? " AND person.DOB<='$dob_from'" : "";
                                        $staffCondition .= ($dob_to != "") ? " AND person.DOB>='$dob_to'" : "";

                                        $fileHeading .= ($gender != "") ? " (" . $gender . ") " : "";
                                        $fileHeading .= ($age_from != "" || $age_to != "") ? " age range " : "";
                                        $fileHeading .= ($age_from != "") ? " from " . $age_from : "";
                                        $fileHeading .= ($age_to != "") ? " to " . $age_to : "";
                                        $fileHeading .= ($position != "") ? " who are " . $position : "";
                                        $fileHeading .= ($department != "") ? " in " . $department : "";
                                    }
                                    $fileHeading = strtoupper($fileHeading);
                                    $staffCheck = "SELECT * FROM staff,person WHERE person.Person_Id=staff.Person_Id AND staff.Staff_Status=1 $staffCondition ORDER BY person.Fname";
                                    if (DB::getInstance()->checkRows($staffCheck)) {
                                        $data_sent = serialize(array($fileHeading, $staffCheck));
                                        ?>
                                        <div class="card-head">
                                            <header>
                                                <?php echo $fileHeading ?>
                                                <a target="_blank" href="index.php?page=<?php echo $crypt->encode("staff_list_pdf") . "&print_list=print_staff_list&data_sent=" . $crypt->encode($data_sent) ?>" class="btn btn-success btn-xs"><i class="fa fa-download"></i> print staff list</a>
                                            </header>
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            $staff_list = DB::getInstance()->query($staffCheck);
                                            foreach ($staff_list->results() as $staff):
                                                $brought_profile_picture = ($staff->Photo != "") ? $staff->Photo : "default.jpg";
                                                ?>
                                                <div class="col-lg-6 col-md-12 col-sm-12">
                                                    <div class="team-member">
                                                        <div class="team-img col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                                            <a href="index.php?page=<?php echo $crypt->encode('staff_profile') . '&staff_id=' . $crypt->encode($staff->Staff_Id); ?>">
                                                                <img class="img-responsive" style="height:130px;width: 120px" src="images/staff/<?php echo $brought_profile_picture ?>" alt="">
                                                            </a>
                                                        </div>
                                                        <div class="team-info col-lg-9 col-md-9 col-sm-9 col-xs-9 ">
                                                            <h4>
                                                                <a href="index.php?page=<?php echo $crypt->encode('staff_profile') . '&staff_id=' . $crypt->encode($staff->Staff_Id); ?>"><?php echo $staff->Title . '.  ' . $staff->Fname . ' ' . $staff->Lname ?></a>
                                                                <br/>
                                                                <?php if ($staff->Is_Approved == 1) { ?>
                                                                    &nbsp;<span class='btn btn-warning btn-xs'><a data-toggle="modal"  href="#staff_id<?php echo $staff->Staff_Id ?>"> edit</a></span>
                                                                    <span class='btn btn-danger btn-xs'><a  href="index.php?page=<?php echo $crypt->encode('view_staff') . '&action=' . $crypt->encode("delete_staff") . '&staff_id=' . $crypt->encode($staff->Staff_Id); ?>"  onclick="return confirm('Do you really want to remove <?php echo $staff->Fname . ' ' . $staff->Lname ?> from the system?')" >delete</a></span>
                                                                <?php } else {
                                                                    ?>
                                                                    <span class='btn btn-success btn-xs'><a  href="index.php?page=<?php echo $crypt->encode('view_staff') . '&action=' . $crypt->encode("approve_staff") . '&staff_id=' . $crypt->encode($staff->Staff_Id); ?>"  onclick="return confirm('Do you really want to approve <?php echo $staff->Fname . ' ' . $staff->Lname ?> into the system?')" >approve</a></span>
                                                                    <span class='btn btn-danger btn-xs'><a  href="index.php?page=<?php echo $crypt->encode('view_staff') . '&action=' . $crypt->encode("delete_permanently") . '&staff_id=' . $crypt->encode($staff->Staff_Id); ?>"  onclick="return confirm('Do you really want to permanently remove <?php echo $staff->Fname . ' ' . $staff->Lname ?> from the system?')" >remove permanently</a></span>
                                                                <?php }
                                                                ?>
                                                            </h4>
                                                            <span><?php echo $staff->Rank . ', ' .$staff->Position ; ?><br>
                                                                <small>Gender:</small> <?php echo $staff->Gender; ?>,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                <small>Age:</small> <?php echo calculateAge($staff->DOB, date("Y-m-d")) ?><br>
                                                                <small class="fa fa-phone"> Phone:</small> <?php echo $staff->Phone_Number ?><br>
                                                                <small class="fa fa-envelope">Serial No:</small> <?php echo $staff->Serial_Number ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- General section box modal start -->
                                                <div class="modal fade" id="staff_id<?php echo $staff->Staff_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                    <div class="modal-dialog animated fadeInDown" style="width: 90%">
                                                        <div class="modal-content">
                                                            <form action="" method="POST">
                                                                <div class="modal-header">
                                                                    <a href="" class="close" aria-hidden="true">&times;</a>
                                                                    <h4 class="modal-title"><i class="fa fa-edit"></i> Staff Profile update</h4>
                                                                </div>
                                                                <div class="modal-body row">
                                                                    <input type="hidden" value="<?php echo $staff->Staff_Id ?>" name="staff_id" class="form-control">
                                                                    <input type="hidden" value="<?php echo $staff->Person_Id ?>" name="person_id" class="form-control">
                                                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <label>Title</label>
                                                                            <input type="text" name="title" class="form-control" value="<?php echo $staff->Title ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>First Name</label>
                                                                            <input type="text" class="form-control" name="first_name" value="<?php echo $staff->Fname ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Last Name</label>
                                                                            <input type="text" class="form-control" name="last_name" value="<?php echo $staff->Lname ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Date of Birth</label>
                                                                            <input type="date" name="dob" class="form-control datepicker" value="<?php echo $staff->DOB ?>" data-format="yyyy-mm-dd" max="<?php echo $date_today ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Rank</label>
                                                                            <div class="controls">
                                                                                <input type="text" class="form-control"  value="<?php echo $staff->Rank ?>" name="rank" >
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="form-label" for="Gender">Gender</label>
                                                                            <select class="form-control" name="gender" >
                                                                                <option value="">Select--</option>
                                                                                <option <?php echo $selected = ($staff->Gender == "Male") ? "selected" : ""; ?> value="Male">Male</option>
                                                                                <option <?php echo $selected = ($staff->Gender == "Female") ? "selected" : ""; ?> value="Female">Female</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                                                        <div class="form-group">
                                                                            <label class="form-label">Banks</label>
                                                                            
                                                                            <select class="select2" style="width:100%" name="bank_id" >
                                                                                <option value="">Choose...</option>
                                                                                <?php
                                                                                $bankCheck = "SELECT * FROM bank  ORDER BY Bank_Name";
                                                                                $bank_list = DB::getInstance()->query($bankCheck);
                                                                                foreach ($bank_list->results() as $bank):
                                                                                    $selected = ($bank->Bank_Id == $staff->Bank_Id) ? "selected" : "";
                                                                                    echo '<option value="' . $bank->Bank_Id . '"'.$selected.'>' . $bank->Bank_Name . '</option>';
                                                                                endforeach;
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Branch</label>
                                                                            <div class="controls">
                                                                                <input type="text" class="form-control" value="<?php echo $staff->Branch; ?>" name="branch" >
                                                                            </div>
                                                                        </div> 
                                                                        <div class="form-group">
                                                                            <label>Account Number</label>
                                                                            <div class="controls">
                                                                                <input type="text" class="form-control" value="<?php echo $staff->Account_Number; ?>" name="bank_account" >
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="form-label">Country of origin</label>
                                                                            <select class="select2" style="width:100%" name="country_of_origin" >
                                                                                <?php
                                                                                $country = ($staff->Country_Of_Origin != "") ? $staff->Country_Of_Origin : $current_country;
                                                                                //Declared in the init file
                                                                                for ($i = 0; $i < count($countries_list); $i++) {
                                                                                    $selected = ($countries_list[$i] == $country) ? " selected" : "";
                                                                                    echo'<option value="' . $countries_list[$i] . '" ' . $selected . '>' . $countries_list[$i] . '</option>';
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="form-label">National ID</label>
                                                                            <input type="text" class="form-control" value="<?php echo $staff->Identity_Card ?>" name="identity_card" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Phone Number(s)</label>
                                                                            <input type="text" class="form-control" value="<?php echo $staff->Phone_Number ?>" name="phone_number" >
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 col-sm-6 col-xs-6">
                                                                         <div class="form-group">
                                                                            <label>Email Address</label>
                                                                            <input type="email" class="form-control" name="email" value="<?php echo $staff->Email ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Residence</label>
                                                                            <input type="text" class="form-control" name="residence" value="<?php echo $staff->Residence ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Village</label>
                                                                            <input type="text" class="form-control" name="village" value="<?php echo $staff->Village ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Subcounty</label>
                                                                            <input type="text" class="form-control" name="subcounty" value="<?php echo $staff->Subcounty ?>" >
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>District</label>
                                                                            <div>
                                                                                <input type="text" class="form-control" name="district" value="<?php echo $staff->District ?>" >
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Department</label>
                                                                            <select class="form-control" name="department" >
                                                                                <option value="">Choose....</option>
                                                                                <?php
                                                                                //Department array in the init file
                                                                                for ($x = 0; $x < count($department_list_array); $x++) {
                                                                                    $selected = ($department_list_array[$x] == $staff->Staff_Department) ? "selected" : "";
                                                                                    echo '<option value="' . $department_list_array[$x] . '" ' . $selected . '>' . $department_list_array[$x] . '</option>';
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                     <div class="col-md-3 col-sm-6 col-xs-6">
                                                                         
                                                                        <div class="form-group">
                                                                            <label>Position</label>
                                                                            <select class="form-control" name="position" >
                                                                                <option value="">Select...</option>
                                                                                <?php
                                                                                //Position array declared in the init file
                                                                                for ($x = 0; $x < count($position_list_array); $x++) {
                                                                                    $selected = ($position_list_array[$x] == $staff->Position) ? "selected" : "";
                                                                                    echo '<option value="' . $position_list_array[$x] . '" ' . $selected . '>' . $position_list_array[$x] . '</option>';
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Serial Number</label>
                                                                            <div class="controls">
                                                                                <input type="text" class="form-control" name="serial_number" value="<?php echo $staff->Serial_Number ?>" >
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Service Number</label>
                                                                            <div class="controls">
                                                                                <input type="text" class="form-control" name="service_number" value="<?php echo $staff->Service_Number ?>" >
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Parent/Guardian Name</label>
                                                                            <div class="controls">
                                                                                <input type="text" class="form-control" name="parent" value="<?php echo $staff->Parent_Name ?>" >
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Parent/Guardian Contact</label>
                                                                            <div class="controls">
                                                                                <input type="text" class="form-control" name="parent_contact" value="<?php echo $staff->Parent_contact ?>" >
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Accomplishments</label>
                                                                            <textarea class="form-control" rows="3" cols="10" name="accomplishment"><?php echo $staff->Accomplishment ?></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Enrollment date</label>
                                                                            <input type="date" class="form-control" name="enrollment_date" value="<?php echo $staff->Enrollment_Date ?>" max="<?php echo $date_today ?>">
                                                                        </div>
                                                                     </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a class="btn btn-default" href="">Close</a>
                                                                    <button class="btn btn-success" name="update_staff" value="update_staff" type="submit">Save changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- modal end -->
                                            <?php endforeach; ?>
                                        </div>
                                        <?php
                                    } else {
                                        echo '<div class="alert alert-danger"><strong>NO ' . $fileHeading . '</strong></div>';
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
        <!-- end js include path -->
    </body>

</html>