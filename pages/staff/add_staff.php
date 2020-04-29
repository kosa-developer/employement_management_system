<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD -->

    <head>
        <?php
        include_once 'includes/header_css.php';
        $serial_not = (DB::getInstance()->displayTableColumnValue("select Serial_Number from staff WHERE Serial_Number IS NOT NULL order by Staff_Id desc limit 1", 'Serial_Number') != 0) ? DB::getInstance()->displayTableColumnValue("select Serial_Number from staff where Serial_Number IS NOT NULL order by Staff_Id desc limit 1", 'Serial_Number') + 1 : 1;
        if ($serial_not < 10) {
            $serial_no = "00" . $serial_not;
        } else if ($serial_not >=10 && $serial_not < 100) {
            $serial_no = "0" . $serial_not;
        } else {
            $serial_no = $serial_not;
        }
        ?>
    </head>
    <!-- END HEAD -->
    <body class="page-header-fixed sidemenu-closed-hidelogo page-content-white <?php echo (isset($_SESSION['security_username'])) ? "page-md" : "page-full-width page-md" ?> header-blue">
        <div class="page-wrapper">
            <!-- start header -->
<?php
if (isset($_SESSION['security_username'])) {
    include_once 'includes/header_menu.php';
}
?>
            <!-- end header -->
            <!-- start page container -->
            <div class="page-container">
                <!-- start sidebar menu -->
