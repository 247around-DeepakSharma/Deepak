<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style type="text/css">
    .btn-group-sm>.btn, .btn-sm {padding:1px 5px !important}
</style>
<div id="page-wrapper">
    <input style="width:200px;" type="hidden" class="form-control" id="vendor_id"  name="id" value = "<?php
                                    if (isset($query[0]['id'])) {
                                        echo $query[0]['id'];
                                    }
                                      ?>">
    <div class="row">
        <div class="panel-body">
            <div style="background: #EEEEEE; border-radius: 10px;width: 500px;padding-left:20px;font-size: 110%;margin-bottom: 10px;"><b>NOTE:</b> <i>Please contact us in case you want to change any data.</i></div>
            <div  class = "panel panel-info">
                <div class="panel-heading"><b>Company Information</b></div>
                <div class="panel-body form-horizontal">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div  class="form-group">
                                <label  for="company_name" class="col-md-3">Name</label>
                                <div class="col-md-8">
                                    <input  type="text" class="form-control" id="company_name" name="company_name" value = "<?php
                                    if (isset($query[0]['company_name'])) {
                                        echo $query[0]['company_name'];
                                    }
                                    ?>" placeholder="Company Name" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_type" class="col-md-3">Company Type</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control"  name="company_type" value = "<?php
                                    if (isset($query[0]['company_type'])) {
                                        echo $query[0]['company_type'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="rm" class="col-md-3">RM</label>
                                <div class="col-md-8">
                                    <select id="rm" class="form-control" name ="rm" disabled="">
                                        <option selected disabled>Select Regional Manager</option>
                                        <?php
                                        foreach ($results['employee_rm'] as $value) {
                                            ?>
                                            <option value = "<?php echo $value['id'] ?>"
                                            <?php
                                            if (isset($rm[0]['agent_id']) && $rm[0]['agent_id'] == $value['id']) {
                                                echo "selected";
                                            }
                                            ?>
                                                    >
                                                        <?php echo $value['full_name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div  class="form-group">
                                <label  for="address" class="col-md-3 ">Address</label>
                                <div class="col-md-8">
                                    <input  type="text" class="form-control"  name="address" value = "<?php
                                    if (isset($query[0]['address'])) {
                                        echo $query[0]['address'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div  class="form-group">
                                <label  for="address" class="col-md-3">Landmark</label>
                                <div class="col-md-8">
                                    <input  type="text" class="form-control" value = "<?php
                                    if (isset($query[0]['landmark'])) {
                                        echo $query[0]['landmark'];
                                    }
                                    ?>" name="landmark" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state" class="col-md-3">District</label>
                                <div class="col-md-8">
                                    <select id="district_option" class="district form-control" name ="district" disabled="">
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class ="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state" class="col-md-3">State</label>
                                <div class="col-md-8">
                                    <select class=" form-control" name ="state" id="state" placeholder="Select State" disabled="">
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
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="state" class="col-md-3">Pincode</label>
                                <div class="col-md-8">
                                    <select class="pincode form-control" id="pincode" name ="pincode"  disabled="">
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
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_1" class="col-md-3">Phone 1</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="phone_1" name="phone_1" value = "<?php
                                    if (isset($query[0]['phone_1'])) {
                                        echo $query[0]['phone_1'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div  class="form-group">
                                <label  for="phone_2" class="col-md-3">Phone 2</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="phone_2" name="phone_2" value = "<?php
                                    if (isset($query[0]['phone_2'])) {
                                        echo $query[0]['phone_2'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="col-md-3">Email</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control"  name="email" value = "<?php
                                    if (isset($query[0]['email'])) {
                                        echo $query[0]['email'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                   
                </div>
            </div>
            <div  class = "panel panel-info">
                <div class="panel-heading "><b>POC Details</b></div>
                <div class="panel-body form-horizontal">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="primary_contact_name" class="col-md-3">Name</label>
                                <div class="col-md-8">
                                    <input  type="text" class="form-control"  name="primary_contact_name" value = "<?php
                                    if (isset($query[0]['primary_contact_name'])) {
                                        echo $query[0]['primary_contact_name'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primary_contact_email" class="col-md-2">Email</label>
                                <div class="col-md-8">
                                    <input  type="text" class="form-control"  name="primary_contact_email" value = "<?php
                                    if (isset($query[0]['primary_contact_email'])) {
                                        echo $query[0]['primary_contact_email'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="primary_contact_phone_1" class="col-md-3">Phone 1</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="primary_contact_phone_1" name="primary_contact_phone_1" value = "<?php
                                    if (isset($query[0]['primary_contact_phone_1'])) {
                                        echo $query[0]['primary_contact_phone_1'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="primary_contact_phone_2" class="col-md-3">Phone 2</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="primary_contact_phone_2" name="primary_contact_phone_2" value = "<?php
                                    if (isset($query[0]['primary_contact_phone_2'])) {
                                        echo $query[0]['primary_contact_phone_2'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div  class = "panel panel-info">
                <div class="panel-heading"><b>Owner Details</b></div>
                <div class="panel-body form-horizontal">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner_name" class="col-md-3">Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control"  name="owner_name" value = "<?php
                                    if (isset($query[0]['owner_name'])) {
                                        echo $query[0]['owner_name'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner_email" class="col-md-3">Email</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control"  name="owner_email" value = "<?php
                                    if (isset($query[0]['owner_email'])) {
                                        echo $query[0]['owner_email'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="owner_phone_1" class="col-md-3">Phone 1</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="owner_phone_1" name="owner_phone_1" value = "<?php
                                    if (isset($query[0]['owner_phone_1'])) {
                                        echo $query[0]['owner_phone_1'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="owner_phone_2" class="col-md-3">Phone 2</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="owner_phone_2" name="owner_phone_2" value = "<?php
                                    if (isset($query[0]['owner_phone_2'])) {
                                        echo $query[0]['owner_phone_2'];
                                    }
                                    ?>" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="id_proof_1_file" class="col-md-3">ID Proof 1</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  name="id_proof_1_file" style="display: none;" >
                                    <!-- <?php
                                    if (isset($query[0]['id_proof_1_file'])) {
                                        echo $query[0]['id_proof_1_file'];
                                    }
                                    ?> --> 
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    if (isset($query[0]['id_proof_1_file']) && !empty($query[0]['id_proof_1_file'])) {
                                        $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['id_proof_1_file'];
                                        ?>
                                        <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" title="Click to view" /></a>
                                        <?php
                                    } else {
                                        $src = base_url() . 'images/no_image.png';
                                        ?>
                                        <img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" />
                                    <?php }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="id_proof_2_file" class="col-md-3">ID Proof 2</label>
                                <div class="col-md-6">
                                    <input type="file" class="form-control"  name="id_proof_2_file" style="display: none;">
                                    <!-- <?php
                                    if (isset($query[0]['id_proof_2_file'])) {
                                        echo $query[0]['id_proof_2_file'];
                                    }
                                    ?> -->
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    if (isset($query[0]['id_proof_2_file']) && !empty($query[0]['id_proof_2_file'])) {
                                        $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['id_proof_2_file'];
                                        ?>
                                        <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" title="Click to view" /></a>

                                        <?php
                                    } else {
                                        $src = base_url() . 'images/no_image.png';
                                        ?>
                                        <img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" />
                                    <?php }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div  class = "panel panel-info">
                <div class="panel-heading"><b>Registration Details</b></div>
                <div class="panel-body form-horizontal">
                 <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4" style="margin-right:21px;">
                            <div class="form-group">
                                <label  for="name_on_pan" class="col-md-4">PAN </label>
                                <div class="col-md-7">
                                    <input placeholder="Name on PAN CARD" type="text" class="form-control"  id="name_on_pan" name="name_on_pan" value = "<?php
                                    if (isset($query[0]['name_on_pan'])) {
                                        echo $query[0]['name_on_pan'];
                                    }
                                    ?>" disabled="">
                                </div>

                            </div>
                        </div>
                        <div class="col-md-3" style="margin-right:14px;">
                            <div class="form-group">
                                <input type="text" class="form-control"  id="pan_no" name="pan_no" placeholder="PAN Number" value = "<?php
                                if (isset($query[0]['pan_no'])) {
                                    echo $query[0]['pan_no'];
                                }
                                ?>"  disabled="">

                            </div>
                        </div>
                        <div class="col-md-1" style="display:none">
                            <div class="form-group">

                                <input type="file" class="form-control"  name="pan_file" style="display: none;">
                                <!-- <?php
                                if (isset($query[0]['pan_file'])) {
                                    echo $query[0]['pan_file'];
                                }
                                ?> -->
                            </div>
                        </div>
                        <div class="col-md-1">
                            <?php
                            if (isset($query[0]['pan_file']) && !empty($query[0]['pan_file'])) {
                                $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['pan_file'];
                                ?>
                                <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" title="Click to view" /></a>

                                <?php
                            } else {
                                $src = base_url() . 'images/no_image.png';
                                ?>
                                <img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" />
                            <?php }
                            ?>
                        </div>
                        <div class="col-md-2" style="display: none;">
                            <div class="checkbox">
                                <label>
                                    <b style="font-size: 18px;">Not Available</b> 

                                </label>
                                <input type="checkbox"  value="0" id="is_pan_doc" name ="is_pan_doc" <?php
                                if (isset($query[0]['is_pan_doc'])) {
                                    if ($query[0]['is_pan_doc'] == 0) {
                                        echo "checked";
                                    }
                                }
                                ?> style="margin-left:16px;" disabled=""> 

                            </div>
                        </div>
                    </div>
                </div>
                        <!--<div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label  for="cst_no" class="col-md-4">CST No.</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control"  name="cst_no" id="cst_no" value = "<?php
//                                        if (isset($query[0]['cst_no'])) {
//                                            echo $query[0]['cst_no'];
//                                        }
                                        ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label  for="cst_file" class="col-md-4">CST File</label>
                                    <div class="col-md-7">
                                        <input type="file" class="form-control"  name="cst_file" value = "<?php
//                                        if (isset($query[0]['cst_file'])) {
//                                            echo $query[0]['cst_file'];
//                                        }
                                        ?>" disabled="">
                                    </div>
                                    <div class="col-md-1">
                                        <?php
//                                        if (isset($query[0]['cst_file']) && !empty($query[0]['cst_file'])) {
//                                            $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['cst_file'];
                                            ?>
                                            <a href="<?php //echo $src ?>" target="_blank"><img src="<?php //echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black" title="Click to view" /></a>

                                            <?php
//                                        } else {
//                                            $src = base_url() . 'images/no_image.png';
                                            ?>
                                            <img src="<?php //echo $src ?>" width="35px" height="35px" style="border:1px solid black" />
                                        <?php //}
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="margin-left:60px;">
                                <div class="checkbox">
                                    <label>
                                        <b style="font-size: 18px;">Not Available</b> 
                                    </label>
                                    <input type="checkbox"  value="0" id="is_cst_doc" name ="is_cst_doc" <?php
//                                    if (isset($query[0]['is_cst_doc'])) {
//                                        if ($query[0]['is_cst_doc'] == 0) {
//                                            echo "checked";
//                                        }
//                                    }
                                    ?> style="margin-top:5px; margin-left:24px;" disabled=""> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">

                            <div class="col-md-4">
                                <div class="form-group">

                                    <label  for="tin_no" class="col-md-4">TIN/VAT No.</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control"  id="tin_no" name="tin_no" value = "<?php
//                                        if (isset($query[0]['tin_no'])) {
//                                            echo $query[0]['tin_no'];
//                                        }
                                        ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label  for="tin_file" class="col-md-4">TIN/VAT File</label>
                                    <div class="col-md-7">
                                        <input type="file" class="form-control"  name="tin_file" value = "<?php
//                                        if (isset($query[0]['tin_file'])) {
//                                            echo $query[0]['tin_file'];
//                                        }
                                        ?>" disabled="">
                                    </div>
                                    <div class="col-md-1">
                                        <?php
//                                        if (isset($query[0]['tin_file']) && !empty($query[0]['tin_file'])) {
//                                            $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['tin_file'];
                                            ?>
                                            <a href="<?php //echo $src ?>" target="_blank"><img src="<?php //echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black" title="Click to view" /></a>
                                            <?php
//                                        } else {
//                                            $src = base_url() . 'images/no_image.png';
                                            ?>
                                            <img src="<?php //echo $src ?>" width="35px" height="35px" style="border:1px solid black" />
                                        <?php //}
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="margin-left:60px;">
                                <div class="checkbox">
                                    <label>
                                        <b style="font-size: 18px;">Not Available</b>  
                                    </label>
                                    <input type="checkbox"  value="0" id ="is_tin_doc" name ="is_tin_doc" <?php
//                                    if (isset($query[0]['is_tin_doc'])) {
//                                        if ($query[0]['is_tin_doc'] == 0) {
//                                            echo "checked";
//                                        }
//                                    }
                                    ?> style="margin-top:5px; margin-left:24px;" disabled=""> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label  for="service_tax_no" class="col-md-4">Service Tax No.</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control"  id ="service_tax_no" name="service_tax_no" value = "<?php
//                                        if (isset($query[0]['service_tax_no'])) {
//                                            echo $query[0]['service_tax_no'];
//                                        }
                                        ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="service_tax_no" class="col-md-4">Tax File</label>
                                    <div class="col-md-7">
                                        <input type="file" class="form-control"  name="service_tax_file" value = "<?php
//                                        if (isset($query[0]['service_tax_file'])) {
//                                            echo $query[0]['service_tax_file'];
//                                        }
                                        ?>" disabled="">
                                    </div>
                                    <div class="col-md-1">
                                        <?php
//                                        if (isset($query[0]['service_tax_file']) && !empty($query[0]['service_tax_file'])) {
//                                            $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['service_tax_file'];
                                            ?>
                                            <a href="<?php //echo $src ?>" target="_blank"><img src="<?php //echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black" title="Click to view" /></a>

                                            <?php
//                                        } else {
//                                            $src = base_url() . 'images/no_image.png';
                                            ?>
                                            <img src="<?php //echo $src ?>" width="35px" height="35px" style="border:1px solid black" />
                                        <?php //}
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="margin-left:60px;">
                                <div class="checkbox">
                                    <label>
                                        <b style="font-size: 18px;">Not Available</b>   
                                    </label>
                                    <input type="checkbox"  value="0" id="is_st_doc" name ="is_st_doc" <?php
//                                    if (isset($query[0]['is_st_doc'])) {
//                                        if ($query[0]['is_st_doc'] == 0) {
//                                            echo "checked";
//                                        }
//                                    }
                                    ?> style="    margin-left: 24px;margin-top: 5px;" disabled="">
                                </div>
                            </div>
                        </div>-->
                        
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-4" style='margin-right: 8px;'>
                                <div class="form-group">
                                    <label  for="service_tax_no" class="col-md-4">GST No.</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control"  id ="gst_no" name="gst_no" value = "<?php
                                        if (isset($query[0]['gst_no'])) {
                                            echo $query[0]['gst_no'];
                                        }
                                        ?>" disabled="" >
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label for="service_tax_no" class="col-md-4">GST File</label>
                                    <div class="col-md-7">
                                        <input type="file" class="form-control"  name="gst_file" style="display: none;" >
                                        <!-- <?php
                                        if (isset($query[0]['gst_file'])) {
                                            echo $query[0]['gst_file'];
                                        }
                                        ?> -->
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                                        if (isset($query[0]['gst_file']) && !empty($query[0]['gst_file'])) {
                                            $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$query[0]['gst_file'];
                                            ?>
                                            <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black" title="Click to view" /></a>

                                            <?php
                                        } else {
                                            $src = base_url() . 'images/no_image.png';
                                            ?>
                                            <img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black" />
                                        <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="margin-left:60px;display: none;">
                                <div class="checkbox">
                                    <label>
                                        <b style="font-size: 18px;">Not Available</b>   
                                    </label>
                                    <input type="checkbox"  value="0" id="is_st_doc" name ="is_st_doc" <?php
                                    if (isset($query[0]['is_gst_doc'])) {
                                        if ($query[0]['is_gst_doc'] == 0) {
                                            echo "checked";
                                        }
                                    }
                                    ?> style="    margin-left: 24px;margin-top: 5px;" disabled="">
                                </div>
                        </div>
                    </div>
                                    </div>
                                <div class="row">
                                    <div class="col-md-12">
                    <div class="col-md-4" style='margin-right: 8px;'>
                        <div class="form-group">
                            <label  for="contract_file" class="col-md-4">Contract File</label>
                            <div class="col-md-6">
                                <input type="file" class="form-control"  name="contract_file" style="display: none;" >
                                <!-- <?php
                                if (isset($query[0]['contract_file'])) {
                                    echo $query[0]['contract_file'];
                                }
                                ?> --> 
                            </div>
                            <div class="col-md-6" >
                                <?php
                                if (isset($query[0]['contract_file']) && !empty($query[0]['contract_file'])) {
                                    $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['contract_file'];
                                    ?>
                                    <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" title="Click to view" /></a>
                                    <?php
                                } else {
                                    $src = base_url() . 'images/no_image.png';
                                    ?>
                                    <img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;" />
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                                <div class="col-md-4">
                        <div class="form-group">
                            <label for="address_proof_file" class="col-md-4">Address Proof File</label>
                            <div class="col-md-5">
                                <input type="file" class="form-control"  name="address_proof_file" disabled="" style="display: none;">
                            </div>
                            <div class="col-md-6">
                                <?php
                                if (isset($query[0]['address_proof_file']) && !empty($query[0]['address_proof_file'])) {
                                    $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['address_proof_file'];
                                    ?>
                                    <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo base_url().'images/view_image.png'; ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" title="Click to view" /></a>
                                    <?php
                                } else {
                                    $src = base_url() . 'images/no_image.png';
                                    ?>
                                    <img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;" />
                                <?php } ?>
                            </div>
                            </div>
                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="signature_file" class="col-md-4 vertical-align" style="width: 22%;">Signature File</label>
                                                <div class="col-md-7">
                                                    <input type="file" class="form-control" style="display: none;" disabled=""/>

                                                </div> 


                                                <div class="col-md-6">
                                                    <?php
                                                    $src = base_url() . 'images/no_image.png';
                                                    $image_src = $src;
                                                    if (isset($query[0]['signature_file']) && !empty($query[0]['signature_file'])) {
                                                        //Path to be changed
                                                        $src = "https://s3.amazonaws.com/" . BITBUCKET_DIRECTORY . "/vendor-partner-docs/" . $query[0]['signature_file'];
                                                        $image_src = base_url() . 'images/view_image.png';
                                                    }
                                                    ?>
                                                    <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;    margin-left: 1px;" /></a>
                                                </div>  
                                            </div>  

                                        </div>
                                    </div>         
                                </div>
                </div>
                <div  class = "panel panel-info">
                    <div class="panel-heading"><b>Bank Details</b></div>
                    <div class="panel-body form-horizontal">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div  class="form-group">
                                    <label  for="bank_name" class="col-md-4">Bank Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="bank_name" value = "<?php
                                        if (isset($query[0]['bank_name'])) {
                                            echo $query[0]['bank_name'];
                                        }
                                        ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_type" class="col-md-4">Account Type</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="account_type" value = "<?php
                                        if (isset($query[0]['account_type'])) {
                                            echo $query[0]['account_type'];
                                        }
                                        ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bank_account" class="col-md-4">Bank Account</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="bank_account" value = "<?php
                                        if (isset($query[0]['bank_account'])) {
                                            echo $query[0]['bank_account'];
                                        }
                                        ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ifsc_code" class="col-md-4">IFSC Code</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="ifsc_code" value = "<?php
                                        if (isset($query[0]['ifsc_code'])) {
                                            echo $query[0]['ifsc_code'];
                                        }
                                        ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="beneficiary_name" class="col-md-4">Beneficiary Name</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control"  name="beneficiary_name" value = "<?php
                                        if (isset($query[0]['beneficiary_name'])) {
                                            echo $query[0]['beneficiary_name'];
                                        }
                                        ?>" disabled="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label  for="cancelled_cheque_file" class="col-md-4">Cancelled Cheque File</label>
                                    <div class="col-md-5" style='display:none'>
                                        <input type="file" class="form-control"  name="cancelled_cheque_file" style="display: none;" >
                                        <!-- <?php
                                        if (isset($query[0]['cancelled_cheque_file'])) {
                                            echo $query[0]['cancelled_cheque_file'];
                                        }
                                        ?>
 -->                                    </div>
                                    <div class="col-md-1">
                                        <?php
                                        if (isset($query[0]['cancelled_cheque_file']) && !empty($query[0]['cancelled_cheque_file'])) {
                                            $src = "https://s3.amazonaws.com/bookings-collateral/vendor-partner-docs/" . $query[0]['cancelled_cheque_file'];
                                            ?>
                                            <a href="<?php echo $src ?>" target="_blank"><img src="<?php echo base_url().'images/view_image.png' ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" title="Click to view" /></a>
                                            <?php
                                        } else {
                                            $src = base_url() . 'images/no_image.png';
                                            ?>
                                            <img src="<?php echo $src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;" />
                                        <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div  class = "panel panel-info">
                    <div class="panel-heading"><b>Appliance</b></div>
                    <div class="panel-body form-horizontal">
                        <div class="col-md-12">
                            <div  class="form-group">
                                <?php foreach ($results['services'] as $key => $appliance) { ?>
                                    <label for="Appliance" >
                                        <input type="checkbox" class="appliance" name="appliances[]" onchange="get_brands()" value ="<?php echo $appliance->services; ?>"
                                        <?php
                                        if (isset($selected_appliance_list)) {
                                            if (in_array($appliance->services, $selected_appliance_list))
                                                echo "checked";
                                        }
                                        ?> disabled="">
                                        <?php echo $appliance->services; ?> &nbsp;&nbsp;&nbsp;
                                    </label>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div  class = "panel panel-info">
                    <div class="panel-heading"><b>Brands</b></div>
                    <div class="panel-body form-horizontal brands">
                        <div class="col-md-12">
                            <?php //foreach ($results['brands'] as $key => $brands) {
                                ?>
                                <!--<label for="Brand" >-->
                                    <!--<input type="checkbox" name="brands[]" value ="<?php //echo $brands->brand_name; ?>"-->
                                    <?php
                                    //if (isset($selected_brands_list)) {
                                      //  if (in_array($brands->brand_name, $selected_brands_list))
                                        //    echo "checked";
                                    //}
                                    ?> 
                                <!--disabled="">-->
                                    <?php //echo $brands->brand_name; ?> &nbsp;&nbsp;&nbsp;
                                <!--</label>-->
                            <?php //} ?>
                        </div>
                    </div>
                </div>
                <div  class = "panel panel-info">
                    <div class="panel-heading"><b>Non Working Days</b></div>
                    <div class="panel-body form-horizontal">
                        <div class="col-md-12">
                            <?php foreach ($days as $key => $day) { ?>
                                <label for="non_working_days" >
                                    <input type="checkbox" name="day[]" value ="<?php echo $day; ?>"
                                    <?php
                                    if (isset($selected_non_working_days)) {
                                        if (in_array($day, $selected_non_working_days))
                                            echo "checked";
                                    }
                                    ?> disabled="">
                                    <?php echo $day; ?> &nbsp;&nbsp;&nbsp;
                                </label>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div style="float:left;width:90%;" class="form-group">
                </div>
                <div class="col-md-12">
                    <div class="col-md-7 pull-right"><?php echo "<a class='btn btn-md btn-primary' href=" . base_url() . "employee/service_centers/pending_booking>BACK</a>"; ?></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function(){
        get_brands();
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
                data: {appliance: appliance, service_center_id: service_center_id,is_sf:1},
                success: function (data) {
                    $('.brands').html(data);
                }
            });
        } else {
            $('.brands').html('Please select appliance.');
        }
    }
    </script>