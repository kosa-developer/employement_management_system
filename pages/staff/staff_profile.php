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
                        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
                            <div class="page-title">
                                <div class="pull-left">
                                    <h1 class="title">Staff Profile</h1>                            
                                </div>
                                <div class="pull-right hidden-xs">
                                    <?php
                                    if (isset($_GET["staff_id"]) && $_GET["staff_id"] != "") {
                                        $staff_id = $crypt->decode($_GET["staff_id"]);
                                    } else {
                                        
                                    }
                                    if (in_array("Hospital Staff", $_SESSION['hospital_user_modules'])) {
                                        ?>
                                        <a class="btn btn-primary" href="index.php?page=<?php echo $crypt->encode("view_staff"); ?>"><i class="fa fa-backward"></i> Back to Staff</a>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- BEGIN PROFILE SIDEBAR -->
                                <?php
                                if (Input::exists() && Input::get("update_picture") == "update_picture") {
                                    $staff_photo = ($_FILES['new_picture']['name']);
                                    $tmp_name = $_FILES["new_picture"]["tmp_name"];
                                    $image = Input::get("new_picture");
                                    $fname = Input::get("fname");
                                    $lname = Input::get("lname");
                                    $photo_name_array = explode(".", $staff_photo);
                                    $destination = strtoupper($fname) . '_' . strtoupper($lname) . '_' . date('Ymdh') . '.' . end($photo_name_array);

                                    $target_dir = "images/staff/";
                                    $target_file = $target_dir . basename($destination);
                                    if ($staff_photo != "") {
                                        move_uploaded_file($tmp_name, $target_file);
                                        $insertStaff = DB::getInstance()->update("staff", $staff_id, array(
                                            'Photo' => $destination
                                                ), "Staff_Id");
                                        $_SESSION['hospital_profile_picture'] = $destination;
                                    }
                                    Redirect::to("index.php?page=" . $crypt->encode("staff_profile") . "&staff_id=" . $crypt->encode($staff_id));
                                }
                                $staffCheck = "SELECT * FROM staff,person WHERE person.Person_Id=staff.Person_Id AND staff.Staff_Id='$staff_id'  ORDER BY person.Fname";
                                if (DB::getInstance()->checkRows($staffCheck)) {
                                    $staff_list = DB::getInstance()->query($staffCheck);
                                    foreach ($staff_list->results() as $staff):
                                        $brought_profile_picture = ($staff->Photo != "") ? $staff->Photo : "default.jpg";
                                        ?>
                                        <div class="profile-sidebar">
                                            <div class="card card-topline-aqua">
                                                <div class="card-body no-padding height-9">
                                                    <div class="row">
                                                        <div class="profile-userpic">
                                                            <img id="final_image" width="120px" height="120px" src="images/staff/<?php echo $brought_profile_picture ?>" class="img-responsive" alt=""> 
                                                            <form action="" method="POST" enctype="multipart/form-data">
                                                                <input type="hidden" name="fname" value="<?php echo $staff->Fname ?>">
                                                                <input type="hidden" name="lname" value="<?php echo $staff->Lname ?>">
                                                                <input id="piture" type="file" name="new_picture" class="form-control" onchange="returnImage(this)">
                                                                <button type="submit" name="update_picture" value="update_picture" class="btn btn-success btn-xs">Save</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="profile-usertitle">
                                                        <div class="profile-usertitle-name"> <?php echo $staff->Title . '.  ' . $staff->Fname . ' ' . $staff->Lname ?> </div>
                                                        <div class="profile-usertitle-job"> </div>
                                                    </div>
                                                    <ul class="list-group list-group-unbordered">
                                                        <li class="list-group-item">
                                                            <b><i class='fa fa-home'></i> Residence </b> <a class="pull-right"> <?php echo $staff->Village ?></a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b><i class='fa fa-phone'></i> Phone </b> <a class="pull-right"> <?php echo $staff->Phone_Number ?></a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b><i class='fa fa-user-circle'></i> Position</b> <a class="pull-right"><?php echo $staff->Position; ?> </a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b><i class='fa fa-home'></i> Department</b> <a class="pull-right"><?php echo $staff->Staff_Department; ?> </a>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b><i class='fa fa-calendar'></i> Enrollment date</b> <a class="pull-right"><?php echo english_date($staff->Enrollment_Date); ?> </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END BEGIN PROFILE SIDEBAR -->
                                        <!-- BEGIN PROFILE CONTENT -->
                                        <div class="profile-content">
                                            <div class="row">
                                                <div class="card">
                                                    <div class="card-head card-topline-aqua">
                                                        <header></header>
                                                    </div>
                                                    <div class="card-body no-padding height-9">
                                                        <div class="container-fluid">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="tabbable-line">
                                                                        <div class="row">
                                                                            <div id="biography" >
                                                                                <h4>Biography:</h4>
                                                                                <p><?php echo $staff->Biography ?></p>
                                                                                <hr/>
                                                                                <h4>Education:</h4>
                                                                                <?php
                                                                                $education_array = unserialize($staff->Education_Background);
                                                                                if (!empty($education_array)) {
                                                                                    ?>
                                                                                    <table class="table table-bordered">
                                                                                        <thead><tr><th>Year</th><th>Award</th><th>Institution</th></tr></thead>
                                                                                        <tbody>
                                                                                            <?php
                                                                                            for ($i = 0; $i < count($education_array[0]); $i++) {
                                                                                                ?>
                                                                                                <tr>
                                                                                                    <td><?php echo $education_array[0][$i]; ?></td>
                                                                                                    <td><?php echo $education_array[1][$i] ?></td>
                                                                                                    <td><?php echo $education_array[2][$i] ?></td>
                                                                                                </tr>
                                                                                                <?php
                                                                                            }
                                                                                            ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <?php
                                                                                } else {
                                                                                    echo $staff->Education_Background;
                                                                                }
                                                                                ?>
                                                                                <h4>Experience:</h4>
                                                                                <?php
                                                                                $experience_array = unserialize($staff->Experience);
                                                                                if (!empty($experience_array)) {
                                                                                    ?>
                                                                                    <table class="table table-bordered">
                                                                                        <thead><tr><th>Year</th><th>Position</th><th>Organisation</th></tr></thead>
                                                                                        <tbody>
                                                                                            <?php for ($i = 0; $i < count($experience_array[0]); $i++) { ?>
                                                                                                <tr>
                                                                                                    <td><?php echo $experience_array[0][$i] . " - " . $experience_array[1][$i] ?></td>
                                                                                                    <td><?php echo $experience_array[2][$i] ?></td>
                                                                                                    <td><?php echo $experience_array[3][$i] ?></td>
                                                                                                </tr>
                                                                                            <?php }
                                                                                            ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                    <?php
                                                                                } else {
                                                                                    echo $staff->Experience;
                                                                                }
                                                                                ?>
                                                                                <hr>
                                                                                <h4>Accomplishments:</h4>
                                                                                <p><?php echo $staff->Accomplishment ?></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- END PROFILE CONTENT -->
                                        </div>
                                        <?php
                                    endforeach;
                                } else {
                                    echo '<div class="alert alert-warning">Staff Undefined</div>';
                                }
                                ?>
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
        </div>
        <!-- start js include path -->
        <?php include_once 'includes/footer_js.php'; ?>
        <script>
                                                            function returnImage(input) {
                                                                if (input.files && input.files[0]) {
                                                                    var reader = new FileReader();

                                                                    reader.onload = function (e) {
                                                                        $('#final_image')
                                                                                .attr('src', e.target.result)
                                                                                .width(120)
                                                                                .height(120);
                                                                    };
                                                                    reader.readAsDataURL(input.files[0]);
                                                                }
                                                            }
        </script>
    </body>

</html>