<?php
if (isset($_SESSION['security_username'])) {
    include_once 'includes/side_menu.php';
}
?>
                <!-- end sidebar menu -->
                <!-- start page content -->
                <div class="page-content-wrapper">
                    <div class="page-content">
                        <div class="page-bar">
                            <div class="page-title-breadcrumb">
                                <h2 class="title pull-left">Hospital staff enrollment</h2>
                                <div class="actions panel_actions pull-right">
                                    <?php
                                    if (isset($_SESSION['security_username'])) {
                                        ?>
                                        <a class="btn btn-primary" href="index.php?page=<?php echo $crypt->encode("view_staff"); ?>"><i class="fa fa-eye"></i> View Staff</a>
<?php } else { ?>
                                        <a href="index.php?page=<?php echo $crypt->encode("login") ?>" class="btn btn-warning">Cancel</a>
                                <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                $readonly = (isset($_SESSION['security_username'])) ? "" : "readonly";
                                if (Input::exists() && Input::get("add_staff") == "add_staff") {
                                    $is_approved = (isset($_SESSION['security_username']) && in_array("Security Staff", $_SESSION['security_user_modules'])) ? 1 : 0;
                                    $fname = strtoupper(Input::get("first_name"));
                                    $lname = strtoupper(Input::get("last_name"));
                                    $country_of_origin = Input::get("country_of_origin");
                                    $dob = Input::get("dob");
                                    $rank = (Input::get("rank") != '') ? Input::get("rank") : NULL;
                                    $gender = Input::get("gender");
                                    $identity_card = Input::get("identity_card");
                                    $phone = Input::get("phone_number");
                                    $residence = Input::get("residence");
                                    $village = Input::get("village");
                                    $subcounty = Input::get("subcounty");
                                    $district = Input::get("district");
                                    $serial_number = (Input::get("serial_number") != '') ? Input::get("serial_number") : NULL;
                                    $service_number = (Input::get("service_number") != '') ? Input::get("service_number") : NULL;
                                    $parent = (Input::get("parent") != '') ? Input::get("parent") : NULL;
                                    $parent_contact = (Input::get("parent_contact") != '') ? Input::get("parent_contact") : NULL;
                                    $bank_account = (Input::get("bank_account") != '') ? Input::get("bank_account") : NULL;
                                    $bank_id = (Input::get("bank_id") != '') ? Input::get("bank_id") : NULL;
                                    $branch = (Input::get("branch") != '') ? Input::get("branch") : NULL;

                                    $education_year = Input::get("education_year");
                                    $education_award = Input::get("education_award");
                                    $education_institution = Input::get("education_institution");

                                    $experience_year_from = Input::get("experience_year_from");
                                    $experience_year_to = Input::get("experience_year_to");
                                    $experience_title = Input::get("experience_title");
                                    $experience_organisation = Input::get("experience_organisation");

                                    $education_array = array($education_year, $education_award, $education_institution);
                                    $experience_array = array($experience_year_from, $experience_year_to, $experience_title, $experience_organisation);


                                    $staff_title = Input::get("title");
                                    $email = (Input::get("email") != '') ? Input::get("email") : NULL;

                                    $accomplishment = Input::get("accomplishment");

                                    $staff_photo = ($_FILES['photo']['name']);
                                    $tmp_name = $_FILES["photo"]["tmp_name"];
                                    $enrollment_date = Input::get("enrollment_date");
                                    if ($staff_photo != "") {
                                        $photo_name_array = explode(".", $staff_photo);
                                        $destination = strtoupper($fname) . '_' . strtoupper($lname) . '_' . date('Ymdh') . '.' . end($photo_name_array);

                                        $target_dir = "images/staff/";
                                        $target_file = $target_dir . basename($destination);
                                    } else {
                                        $destination = "";
                                    }

                                    $position = Input::get("position");
                                    $department = Input::get("department");

                                    $queryDup = "SELECT * FROM person WHERE Phone_Number='$phone' AND Fname='$fname' AND Lname='$lname'";
                                    if (DB::getInstance()->checkRows($queryDup)) {
                                        //Duplicate
                                    } else {
                                        if ($staff_photo != "") {
                                            move_uploaded_file($tmp_name, $target_file);
                                        }
                                        $queryInsert = DB::getInstance()->insert("person", array(
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
                                        ));
                                        if ($queryInsert) {
                                            $person_id = DB::getInstance()->displayTableColumnValue($queryDup . " ORDER BY Person_Id DESC LIMIT 1", "Person_Id");
                                            $staffCheck = "SELECT * FROM staff WHERE Person_Id=$person_id";
                                            if (!DB::getInstance()->checkRows($staffCheck)) {
                                                $insertStaff = DB::getInstance()->insert("staff", array(
                                                    'Person_Id' => $person_id,
                                                    'Enrollment_Date' => $enrollment_date,
                                                    'Serial_Number' => $serial_number,
                                                    'Service_Number' => $service_number,
                                                    'Parent_Name' => $parent,
                                                    'Parent_contact' => $parent_contact,
                                                    'Staff_Department' => $department,
                                                    'Position' => $position,
                                                    'Email' => $email,
                                                    'Rank' => $rank,
                                                    'Title' => $staff_title,
                                                    'Photo' => $destination,
                                                    'Education_Background' => serialize($education_array),
                                                    'Experience' => serialize($experience_array),
                                                    'Bank_Id' => $bank_id,
                                                    'Branch' => $branch,
                                                    'Account_Number' => $bank_account,
                                                    'Accomplishment' => $accomplishment,
                                                    'Is_Approved' => $is_approved
                                                ));
                                                if ($insertStaff) {
                                                    echo '<div class="alert alert-success">Staff Details registered successfully</div>';
                                                } else {
                                                    echo '<div class="alert alert-danger">Error occured</div>';
                                                }
                                            }
                                        }
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode("add_staff"));
                                }
                                ?>
                                <div class="card card-topline-yellow">
                                    <div class="card-head">
                                        <header>Entry form</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <form method="POST" action="" enctype="multipart/form-data">
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <label>Profile Picture</label>
                                                    <div class="controls">
                                                        <input type="file" accept="image/*" class="form-control" name="photo">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="form-group col-xs-2">
                                                        <label>Title</label>
                                                        <input type="text" name="title" class="form-control" required>
                                                    </div>
                                                    <div class="form-group col-xs-5">
                                                        <label>First Name</label>
                                                        <div class="controls">
                                                            <input type="text" class="form-control" name="first_name" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-xs-5">
                                                        <label>Last Name</label>
                                                        <div class="controls">
                                                            <input type="text" class="form-control" name="last_name" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Serial Number</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="serial_number" value="<?php echo $serial_no; ?>" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Service Number</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="service_number" >
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Rank</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="rank" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Banks</label>
                                                    <select class="select2" style="width:100%" name="bank_id" required>
                                                        <option value="">Choose...</option>
<?php echo DB::getInstance()->dropDowns("bank", "Bank_Id", "Bank_Name"); ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Branch</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="branch" >
                                                    </div>
                                                </div> 
                                                <div class="form-group">
                                                    <label>Account Number</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="bank_account" >
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Date of Birth</label>
                                                    <div class="controls">
                                                        <input type="date" name="dob" class="form-control" max="<?php echo date("Y-m-d"); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label" for="Gender">Gender</label>
                                                    <div class="controls">
                                                        <select class="form-control" name="gender" required>
                                                            <option value="">Select--</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Country of origin</label>
                                                    <select class="select2" style="width:100%" name="country_of_origin" required>
                                                        <?php
                                                        //Declared in the init file
                                                        for ($i = 0; $i < count($countries_list); $i++) {
                                                            $selected = ($countries_list[$i] == $current_country) ? " selected" : "";
                                                            echo'<option value="' . $countries_list[$i] . '" ' . $selected . '>' . $countries_list[$i] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">National ID/Pass port</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="identity_card" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Phone Number(s)</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="phone_number" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Parent/Guardian Name</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="parent" >
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-md-6 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <label>Parent/Guardian Contact</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="parent_contact" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Residence</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="residence" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Village/cell</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="village" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Subcounty</label>
                                                    <div class="controls">
                                                        <input type="text" class="form-control" name="subcounty" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>District</label>
                                                    <div class="controls">
                                                        <select class="select2" style="width:100%" name="district" required>
                                                            <option value="">Choose...</option>
<?php echo $districtList ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Email Address</label>
                                                    <div class="controls">
                                                        <input type="email" class="form-control" name="email" >
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Education (From recent)</label>
                                                    <table class="table table-bordered">
                                                        <tr><th>Year</th><th>Award</th><th>Institution</th><th> <button type="button"  onclick="add_new_element('education_div');" class="btn btn-success btn-xs fa fa-plus-circle"></button></th></tr>
                                                        <tbody id="education_div">
                                                            <tr id="education_tr_1">
                                                                <td>
                                                                    <input type="number" min="1900" max="<?php echo date("Y") ?>" class="form-control" required name="education_year[]">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" required name="education_award[]">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" required name="education_institution[]">
                                                                </td>
                                                                <td><button type="button" value="education_tr_1" class="fa fa-trash-o btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"></button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="form-group">
                                                    <label>Experience (From recent)</label>
                                                    <table class="table table-bordered">
                                                        <tr><th>Year</th><th>Title</th><th>Organisation</th><th> <button type="button"  onclick="add_new_element('experience_div');" class="btn btn-success btn-xs fa fa-plus-circle"></button></th></tr>
                                                        <tbody id="experience_div">
                                                            <tr id="experience_tr_1">
                                                                <td class="input-group">
                                                                    <input type="number" min="1900" max="<?php echo date("Y") ?>" class="form-control" required name="experience_year_from[]">
                                                                    <span class="input-group-addon">To</span>
                                                                    <input type="number" min="1900" max="<?php echo date("Y") ?>" class="form-control" required name="experience_year_to[]">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" required name="experience_title[]">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" required name="experience_organisation[]">
                                                                </td>
                                                                <td><button type="button" value="experience_tr_1" class="fa fa-trash-o btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"></button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="form-group">
                                                    <label>Accomplishments</label>
                                                    <div class="controls">
                                                        <textarea class="form-control" rows="3" cols="10" name="accomplishment"></textarea>
                                                    </div>
                                                </div>
                                                <h2>Allocation</h2>
                                                <div class="form-group">
                                                    <label>Department</label>
                                                    <div class="controls">
                                                        <select class="select2" style="width: 100%" name="department" required>
                                                            <option value="">Choose....</option>
<?php
//Department array in the init file
for ($x = 0; $x < count($department_list_array); $x++) {
    echo '<option value="' . $department_list_array[$x] . '">' . $department_list_array[$x] . '</option>';
}
?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Position</label>
                                                    <div class="controls">
                                                        <select class="select2" style="width: 100%" name="position" required>
                                                            <option value="">Choose....</option>
<?php
//Position array declared in the init file
for ($x = 0; $x < count($position_list_array); $x++) {
    echo '<option value="' . $position_list_array[$x] . '">' . $position_list_array[$x] . '</option>';
}
?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Date of enrollment</label>
                                                    <input type="date" class="form-control" <?php echo $readonly ?> name="enrollment_date" value="<?php echo $date_today ?>" max="<?php echo $date_today ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="pull-right ">
                                                    <button type="submit" name="add_staff" value="add_staff" class="btn btn-success">Save</button>
                                                    <button type="reset" class="btn">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
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
        <?php
        include_once 'includes/footer.php';
        ?>
            <!-- end footer -->
        </div>
        <!-- start js include path -->
<?php include_once 'includes/footer_js.php'; ?>
        <!-- end js include path -->
        <script>
            var value = 1;

            function add_new_element(parent_div) {
                var row_ids = Math.round(Math.random( ) * 300000000);
                var data = '';
                data = (parent_div === "education_div") ? '<tr id="education_tr_' + row_ids + '"><td><input type="number" min="1900" max="<?php echo date("Y") ?>" class="form-control" required name="education_year[]"></td><td><input type="text" class="form-control" required name="education_award[]"></td><td><input type="text" class="form-control" required name="education_institution[]"></td><td><button type="button" value="education_tr_' + row_ids + '" class="fa fa-trash-o btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"></button></td></tr>' : data;
                data = (parent_div === "experience_div") ? '<tr id="experience_tr_' + row_ids + '"><td class="input-group"><input type="number" min="1900" max="<?php echo date("Y") ?>" class="form-control" required name="experience_year_from[]"><span class="input-group-addon">To</span><input type="number" min="1900" max="<?php echo date("Y") ?>" class="form-control" required name="experience_year_to[]"></td><td><input type="text" class="form-control" required name="experience_title[]"></td><td><input type="text" class="form-control" required name="experience_organisation[]"></td><td><button type="button" value="experience_tr_' + row_ids + '" class="fa fa-trash-o btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"></button></td></tr>' : data;
                document.getElementById(parent_div).insertAdjacentHTML('beforeend', data);
            }
            function delete_item(element_id) {

                $('#' + element_id).html('');

                value--;
                num--;
            }
        </script>
    </body>

</html>