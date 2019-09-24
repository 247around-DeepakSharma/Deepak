<script>$(".main_search").css("display","none");</script>

<!--<script src="https://rawgit.com/wasikuss/select2-multi-checkboxes/master/select2.multi-checkboxes.js"></script>-->
<div id="page-wrapper" >
<div class="panel-heading">
    <h4>Show <?php echo $public_name;?> Price </h4>
</div>
<form method="POST" action="#"  class="form-horizontal" name="service_charge" id="service_charge">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-4">
                <div class="form-group col-md-12 ">
                    <label for="Appliance">Appliance * </label>
                    <select class="form-control"  id="service_id" name="service_id" onchange="getBrand(), getPriceTags()">
                        <option selected disabled >Select Appliance</option>
                        <?php foreach ($appliances as $value) { ?>
                        <option value="<?php echo $value->id;?>" <?php if(count($appliances) ==1 ){ echo "selected";}?>>
                            <?php echo $value->services;?>
                        </option>
                        <?php }?>
                    </select>
                    <input type="hidden" name="partner_id"  value="<?php echo $partner_id;?>" />
                    <input type="hidden" name="public_name"  value="<?php echo $public_name;?>" />
                </div>
            </div>
            <div class="col-md-4" id="brand_div">
                <div class="form-group col-md-12 ">
                    <label for="Appliance">Brand *</label>
                    <select  class="form-control select2-multiple2" id="appliance_brand" name="brand[]" onchange="getCategory()" multiple>
                       
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group col-md-12 ">
                    <label for="Appliance">Category *</label>
                    <select class="form-control select2-multiple2" id="category" name="category[]" onchange="getcapacity()" multiple>
                        
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group col-md-12 ">
                    <label for="Appliance">Capacity *</label>
                    <select name="capacity[]" id="capacity" class="form-control select2-multiple2"  multiple>
                      
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group col-md-12 ">
                    <label for="Appliance">Service Category *</label>
                    <select class="form-control" id="request_type" name="request_type" >
                        <option selected disabled  >Select Service category</option>
                    </select>
                    <input type="hidden" id="product_or_services" name="product_or_services" />
                </div>
            </div>
            <div class="col-md-2 free_paid_container">
                <div class="items ">
                            <div class="info-block block-info clearfix">
                                <div class="square-box pull-left">
                                </div>
                                <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                    <label class="btn btn-default" id="free_lable">
                                        <div class="bizcontent">
                                            <input type="checkbox" name="free" autocomplete="off" value="1">
                                            <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                            <h5>FREE</h5>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
            </div>
             <div class="col-md-2 free_paid_container">
                <div class="items">
                            <div class="info-block block-info clearfix">
                                <div class="square-box pull-left">
                                </div>
                                <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                    <label class="btn btn-default">
                                        <div class="bizcontent">
                                            <input type="checkbox" name="paid" autocomplete="off" value="1">
                                            <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                            <h5>PAID</h5>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
            </div>
        </div>
        </div>
         </form>
         <div class="col-md-12" style="margin-top:20px;">
            <button class="btn btn-lg btn-success col-md-offset-4" onclick="return submitForm()">Submit</button>
        </div>

       

        
       
