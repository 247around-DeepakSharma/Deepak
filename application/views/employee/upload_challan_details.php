<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="panel panel-info" style="margin-top:10px;">
            <div class="panel-heading"><center><h5><b>Upload Challan Details</b></h5></center></div>
            <div class="panel-body">
                <div class="form-container">
                    <?php
                    if ($this->session->flashdata('success_msg')) {
                        echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('success_msg').'</strong>
                    </div>';
                    }
                    if ($this->session->flashdata('error_msg')) {
                        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>'.$this->session->flashdata('error_msg').'</strong>
                    </div>';
                    }
                    ?>
                    
                    <section>
                        <form role="form" name="challan_form" id="challan_form" action="<?php echo base_url() ?>employee/invoice/process_challan_upload_form" method="POST" enctype="multipart/form-data" >
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="challan_type">Challan Type</label>
                                        <select class="form-control" name="challan_type">
                                            <option value="ST">Service Tax</option>
                                            <option value="VAT">VAT</option>
                                            <option value="TDS">TDS</option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('challan_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="serial_no">Challan Serial No</label>
                                        <input type="text" class="form-control" id="serial_no" name="serial_no" placeholder="Enter Serial No">
                                        <span class="text-danger"><?php echo form_error('serial_no'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="cin_no">Challan CIN Number</label>
                                        <input type="text" class="form-control" id="cin_no" name="cin_no" placeholder="Enter CIN No">
                                        <span class="text-danger"><?php echo form_error('cin_no'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="tender_date">Challan Tender Date</label>
                                        <input type="text" class="form-control" id="tender_date" name="tender_date">
                                        <span class="text-danger"><?php echo form_error('tender_date'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <select class="form-control" id="bank_name" name="bank_name">
                                            <option value="ICICI">ICICI</option>
                                            <option value="HDFC">HDFC</option>
                                            <option value="PNB">PNB</option>
                                            <option value="SBI">SBI</option>
                                            <option value="OBC">OBC</option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('bank_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter Amount">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="paid_by">Paid by</label>
                                        <select class="form-control" name="paid_by">
                                            <option value="Blackmelon">Blackmelon</option>
                                            <option value="Others">Others</option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('paid_by'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="challan_file">Upload challan file</label>
                                        <input type="file" class="form-control" id="challan_file" name="challan_file">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="daterange">Select Challan Period</label>
                                        <input type="text" class="form-control" id="daterange" name="daterange">
                                        <span class="text-danger"><?php echo form_error('daterange'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea id="remarks" class="form-control" placeholder="Remarks" name="remarks"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-center" style="margin-top: 10px;">
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-success" id="submit">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>    
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<script type="text/javascript">
$(function() {
                $('input[name="daterange"]').daterangepicker({
                    locale: {
                                format: 'YYYY/MM/DD'
                            },
                            startDate: '<?php echo date("Y/m/01", strtotime("-1 month")) ?>',
                            endDate: '<?php echo date("Y/m/01") ?>'
                });
                $('input[name="tender_date"]').daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        locale: {
                                format: 'YYYY/MM/DD'
                            },
                            startDate: '<?php echo date("Y/m/01", strtotime("today")) ?>'
                });

            });
</script>
<script>
    
$.validator.addMethod("regx", function (value, element, regexpr) {
        return regexpr.test(value);
    }, "Please enter a valid Phone Number.");
(function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#challan_form").validate({
                rules: {
                    challan_type: "required",
                    serial_no: {
                        required: true,
                        number: true,
                        regx:/^[a-zA-Z0-9]+$/
                    },
                    cin_no: {
                        required: true,
                        number: true,
                        regx:/^[a-zA-Z0-9]+$/
                    },
                    tender_date: "required",
                    bank_name: "required",
                    amount: {
                        required: true,
                        number: true
                    },
                    paid_by: "required",
                    challan_file: "required",
                    daterange: "required"

                },
                messages: {
                    challan_type: "Please Select Challan Type",
                    serial_no: "Please Enter Valid Serial Number",
                    cin_no: "Please Enter Valid CIN Number",
                    tender_date: "Please Select Tender Date",
                    bank_name: "Please Select Bank Name",
                    amount: "Please Enter Valid Amount",
                    paid_by: "Please Select Paid By",
                    challan_file: "Please Select File",
                    daterange: "Please Select DateRange"

                },
                submitHandler: function(form) {
                    form.submit();
                }

            });
        }
    };


    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);
</script>