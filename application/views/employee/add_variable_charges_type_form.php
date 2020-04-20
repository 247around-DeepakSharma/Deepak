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
        <div class="panel-heading">Add Variable Charges Type</div>
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
                <form name="myForm" class="form-horizontal" id ="charges_form" novalidate="novalidate" action="<?php echo base_url()?>employee/accounting/process_charges_type"  method="POST" enctype="multipart/form-data">
                    <div class="row">
                         <div class="col-md-12">
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('charges_type')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="name" class="col-md-4">Charges Type* </label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" id="charges_type" name="charges_type" value = "" placeholder="Enter Charges Type">
                                        <?php echo form_error('charges_type'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('description')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="hsn_code" class="col-md-4">Description* </label>
                                    <div class="col-md-8">
                                        <input  type="text" class="form-control" id="description" name="description" value = "" placeholder="Enter description">
                                        <?php echo form_error('description'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('hsn_code')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="hsn_code" class="col-md-4">HSN Code* </label>
                                    <div class="col-md-8">
                                        <input  type="number" class="form-control" id="hsn_code" name="hsn_code" value = "<?php echo HSN_CODE; ?>" placeholder="Enter HSN Code">
                                        <?php echo form_error('hsn_code'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div  class="form-group <?php
                                    if (form_error('gst_rate')) {
                                        echo 'has-error';
                                    }
                                    ?>">
                                    <label  for="gst_rate" class="col-md-4">GST Rate* </label>
                                    <div class="col-md-8">
                                        <input  type="number" class="form-control" id="gst_rate" name="gst_rate" value = "18" placeholder="Enter GST Rate">
                                        <?php echo form_error('gst_rate'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <center>
                                <input type="submit" id="submit_btn" name="submit_btn" class="btn btn-info" value="Submit"/>
                            </center>
                        </div>
                    </div>
                </form>
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
                    charges_type: "required",
                    hsn_code: "required",
                    gst_rate: "required",
                    description: "required",
                },
                messages: {
                    charges_type: "Please enter charges type",
                    hsn_code: "Please enter HSN code",
                    gst_rate: "Please enter GST rate",
                    description: "Please enter description",
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

    