<div class="col-md-12" id="div_duplicate" >
<div class="panel-heading">
<h4>Charges </h4>
</div>
<div id="duplicate_data">
</div>
</div>
</div>
<style>
    .free_paid_container{margin:0px 0 0 0}
    .free_paid_container label.btn-default.active{background-color:#007ba7;color:#FFF}
    .free_paid_container label.btn-default{width:90%;border:1px solid #efefef;margin:5px; box-shadow:5px 8px 8px 0 #ccc;}
    .free_paid_container label .bizcontent{width:100%;}
    .free_paid_container .btn-group{width:97%}
    .free_paid_container .btn span.glyphicon{
    opacity: 0;
    }
    .free_paid_container .btn.active span.glyphicon {
    opacity: 1;
    }
    .select2-results__option .wrap:before{
    font-family:fontAwesome;
    color:#999;
    content:"\f096";
    padding-right: 10px;
    }
    .select2-results__option[aria-selected=true] .wrap:before{
    content:"\f14a";
    }
</style>
<script>
     
    $(function() {
    
        $('#service_id').select2();
        
        getBrand();
        $('#category').select2();
        $('#capacity').select2();
        $('#request_type').select2();
        $('#appliance_brand').select2();
        $('#free_vendor_percentage').select2({
            placeholder: "Select Percentage",
            allowClear: true
        });
        
        $("#paid_vendor_percentage").select2({
            placeholder: "Select Percentage",
            allowClear:true
        });
        
        
    });
    
    function getBrand(){
        var postData = {};
        postData['service_id'] = $("#service_id").val();
        postData['partner_id'] = '<?php echo $partner_id; ?>';
        postData['partner_type'] ='<?php echo $partner_type; ?>';
        var url = '<?php echo base_url(); ?>employee/partner/get_brands_from_service';
        sendAjaxRequest(postData, url).done(function (data) {
           
            $("#appliance_brand option[value !='Select Brand']").remove();
            
            $('#appliance_brand').append(data).change();
            getCategory();
             getcapacity();
            getPriceTags();
           
        });
      
    }
    
    function getCategory(){
        var url = '<?php echo base_url(); ?>employee/partner/get_category_from_service';
        var postData = {};
        var service_id = $("#service_id").val();
        if(service_id !== null){
            postData['service_id'] = $("#service_id").val();
            postData['partner_id'] = '<?php echo $partner_id; ?>';
            postData['partner_type'] ='<?php echo $partner_type; ?>';
            postData['brand'] = $("#appliance_brand").val();
            postData['is_mapping'] = 1;
    
            sendAjaxRequest(postData, url).done(function (data) {
    
              $("#category option[value !='Select Category']").remove();
             
              $('#category').append(data).change();
    
               getcapacity();
            });
        }
    }
    
    function getcapacity(){
        var url = '<?php echo base_url(); ?>employee/partner/get_capacity_for_partner';
        var service_id = $("#service_id").val();
        var category = $("#category").val();
        
        if(service_id !== null && category !== null && category.length > 0){
            var postData = {};
            postData['service_id'] = service_id;
            postData['partner_id'] = '<?php echo $partner_id; ?>';
            postData['partner_type'] ='<?php echo $partner_type; ?>';
            postData['brand'] =$("#appliance_brand").val();
            postData['is_mapping'] = 1;
            postData['category'] = category;
            
            sendAjaxRequest(postData, url).done(function (data) {
               $("#capacity option[value !='Select Capacity']").remove();
               
               $('#capacity').append(data).change();
    
            });
        }
    }
    function getPriceTags(){
        var url = '<?php echo base_url(); ?>employee/service_centre_charges/get_service_request_type';
        var postData = {};
        postData['service_id'] = $("#service_id").val();
       
        sendAjaxRequest(postData, url).done(function (data) {
         
           $("#request_type option[value !='Select Service category']").remove();
           $('#request_type').append("<option selected disabled>Select Service category</option>").change();
           $('#request_type').append(data).change();
           
    
        });
    }
    
    function sendAjaxRequest(postData, url) {
        return $.ajax({
            data: postData,
            url: url,
            type: 'post'
        });
    }
    
    jQuery(function($) {
      $('#capacity').select2();
      $('#appliance_brand').select2();
      $('#category').select2();
      
//      $('#capacity').select2MultiCheckboxes({
//        templateSelection: function(selected, total) {
//          return "Selected " + selected.length + " of " + total;
//        }
//      });
//      
//       $('#appliance_brand').select2MultiCheckboxes({
//        templateSelection: function(selected, total) {
//          return "Selected " + selected.length + " of " + total;
//        }
//      });
//      
//      $('#category').select2MultiCheckboxes({
//        templateSelection: function(selected, total) {
//          return "Selected " + selected.length + " of " + total;
//        }
//      });
      
    });
    
    function submitForm(){
       var category = $("#category").val();
       var service_id = $("#service_id").val();
       var service_category = $("#request_type").val();
       var free = Number($("input[name='free']:checked"). val());
       var paid = Number($("input[name='paid']:checked"). val());
      
       if(service_id  === null){
           alert("Please Select Category");
          return false;
       }
       if(category === null || category.length === 0){
           alert("Please Select Category");
          return false;
       }
       if(service_category ===  null){
           alert("Please Select Service Category");
           return false;
       }
      console.log(paid);
       if(free === 1 || paid === 1 ){} else {
             alert("Please Select atleast One Free OR Paid Checkbox");
            return false;
       }
       
        url = "<?php echo base_url();?>employee/service_centre_charges/price_table";
       
        var fd = new FormData(document.getElementById("service_charge"));
       fd.append("label", "WEBUPLOAD");
       $.ajax({
       url: url,
       type: "POST",
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
      data: fd,
      processData: false,  // tell jQuery not to process the data
      contentType: false   // tell jQuery not to set contentType
    }).done(function( data ) {
      
        $("#duplicate_data").html(data);
        $('body').loadingModal('destroy');
        $('body').animate({
        scrollTop: $("#div_duplicate").offset().top
        }, 2000);
         window.location.hash = "#div_duplicate";
        });
          
       return false;
    }
    
    
    function delete_form(){
        var count = 0;
        $(".service_charge_id:checked").each(function (i) {
            console.log("abhay");
            count = count + 1;
           
        });
        console.log(count);
        if(count > 0){
             swal({
                title: "Do You Want To Continue?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: true
                
            },function(){     
            url = "<?php echo base_url();?>employee/service_centre_charges/delete_service_charges";
            var fd = new FormData(document.getElementById("delete_service_charges"));
            fd.append("label", "WEBUPLOAD");
           
            $.ajax({
                url: url,
                type: "POST",
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
               data: fd,
               processData: false,  // tell jQuery not to process the data
               contentType: false   // tell jQuery not to set contentType
             }).done(function( data ) {
                 console.log(data);
                    if(data === "success"){
                        swal("Thanks!", " successfully Charges Deleted", "success");
                        
                        
                    } else {
                         swal("Oops", "There is something issues, Please Conatct 247Around Dev Team", "error");
                        
                    }
                    $("#duplicate_data").html("");
                    $('body').loadingModal('destroy');
             
                 });
             });
                return false;
        } else {
            alert("Please Select Atleast One Checkbox to Delete Charges");
            return false;
        }

             return false;
                
    }
    
   

</script>