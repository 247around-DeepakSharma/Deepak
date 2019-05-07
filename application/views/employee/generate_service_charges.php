<script>$(".main_search").css("display","none");</script>

<script src="https://rawgit.com/wasikuss/select2-multi-checkboxes/master/select2.multi-checkboxes.js"></script>
<div id="page-wrapper" >
<div class="panel-heading">
    <h4>Generate <?php echo $public_name;?> Price </h4>
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
                    <label class="col-md-12 " style="padding: 0px;" for="Appliance">Capacity * <div class="pull-right"><input  onchange="selectAllCapacity()" id="capacity_all" type="checkbox" value="">Select All</div></label>
                    <select name="capacity[]" id="capacity" class="form-control select2-multiple2"  multiple>
                      
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group col-md-12 ">
                    <label for="Appliance">Service Category *</label>
                    <select class="form-control" id="request_type" name="request_type"  onchange="getproduct_or_services()">
                       
                    </select>
                    <input type="hidden" id="product_or_services" name="product_or_services" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group col-md-12 ">
                    <label for="Flat Upcountry">Flat Upcountry *</label>
                    <select onchange="enable_disable_flat_upcountry()" class="form-control" id="flat_upcountry" name="flat_upcountry"  >
                        <option value="0" selected>No</option>
                        <option value="1">Yes</option>
                    </select>
                    
                </div>
            </div>
        </div>
        <div class="free_paid_container col-md-12">
            <table class="table priceList table-striped table-bordered">
                <tr>
                    <td align="center" style="vertical-align: middle;">
                     <strong> For Customer Free </strong>
                    </td>
                    <td>
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
                    </td>
                    <td>
                        <div class="items ">
                            <div class="info-block block-info clearfix">
                                <div class="square-box pull-left">
                                </div>
                                <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                    <label class="btn btn-default">
                                        <div class="bizcontent">
                                            <input type="checkbox" name="free_pod" autocomplete="off" value="1">
                                            <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                            <h5>POD</h5>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            <label for="upcountry" >Upcountry  </label><br>
                            <input type="radio" onchange="addcheckforFlatUpcountry('Free', 0)" name="free_upcountry" value="0"> Customer Paid<br>
                            <input type="radio" onchange="addcheckforFlatUpcountry('Free', 1)" name="free_upcountry" value="1"> Partner Paid<br>
                            <input type="radio" onchange="addcheckforFlatUpcountry('Free', -1)" name="free_upcountry" value="-1"> Not Upcountry <i class="fa fa-info-circle" aria-hidden="true" data-toggle="popover" title="No One Provide Upcountry" data-content="No One Provide Upcountry"></i>
                        </div>
                    </td>
                    <td id="free_upcounry_td" style="display:none;">
                        
                        <div class="form-group col-md-12" id="free_flat_upcountry_customer_price_div">
                            <label for="customer total">Flat Upcountry Customer Price </label>
                            <input type="number" class="form-control"  id="free_flat_upcountry_customer_price" name="free_upcountry_customer_price" value = "" placeholder="Enter Upcountry Amount" >
                        </div>
                        
                        <div class="form-group col-md-12" id="free_flat_upcountry_partner_price_div">
                            <label for="customer total">Flat Upcountry Partner Price </label>
                            <input type="number" class="form-control"  id="free_flat_upcountry_partner_price" name="free_upcountry_partner_price" value = "" placeholder="Enter Upcountry Amount" >
                        </div>
                        
                        <div class="form-group col-md-12">
                            <label for="customer total">Flat Upcountry Vendor Price </label>
                            <input type="number" class="form-control"  id="free_upcountry_vendor_price" name="free_upcountry_vendor_price" value = "" placeholder="Enter Upcountry Amount" >
                        </div>
                    </td>
                    <td>
                        <div class="form-group col-md-12">
                            <label for="customer total">Customer Total </label>
                            <input type="number" class="form-control"  id="free_customer_total" name="free_customer_total" value = "" placeholder="Enter Customer Amount" required>
                        </div>
                    </td>
                    <td >
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="Appliance">Vendor Payout *</label>
                                <select class="form-control" name="free_vendor_percentage" onchange="vendor_percentage('free')" id="free_vendor_percentage" style="margin-bottom:10px;">
                                    <option selected disabled>Select Percentage</option>
                                    <option value=".70">70</option>
                                    <option value=".95">95</option>
                                </select>
                                <input style="margin-top:10px;" type="number" class="form-control" id="free_vendor_total" name="free_vendor_total" value = "" placeholder="Enter Vendor Amount" required>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="vertical-align: middle;">
                     <strong> For Customer Paid </strong>
                    </td>
                    <td>
                        <div class="items ">
                            <div class="info-block block-info clearfix">
                                <div class="square-box pull-left">
                                </div>
                                <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                    <label class="btn btn-default" id="paid_label">
                                        <div class="bizcontent">
                                            <input type="checkbox" name="paid" autocomplete="off" value="1">
                                            <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                            <h5>PAID</h5>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="items">
                            <div class="info-block block-info clearfix">
                                <div class="square-box pull-left">
                                </div>
                                <div data-toggle="buttons" class="btn-group bizmoduleselect">
                                    <label class="btn btn-default">
                                        <div class="bizcontent">
                                            <input type="checkbox" name="paid_pod" autocomplete="off" value="1">
                                            <span class="glyphicon glyphicon-ok glyphicon-lg"></span>
                                            <h5>POD</h5>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            <label for="vendor_total" >Upcountry  </label><br>
                            <input type="radio" onchange="addcheckforFlatUpcountry('Paid', 0)" name="paid_upcountry" value="0"> Customer Paid<br>
