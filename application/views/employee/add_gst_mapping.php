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
                        <h2>Add GST Number  </h2>
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
                        <form id="new_credit_note" data-parsley-validate class="form-horizontal form-label-left" action="<?php echo base_url(); ?>employee/spare_parts/process_add_gst_details_for_partner" method="POST" enctype="multipart/form-data" >

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="entity_type">Entity Type <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                  
                                  <!--     <label class="radio-inline">
                                  <input type="radio" name="entity_type"  class="radiobutton" checked value="warehouse" id="warehouse"   >Warehouse Hub
                                  </label>-->
                                  <label class="radio-inline">
                                      <input type="radio" name="entity_type" class="radiobutton" value="partner"  id="partner" checked="checked" disabled>Partner
                                  </label>

                                    <span class="text-danger"><?php echo form_error('entity_type'); ?></span>
                                </div>
                            </div>

                               <div class="form-group" id="warehousepartner" >
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">Select Partner Warehouse <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                      <select class="form-control" name="warehousehub" id="partnerwarehouselist">
                                            
                                      </select>
                                     
                                </div>
                               </div>
                                 <div class="form-group hide" id="partnersdiv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">Select Partner   <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select style="width: 100% !important;" name="partner" class="form-control" id="partners" required>
                                            
                                    </select>
                                </div>
                               </div>


                                <div class="form-group hide" id="statediv">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">Select State   <span class="required">*</span>
                                </label> 
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                      <select style="width: 100% !important;" name="state" class="form-control" id="states" required>
                                          <option selected="" disabled="" value="">Select State</option>
                                            <?php foreach ($select_state as   $state) { ?>
                                                 <option value="<?php echo $state['state_code']; ?>"><?php echo $state['state'];  ?></option>
                                            <?php }  ?>
                                      </select>
                                     
                                </div>
                               </div>


                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12 allownumericwithdecimal" for="gst_number">GST Number <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="text" id="gst_number" step=".02" required="required" class="form-control col-md-7 col-xs-12" name="gst_number" placeholder="Enter GST Number">
                                    <span class="text-danger"><?php echo form_error('gst_number'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="gst_file" class="control-label col-md-3 col-sm-3 col-xs-12">GST File<span class="required"></span></label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input type="file" id="gst_file" class="form-control col-md-7 col-xs-12" name="gst_file">
                                    <span class="text-danger"><?php echo form_error('gst_file'); ?></span>
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

   $(document).ready(function(){

    //get_partner_list_warehouse();
    get_partners();
    $("#partnersdiv").addClass('show');
    $("#statediv").addClass('show');
    $("#warehousepartner").addClass('hide');
    $("#states").select2();


//    $(".radiobutton").click(function(){
//
//        if($(this).is(":checked")){
//            var val = $(this).val();
//            if(val=='partner'){
//                $("#partnersdiv").removeClass('hide');
//                $("#statediv").removeClass('hide');
//                $("#warehousepartner").addClass('hide');
//            }else{
//                $("#partnersdiv").addClass('hide');
//                $("#statediv").addClass('hide');
//                $("#warehousepartner").removeClass('hide');
//            }
//        }else{
//            if(val=='partner'){
//                $("#partnersdiv").addClass('hide');
//                $("#statediv").addClass('hide');
//                $("#warehousepartner").removeClass('hide');
//            }else{
//                $("#warehousepartner").removeClass('hide');
//                $("#statediv").addClass('hide');
//                $("#partnersdiv").addClass('hide');
//            }
//        }
//
//    });


   });



    $(".allownumericwithdecimal").on("keypress blur", function (event) {
        $(this).val($(this).val().replace(/[^0-9\.]/g, ''));
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });


    function get_partner_list_warehouse() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list_warehouse',
            data:{'is_wh' : 1},
            success: function (response) {
               // console.log(response);
                 $('#partnerwarehouselist').append(response);
                 $('#partnerwarehouselist').select2();
                
            }
        });
    }



    function get_partners(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data:{is_wh:true},
            success: function (response) {
                $("#partners").html(response);
                $('#partners').select2();
            }
        });
    }

</script>