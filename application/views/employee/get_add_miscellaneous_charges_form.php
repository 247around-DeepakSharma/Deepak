<div id="page-wrapper" >
    <div class="container col-md-12" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" >
            <div class="panel-heading" >
                <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
        <?php if($this->session->userdata('success')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('success') . '</strong>
            </div>';
            }
            ?>
        <?php if($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('error') . '</strong>
            </div>';
            }
            ?>
    </div>
    <div class="container col-md-12" >
        <div class="panel panel-info" >
            <div class="panel-heading" >Add Miscellaneous Charges</div>
            <div class="panel-body">
                <div class="col-md-12">
                    <form class="form-horizontal" enctype="multipart/form-data" id ="misc_charges" action="<?php echo base_url();?>employee/service_centre_charges/process_add_misc_charges"  method="POST" >
                        <div class="col-md-12" >
                            <div class="static-form-box">
                                <div class="col-md-3" >
                                    <div class="form-group col-md-12  ">
                                        <label for="product serices">Product/Service * </label>
                                        <select name="misc[0][product_or_services]" class="form-control" id="product_or_services_0" required>
                                            <option value="Service">Service</option>
                                            <option value="Product">Product</option>
                                        </select>
                                        
                                    </div>
                                </div>
                                <div class="col-md-5" >
                                    <div class="form-group col-md-12  ">
                                        <label for="Service Description">Description * </label>
                                        <input type="text" class="form-control" id="description_0" placeholder="Enter Description" name="misc[0][description]" value = "" required>
                                    </div>
                                </div>
                                <div class="col-md-4 " style="width:17%;">
                                    <div class="form-group col-md-12  ">
                                        <label for="Vendor Amounts">Vendor Charge </label>
                                        <input type="number" step=".02" class="form-control" id="vendor_charge_0" placeholder="Vendor Amount" name="misc[0][vendor_charge]" value = "0" >
                                    </div>
                                </div>
                                <div class="col-md-4 " style="width:17%;">
                                    <div class="form-group col-md-12  ">
                                        <label for="partner Charge">Partner Charge </label>
                                        <input type="number" step=".02" class="form-control" id="partner_charge_0" placeholder="Enter Partner Charge" name="misc[0][partner_charge]" value = "0" >
                                    </div>
                                </div>
                                <div class="col-md-1 text-center" style="margin-top:20px;">
                                    <div class="form-group col-md-12  ">
                                        <button type="button" class="btn btn-default addButton"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="dynamic-form-box hide" id="template">
                                <div class="clone">
                                    <div class="col-md-3" >
                                    <div class="form-group col-md-12  ">
                                        <label for="product serices">Product/Service * </label>
                                        <select class="form-control" id="product_or_services" >
                                            <option value="Service">Service</option>
                                            <option value="Product">Product</option>
                                        </select>
                                        
                                    </div>
                                </div>
                                    <div class="col-md-5" >
                                        <div class="form-group col-md-12  ">
                                            <label for="Service Description">Description * </label>
                                            <input type="text" class="form-control" id="description" placeholder="Enter Description"  value = "">
                                        </div>
                                    </div>
                                    <div class="col-md-4 " style="width:17%;">
                                        <div class="form-group col-md-12  ">
                                            <label for="Vendor Amounts">Vendor Charge </label>
                                            <input type="number" step=".02" class="form-control" id="vendor_charge" placeholder="Vendor Amount" value = "0" >
                                        </div>
                                    </div>
                                    <div class="col-md-4 " style="width:17%;">
                                        <div class="form-group col-md-12  ">
                                            <label for="partner Charge">Partner Charge </label>
                                            <input type="number" step=".02" class="form-control" id="partner_charge" placeholder="Enter Partner Charge" value = "0" >
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-center" style="margin-top:20px;">
                                        <div class="form-group col-md-12  ">
                                            <button type="button" class="btn btn-default removeButton"><i class="fa fa-minus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" >
                                    <div class="form-group col-md-12  ">
                                        <label for="booking ID">Booking ID * </label>
                                        <input type="text" class="form-control" id="booking_id" placeholder="Enter Booking ID" name="booking_id" value = "" required>
                                    </div>
                                </div>
                            <div class="col-md-5 ">
                                <div class="form-group col-md-12  ">
                                    <label for="remarks">Remarks * </label>
                                    <input type="text" class="form-control" id="remarks" placeholder="Enter Remarks" name="remarks" value = "" required>
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group col-md-11  ">
                                    <label for="Approval Email File">Approval/Email File * </label>
                                    <input type="file" class="form-control" id="approval_misc_charges_file" name="approval_misc_charges_file" required>
                                    <input type="hidden" name="file_required" value="1" >
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="form-group col-md-11  ">
                                    <label for="Purchase Invoice File">Purchase Invoice File </label>
                                    <input type="file" class="form-control" id="purchase_invoice_file" name="purchase_invoice_file" accept="image/* , application/pdf" >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-5 col-md-4 col-md-offset-5">
                                    <button type="submit" class="btn btn-success"  id="submit_btn">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    partIndex = 0;
    $('#misc_charges').on('click', '.addButton', function () {
           partIndex++;
           var $template = $('#template'),
               $clone = $template
                       .clone()
                       .removeClass('hide')
                       .removeAttr('id')
                       .attr('data-book-index', partIndex)
                       .insertBefore($template);
    
           // Update the name attributes
           $clone
               .find('[id="product_or_services"]').attr('name', 'misc[' + partIndex + '][product_or_services]').attr('id','product_or_services'+partIndex).attr('required','').end()
               .find('[id="description"]').attr('name', 'misc[' + partIndex + '][description]').attr('id','description_'+partIndex).attr('required','').end()
               .find('[id="vendor_charge"]').attr('name', 'misc[' + partIndex + '][vendor_charge]').attr('id','vendor_charge_'+partIndex).end()
               .find('[id="partner_charge"]').attr('name', 'misc[' + partIndex + '][partner_charge]').attr('id','partner_charge_'+partIndex).end();
    
       })
    
       // Remove button click handler
       .on('click', '.removeButton', function () {
           var $row = $(this).parents('.clone'),
               index = $row.attr('data-part-index');
               partIndex = partIndex -1;
           $row.remove();
       });
       
</script>
<?php 
if($this->session->userdata('success')){$this->session->unset_userdata('success');}
if($this->session->userdata('error')){$this->session->unset_userdata('error');}
?>