<!--                            <input type="radio" name="paid_upcountry" value="1"> Partner Paid<br>-->
                            <input type="radio" onchange="addcheckforFlatUpcountry('Paid', -1)" name="paid_upcountry" value="-1"> Not Upcountry <i class="fa fa-info-circle" aria-hidden="true" data-toggle="popover" title="No One Provide Upcountry" data-content="No One Provide Upcountry"></i>
                        </div>
                    </td>
                    <td id="paid_upcounry_td" style="display:none;">
                        
                        <div class="form-group col-md-12">
                            <label for="customer total">Flat Upcountry Customer Price </label>
                            <input type="number" class="form-control"  id="paid_flat_upcountry_customer_price" name="paid_upcountry_customer_price" value = "" placeholder="Enter Upcountry Amount" >
                        </div>

                        <div class="form-group col-md-12">
                            <label for="customer total">Flat Upcountry Vendor Price </label>
                            <input type="number" class="form-control"  id="paid_upcountry_vendor_price" name="paid_upcountry_vendor_price" value = "" placeholder="Enter Upcountry Amount" >
                        </div>
                    </td>
                    <td>
                        <div class="form-group  col-md-12">
                            <label for="customer total">Customer Total </label>
                            <input type="number" class="form-control"  id="paid_customer_total" name="paid_customer_total" value = "" placeholder="Enter Customer Amount" required>
                        </div>
                    </td>
                    <td >
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label for="Appliance">Vendor Payout *</label>
                                <select class="form-control" name="paid_vendor_percentage" onchange="vendor_percentage('paid')" id="paid_vendor_percentage" style="margin-bottom:10px;">
                                    <option selected disabled>Select Percentage</option>
                                    <option value=".70">70</option>
                                    <option value=".95">95</option>
                                </select>
                                <input style="margin-top:10px;" type="number" class="form-control" id="paid_vendor_total" name="paid_vendor_total" value = "" placeholder="Enter Vendor Amount" required>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-12" style="margin-top:20px;">
            <button class="btn btn-lg btn-success col-md-offset-4" onclick="return submitForm()">Submit</button>
        </div>
