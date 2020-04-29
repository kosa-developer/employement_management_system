<!DOCTYPE html>
<html lang="en">
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
                                    <div class="page-title">Hospital Facilities</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                <?php
                                if (isset($_GET["action"]) && $_GET["action"] == $crypt->encode('remove_bed') && $_GET["bed_id"] != "") {
                                    $bed_id = $crypt->decode($_GET["bed_id"]);
                                    $bedUpdate = DB::getInstance()->update("bed", $bed_id, array("Status" => "Removed"), "Bed_Id");
                                    if ($bedUpdate) {
                                        echo '<div class="alert alert-success">Bed  has been successfully removed from the room</div>';
                                    } else {
                                        echo '<div class="alert alert-warning">There is a problem removing the room</div>';
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode('facility'));
                                }
                                if (Input::exists() && Input::get("edit_facility") == "edit_facility") {
                                    $facility_type = strtolower(Input::get("facility_type"));
                                    $facility_id = Input::get("facility_id");
                                    $facility_name = strtoupper(Input::get("facility_name"));
                                    $accomodation_bill = Input::get("accomodation_bill");
                                    $room_number = Input::get("room_number");
                                    $facilityUpdate = FALSE;
                                    if ($facility_type == "ward") {
                                        $facilityUpdate = DB::getInstance()->update("ward", $facility_id, array("Ward_Name" => $facility_name, "Accomodation_Bill" => $accomodation_bill), "Ward_Id");
                                    }if ($facility_type == "bed") {
                                        $facilityUpdate = DB::getInstance()->update("bed", $facility_id, array("Bed_Number" => $facility_name, "Accomodation_Bill" => $accomodation_bill), "Bed_Id");
                                    }
                                    if ($facilityUpdate) {
                                        echo '<div class="alert alert-success">' . $facility_type . '  has been successfully updated</div>';
                                    } else {
                                        echo '<div class="alert alert-warning">There is a problem updating ' . $facility_type . '</div>';
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode('facility'));
                                }
                                if (Input::exists() && Input::get('submit_ward') == "submit_ward") {
                                    $ward_name = strtoupper(Input::get('ward_name'));
                                    $accomodation_bill = Input::get('accomodation_bill');
                                    $accomodation_bill = ($accomodation_bill != "") ? $accomodation_bill : NULL;
                                    if (DB::getInstance()->checkRows("select * from ward where Ward_Name='$ward_name'")) {
                                        echo '<div class="alert alert-warning col-sm-6">Ward already exists</div>';
                                    } else {

                                        $executeQuery = DB::getInstance()->insert("ward", array(
                                            'Ward_Name' => $ward_name,
                                            'Accomodation_Bill' => $accomodation_bill
                                        ));

                                        if ($executeQuery) {
                                            echo '<div class="alert alert-success">' . $ward_name . '  has been successfully registered</div>';
                                        } else {
                                            echo '<div class="alert alert-warning">There is a problem</div>';
                                        }
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode('facility'));
                                }
                                if (Input::exists() && Input::get('submit_bed') == "submit_bed") {
                                    $ward_id = Input::get("ward_id");
                                    $bed_number = Input::get('bed_number');
                                    $accomodation_bill = Input::get('accomodation_bill');
                                    $updated = 0;
                                    $duplicates = 0;
                                    if (!empty($bed_number)) {
                                        for ($x = 0; $x < count($bed_number); $x++) {
                                            $bed = strtoupper($bed_number[$x]);
                                            if (DB::getInstance()->checkRows("select * from bed where Bed_Number='$bed' AND Ward_Id='$ward_id'")) {
                                                if (DB::getInstance()->checkRows("select * from bed where Bed_Number='$bed' AND Status='Removed' AND Ward_Id='$ward_id'")) {
                                                    $bedUpdate = DB::getInstance()->query("UPDATE bed SET Accomodation_Bill = '$accomodation_bill[$x]',Status= 'Free' where Bed_Number='$bed' AND Ward_Id='$ward_id'");
                                                    if ($bedUpdate) {
                                                        $updated++;
                                                    }
                                                } else {
                                                    $duplicates++;
                                                }
                                            } else {
                                                $accomodation_bill[$x] = ($accomodation_bill[$x] != "") ? $accomodation_bill[$x] : NULL;
                                                $bedInsert = DB::getInstance()->insert("bed", array(
                                                    'Bed_Number' => $bed,
                                                    'Accomodation_Bill' => $accomodation_bill[$x],
                                                    'Ward_Id' => $ward_id
                                                ));
                                                if ($bedInsert) {
                                                    $updated++;
                                                }
                                            }
                                        }
                                    }
                                    if ($updated != 0) {
                                        echo '<div class="alert alert-success col-sm-6">' . $updated . ' beds  have been successfully registered</div>';
                                    } if ($duplicates != 0) {
                                        echo '<div class="alert alert-warning col-sm-6">' . $duplicates . '  beds could not be re-registered in the selected room</div>';
                                    }
                                    Redirect::go_to("index.php?page=" . $crypt->encode('facility'));
                                }
                                ?>
                            </div>
                                <div class="card card-topline-red">
                                    <div class="card-head">
                                        <header>All Facility entry</header>
                                    </div>
                                    <div class="card-body " id="bar-parent">
                                        <div class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                                <ul class="nav nav-tabs tabs-left">
                                                    <li class="active">
                                                        <a href="#tab-ward" data-toggle="tab">
                                                            <i class="fa fa-hospital-o"></i> Wards
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#tab-beds" data-toggle="tab">
                                                            <i class="fa fa-table"></i> Beds
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-lg-10 col-md-10 col-sm-9 col-xs-9">
                                                <div class="tab-content">
                                                    <div class="tab-pane fade in active" id="tab-ward">
                                                        <ul class="nav nav-tabs transparent">
                                                            <li class="active">
                                                                <a href="#tab_ward_new" data-toggle="tab">
                                                                    <i class="fa fa-plus-circle"></i> Add new Ward
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#tab_ward_view" data-toggle="tab">
                                                                    <i class="fa fa-eye"></i> View Wards 
                                                                </a>
                                                            </li>
                                                        </ul>

                                                        <div class="tab-content transparent">
                                                            <div class="tab-pane fade in active" id="tab_ward_new">
                                                                <div class="row">
                                                                    <h1>Register Ward Here</h1>
                                                                    <form id="" method="post" action="" >
                                                                        <div class="col-lg-6">
                                                                            <div class="form-group">
                                                                                <label class="form-label" for="formfield1">Ward Name</label>
                                                                                <div class="controls">
                                                                                    <select class="form-control" id="formfield1" name="ward_name" required>
                                                                                        <option value="">Choose....</option>
                                                                                        <option value="Maternity">Maternity</option>
                                                                                        <option value="Antenatal">Antenatal</option>
                                                                                        <option value="Gynecology">Gynecology</option>
                                                                                        <option value="Postnatal">Postnatal</option>
																						<option value="Medical">Medical</option>
																						<option value="Surgical">Surgical</option>
																						<option value="Emergency">Emergency</option>
																						<option value="Paeditric">Paeditric</option>
                                                                                        <!--<option value=""></option>
                                                                                        <option value=""></option>
                                                                                        <option value=""></option>-->
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label class="form-label" for="formfield1">Accomodation Bills</label>
                                                                                <div class="controls">
                                                                                    <i class=""></i>
                                                                                    <input  type="number" min="0" class="form-control" id="formfield1" name="accomodation_bill">
                                                                                </div>
                                                                            </div>
                                                                            <div class="pull-right">
                                                                                <button type="submit" class="btn btn-success" name="submit_ward" value="submit_ward">Submit<i class="fa fa-check"></i></button>
                                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                                            </div>

                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="tab_ward_view">
                                                                <?php
                                                                $queryward = 'select * from ward ORDER BY Ward_Id DESC';
                                                                if (DB::getInstance()->checkRows($queryward)) {
                                                                    ?> <h1>Registered Buildings/Wards</h1>                                                   
                                                                    <table id="example4" class="table table-striped dt-responsive display table-bordered" cellspacing="1" width="100%">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Ward/Building</th>
                                                                                <th>Accomodation Bills</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $fetchward = DB::getInstance()->querySample($queryward);
                                                                            $no = 1;
                                                                            foreach ($fetchward as $warddata) {
                                                                                ?>
                                                                                <tr>
                                                                                    <td><?php echo $warddata->Ward_Name; ?></td>
                                                                                    <td><?php echo $warddata->Accomodation_Bill; ?></td>
                                                                                    <td>
                                                                                        <a href="#edit_facility" data-toggle="modal" onclick="returnModelData('Ward', '<?php echo $warddata->Ward_Id; ?>', '<?php echo $warddata->Ward_Name; ?>', '', '<?php echo $warddata->Accomodation_Bill; ?>');"><i class="fa fa-edit"></i> Edit</a>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                    <?php
                                                                } else {

                                                                    echo '<h3 style="color:red;">No Ward/Building to Display</h3>';
                                                                }
                                                                ?>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="tab-beds">
                                                        <ul class="nav nav-tabs transparent">
                                                            <li class="active">
                                                                <a href="#tab_bed_new" data-toggle="tab">
                                                                    <i class="fa fa-plus-circle"></i> Add new Bed
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a href="#tab_bed_view" data-toggle="tab">
                                                                    <i class="fa fa-eye"></i> View Beds 
                                                                </a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content transparent">
                                                            <div class="tab-pane fade in active" id="tab_bed_new">
                                                                <h1>Register bed here</h1>

                                                                <form id="" class="" method="post" action="" >
                                                                    <div class="row">
                                                                        <div class="col-lg-8">
                                                                            <div class="row form-group">
                                                                                <div class="col-md-6">
                                                                                    <label>Select Ward</label>
                                                                                    <select class="form-control" name="ward_id" required>
                                                                                        <?php
                                                                                        echo DB::getInstance()->DropDowns("ward", "Ward_Id", "Ward_Name");
                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <table class="table table-bordered table-striped">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Bed Number</th><th>Accomodation Bill<button type="button" class="btn btn-success btn-xs pull-right" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="add_more_beds">
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input type="text" class="form-control" name="bed_number[]" required>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="number" min="0" class="form-control" name="accomodation_bill[]">  
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                            <div class="pull-right">
                                                                                <button type="submit" class="btn btn-success" name="submit_bed" value="submit_bed">Submit<i class="fa fa-check"></i></button>
                                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="tab-pane fade" id="tab_bed_view">
                                                                <h1>View Registered Beds</h1>
                                                                <?php
                                                                $bedCheck = "SELECT bed.Bed_Id,bed.Bed_Number,bed.Status,bed.Accomodation_Bill,ward.Ward_Name FROM bed,ward WHERE bed.Ward_Id=ward.Ward_Id  AND bed.Status!='Removed'";
                                                                if (DB::getInstance()->checkRows($bedCheck)) {
                                                                    ?>
                                                                    <table id="example1" class="table table-striped table-bordered table-responsive" >
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Bed Number</th>
                                                                                <th>Ward</th>
                                                                                <th>Accomodation Bill</th>
                                                                                <th>Occupancy Status</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $bed_list = DB::getInstance()->query($bedCheck);
                                                                            foreach ($bed_list->results() as $beds):
                                                                                $class = ($beds->Status == "Occupied") ? "label label-warning" : "label label-info";
                                                                                ?>
                                                                                <tr>
                                                                                    <td><?php echo $beds->Bed_Number ?></td>
                                                                                    <td><?php echo $beds->Ward_Name ?></td>
                                                                                    <td><?php echo $beds->Accomodation_Bill ?></td>
                                                                                    <td><label class="col-xs-12 <?php echo $class; ?>"><?php echo $beds->Status ?></label></td>
                                                                                    <td>
                                                                                        <a href="#edit_facility" data-toggle="modal" onclick="returnModelData('Bed', '<?php echo $beds->Bed_Id; ?>', '<?php echo $beds->Bed_Number; ?>', '', '<?php echo $beds->Accomodation_Bill; ?>');" class="btn btn-success btn-xs"><i class=""></i> Edit</a>
                                                                                        <?php if ($beds->Status != "Occupied") { ?>
                                                                                            <a href="index.php?page=<?php echo $crypt->encode('facility') . '&action=' . $crypt->encode('remove_bed') . '&bed_id=' . $crypt->encode($beds->Bed_Id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Do you really want to remove this bed from the room?');"><i class="fa fa-trash-o"></i> Remove</a>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>
                                                                    </table>

                                                                    <?php
                                                                } else {
                                                                    echo '<div class="alert alert-warning">No beds registered</div>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="edit_facility" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                        <form action="" method="post">
                                                            <div class="modal-dialog animated fadeInDown">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                        <h4 class="modal-title">Edit <b id="model_heading"></b>&nbsp;Information</h4>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label id="model_item_header">Name</label>
                                                                            <input type="hidden" id="facility_model_id" name="facility_id">
                                                                            <input type="hidden" id="facility_type" name="facility_type">
                                                                            <input type="text" class="form-control"id="facility_model_name" name="facility_name" required>
                                                                        </div>
                                                                        <div class="form-group" id="room_number_div">

                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Accomodation Bill (UGx)</label>
                                                                            <input type="number" min="0" id="facility_model_bill" class="form-control" name="accomodation_bill" value="">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                        <button type="submit" name="edit_facility" value="edit_facility"class="btn btn-success" type="button">Save changes</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
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
        <script>
                                                                                                function add_element() {
                                                                                                    var row_ids = Math.round(Math.random( ) * 300000000);
                                                                                                    document.getElementById('add_more_beds').insertAdjacentHTML('beforeend',
                                                                                                            '<tr id="' + row_ids + '">\n\
                    <td><input name="bed_number[]" class="form-control" type="text" required></td>\n\
                    <td class="form-inline"><input type="number" min="0" class="form-control" name="accomodation_bill[]"><button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>\n\
                    </td>\n\
                </tr>');

                                                                                                }
                                                                                                function delete_item(element_id) {
                                                                                                    $('#' + element_id).html('');
                                                                                                }
                                                                                                function returnModelData(model_type, id, name, room_number, bill) {
                                                                                                    $('#model_heading').html(model_type + "'s");
                                                                                                    $('#model_item_header').html(model_type + " Name");
                                                                                                    document.getElementById('facility_type').value = model_type;
                                                                                                    document.getElementById('facility_model_id').value = id;
                                                                                                    document.getElementById('facility_model_name').value = name;
                                                                                                    document.getElementById('facility_model_bill').value = bill;
                                                                                                    if (model_type == "Room") {
                                                                                                        $('#room_number_div').html('<label>Room Number</label><input type="text" id="facility_model_room_number" class="form-control" name="room_number" value="' + room_number + '" required>');
                                                                                                    } else {
                                                                                                        var readonly = (model_type == "Ward") ? true : false;
                                                                                                        $("#facility_model_name").attr({"readonly": readonly});

                                                                                                        $('#room_number_div').html('');
                                                                                                    }
                                                                                                }
        </script>
    </body>
</html>