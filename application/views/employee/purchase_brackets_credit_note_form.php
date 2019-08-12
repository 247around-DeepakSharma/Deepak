<style>
    .form-control{
        border-radius: 0;
        width: 100%;
    }
</style>
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>New Credit Note (Brackets)</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br />
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
                        <form id="new_credit_note" data-parsley-validate class="form-horizontal form-label-left" action="<?php echo base_url(); ?>employee/invoice/process_purchase_bracket_credit_note" method="POST" enctype="multipart/form-data" >

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="order_id">Order Id <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" id="order_id" required="required" class="form-control col-md-7 col-xs-12" name="order_id">
                                    <span class="text-danger"><?php echo form_error('order_id'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="courier_charges">Courier Charges <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="number" id="courier_charges" step=".02" required="required" class="form-control col-md-7 col-xs-12" name="courier_charges">
                                    <span class="text-danger"><?php echo form_error('courier_charges'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="courier_charges_file" class="control-label col-md-3 col-sm-3 col-xs-12">Upload Courier Charges File<span class="required">*</span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="file" id="courier_charges_file" class="form-control col-md-7 col-xs-12" name="courier_charges_file" required>
                                    <span class="text-danger"><?php echo form_error('courier_charges_file'); ?></span>
                                </div>
                            </div>
                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button class="btn btn-primary" type="reset">Reset</button>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(".allownumericwithdecimal").on("keypress blur", function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
</script>