</form>
<div class="col-md-12" id="div_duplicate" style="display:none">
<div class="panel-heading">
<h4>Duplicate Charges </h4>
</div>
<div id="duplicate_data">
</div>
</div>
</div>
<style>
    .free_paid_container{margin:65px 0 0 0}
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
    
    
    function selectAllCapacity(){
        if ($('#capacity_all').is(":checked")){
            $('#capacity option').prop('selected', true);
             $('#capacity').select2MultiCheckboxes({
                   templateSelection: function(selected, total) {
                     return "Selected " + selected.length + " of " + total;
                   }
                 });

        } else{
            $('#capacity option').prop('selected', false);
             $('#capacity').select2MultiCheckboxes({
               templateSelection: function(selected, total) {
                 return "Selected " + selected.length + " of " + total;
               }
             });
     
       }
    }
    
    
    jQuery(function($) {
      
      $('#capacity').select2MultiCheckboxes({
        templateSelection: function(selected, total) {
          return "Selected " + selected.length + " of " + total;
        }
      });
      
       $('#appliance_brand').select2MultiCheckboxes({
        templateSelection: function(selected, total) {
          return "Selected " + selected.length + " of " + total;
        }
      });
      
      $('#category').select2MultiCheckboxes({
        templateSelection: function(selected, total) {
          return "Selected " + selected.length + " of " + total;
        }
      });
      
    });
    
    
    function free_upcountry(id, btn){
      
        if($("#checkbox_"+ id + "_" + btn). prop("checked") === false){
            // Previous unchecked
            if(btn === 1){
              
                 $("#"+ id + "_2").removeClass("active");
                 $("#checkbox_"+ id + "_2").attr('checked', false); // Unchecks it
            } else {
                 $("#"+ id + "_1").removeClass("active");
                 $("#checkbox_"+ id + "_1").attr('checked', false); // Unchecks it
            }
         } 
    
    }
    
     $(document).on('keyup', '#free_customer_total', function (e) {
         var charge = $("#free_customer_total").val();
         
         if(Number(charge) > 0){
            
             $("#free_lable").addClass("active");
         } 
     vendor_percentage("free");
     
    });
    
     $(document).on('keyup', '#paid_customer_total', function (e) {
         var charge = $("#paid_customer_total").val();
         
         if(Number(charge) > 0){
            
             $("#paid_lable").addClass("active");
         } 
     vendor_percentage("paid");
    });
    
    function vendor_percentage(tag){
        var per = $("#"+tag+"_vendor_percentage").val();
        if(per !== null){
            var customer_total = $("#"+tag+"_customer_total").val();
            if(Number(customer_total) < 1){
                alert("Please Enter Customer Total");
                
                $("#"+tag+"_vendor_total").attr("readonly", false);
                $("#"+tag+"_vendor_total").val("");
                return false;
            } else {
                var vendor_total = Number(per) * Number(customer_total);
                 $("#"+tag+"_vendor_total").attr("readonly", true);
                $("#"+tag+"_vendor_total").val(vendor_total.toFixed(2));
            }
        } else {
           
              $("#"+tag+"_vendor_total").val("");
              $("#"+tag+"_vendor_total").attr("readonly", false);
              return false;
        }
    }
    
    function getproduct_or_services(){
        var product_or_services = $("#request_type").find(':selected').attr('data-type');
        $("#product_or_services").val(product_or_services);
    }
    
    
    function submitForm(){
       var category = $("#category").val();
       var service_id = $("#service_id").val();
       var service_category = $("#request_type").val();
       var free_customer_total = $("#free_customer_total").val();
       var paid_customer_total = $("#paid_customer_total").val();
       var free_vendor_total = $("#free_vendor_total").val();
       var paid_vendor_total = $("#paid_vendor_total").val();
      
       var paid_upcountry = Number($("input[name='paid_upcountry']:checked"). val());
       var free_upcountry = Number($("input[name='free_upcountry']:checked"). val());
       var free = Number($("input[name='free']:checked"). val());
       var paid = Number($("input[name='paid']:checked"). val());
       var flat_upcountry = $("#flat_upcountry").val();
       
      
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
      
       if(free === 1 || paid === 1 ){} else {
             alert("Please Select atleast One Free OR Paid Checkbox");
            return false;
       }
       
       if(free === 1){
           
            if(free_upcountry === 1 || free_upcountry === 0 || free_upcountry === -1){} else{
               alert("Please check Upcountry radio Button");
               return false;
            }
           
            if(free_upcountry === 0 && flat_upcountry === 1){
                var free_flat_upcountry_customer_price =  $("#free_flat_upcountry_customer_price").val();
                var free_upcountry_vendor_price =  $("#free_upcountry_vendor_price").val();
                
                if(free_flat_upcountry_customer_price < 1){
                    alert("Please add Upcountry Customer Paid Price");
                    return false;
                }
                
                if(free_upcountry_vendor_price < 1){
                    alert("Please add Upcountry Venodor Payout");
                    return false;
                }
            }
            
            if(free_upcountry === 1 && flat_upcountry === 1){
                var free_flat_upcountry_partner_price =  $("#free_flat_upcountry_partner_price").val();
                var free_upcountry_vendor_price =  $("#free_upcountry_vendor_price").val();
                if(free_flat_upcountry_partner_price < 1){
                    alert("Please add Upcountry Partner Offer Price");
                    return false;
                }
                
                if(free_upcountry_vendor_price < 1){
                    alert("Please add Upcountry Venodor Payout");
                    return false;
                }
            }
            
            if(free_customer_total < 1){
                alert('Please add customer total');
                return false;
            }
            
            if(free_vendor_total < 1){
                 alert('Please add vendor total');
                return false;
            }
           
       }
       
       if(paid === 1){
            if(paid_upcountry === 1 || paid_upcountry === 0 || paid_upcountry === -1){} else{
               alert("Please check Upcountry radio Button");
               return false;
            }
            
            var paid_upcountry_vendor_price = $("#paid_upcountry_vendor_price").val();
            var paid_flat_upcountry_customer_price = $("#paid_flat_upcountry_customer_price").val();
            if(paid_upcountry === 0 && flat_upcountry === 1){
                if(paid_upcountry_vendor_price < 1){
                    alert("Please add Upcountry Venodor Payout");
                    return false;
                }
                
                if(paid_flat_upcountry_customer_price < 1){
                    alert("Please add Upcountry Venodor Payout");
                    return false;
                }
            }
            
            if(paid_customer_total < 1){
                alert('Please add customer total');
                return false;
            }
            
            if(paid_vendor_total < 1){
                 alert('Please add vendor total');
                return false;
            }
            
            
       }

       url = "<?php echo base_url();?>employee/service_centre_charges/generate_service_charges";
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
            console.log(data);
          if(data === "success"){
               $('body').loadingModal('destroy');
               $("#div_duplicate").css("display","none");
               $("#duplicate_data").html("");
                swal("Thanks!", "Charges Inserted successfully!", "success");
          } else {
              $("#div_duplicate").css("display","block");
              $("#duplicate_data").html(data);
              $('body').loadingModal('destroy');
              $('body').animate({
              scrollTop: $("#div_duplicate").offset().top
              }, 2000);
               window.location.hash = "#div_duplicate";
              }


          });
       return false;
    }
    
    function enable_disable_flat_upcountry(){
        var flat_upcountry = $("#flat_upcountry").val();
        if(flat_upcountry == 1){
            $("#free_upcounry_td").css("display","grid");
            $("#paid_upcounry_td").css("display","grid");
        } else {
            $("#paid_upcounry_td").css("display","none");
            $("#free_upcounry_td").css("display","none");
        }
    }
    
    function addcheckforFlatUpcountry(free_paid, upcountry_flag){
        if(free_paid === "Free"){
            $("#free_flat_upcountry_customer_price_div").css('display', 'grid');
            $("#free_flat_upcountry_partner_price_div").css('display', 'grid');
            enable_disable_flat_upcountry();
            if(upcountry_flag === 0){
                
                $("#free_flat_upcountry_partner_price").val('0');
                $("#free_flat_upcountry_partner_price_div").css('display', 'none');
               
            } else if(upcountry_flag === 1){
               $("#free_flat_upcountry_customer_price").val('0');
               $("#free_flat_upcountry_customer_price_div").css('display', 'none');
               
            } if(upcountry_flag === -1){
                $("#free_flat_upcountry_customer_price").val('0');
                $("#free_flat_upcountry_partner_price").val('0');
                $("#free_upcountry_vendor_price").val('0');
                $("#free_upcounry_td").css("display","none");
            }
        } else {
            $("#paid_flat_upcountry_customer_price_div").css('display', 'grid');
            $("#paid_flat_upcountry_partner_price_div").css('display', 'grid');
            enable_disable_flat_upcountry();
           if(upcountry_flag === -1){
               $("#paid_upcounry_td").css("display","none");
               $("#paid_upcountry_vendor_price").val('0');
               $("#paid_flat_upcountry_customer_price").val('0');
           }
        }
    }
</script>