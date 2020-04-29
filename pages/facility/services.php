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
                                    <div class="page-title">Hospital Services</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <?php
                                    if (Input::exists()) {
                                        if (Input::get("edit_service") == "edit_service") {
                                            $service_id = Input::get("service_id");
                                            $service_name = Input::get("service_name");
                                            $service_type = Input::get("service_type");
                                            $price = Input::get("price");
                                            $serviceUpdate = DB::getInstance()->update("service", $service_id, array(
                                                "Service_Name" => $service_name,
                                                "Service_Type" => $service_type,
                                                "Price" => $price
                                                    ), "Service_Id");
                                            if ($serviceUpdate) {
                                                echo '<div class="alert alert-success"> Services details updated successfully</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("services"));
                                        }
                                        if (Token::check(Input::get("token_new_service"))) {
                                            $service_name = Input::get("service_name");
                                            $service_type = Input::get("service_type");
                                            $price = Input::get("price");
                                            $services_added = 0;
                                            $duplicates = 0;
                                            if (!empty($service_name)) {
                                                for ($x = 0; $x < count($service_name); $x++) {
                                                    $queryDup = DB::getInstance()->checkRows("SELECT * FROM service WHERE Service_Name='$service_name[$x]'");
                                                    if ($queryDup) {
                                                        $duplicates++;
                                                    } else {
                                                        $serviceInsert = DB::getInstance()->insert("service", array(
                                                            "Service_Name" => $service_name[$x],
                                                            "Service_Type" => $service_type[$x],
                                                            "Price" => $price[$x]
                                                        ));
                                                        if ($serviceInsert) {
                                                            $services_added++;
                                                        }
                                                    }
                                                }
                                            }
                                            if ($services_added != 0) {
                                                echo '<div class="alert alert-success col-sm-6">' . $services_added . ' Services successfully registered</div>';
                                            } if ($duplicates != 0) {
                                                echo '<div class="alert alert-warning col-sm-6">' . $duplicates . ' duplicates could not be re registered</div>';
                                            }
                                            Redirect::go_to("index.php?page=" . $crypt->encode("services"));
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="card card-topline-yellow">
                                    <div class="card-body " id="bar-parent">
                                        <ul class="nav nav-tabs primary">
                                            <li class="active">
                                                <a href="#tab_new" data-toggle="tab">
                                                    <i class="fa fa-plus-circle"></i> Register new Service
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#tab_view" data-toggle="tab">
                                                    <i class="fa fa-eye"></i> View Services 
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content primary">
                                            <div class="tab-pane fade in active" id="tab_new">

                                                <div class="row">
                                                    <form id="" method="post" action="" >
                                                        <div class="col-md-9">
                                                            <div class="form-group">

                                                                <table class="table table-bordered table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Service Name</th>
                                                                            <th>Service Type</th>
                                                                            <th>Price Charged (UGx)<button type="button" class="btn btn-success btn-xs pull-right" id="add_more[]" onclick="add_element();">Add more</button></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody  id="add_element">
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text" class="form-control" name="service_name[]" required> 
                                                                            </td>
                                                                            <td>
                                                                                <select class="form-control" name="service_type[]">
                                                                                    <option value="">General (Others)</option>
                                                                                    <option value="Lab Services">Lab Services</option>
                                                                                    <option value="X-Ray Services">X-Ray Services</option>
                                                                                </select>  
                                                                            </td>
                                                                            <td>
                                                                                <input type="number" min="1" class="form-control" name="price[]">  
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="pull-right">
                                                                <input type="hidden" name="token_new_service" value="<?php echo Token::generate() ?>">
                                                                <button type="submit" class="btn btn-success" name="submit" value="submit">Submit<i class="fa fa-check"></i></button>
                                                                <button type="reset" class="btn btn-default"><i class="fa fa-times"></i></button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade" id="tab_view">

                                                <?php
                                                $queryServices = "SELECT * FROM service ORDER BY Service_Name";
                                                if (DB::getInstance()->checkRows($queryServices)) {
                                                    ?>                                                    
                                                    <table id="example1" class="table table-striped table-bordered" cellspacing="1" width="100%">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Service Name</th>
                                                                <th>Service Type</th>
                                                                <th>Price</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $service_list = DB::getInstance()->querySample($queryServices);
                                                            $no = 0;
                                                            foreach ($service_list as $services) {
                                                                $no++;
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $no ?></td>
                                                                    <td><?php echo $services->Service_Name; ?></td>
                                                                    <td><?php echo $services->Service_Type; ?></td>
                                                                    <td><?php echo ($services->Price != "") ? ugandan_shillings($services->Price) : ""; ?></td>
                                                                    <td> 
                                                                        <a data-toggle="modal"  href="#edit_<?php echo $services->Service_Id ?>">
                                                                            <i class="fa fa-pencil"></i> Edit</a>
                                                                    </td>
                                                            <div class="modal fade" id="edit_<?php echo $services->Service_Id ?>" tabindex="-1" role="dialog" aria-labelledby="ultraModal-Label" aria-hidden="true">
                                                                <form action="" method="post">
                                                                    <div class="modal-dialog animated fadeInDown">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                <h4 class="modal-title">Edit <?php echo $services->Service_Name; ?>'s&nbsp;Information</h4>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group">
                                                                                    <label>Service Name:</label>
                                                                                    <input type="hidden" name="service_id" value="<?php echo $services->Service_Id ?>">
                                                                                    <input type="text" class="form-control" name="service_name" value="<?php echo $services->Service_Name ?>">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Service Type:</label>
                                                                                    <select class="form-control" name="service_type">
                                                                                        <option value="" <?php echo ($services->Service_Type == "") ? " selected" : ""; ?>>General (Others)</option>
                                                                                        <option value="Lab Services"<?php echo ($services->Service_Type == "Lab Services") ? " selected" : ""; ?>>Lab Services</option>
                                                                                        <option value="X-Ray Services"<?php echo ($services->Service_Type == "X-Ray Services") ? " selected" : ""; ?>>X-Ray Services</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label>Price (UGx):</label>
                                                                                    <input type="number" min="1" class="form-control" name="price" value="<?php echo $services->Price ?>">
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                                                <button type="submit" name="edit_service" value="edit_service"class="btn btn-success" type="button">Save changes</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>


                                                    <?php
                                                } else {
                                                    echo '<div class="alert alert-warning">No services registered</div>';
                                                }
                                                ?>

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
            <?php
            include_once 'includes/footer.php';
            ?>
            <!-- end footer -->
        </div>
        <!-- start js include path -->
        <?php include_once 'includes/footer_js.php'; ?>
        <!-- end js include path -->
        <script>
                                                                                function add_element() {
                                                                                    var row_ids = Math.round(Math.random( ) * 300000000);
                                                                                    document.getElementById('add_element').insertAdjacentHTML('beforeend',
                                                                                            '<tr id="' + row_ids + '">\n\
                    <td><input name="service_name[]" class="form-control" type="text" required></td>\n\
                    <td><select class="form-control" name="service_type[]"><option value="">General (Others)</option>\n\
                    <option value="Lab Services">Lab Services</option><option value="X-Ray Services">X-Ray Services</option></select></td>\n\
                    <td class="form-inline"><input name="price[]" class="form-control" type="number" min="1" style="width:85%">\n\
                        <button type="button" value="' + row_ids + '" class="btn btn-danger btn-xs pull-right" onclick="delete_item(this.value);"><i class ="fa fa-times"></i></button>\n\
                    </td></tr>');

                                                                                }
                                                                                function delete_item(element_id) {
                                                                                    $('#' + element_id).html('');
                                                                                }
        </script>
    </body>

</html>