<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<style>
    #charges_form .form-group label.error {
    color: #FB3A3A;
    display: inline-block;;
    padding: 0;
    text-align: left;
    width: 250px;
    margin: 0px;
    }
</style>
<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading">Add Variable Charges <a href="<?php echo base_url(); ?>employee/accounting/add_charges_type" class="btn btn-primary btn-sm pull-right">Add Charges Type</a></div>
            <div class="panel-body">
            <div class="row">
                 <?php
                if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
                }
                if ($this->session->userdata('error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
                }
                ?>
                <form name="myForm" class="form-horizontal" id ="charges_form" novalidate="novalidate" action="<?php echo base_url()?>employee/accounting/process_variable_charges"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                         <div class="col-md-12">
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('vendor_partner')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="vendor_partner" class="col-md-4">Entity Type*</label>
                                    <div class="col-md-8">
                                        <select id="vendor_partner" onchange="get_vendor_partner_list()" name="vendor_partner" class="form-control">
                                            <option selected disabled>Select Entity</option>
                                            <option value="<?php echo _247AROUND_PARTNER_STRING; ?>"><?php echo _247AROUND_PARTNER_STRING; ?></option>
                                            <option value="<?php echo _247AROUND_SF_STRING; ?>"><?php echo _247AROUND_SF_STRING; ?></optio>
                                        </select>
                                        <?php echo form_error('vendor_partner'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('vendor_partner_id')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="vendor_partner_id" class="col-md-4">Entity*</label>
                                    <div class="col-md-8">
                                        <select id="vendor_partner_id" name="vendor_partner_id" class="form-control">
                                            <option selected disabled>Select Entity</option>
                                        </select>
                                        <?php echo form_error('name'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="col-md-12">
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('charges_type')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="charges_type" class="col-md-4">Charges Type*</label>
                                        <div class="col-md-8">
                                            <select  id="charges_type" name="charges_type" class="form-control">
                                            <option selected disabled>Select Charges Type</option>
                                               <?php foreach ($charges_type as $charges_type) { ?>
                                                <option value="<?php echo $charges_type['id'] ?>"><?php echo $charges_type['name'] ?></option> 
                                                <?php } ?>
                                            </select>
                                            <?php echo form_error('charges_type'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        if (form_error('fixed_charges')) {
                                            echo 'has-error';
                                        }
                                        ?>">
                                        <label  for="fixed_charges" class="col-md-4">Fixed Charges</label>
                                        <div class="col-md-8">
                                            <input  type="number" class="form-control" id="fixed_charges" name="fixed_charges" value = "" placeholder="Enter Fixed Charges">
                                            <?php echo form_error('fixed_charges'); ?>
                                        </div>
                                    </div>
                                </div>
<!--                                <div class="col-md-6">
                                    <div  class="form-group <?php
                                        //if (form_error('description')) {
                                            //echo 'has-error';
                                        //}
                                        ?>">
                                        <label  for="name" class="col-md-4">Description </label>
                                        <div class="col-md-8">
                                            <input  type="text" class="form-control" id="description" name="description" value = "" placeholder="Enter Description">
                                            <?php //echo form_error('description'); ?>
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                        <div class="form-group col-md-12">
                            <center>
                                <input type="submit" id="submit_btn" name="submit_btn" class="btn btn-info" value="Submit"/>
                                <a href="<?php echo base_url(); ?>employee/accounting/add_variable_charges" class="btn btn-warning" style="display:none" id="clear_btn">Clear</a>
                                <input type="hidden" id="variable_charges_id" name="variable_charges_id" value="">
                            </center>
                        </div>
                    </div>
                </form>
            </div>
            <div class="clear"></div>
            <hr>
            <div class="col-md-12" style="padding: 0px;">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Vendor / Partner</th>
                            <th>Name</th>
                            <th>Charges Type</th>
                            <th>Fixed Charges</th>
                            <th>HSN Code</th>
                            <th>GST Rate</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i = 1;
                            foreach ($charges as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $value['entity_type']; ?></td>
                            <td entity_id='<?php echo $value['entity_id']; ?>'><?php echo $value['name']; ?></td>
                            <td><?php echo $value['charges_type']; ?></td>
                            <td><?php echo $value['fixed_charges']; ?></td>
                            <td><?php if(!empty($value['hsn_code'])){ echo $value['hsn_code']; } ?></td>
                            <td><?php if(!empty($value['gst_rate'])){ echo $value['gst_rate']; } ?></td>
                            <td><?php if(!empty($value['description'])){ echo $value['description']; } ?></td>
                            <td><button type="button" class="btn btn-info btn-xs" onclick="update_charge(<?php echo $value['id']; ?>, this)">Update</button></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php if($this->session->userdata('error')){ $this->session->unset_userdata('error'); } ?>
<?php if($this->session->userdata('success')){ $this->session->unset_userdata('success');  } ?>
<script type="text/javascript">
    (function ($, W, D){
        var JQUERY4U = {};
        JQUERY4U.UTIL = { setupFormValidation: function (){
                $("#charges_form").validate({
                rules: {
                    vendor_partner: "required",
                    vendor_partner_id: "required",
                    charges_type: "required",
                },
                messages: {
                    vendor_partner: "Please select entity type",
                    vendor_partner_id: "Please select entity",
                    charges_type: "Please enter charges type",
                },
                submitHandler: function (form) {
                    form.submit();
                }
                });
            }
        };
        $(D).ready(function ($) {
            JQUERY4U.UTIL.setupFormValidation();
        });
    })(jQuery, window, document);
</script> 
<script>
    
    $("#vendor_partner").select2();
    $("#vendor_partner_id").select2();
    $("#charges_type").select2();
    function get_vendor_partner_list(){
        var par_ven = $("#vendor_partner").val();
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/invoice/getPartnerOrVendor/' + par_ven,
                data: {vendor_partner_id: "", invoice_flag: 1},
                success: function (data) {
                    $("#vendor_partner_id").html(data);
                    $("#vendor_partner_id option[value='All']").remove();
            }
        });
    }
    
    function update_charge(id, button){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/accounting/getVendorPartnerVariableChargesType',
            data: {type:$(button).closest('tr').find('td').eq(3).text()},
            success: function (data) {
                $("#charges_type").html(data);
            }
        });
    
        $("#vendor_partner").val($(button).closest('tr').find('td').eq(1).text()).trigger('change');
        $("#fixed_charges").val($(button).closest('tr').find('td').eq(4).text());
        $("#percentage_charge").val($(button).closest('tr').find('td').eq(5).text());
        $("#hsn_code").val($(button).closest('tr').find('td').eq(6).text());
        $("#gst_rate").val($(button).closest('tr').find('td').eq(7).text());
        $("#description").val($(button).closest('tr').find('td').eq(8).text());
        $("#clear_btn").show();
        $("#variable_charges_id").val(id);
        setTimeout(function(){
            $("#charges_type").val($("#charges_type option:selected" ).val()).trigger('change');
            $("#vendor_partner_id").val($(button).closest('tr').find('td').eq(2).attr('entity_id')).trigger('change');
        }, 500);
    }
</script>
