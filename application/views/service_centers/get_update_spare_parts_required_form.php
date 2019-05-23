<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="page-header">
                    Update Spare Parts                  
                </h2>
                <?php if(validation_errors()) { ?>
                <div class=" alert alert-danger">
                    <?php echo validation_errors(); ?>
                </div>
                <?php } ?>
                <?php
            if ($this->session->userdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible partner_error" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>' . $this->session->userdata('error') . '</strong>
            </div>';
            }
            ?>
                <form class="form-horizontal" id="requested_parts" name="myForm" action="<?php echo base_url() ?>employee/service_centers/update_spare_parts_details" method="POST" onSubmit="document.getElementById('submitform').disabled=true;" enctype="multipart/form-data">
                    <div class="panel panel-default col-md-offset-2">
                        <div class="panel-body" >
                            <div class="row">
                                <div class = 'col-md-6'>
                                    <div class="form-group">
                                        <label for="model_number" class="col-md-4">Model Number *</label>
                                        <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                        <div class="col-md-6">
                                            <select class="form-control spare_parts" id="model_number_id" name="model_number_id">
                                                <option value="" disabled="" selected="">Select Model Number</option>
                                                <?php foreach ($inventory_details as $key => $value) { ?> 
                                                <option <?php if($value['model_number'] == $spare_parts_details['model_number']){ echo 'selected';} ?> value="<?php echo $value['id']; ?>"><?php echo $value['model_number']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <input type="hidden" id="model_number" name="model_number">
                                        </div>
                                        <?php } else { ?> 
                                        <div class="col-md-6">
                                            <input type="hidden" id="model_number_id" name="model_number_id">
                                            <input type="text" class="form-control spare_parts" id="model_number" name="model_number" value = "<?php echo $spare_parts_details['model_number']; ?>" placeholder="Model Number">
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" >
                                        <label for="dop" class="col-md-4">Date of Purchase *</label>
                                        <div class="col-md-6">
                                            <div class="input-group input-append date">
                                                <input id="dop" class="form-control" placeholder="Select Date" name="dop" type="text" value="<?php echo $spare_parts_details['date_of_purchase']; ?>" autocomplete='off' onkeypress="return false;">
                                                <span class="input-group-addon add-on" onclick="dop_calendar()"><span class="glyphicon glyphicon-calendar"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serial_number" class="col-md-4">Serial Number *</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control spare_parts" id="serial_number" name="serial_number"  value="<?php echo $spare_parts_details['serial_number']; ?>" placeholder="Serial Number" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 8">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="serial_number_pic" class="col-md-4">Serial Number Picture *</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control spare_parts" id="serial_number_pic" name="serial_number_pic" >
                                            <input type="hidden" class="form-control spare_parts" id="old_serial_number_pic" name="old_serial_number_pic" value="<?php echo $spare_parts_details['serial_number_pic']; ?>">
                                        </div>
                                        <?php if(!empty($spare_parts_details['serial_number_pic'])){ ?>
                                        <img src="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $spare_parts_details['serial_number_pic']; ?>" id="display_serial_number_pic" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;">
                                        <?php } ?>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="invoice_pic" class="col-md-4">Invoice Picture</label>
                                        <div class="col-md-6">
                                            <input type="file" class="form-control spare_parts" id="invoice_pic" name="invoice_image">
                                            <input type="hidden" class="form-control spare_parts" id="old_invoice_image" name="old_invoice_image" value="<?php echo $spare_parts_details['invoice_pic']; ?>">
                                        </div>
                                        <?php if(!empty($spare_parts_details['serial_number_pic'])){ ?>
                                        <img src="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $spare_parts_details['invoice_pic']; ?>" id="display_invoice_image" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;">
                                      <?php } ?>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="panel panel-default" style="margin-left:10px; margin-right:10px;">
                            <div class="panel-body" >
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="part_warranty" class="col-md-4">Part In Warranty*</label>
                                            <div class="col-md-6">
                                                <select class="form-control part_in_warranty_status" id="part_warranty_status_0" name="part_warranty_status"> <!--  onchange="get_symptom(0)" -->
                                                    <option selected="" disabled="">Select warranty status</option>
                                                    <option value="1"  data-request_type = "<?php echo REPAIR_IN_WARRANTY_TAG;?>" <?php if ($spare_parts_details['part_warranty_status'] == 1) { echo 'selected'; } ?>> In-Warranty </option>
                                                    <option value="2"  data-request_type = "<?php echo REPAIR_OOW_TAG;?>" <?php if ($spare_parts_details['part_warranty_status'] == 2) { echo 'selected'; } ?>> Out-Warranty </option>
                                                </select>
                                            </div>                                            
                                        </div>
                                    </div>
                                    <!--<div class = 'col-md-6'>
                                        <div class="form-group">
                                            <label for="Technical Issue" class="col-md-4">Technical Problem *</label>                                             
                                            <div class="col-md-6">
                                                <select class="form-control spare_request_symptom" id="spare_request_symptom_0" name="part[0][spare_request_symptom]" required="">
                                                    <option selected disabled>Select Technical Problem</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>-->
                                    <div class = 'col-md-6'>
                                        <div class="form-group">
                                            <label for="parts_type" class="col-md-4">Part Type *</label>
                                            <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                            <div class="col-md-6">
                                                <select class="form-control parts_type spare_parts" id="parts_type"  name="part[0][parts_type]">
                                                    <option selected disabled>Select Part Type</option>
                                                </select>
                                                <span id="spinner" style="display:none"></span>
                                            </div>
                                            <?php } else { ?> 
                                            <div class="col-md-6">                                                
                                                <select class="form-control parts_type spare_parts" id="parts_type" name="part[0][parts_type]" value = "<?php echo set_value('parts_type'); ?>">
                                                    <option selected disabled>Select Part Type</option>
                                                </select>
                                            </div>
                                            <?php } ?>
                                        </div>                              
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="parts_name" class="col-md-4">Part Name *</label>
                                            <?php if (isset($inventory_details) && !empty($inventory_details)) { ?> 
                                            <div class="col-md-6">
                                                <select class="form-control spare_parts parts_name" id="parts_name" name="part[0][parts_name]" onchange="get_inventory_id(this.id)">
                                                    <option selected disabled>Select Part Name</option>
                                                </select>
                                                <span id="spinner" style="display:none"></span>                                                
                                            </div>
                                            <?php } else { ?> 
                                            <div class="col-md-6">
                                                <input type="text" class="form-control spare_parts parts_name" id="parts_name" name="part[0][parts_name]" value = "<?php echo $spare_parts_details['parts_requested']; ?>" placeholder="Part Name" >
                                            </div>
                                            <?php } ?>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="defective_parts_pic" class="col-md-4">Defective Front Part Picture *</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control defective_parts_pic spare_parts" id="defective_parts_pic" name="defective_parts_pic[0]">
                                                <input type="hidden" class="form-control spare_parts" id="old_defective_parts_pic" name="old_defective_parts_pic" value="<?php echo $spare_parts_details['defective_parts_pic']; ?>">
                                            </div>
                                            <?php if(!empty($spare_parts_details['serial_number_pic'])){ ?>
                                            <img src="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $spare_parts_details['defective_parts_pic']; ?>" id="display_defective_parts_pic" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;">
                                           <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="defective_parts_pic" class="col-md-4">Defective Back Part Picture *</label>
                                            <div class="col-md-6">
                                                <input type="file" class="form-control defective_back_parts_pic spare_parts" id="defective_back_parts_pic" name="defective_back_parts_pic[0]">
                                                <input type="hidden" class="form-control spare_parts" id="old_defective_back_parts_pic" name="old_defective_back_parts_pic" value="<?php echo $spare_parts_details['defective_back_parts_pic']; ?>">
                                            </div>
                                            <?php if(!empty($spare_parts_details['serial_number_pic'])){ ?>
                                            <img src="<?php echo S3_WEBSITE_URL; ?>misc-images/<?php echo $spare_parts_details['defective_back_parts_pic']; ?>" id="display_defective_back_parts_pic" width="35px" height="35px" style="border:1px solid black;margin-left:-4px;">
                                           <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-md-12" style="margin-left:10px; margin-right:10px;">
                                <div class="form-group">
                                    <label for="prob_desc" class="col-md-4">Problem Description* </label>
                                    <div class="col-md-11" style="width: 89.666667%;">
                                        <textarea class="form-control spare_parts"  id="prob_desc" name="reason_text" rows="5" placeholder="Problem Description"><?php echo $spare_parts_details['remarks_by_sc']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-warning"> <span class="badge badge-info"><i class="fa fa-info"></i></span> * These fields are required</div>
                    </div>                                        
                    <div class="col-md-6 col-md-offset-2">
                        <input type="hidden" name="partner_id" value="<?php echo $spare_parts_details['partner_id']; ?>"> 
                        <input type="hidden" name="booking_partner_id" value="<?php echo $spare_parts_details['booking_partner_id']; ?>">  
                        <input type="hidden" name="entity_type" value="<?php echo $spare_parts_details['entity_type']; ?>">
                        <input type="hidden" name="spare_id" value="<?php echo $spare_parts_details['id']; ?>">
                        <input type="hidden" name="booking_id" value="<?php echo $spare_parts_details['booking_id']; ?>">
                        <input type="hidden" name="previous_inventory_id" value="<?php echo $spare_parts_details['requested_inventory_id']; ?>"> 
                        <input type="hidden" name="current_inventory_id" id="current_inventory_id" value="">
                        <input type="submit"  value="Update" id="submitform" style="background-color: #2C9D9C; border-color: #2C9D9C; " onclick="return submitForm();"   class="btn btn-danger btn-large">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<?php if(empty($inventory_details)){ ?>
<script>
$(document).ready(function(){ 
    defults_inventory_part_type();  
    function defults_inventory_part_type(){
        $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/inventory/get_inventory_parts_type',
                data: { service_id:<?php echo $spare_parts_details['service_id']; ?>},
                success:function(data){                       
                    $('.parts_type').html(data);
                    $('.parts_type option[value="<?php echo $spare_parts_details['parts_requested_type']; ?>"]').attr('selected','selected');
                    
                }
            });
    }
});
</script>
<?php } ?>
<?php if(!empty($inventory_details)){ ?>
<script>
$(document).ready(function(){    
    
        $('#model_number_id').on('change', function() {        
           load_model_number();     
         });
        
        $('#parts_type').on('change', function() {
            var part_type = $('#parts_type').val();
            load_parts_type(part_type);           
        });
        
        $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true,maxDate:0});
        
        $("#serial_number_pic").on('change',function(){
            var serial_number_pic = $("#serial_number_pic").val();
            if(serial_number_pic!=''){
                $("#display_serial_number_pic").hide();
            }
        });
        
        $("#invoice_pic").on('change',function(){
            var invoice_image = $("#invoice_pic").val();
            if(invoice_image!=''){
                $("#display_invoice_image").hide();
            }
        });
        
        $("#defective_parts_pic").on('change',function(){
            var defective_parts_pic = $("#defective_parts_pic").val();
            if(defective_parts_pic!=''){
                $("#display_defective_parts_pic").hide();
            }
        });
        
        $("#defective_back_parts_pic").on('change',function(){
            var defective_back_parts_pic = $("#defective_back_parts_pic").val();
            if(defective_back_parts_pic!=''){
                $("#display_defective_back_parts_pic").hide();
            }
        });
        
    var part_type = "<?php echo $spare_parts_details['parts_requested_type']; ?>"; 
       load_model_number(); 
       load_parts_type(part_type);
      
      function load_model_number(){
          var model_number_id = $('#model_number_id').val();
          var model_number = $("#model_number_id option:selected").text();
            $('#spinner').addClass('fa fa-spinner').show();
            if(model_number){
                $('#model_number').val(model_number);
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>employee/inventory/get_parts_type',
                    data: { model_number_id:model_number_id},
                    success:function(data){                       
                        $('#parts_type').html(data);
                        $('#parts_type option[value="<?php echo $spare_parts_details['parts_requested_type']; ?>"]').attr('selected','selected');
                        $('#spinner').removeClass('fa fa-spinner').hide();
                    }
                });
            }else{
                alert("Please Select Model Number");
            }
      }  
       
      function load_parts_type(part_type){          
            var model_number_id = $('#model_number_id').val();             
            $('#spinner').addClass('fa fa-spinner').show();
            if(model_number_id && part_type){
                $.ajax({
                    method:'POST',
                    url:'<?php echo base_url(); ?>employee/inventory/get_parts_name',
                    data: {model_number_id:model_number_id,entity_id: '<?php echo $spare_parts_details['partner_id']; ?>' , entity_type: '<?php echo _247AROUND_PARTNER_STRING; ?>' , service_id: '<?php echo $spare_parts_details['service_id']; ?>', part_type:part_type},
                    success:function(data){
                        $('#parts_name').html(data);  
                         var inventory_id =$("#parts_name").find('option:selected').attr("data-inventory"); 
                        $("#current_inventory_id").val(inventory_id);
                        $('#spinner').removeClass('fa fa-spinner').hide();
                    }
                });
            }else{
                console.log("Please Select Model Number");
            }
      } 
            
});

