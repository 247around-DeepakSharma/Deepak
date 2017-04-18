<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="panel panel-info" style="margin-top:10px;">
            <div class="panel-heading"><center><h5><b><?php if(!empty($challan_data)){echo "Edit Challan";}else{echo 'Add Challan';}?></b></h5></center></div>
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
                            <div>
                                <input type="hidden" name="id" value = "<?php
                                    if (isset($challan_data[0]['id'])) {
                                        echo $challan_data[0]['id'];
                                    }
                                    ?>">
                                <?php echo form_error('id'); ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="challan_type">Challan Type</label>
                                        <select class="form-control" name="challan_type">
                                            <option selected disabled>Select Challan Type</option>
                                            <option value="ST" <?php if(isset($challan_data[0]['type']) && $challan_data[0]['type'] == 'ST' ){ ?>selected <?php }?>>Service Tax</option>
                                            <option value="VAT" <?php if(isset($challan_data[0]['type']) && $challan_data[0]['type'] == 'VAT' ){ ?>selected <?php }?>>VAT</option>
                                            <option value="TDS" <?php if(isset($challan_data[0]['type']) && $challan_data[0]['type'] == 'TDS' ){ ?>selected <?php }?>>TDS</option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('challan_type'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="serial_no">Challan Serial No</label>
                                        <input type="text" class="form-control" id="serial_no" name="serial_no" placeholder="Enter Serial No" value ="<?php
                                                if (isset($challan_data[0]['serial_no'])) {
                                                    echo $challan_data[0]['serial_no'];
                                                }
                                                ?>">
                                        <span class="text-danger"><?php echo form_error('serial_no'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="cin_no">Challan CIN Number</label>
                                        <input type="text" class="form-control" id="cin_no" name="cin_no" placeholder="Enter CIN No" value ="<?php
                                                if (isset($challan_data[0]['cin_no'])) {
                                                    echo $challan_data[0]['cin_no'];
                                                }
                                                ?>">
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
                                            <option selected disabled>Select Bank</option>
                                            <option value="ICICI" <?php if(isset($challan_data[0]['bank_name']) && $challan_data[0]['bank_name'] == 'ICICI' ){ ?>selected <?php }?>>ICICI</option>
                                            <option value="HDFC" <?php if(isset($challan_data[0]['bank_name']) && $challan_data[0]['bank_name'] == 'HDFC' ){ ?>selected <?php }?>>HDFC</option>
                                            <option value="PNB" <?php if(isset($challan_data[0]['bank_name']) && $challan_data[0]['bank_name'] == 'PNB' ){ ?>selected <?php }?>>PNB</option>
                                            <option value="SBI" <?php if(isset($challan_data[0]['bank_name']) && $challan_data[0]['bank_name'] == 'SBI' ){ ?>selected <?php }?>>SBI</option>
                                            <option value="OBC" <?php if(isset($challan_data[0]['bank_name']) && $challan_data[0]['bank_name'] == 'OBC' ){ ?>selected <?php }?>>OBC</option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('bank_name'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter Amount" value ="<?php
                                                if (isset($challan_data[0]['amount'])) {
                                                    echo $challan_data[0]['amount'];
                                                }
                                                ?>">
                                        <span class="text-danger"><?php echo form_error('amount'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="paid_by">Paid by</label>
                                        <select class="form-control" name="paid_by">
                                            <option selected disabled>Select Paid By</option>
                                            <option value="Blackmelon" <?php if(isset($challan_data[0]['paid_by']) && $challan_data[0]['paid_by'] == 'Blackmelon' ){ ?>selected <?php }?>>Blackmelon</option>
                                            <option value="Others" <?php if(isset($challan_data[0]['paid_by']) && $challan_data[0]['paid_by'] == 'Others' ){ ?>selected <?php }?>>Others</option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('paid_by'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="row">
                                        <div class="col-md-8 col-sm-8">
                                            <div class="form-group">
                                                <label for="challan_file">Upload challan file</label>
                                                <input type="file" class="form-control" id="challan_file" name="challan_file" value = "<?php
                                                if (isset($challan_data[0]['challan_file'])) {
                                                    echo $challan_data[0]['challan_file'];
                                                }
                                                ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4" style="margin-top:23px;">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($challan_data[0]['challan_file']) && !empty($challan_data[0]['challan_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$challan_data[0]['challan_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                            <?php //if(isset($challan_data[0]['challan_file']) && !empty($challan_data[0]['challan_file'])){?>
                                            <!--<a href="javascript:void(0)" onclick="remove_image('challan_file',<?php //echo $challan_data[0]['id']?>,'<?php //echo $challan_data[0]['challan_file']?>')" class="btn btn-sm btn-primary" title="Remove Image">  <i class="fa fa-times" aria-hidden="true"></i></a> -->
                                            <?php //}?>
                                        </div>
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
                                    <div class="row">
                                        <div class="col-md-8 col-sm-8">
                                            <div class="form-group">
                                                <label for="challan_file">Upload Annexure File</label>
                                                <input type="file" class="form-control" id="challan_file" name="annexure_file" value = "<?php
                                                if (isset($challan_data[0]['annexure_file'])) {
                                                    echo $challan_data[0]['annexure_file'];
                                                }
                                                ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4" style="margin-top:23px;">
                                            <?php
                                                $src = base_url() . 'images/no_image.png';
                                                $image_src = $src;
                                                if (isset($challan_data[0]['annexure_file']) && !empty($challan_data[0]['annexure_file'])) {
                                                    //Path to be changed
                                                    $src = "https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/vendor-partner-docs/".$challan_data[0]['annexure_file'];
                                                    $image_src = base_url().'images/view_image.png';
                                                }
                                                ?>
                                            <a href="<?php echo $src?>" target="_blank"><img src="<?php echo $image_src ?>" width="35px" height="35px" style="border:1px solid black;margin-left:-5px;" /></a>
                                            <?php //if(isset($challan_data[0]['challan_file']) && !empty($challan_data[0]['challan_file'])){?>
                                            <!--<a href="javascript:void(0)" onclick="remove_image('challan_file',<?php //echo $challan_data[0]['id']?>,'<?php //echo $challan_data[0]['challan_file']?>')" class="btn btn-sm btn-primary" title="Remove Image">  <i class="fa fa-times" aria-hidden="true"></i></a> -->
                                            <?php //}?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="remarks">Remarks</label>
                                        <textarea id="remarks" class="form-control" placeholder="Remarks" name="remarks"><?php if (isset($challan_data[0]['remarks'])) {echo $challan_data[0]['remarks'];}?></textarea>
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
                            startDate: '<?php if(isset($challan_data[0]['from_date'])){echo $challan_data[0]['from_date'];}else{echo date("Y/m/01", strtotime("-1 month"));}?>',
                            endDate: '<?php if(isset($challan_data[0]['to_date'])){echo $challan_data[0]['to_date'];}else{echo date("Y/m/01", strtotime("-1 month"));} ?>'
                });
                $('input[name="tender_date"]').daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        locale: {
                                format: 'YYYY/MM/DD'
                            },
                            startDate: '<?php if(isset($challan_data[0]['challan_tender_date'])){echo $challan_data[0]['challan_tender_date'];}else{echo date("Y/m/01", strtotime("-1 month"));} ?>'
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