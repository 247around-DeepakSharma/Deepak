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
<?php
            if ($this->session->userdata('success')) {
                echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">
            
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('success') . '</strong>
                   </div>';
            }
            
            if ($this->session->userdata('error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: 0px;">
            
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('error') . '</strong>
                   </div>';
            }
                              
            ?>
<div id="page-wrapper">
    <div class="row">
        <div class="clear" style="margin-top:0px;"></div>
        <div id="container-4" style="display:block;padding-top: 0px;" class="form_container panel-body">
            <form name="myForm" class="form-horizontal" id ="taging_invoice_ids" novalidate="novalidate" method="post">            
                <div  class = "panel panel-info">
                    <div class="panel-heading" style="background-color:#ECF0F1"><b> Invoice Ids Details </b></div>
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group <?php
                                if (form_error('primary_contact_name')) {
                                    echo 'has-error';
                                }
                                ?>">
                                    <label  for="primary_contact_name" class="col-md-3 vertical-align">AWB*</label>
                                    <div class="col-md-8">                                             
                                        <input type="text" onblur="check_awb_exist()" class="form-control"  id="awb_by_wh" name="awb_by_wh" placeholder="Please Enter AWB" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primary_contact_email" class="col-md-2 vertical-align">Courier Name*</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="courier_name_by_wh" name="courier_name_by_wh" required="">
                                            <option selected="" disabled="" value="">Select Courier Name</option>
                                            <?php foreach ($courier_details as $value1) { ?> 
                                                <option value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                            <?php } ?>
                                        </select>                                           
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primary_contact_phone_1" class="col-md-3 vertical-align">Courier Price*</label>
                                    <div class="col-md-8">
                                        <input type="number" class="form-control"  id="courier_price_by_wh" name="courier_price_by_wh" placeholder="Please Enter Courier Price" required>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="primary_contact_phone_2" class="col-md-3 vertical-align">Courier Shipped Date*</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control"  id="defective_parts_shippped_date_by_wh" name="defective_parts_shippped_date_by_wh" placeholder="Please enter Shiiped Date" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="courier_picture" class="col-md-3 vertical-align">Courier Pic*</label>
                                    <div class="col-md-8">                                            
                                        <input type="hidden" class="form-control"  id="exist_courier_image" name="exist_courier_image" >
                                        <input type="file" class="form-control"  id="defective_parts_shippped_courier_pic_by_wh" name="defective_parts_shippped_courier_pic_by_wh" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="invoice_id" class="col-md-3 vertical-align">Invoice Ids*</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="invoice_id" name="invoice_ids" value = "" placeholder="Enter comma separated Invoice id as TXS-CED-0094,TXS-CED-69540,....">
                                        <span style="color:red;">Please Enter comma separated Invoice id * </span>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <br> 
                    <div class="clear clear_bottom">                                      
                        <center><input type="Submit" value="Submit" class="btn btn-primary" id="submit_btn"></center>
                    </div>
                    <br> 
                </div>
            </form>
        </div>
    </div>
</div>
   
<script type="text/javascript">
        
       $("#defective_parts_shippped_date_by_wh").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});
       
        function check_awb_exist(){
            var awb = $("#awb_by_wh").val();
            if(awb){
                    $.ajax({
                    type: 'POST',
                    beforeSend: function(){

                        $('body').loadingModal({
                        position: 'auto',
                        text: 'Loading Please Wait...',
                        color: '#fff',
                        opacity: '0.7',
                        backgroundColor: 'rgb(0,0,0)',
                        animation: 'wave'
                    });

                        },
                    url: '<?php echo base_url() ?>employee/service_centers/check_wh_shipped_defective_awb_exist',
                    data:{awb:awb},
                    success: function (response) {
                        console.log(response);
                        var data = jQuery.parseJSON(response);
                        if(data.code === 247){
                            alert("This AWB already used same price will be added");
                            $("#same_awb").css("display","block");
                            $('body').loadingModal('destroy');
                           
                            $("#defective_parts_shippped_date_by_wh").val(data.message[0].shipment_date);
                            $("#courier_name_by_wh").val(data.message[0].courier_name);
                            $("#courier_price_by_wh").val("0");
                            $("#courier_price_by_wh").css("display","none");
                            if(data.message[0].courier_file){
                               
                                $("#exist_courier_image").val(data.message[0].courier_file);
                                $("#defective_parts_shippped_courier_pic_by_wh").css("display","none");
                            }

                        } else {

                            $('body').loadingModal('destroy');
                            $("#defective_parts_shippped_courier_pic_by_wh").css("display","block");
                            $("#courier_price_by_wh").css("display","block");
                            $("#same_awb").css("display","none");
                            $("#exist_courier_image").val("");
                        }

                    }
                });
            }
            
        }
          
          
  $('#submit_btn').on('click',function(){
        $('#submit_btn').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
        postData ={};
        postData['awb_by_wh'] = $('#awb_by_wh').val();
        postData['courier_name_by_wh'] = $('#courier_name_by_wh').val();
        postData['courier_price_by_wh'] = $('#courier_price_by_wh').val();
        postData['defective_parts_shippped_date_by_wh'] = $('#defective_parts_shippped_date_by_wh').val();
        invoice_id = $('#invoice_id').val();  
        if(invoice_id.indexOf(",") > 0){
          postData['invoice_ids'] = $('#invoice_id').val();  
        }
          
        var exist_courier_image = $("#exist_courier_image").val();       
        //Declaring new Form Data Instance  
        var formData = new FormData();
                
        //Getting Files Collection
        var files = $("#defective_parts_shippped_courier_pic_by_wh")[0].files;
       
        //Looping through uploaded files collection in case there is a Multi File Upload. This also works for single i.e simply remove MULTIPLE attribute from file control in HTML.  
        for (var i = 0; i < files.length; i++) {
            formData.append('file', files[i]);
        }
        var is_exist_file = false;
        if(exist_courier_image){
            is_exist_file = true;
        }
        
        if(files.length >= 1){
            is_exist_file = true;
        }
        
        //Now Looping the parameters for all form input fields and assigning them as Name Value pairs. 
        $.each(postData, function(index, element) {
            formData.append(index, element);
        });
        
        if(postData['awb_by_wh'] && postData['courier_name_by_wh'] && postData['courier_price_by_wh'] && postData['defective_parts_shippped_date_by_wh'] && postData['invoice_ids'] && is_exist_file){
            $.ajax({
                method:'POST',
                url:'<?php echo base_url() ?>employee/booking/process_to_update_courier_details_by_invoice_ids',
                data:formData,
                contentType: false,
                processData: false,
                success:function(response){
                    $('#submit_courier_form').html('Submit').attr('disabled',false);
                    $('#courier_model').modal('toggle');
                    obj = JSON.parse(response);
                    if(obj.status){                        
                        $('#success_msg').html(obj.message);
                        alert(obj.message);
                        window.location.reload();
                    }else{                        
                        $('#error_msg').html(obj.message);
                    }
                }
            });
        }else{
            $('#submit_btn').html('Submit').attr('disabled',false);
            alert("Please enter all required field");
        }
        
    });
          
</script> 
<!--page 1 validations end here-->
<?php if ($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>
<?php if ($this->session->userdata('error')) {$this->session->unset_userdata('error');} ?>





	