function get_inventory_id(id){       
        var inventory_id =$("#parts_name").find('option:selected').attr("data-inventory"); 
        $("#current_inventory_id").val(inventory_id);
              
}  

</script>
<?php } ?>
<script>
    
    $(document).ready(function(){
        var array = [];
        var postData = {};
        var price_tags = "<?php if($spare_parts_details['part_warranty_status'] == 1){ echo REPAIR_IN_WARRANTY_TAG; }else{ echo REPAIR_OOW_TAG; } ?>";
        array.push(price_tags);
        if(array.length > 0){
            postData['request_type'] = array;
            postData['service_id'] = '<?php echo $spare_parts_details['service_id'];?>';
        }
        //get_technical_problem(postData,'0');
    });

   /*function get_symptom(key){      
        var array = [];
        var postData = {};
        var price_tags = $("#part_warranty_status_" + key).find(':selected').attr('data-request_type');
        array.push(price_tags);
        if(array.length > 0){
            postData['request_type'] = array;
            postData['service_id'] = '<?php echo $spare_parts_details['service_id'];?>';
        }
        get_technical_problem(postData,key);
    }*/
    
    /*function get_technical_problem(postData,key) {
        var url =  '<?php echo base_url();?>employee/booking_request/get_spare_request_dropdown';
         $.ajax({
             method:'POST',
             url: url,
             data: postData,
             success:function(data){ 
                 console.log(data);
                 if(data === "Error"){
                     $('#spare_request_symptom_' + key).html("").change();
                 } else {
                     $('#spare_request_symptom_' + key).html(data).change();

                 }                  
             }
         });
    }*/
    
    
    function dop_calendar(){
        $("#dop").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true,maxDate:0}).datepicker('show');
    }
    
    function submitForm(){
        var checkbox_value = 1;
        var model_number = $('#model_number').val();
        var serial_number = $("#serial_number").val();
        var prob_des = $("#prob_desc").val();
        var dop = $("#dop").val();
        var serial_number_pic = $('#serial_number_pic').val();
        var old_serial_number_pic = $('#old_serial_number_pic').val();
        var defective_parts_pic = $("#defective_parts_pic").val();
        var old_defective_back_parts_pic = $("#old_defective_back_parts_pic").val();
        var defective_parts_pic = $("#defective_parts_pic").val();
        var old_defective_back_parts_pic = $("#old_defective_back_parts_pic").val();
        var defective_back_parts_pic = $("#defective_back_parts_pic").val();
        var old_defective_back_parts_pic = $("#old_defective_back_parts_pic").val();
        
        if(model_number ==="" || model_number === null){
            alert("Please enter model number");
            return false;
        }

        if(dop === ""){
          alert("Please Select Date of Purchase");
          checkbox_value = 0; 
          return false;

        }

        if(serial_number === "" || serial_number === null){
          alert("Please Enter serial number");
          return false;
        }

        if(serial_number_pic.length === 0 && old_serial_number_pic.length === 0){
          alert("Please Upload Serial Number Image");
          return false;
        }
        
        if(defective_parts_pic.length === 0 && old_defective_back_parts_pic.length === 0){
          alert('Please Upload Defective Front Part Image');
          return false;
        }
        
        if(defective_back_parts_pic.length === 0 && old_defective_back_parts_pic.length === 0){
          alert('Please Upload Defective Back Part Image');
          return false;
        }
        
        $('.parts_name').each(function() {
            var id = $(this).attr('id');
            if(id === "parts_name"){
                if(!$(this).val() || $(this).val() === "undefined" ||  $(this).val() === null){
                    alert('Please Enter Part Name');
                    checkbox_value = 0;
                    return false;

                }
              }

        });

        $('.parts_type').each(function() {
            var id = $(this).attr('id');
            if(id === "parts_type"){
                if(!$(this).val() || $(this).val() === "undefined" ||  $(this).val() === null){
                    alert('Please Enter Part Type');
                    checkbox_value = 0;
                    return false;
                }
            }
        });

        $('.part_in_warranty_status').each(function() {
            var id = $(this).attr('id');
            if(id !== "part_in_warranty_status"){
                if(!$(this).val() || $(this).val() === "undefined" ||  $(this).val() === null){
                    alert('Please Select Part Warranty Status');  
                    checkbox_value = 0;
                    return false;
                }
            }
        });
        
        if(prob_des === "" || prob_des === null){
            alert("Please Enter problem description");
            return false;
        } 
        if(checkbox_value === 0){
            return false;
        }
        else
            return true;
    }
    
</script>
<style type="text/css">
    #hide_spare, #hide_rescheduled { display: none;}
    .col-md-offset-2 {
        margin-left: 10%;
        margin-right: 10%;
    }
    .page-header {
        padding-bottom: 9px;
        margin: 31px 150px 37px;
        border-bottom: 1px solid #eee;
    }
</style>
<?php if($this->session->userdata('error')){$this->session->unset_userdata('error');} ?>