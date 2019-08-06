<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<!--<script src="<?php echo base_url() ?>js/custom_js.js"></script>-->
<style type="text/css">
    .btn-group-sm>.btn, .btn-sm {padding:1px 5px !important}
    #tabs ul{
    margin:0px;
    padding:0px;
    
    }
    #tabs li{
    list-style: none;
    float: left;
    position: relative;
    top: 0;
    margin: 1px .2em 0 0;
    border-bottom-width: 0;
    padding: 0;
    white-space: nowrap;
    border: 1px solid #2c9d9c;
    background: #d9edf7 url(images/ui-bg_glass_75_e6e6e6_1x400.png) 50% 50% repeat-x;
    font-weight: normal;
    color: #555555;
    border-top-right-radius: 4px;
    border-top-left-radius: 4px;
    border-bottom: 0px;
    background-color: white;
    }
    #tabs button{
        
        align:center;
        font-weight: bold
    }
    #tabs a{
    float: left;
    padding: .5em 1em;
    text-decoration: none;
    }
    .col-md-12 {
    padding: 10px;
    }
    
    /* example styles for validation form demo */
    #booking_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
    .err1{
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
    .vertical-align{
        vertical-align: middle;
        padding-top: 1%;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div  class = "panel panel-info" style="margin:20px;" >
            <div class="panel-heading" style="font-size:130%;">
                <?php if(isset($query)){?>
                <form action="<?php echo base_url(); ?>employee/upcountry/assign_sc_to_upcountry" method="POST" style="margin-bottom:8px;" target="_blank">
                    <input type="hidden" value="<?php echo $query[0]['id']; ?>" name="service_center_id" />
                     <input type="hidden" value="<?php echo $query[0]['state']; ?>" name="state" />
                     <input type="submit" value="Add Upcountry" class="btn btn-primary btn-md pull-right" style="margin-left: 1%;"/>
                     <?php if($this->session->userdata['user_group'] == _247AROUND_ADMIN || $this->session->userdata['user_group'] == _247AROUND_RM) { ?>
                        <a onclick="edit_form();" class="btn btn-primary pull-right" href="javascript:void(0);" title="Edit Service Center" style="margin-left:1%;"><span class="glyphicon glyphicon-pencil"></span></a>
                     <?php } ?>
                </form>
                <?php }?>
                <center><b>
                    <?php
                        if (isset($selected_brands_list)) {
                            echo "Edit Vendor";
                        } else {
                            echo "Add Vendor";
                        }
                        ?>
                </b>
                    </center>
                <?php  if (isset($selected_brands_list)) { ?>
               
               <?php }?>
            </div>
            <div class="panel-body" style="padding: 0px 23px;">
                <?php if($this->session->userdata('checkbox')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('checkbox') . '</strong>
                    </div>';
                    }
                    ?>
                <?php if(validation_errors()) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . validation_errors() . '</strong>
                    </div>';
                    }
                    ?>
<!--                Tabs below-->
            <div id="tabs" style="border:0px solid #fff;float:left;" class="panel-info">
                <div class="row">
                    <?php
                        if (!isset($selected_brands_list)) {            
                    ?>

                    <div id="tabs" style=""  class="col-md-12 panel-title"style="padding: 10px 8px 0px;">
                        <ul>
                            <li><a href="#" id="1" class="btn nav nav-pills panel-title" style="background-color:#fff">Basic Details</a></li>
                            <li><a href="#tab-2"  id="2" onclick="alert('Please Add Basic Details FIrst')" class="btn nav nav-pills panel-title" style="background-color:#d9edf7">Documents</a</li>
                            <li><a href="#tab-3"  id="3" onclick="alert('Please Add Basic Details FIrst')" class="btn nav nav-pills panel-title"  style="background-color:#d9edf7">Products and Brands</a></li>
                            <li><a href="#tab-4"  id="4" onclick="alert('Please Add Basic Details FIrst')" class="btn nav nav-pills panel-title"  style="background-color:#d9edf7">Contact Person</a></li>
                             <li><a href="#tab-5"  id="4" onclick="alert('Please Add Basic Details FIrst')" class="btn nav nav-pills panel-title"  style="background-color:#d9edf7">Bank Details</a></li>
                        </ul>
                    </div>
                </div>

<!--First condition tab ends here-->
                    <?php
                         }
                        else{
                    ?>
<!--Case 2 tab starts here-->
                    <div id="tabs" style=""  class="col-md-12 panel-title" style="padding: 10px 8px 0px;">
                        <ul>
                            <li><a href="#" id="1" onclick="load_form(this.id)"  class="nav nav-pills panel-title">Basic Details</a></li>
                            <li><a href="#tab-2"  id="2" onclick="load_form(this.id)"  class="nav nav-pills panel-title" style="background-color:#d9edf7">Documents</a></li>
                            <li><a href="#tab-3"  id="3" onclick="load_form(this.id)"  class="nav nav-pills panel-title" style="background-color:#d9edf7">Products and Brands</a></li>
                            <li><a href="#tab-4"  id="4" onclick="load_form(this.id)"  class="nav nav-pills panel-title" style="background-color:#d9edf7">Contact Person</a></li>
                            <li><a href="#tab-4"  id="5" onclick="load_form(this.id)"  class="nav nav-pills panel-title" style="background-color:#d9edf7">Bank Details</a></li>
                        </ul>
                    </div>
                    <?php
                         }    
                    ?>
                </div>
            </div>
        </div>
        <?php
        if($this->session->flashdata('vendor_added')){
            echo "<p style ='text-align: center;line-height: 22px;background: #70e2b3;'>".$this->session->flashdata('vendor_added')."</p>";
        }
        ?>
        <div id="container-1" class="panel-body form_container" style="display:block;padding-top: 0px;">
            <form name="myForm" class="form-horizontal" id ="booking_form" novalidate="novalidate" action="<?php echo base_url() ?>employee/vendor" method="POST" enctype="multipart/form-data">
                <div  class = "panel panel-info">
                    <div class="panel-heading" style="background-color:#ECF0F1"><b>Company Information</b></div>
                        <div class="panel-body">
                            <div>
                                <input style="width:200px;" type="hidden" class="form-control" id="vendor_id"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                      ?>">
                                <?php echo form_error('id'); ?>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('company_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="company_name" class="col-md-3">Company Name*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" id="company_name" name="company_name" value = "<?php
                                                if (isset($query[0]['company_name'])) {
                                                    echo $query[0]['company_name'];
                                                }
                                                ?>" placeholder="Company Name">
                                            <?php echo form_error('company_name'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="name" class="col-md-3">Display Name*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar" id="name" name="name" value = "<?php
                                                if (isset($query[0]['name'])) {
                                                    echo $query[0]['name'];
                                                }
                                                ?>" placeholder="Public Name" onchange="remove_white_space(this.value)">
                                            <?php echo form_error('name'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('address')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="address" class="col-md-3 vertical-align">Address*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control"  name="address" value = "<?php
                                                if (isset($query[0]['address'])) {
                                                    echo $query[0]['address'];
                                                }
                                                ?>" >
                                            <?php echo form_error('address'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="form-group">
                                        <label  for="address" class="col-md-3 vertical-align">Landmark</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control" value = "<?php
                                                if (isset($query[0]['landmark'])) {
                                                    echo $query[0]['landmark'];
                                                }
                                                ?>" name="landmark" >
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-12">
                                
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('state')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="state" class="col-md-3 vertical-align">State*</label>
                                        <div class="col-md-8">
                                            <select class=" form-control" name ="state" id="state" onChange="getDistrict(); getRMs();" placeholder="Select State">
                                                <option disabled="disabled" selected="selected"> Select State</option>
                                                <?php
                                                    foreach ($results['select_state'] as $state) {
                                                    ?>
                                                <option value = "<?php echo $state['state'] ?>"
                                                    <?php
                                                        if (isset($query[0]['state'])) {
                                                            if (strtolower(trim($query[0]['state'])) == strtolower(trim($state['state']))) {
                                                        echo "selected";
                                                        }
                                                        }
                                                        ?>
                                                    >
                                                    <?php echo $state['state']; ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                            <?php echo form_error('state'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('district')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="state" class="col-md-3 vertical-align">District*</label>
                                        <div class="col-md-8">
                                            <select id="district_option" class="district form-control" name ="district" onChange="getPincode()">
                                                <option selected disabled>Select District</option>
                                                <option <?php
                                                    if (isset($query[0]['district'])) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php
                                                    if (isset($query[0]['district'])) {
                                                        echo $query[0]['district'];
                                                    }
                                                    ?></option>
                                            </select>
                                            <?php echo form_error('district'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class ="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="state" class="col-md-3 vertical-align">Pincode</label>
                                        <div class="col-md-8">
                                            <select class="pincode form-control" id="pincode" name ="pincode"  >
                                                <option selected disabled>Select Pincode</option>
                                                <option <?php
                                                    if (isset($query[0]['pincode'])) {
                                                        echo "selected";
                                                    }
                                                    ?>><?php
                                                    if (isset($query[0]['pincode'])) {
                                                        echo $query[0]['pincode'];
                                                    }
                                                    ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('rm')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="rm" class="col-md-3 vertical-align">RM*</label>
                                        <div class="col-md-8">
                                            <select id="rm" class="form-control" name ="rm">
                                                <option selected disabled>Select Regional Manager</option>
                                                
                                            </select>
                                            <?php echo form_error('rm'); ?>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('phone_1')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="phone_1" class="col-md-3 vertical-align">Phone 1*</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="phone_1" name="phone_1" value = "<?php
                                                if (isset($query[0]['phone_1'])) {
                                                    echo $query[0]['phone_1'];
                                                }
                                                ?>">
                                            <?php echo form_error('phone_1'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('phone_2')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="phone_2" class="col-md-3 vertical-align">Phone 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="phone_2" name="phone_2" value = "<?php
                                                if (isset($query[0]['phone_2'])) {
                                                    echo $query[0]['phone_2'];
                                                }
                                                ?>">
                                            <?php echo form_error('phone_2'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('email')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="email" class="col-md-3 vertical-align">Email</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="email" value = "<?php
                                                if (isset($query[0]['email'])) {
                                                    echo $query[0]['email'];
                                                }
                                                ?>">
                                            <?php echo form_error('email'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('company_type')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="company_type" class="col-md-3">Company Type*</label>
                                        <div class="col-md-8">
                                            <select name="company_type" class="form-control">
                                                <option disabled selected >Select Company Type</option>
                                                <option value="Individual" <?php if(isset($query[0]['company_type'])){
                                                    if ($query[0]['company_type'] == "Individual") {
                                                         echo "Selected";
                                                    } }
                                                    ?>>Individual</option>
                                                <option value="Proprietorship Firm" <?php if(isset($query[0]['company_type'])){
                                                    if ($query[0]['company_type'] == "Proprietorship Firm") {
                                                         echo "Selected";
                                                    } }
                                                    ?>>Proprietorship Firm</option>
                                                <option value="Partnership Firm" <?php if(isset($query[0]['company_type'])){
                                                    if ($query[0]['company_type'] == "Partnership Firm") {
                                                         echo "Selected";
                                                    } }
                                                    ?>>Partnership Firm</option>
                                                <option value="Private Ltd Company" <?php if(isset($query[0]['company_type'])){
                                                    if ($query[0]['company_type'] == "Private Ltd Company") {
                                                         echo "Selected";
                                                    } }
                                                    ?>>Private Ltd Company</option>
                                            </select>
                                            <?php echo form_error('company_type'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="panel panel-info">
                        <div class="panel-heading"  style="background-color:#ECF0F1"><b>Vendor Type*</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <label class="checkbox-inline col-md-2">
                                    <input type="checkbox" id="is_sf" <?php if(isset($query[0]['is_sf'])) { if($query[0]['is_sf'] == 1){ echo "checked";}}?> name="is_sf" value="1"><b>Service Center</b>
                                </label>
                                                <?php if(!$saas_module){ ?>
                                <label class="checkbox-inline col-md-2">
                                    <input type="checkbox" id="is_cp" name="is_cp" <?php if(isset($query[0]['is_cp'])) { if($query[0]['is_cp'] == 1){ echo "checked";}}?> value="1"><b>Collection Partner</b>
                                </label>
                                                <?php }?>
                                <label class="checkbox-inline col-md-2">
                                    <input type="checkbox" id="is_wh" name="is_wh" <?php if(isset($query[0]['is_wh'])) { if($query[0]['is_wh'] == 1){ echo "checked";}}?> value="1"><b>Warehouse</b>
                                </label>
                                  <?php if(!$saas_module){ ?>
                                <label class="checkbox-inline col-md-2">
                                    <input type="checkbox" id="is_buyback_gst_invoice" name="is_buyback_gst_invoice" <?php if(isset($query[0]['is_buyback_gst_invoice'])) { if($query[0]['is_buyback_gst_invoice'] == 1){ echo "checked";}}?> value="1"><b>Buyback Invoice on GST</b>
                                </label>
                                  <?php } ?>
                                
                                <label class="checkbox-inline col-md-2">
                                    <input type="checkbox" id="is_engineer" <?php if(isset($query[0]['isEngineerApp'])) { if($query[0]['isEngineerApp'] == 1){ echo "checked";}}?> name="is_engineer" value="1"><b>Engineer App</b>
                                </label>
                            </div>
                        </div>
                </div>
                <div  class = "panel panel-info">
                        <div class="panel-heading"  style="background-color:#ECF0F1"><b>Non Working Days</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <?php foreach ($days as $key => $day) { ?>
                                    <label id="non_working_days">
                                    <input type="checkbox" name="day[]" value ="<?php echo $day; ?>"
                                    <?php
                                        if (isset($selected_non_working_days)) {
                                            if (in_array($day, $selected_non_working_days)){
                                                echo "checked";
                                            }
                                        }
                                        ?> >
                                <?php echo $day; ?> &nbsp;&nbsp;&nbsp;
                                </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                
                    <div class="panel panel-info">
                        <div class="panel-heading" style="background-color:#ECF0F1"><b>Upcountry*</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="upcountry_min_distance_threshold" class="col-md-4 vertical-align">Municipal Limit</label>
                                    <div class="col-md-8">
                                        <input  type="text" id="municipal_limit" class="form-control"  name="min_upcountry_distance" value = "<?php if (isset($query[0]['min_upcountry_distance'])) {
                                            echo $query[0]['min_upcountry_distance'];
                                        } ?>">
                                    </div>
                                    
                                </div>
                            </div>
                            </div>
                        </div>
                   
                        
                    </div>    
                <div class="panel panel-info">
                        <div class="panel-heading" style="background-color:#ECF0F1"><b>Minimum Guarantee</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                <div class="form-group ">
                                    <label for="minimum_guarantee" class="col-md-4 vertical-align">Minimum Guarantee</label>
                                    <div class="col-md-8">
                                        <input  type="text" id="minimum_guarantee" class="form-control"  name="minimum_guarantee_charge" value = "<?php if (isset($query[0]['minimum_guarantee_charge'])) {
                                            echo $query[0]['minimum_guarantee_charge'];
                                        } else { echo "0";} ?>">
                                    </div>
                                    
                                </div>
                            </div>
                            </div>
                        </div>
                   
                        
                    </div>    
                    <div class="clear clear_bottom">
                        <br>
                        <center><input type="Submit" value="<?php
                        if (isset($selected_brands_list)) {
                            echo "Update Basic Details";
                        } else {
                            echo "Save Basic Details";
                        }
                        ?>" class="btn btn-primary" id="submit_btn">
                        <?php echo "<a class='btn btn-small btn-primary cancel' href=" . base_url() . "employee/vendor/viewvendor>Cancel</a>"; ?>
                        </center>
<!--                    </div>-->
                </div>
            
        </form>
     </div>    
<!--page2starts here-->
<div class="clear" style="margin-top: 0px;"></div>
 <div id="container-2" style="display:none;padding-top: 0px;" class="form_container panel-body">
        <form name="myForm" class="form-horizontal" id ="booking_form2" novalidate="novalidate" action="<?php echo base_url() ?>employee/vendor/save_vendor_documents" method="POST" enctype="multipart/form-data">
            <div  class = "panel panel-info">
                <div>
                                <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                      ?>">
                                <?php echo form_error('id'); ?>
                            </div>
                    <div class="panel-heading" style="background-color:#ECF0F1"><b>Registration Details</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-12" style="height: 59px;">
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('name_on_pan')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="name_on_pan"  class="col-md-4 vertical-align">PAN </label>
                                        <div class="col-md-7">
                                            <input placeholder="Name on PAN CARD" type="text" class="form-control blockspacialchar"  id="name_on_pan" name="name_on_pan" value = "<?php
                                                if (isset($query[0]['name_on_pan'])) {
                                                    echo $query[0]['name_on_pan'];
                                                }
                                                ?>">
                                            
                                            <span class="err1"><?php echo form_error('name_on_pan'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-right:14px;">
                                    <div class="form-group  <?php
                                        if (form_error('pan_no')) {
                                            echo 'has-error';
                                        }
                                        ?>">
<!--                                        <label  for="pan_no" class="col-md-4">PAN No.</label>-->
                                        <input type="text" class="form-control blockspacialchar"  id="pan_no" name="pan_no" placeholder="PAN Number" value = "<?php
                                            if (isset($query[0]['pan_no'])) {
                                                echo $query[0]['pan_no'];
                                            }
                                            ?>" style="width:117%">
                                        <span class="err1"><?php echo form_error('pan_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-3" style="margin-left:40px;">
                                    <div class="form-group">
                                        <!--                                        <label  for="pan_file" class="col-md-4">PAN File :</label>-->
                                        <input type="file" class="form-control"  id="pan_file" name="pan_file" value = "<?php
                                            if (isset($query[0]['pan_file'])) {
                                                echo $query[0]['pan_file'];
                                            }
                                            ?>">
                                        <?php echo form_error('pan_file'); ?>
                                    </div>
                                </div>
                                <div class="col-md-1" style="margin-left: 20px;">
                                    <?php
                                        $src = base_url() . 'images/no_image.png';
                                        $image_src = $src;
                                        if (isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])) {
                                            //Path to be changed
                                            $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['pan_file'];
                                            $image_src = base_url().'images/view_image.png';
                                        }
                                        ?>
                                    <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                    <?php if(isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])){?>
                                    <a href="javascript:void(0)" onclick="remove_image('pan_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['pan_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                    <?php }?>
                                </div>
                                <div class="col-md-2">
                                    <div class="checkbox">
                                        <label>
                                        <b style="font-size: 18px;">Not Available</b> 
                                        </label>
                                        <input type="checkbox"  value="0" id="is_pan_doc" name ="is_pan_doc" <?php if(isset($query[0]['is_pan_doc'])){ if($query[0]['is_pan_doc'] == 0){ echo "checked" ;}}?> style="margin-left:16px;zoom:1.5"> 
                                    </div>
                                </div>
                                    </div>
                                <hr style="border: 1px solid;padding: 0px;margin: 10px;border-color: #9e9da7;">
                                <div class="col-md-12">
                                    <div class="gst_1">
                                        <div class="col-md-4" style="height: 39px;">
                                    <div class="form-group <?php
                                        if (form_error('gst_no')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="gst_no" class="col-md-4">GST No.</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control blockspacialchar" style="text-transform: uppercase;"  id ="gst_no" name="gst_no" value = "<?php
                                                if (isset($query[0]['gst_no'])) {
                                                    echo $query[0]['gst_no'];
                                                }
                                                ?>" oninput="validateGSTNo()">
                                            <span class="err1"> <?php echo form_error('gst_no'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-md-4">
                                    <div class="form-group">
                                        <label  for="" class="col-md-4">GST Type</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control"  id ="gst_type" name="gst_type" value="<?php if (isset($query[0]['gst_taxpayer_type'])) {
                                                    echo $query[0]['gst_taxpayer_type'];
                                                    }  ?>" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-md-4">
                                    <div class="form-group">
                                        <label  for="" class="col-md-4">GST Status</label>
                                        <div class="col-md-7">
                                            <input type="text" class="form-control"  id ="gst_status" name="gst_status" value="<?php  if (isset($query[0]['gst_status'])) {
                                                    echo $query[0]['gst_status'];
                                                } ?>" readonly="readonly">
                                            <input type="hidden" class="form-control"  id ="gst_cancelled_date" name="gst_cancelled_date" value="<?php if (isset($query[0]['gst_cancelled_date'])) {
                                                    echo $query[0]['gst_cancelled_date'];
                                                }  ?>">
                                        </div>
                                    </div>
                                </div>
                                        </div>
                                    <div class="gst_2">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('gst_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="gst_file" class="col-md-2 vertical-align" style="margin-right: 5%;">GST File</label>
                                        <div class="col-md-7">
                                            <input type="file" class="form-control"  id="gst_file" name="gst_file" value = "<?php
                                                if (isset($query[0]['gst_file'])) {
                                                    echo $query[0]['gst_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('gst_file'); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['gst_file']) && !empty($query[0]['gst_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['gst_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black" /></a>
                                            <?php if(isset($query[0]['gst_file']) && !empty($query[0]['gst_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('gst_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['gst_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-left:0px;padding-left: 1%">
                                    <div class="checkbox">
                                        <label>
                                        <b style="font-size: 18px;">Not Available</b>   
                                        </label>
                                        <input type="checkbox"  value="0" id="is_gst_doc" name ="is_gst_doc" <?php if(isset($query[0]['is_gst_doc'])){ if($query[0]['is_gst_doc'] == 0){ echo "checked" ;}}?> style="    margin-left: 17px;margin-top: 5px;zoom:1.5;">
                                    </div>
                                </div>
                                        </div>
                            </div>
                                    <hr style="border: 1px solid;padding: 0px;margin: 10px;border-color: #9e9da7;">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('signature_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="signature_file" class="col-md-4 vertical-align" style="width: 22%;">Signature File</label>
                                        <div class="col-md-7">
                                            <input type="file" class="form-control"  name="signature_file" id="signature_file" value = "<?php
                                                if (isset($query[0]['signature_file'])) {
                                                    echo $query[0]['signature_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('signature_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';                                                $image_src = $src;
                                                if (isset($query[0]['signature_file']) && !empty($query[0]['signature_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['signature_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black" /></a>
                                            <?php if(isset($query[0]['signature_file']) && !empty($query[0]['signature_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('signature_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['signature_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                          
                                        </div>  
                                    </div>   
                           
                                </div>
                                 <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('address_proof_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="address_proof_file" class="col-md-3 vertical-align" style="width: 25%;">Address Proof File</label>
                                        <div class="col-md-7" style="    width: 65%;">
                                            <input type="file" class="form-control"  name="address_proof_file" >
                                            <?php echo form_error('address_proof_file'); ?>
                                        </div>
                                        <div class="col-md-2" style="width: 9%;">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['address_proof_file']) && !empty($query[0]['address_proof_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['address_proof_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                            <?php if(isset($query[0]['address_proof_file']) && !empty($query[0]['address_proof_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('address_proof_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['address_proof_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>


                            </div>
                                    <hr style="border: 1px solid;padding: 0px;margin: 10px;border-color: #9e9da7;">
                                 <div class="col-md-12">

                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('contract_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="contract_file" class="col-md-3 vertical-align" style="width: 22%;">Contract File</label>
                                        <div class="col-md-8" style="width: 59%;">
                                            <input type="file" class="form-control"  name="contract_file" value = "<?php
                                                if (isset($query[0]['contract_file'])) {
                                                    echo $query[0]['contract_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('contract_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['contract_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('contract_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['contract_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
            </div>
            <center><input type="Submit" onclick="return validate_documents()" value="<?php
                                    if (isset($selected_brands_list)) {
                                        echo "Update Documents";
                                    } else {
                                        echo "Save Documents";
                                    }
                                    ?>" class="btn btn-primary" id="submit_btn">
                            <?php echo "<a class='btn btn-small btn-primary cancel' href=" . base_url() . "employee/vendor/viewvendor>Cancel</a>"; ?>
                                </center>
   </form>
 </div>   
<div class="clear" style="margin-top:0px;"></div>
<div id="container-3" style="display:none;padding-top: 0px;" class="form_container panel-body">
    <form name="myForm" class="form-horizontal" id ="booking_form3" novalidate="novalidate" action="<?php echo base_url() ?>employee/vendor/save_vendor_brand_mapping" method="POST" enctype="multipart/form-data">            
         <div>
                                <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                      ?>">
                                <?php echo form_error('id'); ?>
                            </div>
        <div  class = "panel panel-info">
                        <div class="panel-heading" style="background-color:#ECF0F1"><b>Appliance</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div  class="form-group <?php
                                    if (form_error('appliance')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <?php foreach ($results['services'] as $key => $appliance) { ?>
                                    <span for="Appliance" class="col-md-3">
                                    <input type="checkbox" class="appliance" onchange="get_brands()" name="appliances[]" value ="<?php echo $appliance->services; ?>"
                                        <?php
                                            if (isset($selected_appliance_list)) {
                                                if (in_array($appliance->services, $selected_appliance_list))
                                                    echo "checked";
                                            }
                                            ?> >
                                    <?php echo $appliance->services; ?> &nbsp;&nbsp;&nbsp;
                                    </span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div  class = "panel panel-info">
                        <div class="panel-heading" style="background-color:#ECF0F1">
                            <b>Exclusive Brands</b>
                            <!--<label class="pull-right">All <input type="checkbox" name="brands_all" id="brands_all" value="All" title="Select All"></label>-->
                        </div>
                        <div class="panel-body brands">
                            <div class="col-md-12">
                                
                            </div>
                        </div>
                    </div>
                    <hr>
                    <p>
                        Note -<br/> 
                        1) Bookings for selected product and brand combination will always be allocated to this SF in his serviceable pincodes, no other brand's booking will be allocated to this SF.<br/>
                        2) If no brand is selected for any particular product, bookings for all brands will be allocated to this SF in his serviceable pincodes.
                    </p>
                    <div class="clear clear_bottom">
                                <br>
                                <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                    ?>">
                                <center><input type="Submit" value="<?php
                                    if (isset($selected_brands_list)) {
                                        echo "Update";
                                    } else {
                                        echo "Save";
                                    }
                                    ?>" class="btn btn-primary" id="submit_btn">
                                                                <?php echo "<a class='btn btn-small btn-primary cancel' href=" . base_url() . "employee/vendor/viewvendor>Cancel</a>"; ?>
                                </center>
                            </div>
                                                            </form>
                        </div>
<div class="clear" style="margin-top:0px;"></div>
    <div id="container-4" style="display:none;padding-top: 0px;" class="form_container panel-body">
        <form name="myForm" class="form-horizontal" id ="booking_form4" novalidate="novalidate" action="<?php echo base_url() ?>employee/vendor/save_vendor_contact_person" method="POST" enctype="multipart/form-data">            
           <div>
                                <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                      ?>">
                                <?php echo form_error('id'); ?>
                            </div>
            <div>
                                <input style="width:200px;" type="hidden" class="form-control"  name="sc_code" value = "<?php
                                    if (isset($query[0]['sc_code'])) {
                                        echo $query[0]['sc_code'];
                                    }
                                      ?>">
                            </div>
               <div>
                                <input style="width:200px;" type="hidden" class="form-control"  name="name" value = "<?php
                                    if (isset($query[0]['name'])) {
                                        echo $query[0]['name'];
                                    }
                                      ?>">
                            </div>
            <div>
                                <input style="width:200px;" type="hidden" class="form-control"  name="company_name" value = "<?php
                                    if (isset($query[0]['company_name'])) {
                                        echo $query[0]['company_name'];
                                    }
                                      ?>">
                            </div>
             <div>
                                <input style="width:200px;" type="hidden" class="form-control"  name="district" value = "<?php
                                    if (isset($query[0]['district'])) {
                                        echo $query[0]['district'];
                                    }
                                      ?>">
                                <input style="width:200px;" type="hidden" class="form-control"  name="already_send_notification" value = "<?php
                                    if (isset($query[0]['primary_contact_email'])) {
                                        echo "1";
                                    }
                                    else{
                                        echo "0";
                                    }
                                      ?>">
                            </div>
            <div  class = "panel panel-info">
                        <div class="panel-heading" style="background-color:#ECF0F1"><b>POC Details</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('primary_contact_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="primary_contact_name" class="col-md-3 vertical-align">Name*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control blockspacialchar"  name="primary_contact_name" value = "<?php
                                                if (isset($query[0]['primary_contact_name'])) {
                                                    echo $query[0]['primary_contact_name'];
                                                }
                                                ?>">
                                            <?php echo form_error('primary_contact_name'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('primary_contact_email')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="primary_contact_email" class="col-md-2 vertical-align">Email*</label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control"  name="primary_contact_email" value = "<?php
                                                if (isset($query[0]['primary_contact_email'])) {
                                                    echo $query[0]['primary_contact_email'];
                                                }
                                                ?>">
                                            <?php echo form_error('primary_contact_email'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('primary_contact_phone_1')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="primary_contact_phone_1" class="col-md-3 vertical-align">Phone 1*</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="primary_contact_phone_1" name="primary_contact_phone_1" value = "<?php
                                                if (isset($query[0]['primary_contact_phone_1'])) {
                                                    echo $query[0]['primary_contact_phone_1'];
                                                }
                                                ?>" >
                                            <?php echo form_error('primary_contact_phone_1'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('primary_contact_phone_2')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="primary_contact_phone_2" class="col-md-3 vertical-align">Phone 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="primary_contact_phone_2" name="primary_contact_phone_2" value = "<?php
                                                if (isset($query[0]['primary_contact_phone_2'])) {
                                                    echo $query[0]['primary_contact_phone_2'];
                                                }
                                                ?>">
                                            <?php echo form_error('primary_contact_phone_2'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
            <div  class = "panel panel-info">
                        <div class="panel-heading" style="background-color:#ECF0F1"><b>Owner Details</b></div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('owner_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="owner_name" class="col-md-3 vertical-align">Name*</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control blockspacialchar"  name="owner_name" value = "<?php
                                                if (isset($query[0]['owner_name'])) {
                                                    echo $query[0]['owner_name'];
                                                }
                                                ?>" >
                                            <?php echo form_error('owner_name'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('owner_email')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="owner_email" class="col-md-3 vertical-align">Email*</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control"  name="owner_email" value = "<?php
                                                if (isset($query[0]['owner_email'])) {
                                                    echo $query[0]['owner_email'];
                                                }
                                                ?>" >
                                            <?php echo form_error('owner_email'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('owner_phone_1')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="owner_phone_1" class="col-md-3 vertical-align">Phone 1*</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="owner_phone_1" name="owner_phone_1" value = "<?php
                                                if (isset($query[0]['owner_phone_1'])) {
                                                    echo $query[0]['owner_phone_1'];
                                                }
                                                ?>">
                                            <?php echo form_error('owner_phone_1'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('owner_phone_2')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="owner_phone_2" class="col-md-3 vertical-align">Phone 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="owner_phone_2" name="owner_phone_2" value = "<?php
                                                if (isset($query[0]['owner_phone_2'])) {
                                                    echo $query[0]['owner_phone_2'];
                                                }
                                                ?>">
                                            <?php echo form_error('owner_phone_2'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('id_proof_1_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="id_proof_1_file" class="col-md-3">ID Proof 1</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control"  name="id_proof_1_file" value = "<?php
                                                if (isset($query[0]['id_proof_1_file'])) {
                                                    echo $query[0]['id_proof_1_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('id_proof_1_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['id_proof_1_file']) && !empty($query[0]['id_proof_1_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['id_proof_1_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($query[0]['id_proof_1_file']) && !empty($query[0]['id_proof_1_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('id_proof_1_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['id_proof_1_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group <?php
                                        if (form_error('id_proof_2_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="id_proof_2_file" class="col-md-3">ID Proof 2</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control"  name="id_proof_2_file" value = "<?php
                                                if (isset($query[0]['id_proof_2_file'])) {
                                                    echo $query[0]['id_proof_2_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('id_proof_2_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['id_proof_2_file']) && !empty($query[0]['id_proof_2_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['id_proof_2_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($query[0]['id_proof_2_file']) && !empty($query[0]['id_proof_2_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('id_proof_2_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['id_proof_2_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
            <div class="clear clear_bottom">
                                <br>
                                <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                    ?>">
                                <center><input type="Submit" value="<?php
                                    if (isset($selected_brands_list)) {
                                        echo "Update Contact Person";
                                    } else {
                                        echo "Save Contact Person";
                                    }
                                    ?>" class="btn btn-primary" id="submit_btn">
                            <?php echo "<a class='btn btn-small btn-primary cancel' href=" . base_url() . "employee/vendor/viewvendor>Cancel</a>"; ?>
                                </center>
            </div>    
        </form>
    </div>
<div class="clear" style="margin-top:0px;"></div>
    <div id="container-5" style="display:none;padding-top: 0px;" class="form_container panel-body">
        <form name="myForm" class="form-horizontal" id ="booking_form5" novalidate="novalidate" action="<?php echo base_url() ?>employee/vendor/save_vendor_bank_details" method="POST" enctype="multipart/form-data">            
                                <input style="width:200px;" type="hidden" class="form-control"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                      ?>">
                                <?php echo form_error('id'); ?>
                    <div  class = "panel panel-info">
                        <div class="panel-heading" style="background-color:#ECF0F1"><b>Bank Details</b></div>
                        <div class="panel-body" id="bank_details">
                            <div class="col-md-12">
                                <div class="alert alert-info alert-dismissible" id="info_div" role="alert" style="display:none">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong id="info_msg"></strong>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('ifsc_code')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="ifsc_code" class="col-md-4" >IFSC Code*</label>
                                        <div class="col-md-6">
                                            <input type="hidden" id="ifsc_validation" name="ifsc_validation" value = "<?php
                                                if (isset($query[0]['ifsc_code_api_response'])) {
                                                    echo $query[0]['ifsc_code_api_response'];
                                                }
                                                ?>">
                                            <input type="text" class="form-control"  name="ifsc_code" id="ifsc_code" style="text-transform: uppercase;" maxlength="11" value = "<?php
                                                if (isset($query[0]['ifsc_code'])) {
                                                    echo $query[0]['ifsc_code'];
                                                }
                                                ?>" oninput="validate_ifsc_code()">
                                            <?php echo form_error('ifsc_code'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div  class="form-group <?php
                                        if (form_error('bank_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="bank_name" class="col-md-4 vertical-align">Bank Name*</label>
                                        <div class="col-md-6">
                                            <select  style="width:195px;" class="form-control" id="bank_name" name="bank_name" onchange="manageAccountNameField(this.value)">
                                                <option selected disabled  >Select Bank</option>
                                                <?php foreach($results['bank_name'] as $key => $value) { ?> 
                                                <option value="<?php echo $value['bank_name']; ?>" 
                                                        <?php if(isset($query[0]['bank_name']) && $query[0]['bank_name'] === $value['bank_name']){ echo 'selected';}?>> <?php echo $value['bank_name'] ;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('account_type')) {
                                            echo 'account_type';
                                        }
                                        ?>">
                                        <label for="account_type" class="col-md-4 vertical-align">Account Type*</label>
                                        <div class="col-md-6">
                                            <select class="form-control" id="account_type" name="account_type">
                                                <option selected disabled>Account Type</option>
                                                <option value="Saving" <?php if(isset($query[0]['account_type']) && $query[0]['account_type'] === 'Saving'){echo 'selected';}?>>Saving</option>
                                                <option value="Current" <?php if(isset($query[0]['account_type']) && $query[0]['account_type'] === 'Current'){echo 'selected';}?>>Current</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('bank_account')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="bank_account" class="col-md-4 vertical-align">Bank Account*</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control allowNumericWithDecimal" <?php if (isset($query[0]['bank_account']) && (strtolower($this->session->userdata('user_group')) !== 'admin')) {echo "disabled";}?>  id = "bank_account" name="bank_account"  value = "<?php
                                                if (isset($query[0]['bank_account'])) {
                                                    echo $query[0]['bank_account'];
                                                }
                                                ?>">
                                            <?php echo form_error('bank_account'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('beneficiary_name')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label for="beneficiary_name" class="col-md-4">Beneficiary Name*</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control blockspacialchar"  name="beneficiary_name" id = "beneficiary_name" value = "<?php
                                                if (isset($query[0]['beneficiary_name'])) {
                                                    echo $query[0]['beneficiary_name'];
                                                }
                                                ?>">
                                            <?php echo form_error('beneficiary_name'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group <?php
                                        if (form_error('cancelled_cheque_file')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="cancelled_cheque_file" class="col-md-4" style="vertical-align:middle">Cancelled Cheque File*</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control"  name="cancelled_cheque_file" id = "cancelled_cheque_file" value = "<?php
                                                if (isset($query[0]['cancelled_cheque_file'])) {
                                                    echo $query[0]['cancelled_cheque_file'];
                                                }
                                                ?>">
                                            <?php echo form_error('cancelled_cheque_file'); ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($query[0]['cancelled_cheque_file']) && !empty($query[0]['cancelled_cheque_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/" . $query[0]['cancelled_cheque_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" /></a>
                                            <?php if(isset($query[0]['cancelled_cheque_file']) && !empty($query[0]['cancelled_cheque_file'])){?>
                                            <a href="javascript:void(0)" onclick="remove_image('cancelled_cheque_file',<?php echo $query[0]['id']?>,'<?php echo $query[0]['cancelled_cheque_file']?>')" class="btn btn-sm btn-primary" title="Remove Image" style="margin-left: 50px;margin-top: -46px;">  <i class="fa fa-times" aria-hidden="true"></i></a>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                        <?php if($this->session->userdata('user_group')=== 'admin'){ ?>
                                    <label for="is_bank_details_verified" class="col-md-3" style="padding-left:2.5%">Verified/Not Verified </label>
                                        <div class="col-md-3">
                                        <input type="checkbox" value="1" name="is_verified" id="is_bank_details_verified" <?php if(isset($query[0]['is_verified']) && $query[0]['is_verified'] == '1') { ?>checked<?php } ?> style="zoom:1.5;">
                                        <?php }else { ?>
                                        <input type="hidden" name="is_verified" id="is_bank_details_verified" value="<?php if(isset($query[0]['is_verified'])) { echo $query[0]['is_verified']; }?>">
                                        <?php } ?>
                                        
                                    
                            </div>
                            </div>
                                </div>
        </div>
                        <center><input type="submit" onclick="return validate_bank_details()" value="Update Bank Details" class="btn btn-primary" id="submit_btn">
                            <?php echo "<a class='btn btn-small btn-primary cancel' href=" . base_url() . "employee/vendor/viewvendor>Cancel</a>"; ?>
                                </center>
                        </div>
                                
        </form>
</div>
</div>
</div>
<!--Validations here-->
<?php if($this->session->userdata('checkbox')){$this->session->unset_userdata('checkbox');}?>
<!--Validation for page1-->
<script type="text/javascript">

    $(document).ready(function(){
        getRMs();
        get_brands();
    });

function manageAccountNameField(value){
        document.getElementById("bank_account").disabled = false;
    }
    //Adding select 2 in Dropdowns
    $("#district_option").select2();
    $("#state").select2();
    $("#pincode").select2();
    $("#bank_name").select2();

    function getDistrict() {
     var state = $("#state").val();
     var district = $(".district").val();
     $.ajax({
       type: 'POST',
       url: '<?php echo base_url(); ?>employee/vendor/getDistrict/1',
       data: {state: state, district: district},
       success: function (data) {
        // console.log(data);
         $(".district").html(data);
         if (district != "") {
           getPincode();
         }
       }
     });
    }
        function getRMs() {
        var state = $("#state").val();
        if(state != ''){
        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/vendor/getRMs',
          data: {state: state},
          success: function (data) {
            $("#rm").html(data);
          }
        });
        }
    }
                function getPincode() {
      var district = $(".district").val();
      var pincode = $(".pincode").val();
      $.ajax({
        type: 'POST',
        url: '<?php echo base_url(); ?>employee/vendor/getPincode/1',
        data: {pincode: pincode, district: district},
        success: function (data) {
          $(".pincode").html(data);
       }
     });
    }
    
                $(function () {
    var state = $("#state").val();
                    if (state != "") {
        getDistrict();
    }
    });
    
    function get_brands() {
        var appliance = [];
        var service_center_id = $('#vendor_id').val();
        
        $. each($(".appliance:checked"), function(){
            appliance.push($(this).val());
        });
 
        if(appliance.length > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/vendor/get_brands',
                data: {appliance: appliance, service_center_id: service_center_id},
                success: function (data) {
                    $('.brands').html(data);
                }
            });
        } else {
            $('.brands').html('Please select appliance.');
        }
    }
</script>
<!--page 1 validations begin here-->
    
<script type="text/javascript">
    (function ($, W, D)
    {
    var JQUERY4U = {};
    
    JQUERY4U.UTIL =
    {
                    setupFormValidation: function ()
    {
    //form validation rules
    $("#booking_form").validate({
    rules: {
        company_name: "required",
        name: "required",
        address: "required",
        district: "required",
        company_type:"required",
        rm: "required",
        phone_1: {
            required: true,
            minlength: 10,
            number: true
        },
        phone_2: {
            minlength: 10,
            number: true
        },
        state: "required",
        
        email: {
            email: true
        },
        municipal_limit: {
            number: true
        },
    },
    messages: {
        company_name: "Please enter Company Name",
        name: "Please enter Public Name",
        address: "Please enter Address",
        district: "Please Select District",
        rm: "Please Select RM",
        state: "Please Select State",
        phone_1: "Please enter Phone Number",
        phone_2: "Please fill correct phone number",
        
        email: "Please fill correct email",
        
        
    },
        submitHandler: function (form) {
            
        var municipal_limit = $("#municipal_limit").val();
            if(Number(municipal_limit) < 1){
            alert("Please Add Municipal Limit");
            return false;}
        if($('#is_sf').is(':checked')==0 && $('#is_cp').is(':checked')==0 && $('#is_wh').is(':checked')==0 && $('#is_buyback_gst_invoice').is(':checked')==0){
            alert("Please Select Atleast One Checkbox of Service Center OR Collection Partner OR Warehouse OR Buyback Invoice on GST");
            return false;
        }
        form.submit();
        }
    });
    }
    };
    
    //when the dom has loaded setup form validation rules
        $(D).ready(function ($) {
    JQUERY4U.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    
    
           
</script> 
<!--page 1 validations end here-->
<!--page 2 validations begin-->
<script type="text/javascript">
    var gstRegExp = /^[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[0-9]{1}[a-zA-Z]{1}[a-zA-Z0-9]{1}/;
    $.validator.addMethod('gstregex', function (value, element, param) {
                return this.optional(element) || gstRegExp.test( value);
            }, 'Please enter Valid GST Number'); 
     function validate_documents(){
            if($('#is_pan_doc').is(":checked")){
               if($('#pan_no').val()!== '' && $('#name_on_pan').val() != ''){
                   alert('Please Enter PAN Details or Tick "Not Available" checkbox');
                   return false;
               }
            }else{
                if($('#pan_no').val() == '' && $('#name_on_pan').val() == ''){
                   alert('Please Enter PAN Details or Tick "Not Available" checkbox');
                   return false;
               }
                //checking case when pan number is empty and pan name is enterd
                else if($('#pan_no').val() == '' && $('#name_on_pan').val() != ''){
                   alert('Please add Pan No along with Pan Name');
                   return false;
               }
               //checking case when pan number is less than 6 and greater than 10 and pan name is enterd
               else if($('#pan_no').val().length !== 10 && $('#name_on_pan').val() != ''){
                   alert('Please add valid 10 digit pan number');
                   return false;
               }
               //checking case when pan number 10 and pan name is enterd but panfile is not uploaded
               <?php if(empty($query[0]['pan_file'])){ ?>
                           else if($('#pan_no').val().length === 10 && $('#name_on_pan').val() != '' && $('#pan_file').val()== 0){
                           alert('Please upload pan file also');
                   return false;
               } <?php }?>
            }
            //Check for GST  no.
            if($('#is_gst_doc').is(":checked")){ 
               if($('#gst_no').val() != ''){
                   alert('Please Enter valid GST Number or Tick "Not Available" checkbox');
                   return false;
               }
            }else{ 
                var is_gst_file = <?php if(isset($query[0]['gst_file']) && !empty($query[0]['gst_file'])){ echo '1';}else{echo '0';}?>;
                if($('#gst_no').val() == ''){
                   alert('Please Enter GST Number or Tick "Not Available" checkbox');
                   return false;
                }
                else if($('#gst_no').val().length === '15'){
                   alert('Please Enter Valid GST Number');
                   return false;
                }
                else if($('#gst_type').val() == '' || $('#gst_status').val() == ''){
                   alert('Please Enter Valid GST Number or Tick "Not Available" checkbox');
                   return false;
                }
                else if($('#gst_no').val() != '' && $('#gst_file').get(0).files.length === 0 && is_gst_file === 0){
                   alert("Please Upload GST File");
                   return false;
                }
            }
             var is_signature_file = <?php if(isset($query[0]['signature_file']) && !empty($query[0]['signature_file'])){ echo '1';}else{echo '0';}?>;
             if(is_signature_file == 0){
                var is_signature_file = $('#signature_file').get(0).files.length;
             }
             if(!(is_signature_file) && ($('#is_gst_doc').is(":checked")) ){
                   alert('Please Update Signature file');
                   return false;
    }
    }
</script>

<!--page 2 validations end-->

<!--page 3 validations - none-->

<!--page 4 validations begin here-->
<script type="text/javascript">
    (function ($, W, D)
    {
    var JQUERY4U = {};
    
    JQUERY4U.UTIL =
    {
                    setupFormValidation: function ()
    {
    //form validation rules
    $("#booking_form4").validate({
    rules: {
        primary_contact_phone_1: {
            required: true,
            minlength: 10,
            number: true
        },
        primary_contact_phone_2: {
            number: true
        },
        owner_phone_1: {
            required: true,
            minlength: 10,
            number: true
        },
        owner_phone_2: {
            number: true
        },
        primary_contact_name: "required",
        owner_name: "required",
        primary_contact_email: {
            required: true
        },
        owner_email: {
            required: true
        },
    },
    messages: {
        primary_contact_phone_1: "Please fill correct phone number",
        primary_contact_phone_2: "Please fill correct phone number",
        owner_phone_1: "Please fill correct phone number",
        owner_phone_2: "Please fill correct phone number",
        primary_contact_name: "Please fill Name",
        owner_name: "Please fill Name",
        primary_contact_email: "Please fill correct email",
        owner_email: "Please fill correct email",
        
    },
        submitHandler: function (form) {
        form.submit();
        }
    });
    }
    };
    
    //when the dom has loaded setup form validation rules
        $(D).ready(function ($) {
    JQUERY4U.UTIL.setupFormValidation();
    });
    
    })(jQuery, window, document);
    
    
</script>    
<!--page 4 validations end here-->

<script type="text/javascript">
    function load_form(tab_id){
       total_div  = document.getElementsByClassName('form_container').length;
       for(var i =1;i<=total_div;i++){
           if(i != tab_id){
             document.getElementById("container-"+i).style.display='none';
             document.getElementById(i).style.background='#d9edf7';
            }
            else{
                document.getElementById("container-"+i).style.display='block';
                document.getElementById(i).style.background='#fff';
            }
       }
       
    }
</script>
<script type="text/javascript">
    <?php if((isset($query[0]['is_verified']) && !empty($query[0]['is_verified'])) && $this->session->userdata('user_group') !='admin'){?>
        $('#bank_details').find('input').attr('readonly', true);
    <?php } ?>
    $(".allowNumericWithDecimal").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40) || e.ctrlKey) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    $(document).ready(function () {
        var charReg = /^[0-9a-zA-Z,.()+\/\s-]*$/;
        $('.blockspacialchar').focusout(function () {
            var inputVal = $(this).val();

            if (!charReg.test(inputVal)) {
                alert("Spacial Characters are not allowed");
                $(this).css({'border-color' : 'red'});
                $('#submit_btn').attr('disabled','disabled');
            }else{
                $(this).css({'border-color' : '#ccc'});
                $('#submit_btn').removeAttr('disabled');
            }

        });
    });
    function remove_white_space(name){
        newValue = name.replace(/\s+$/, '');
        $('#name').val(newValue);
    }
    
    $(document).ready(function () {
        var regxp = /^(\s*|\d+)$/;
        $('.verigymobileNumber').focusout(function () {
            var inputVal = $(this).val();

            if (!regxp.test(inputVal)) {
                alert("Please Enter Valid Phone Number");
                $(this).css({'border-color' : 'red'});
            }else{
                $(this).css({'border-color' : '#ccc'});
            }

        });
    }); 
    
  
</script>
<!--function to remove image-->
<script>
    function remove_image(type,vendor_id,file_name){
            var c  = confirm('Do you want to permanently remove photo?');
            if(c){
             $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url(); ?>employee/vendor/remove_image',
                        data: {type: type, id: vendor_id,file_name:file_name},
                        success: function (data) {
                             location.reload();
    //                             console.log(data);
                            }
                    });
                 }else{
                    return false;
                 }
        }
        function validate_bank_details(){
                cheque_final = 1
                    var cheque_already = <?php if(isset($query[0]['cancelled_cheque_file']) && !empty($query[0]['cancelled_cheque_file'])){ echo '1';}else{echo '0';}?>;
                    var cheque = $('#cancelled_cheque_file').val();      
                    if(cheque == null || cheque == ''){
                        if(cheque_already == 0){
                            cheque_final = 0;
                        }
                }
            var bank_name = $('#bank_name').val();
            var account_type = $('#account_type').val();
            var bank_account = $('#bank_account').val();
            var ifsc_code = $('#ifsc_code').val();
            var beneficiary = $('#beneficiary_name').val();
            if(!$('#ifsc_code').val().match('^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9]+$')){
                alert("Please enter alphanumeric value for IFSC Code");
                return false;
            }
            
            if($('#ifsc_code').val().length < 11){
                alert("Please enter 11 alphanumeric number");
                return false;
            }
            
            if($("#ifsc_validation").val() != null && $("#ifsc_validation").val() == ""){
               alert("Incorrect IFSC Code");
               return false; 
            }
            
            if((cheque_final) && !(bank_name == null || bank_name == '') && !(account_type == null || account_type == '') && !(bank_account == null || bank_account == '') && 
                    !(ifsc_code == null || ifsc_code == '') && !(beneficiary == null || beneficiary == '')){
               return true;
                }
                else{
                    alert("Please Fill all banks related fields");
                    return false;
                }
             
        }
    function validateGSTNo(){
        var gstin = $("#gst_no").val();
        gstin = gstin.trim().toUpperCase();
        $("#gst_no").val(gstin);
        var vendor_id="";
        if($("#vendor_id").val()){
            vendor_id = "/"+$("#vendor_id").val()+"/vendor";
        }
        if(gstin.length == '15'){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/vendor/check_GST_number/'+gstin+vendor_id,
                success: function (response) {
                    response = JSON.parse(response);
                    if(response.status_cd != '0'){
                        $("#gst_type").val(response.dty);
                        $("#gst_status").val(response.sts);
                        $("#gst_cancelled_date").val(response.cxdt);
                        $("#gst_type").attr("readonly", "readonly");
                        $("#gst_status").attr("readonly", "readonly");
                        if(response.dty !== 'Regular' || response.sts !== 'Active'){
                            alert('Filled GST number detail - \n GST Type - '+response.dty+' \n GST Status - '+ response.sts);
                        }
                    }
                    else{
                        $("#gst_type, #gst_status").val("");
                        if(response.errorMsg){
                           alert(response.errorMsg);
                        }
                        else if(response.error.message){
                            if(response.error.error_cd == '<?php echo INVALID_GSTIN; ?>'){
                                alert("<?php echo INVALID_GSTIN_MSG; ?>");
                            }else{
                                alert("Error occured while checking GST number try again");
                            }
                        }
                        else{
                           alert("API unable to work contact tech team!"); 
                        }
                    }
                }
            });
        }
        else{
            $("#gst_type, #gst_status").val("");
        }
    }
    
    function validate_ifsc_code(){
        var ifsc_code = $("#ifsc_code").val();
        if(ifsc_code.length == '11'){
            var first4char =  ifsc_code.substring(0, 4);
            var first5char =  ifsc_code.substring(4, 5);
            if(!first4char.match(/^[A-Za-z]+$/)){
                alert("In IFSC code first four digit should be Charecter");
                return false;
            }
            else if(first5char != "0"){
                alert("In IFSC code fifth digit should be 0");
                return false;
            }
            else{
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/vendor/validate_ifsc_code',
                    data: {ifsc_code:ifsc_code, entity_type:"vendor", entity_id:$("#vendor_id").val()},
                    success: function (response) {
                        response = response.trim();
                        if(response=='"Not Found"'){
                            $("#ifsc_validation").val("");
                            $("#info_div").css("display", "none");
                            alert("Incorrect IFSC Code");
                        }
                        else{
                            if(IsJsonString(response)){
                                var bank_data = JSON.parse(response);
                                $("#ifsc_validation").val(JSON.stringify(bank_data));
                                $("#info_div").css("display", "block");
                                $("#info_msg").html("You have entered valid IFSC code  - <br/> Bank Name = "+bank_data.BANK.toLowerCase()+" <br/> Branch = "+bank_data.BRANCH.toLowerCase()+" <br/> City = "+bank_data.CITY.toLowerCase()+" <br/> State = "+bank_data.STATE.toLowerCase()+" <br/> Address = "+bank_data.ADDRESS.toLowerCase());
                            }
                            else{
                                $("#ifsc_validation").val("");
                                alert("IFSC code verification API fail. Please contact tech team");
                            }
                        }
                    }
                });
            }
        }
        else{
           $("#ifsc_validation").val("");
        }
    }
    
    function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
    
    $('#brands_all').click(function(){
        if($(this).prop('checked'))
        {
            $('input[name="brands[]"]').prop("checked", true);
        }
        else
        {
            $('input[name="brands[]"]').prop("checked", false);
        }
    });

</script>

<style>
    .panel {
        border-radius:0px !important;
    }

<?php if(empty($is_editable) && !empty($query[0]['id'])) {  ?>
    
    #container-1, .form-control, .select2 {
        pointer-events:none; 
    }
    .form-control, .select2 { 
        background-color:#e6ede8;
    }
    
    .select2-container--default .select2-selection--single {
         background-color:#e6ede8;
    }
    
    #submit_btn, .cancel{
        display:none;
    }
    a[title="Remove Image"] {
         display:none;
    }
  .select2-container .select2-selection--multiple {
    background-color: #e6ede8;
}
<?php } ?>

</style>    

<script>
    function edit_form() {
        $('#container-1, .form-control, .select2, #submit_btn').css('pointer-events', 'auto');
        $('.form-control, .select2, .select2-container--default .select2-selection--single, .select2-container .select2-selection--multiple').css('background-color', 'white');
        $('#submit_btn, .cancel, a[title="Remove Image"]').css('display', 'inline-block');
    }
</script>