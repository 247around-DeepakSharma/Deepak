<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">
<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
<?php if($this->uri->segment(3)){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<div class="container-fluid">
   <div class="row" style="margin-top: 40px;">
      <div class="col-md-12">
      <?php if ($this->session->userdata('success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('success') . '</strong>
               </div>';
                }
               ?> 
         <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title" ><i class="fa fa-money fa-fw"></i> Defective Parts Need To Be Shipped</h1>
            </div>
            <div class="panel-body">
               <div class="table-responsive">
                  <form target="_blank"  action="<?php echo base_url(); ?>employee/service_centers/print_partner_address_challan_file" name="fileinfo1"  method="POST" enctype="multipart/form-data">
                   <table class="table table-bordered table-hover table-striped">
                       <thead>
                           <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Booking Id</th>
                            <th class="text-center">User Name</th>
                            <th class="text-center">Age of Pending</th>
                            <th class="text-center">Parts Received</th>
                            <th class="text-center">Parts Code </th>
                             <th class="text-center">Amount</th>
                            <th class="text-center">Remarks By Partner</th>
                            <th class="text-center" >Address <input type="checkbox" id="selectall_address" > </th>
                            <th class="text-center" >Challan<input type="checkbox" id="selectall_challan_file" > </th>   
                            <th class="text-center" >Bulk Send<input type="checkbox" id="selectall_send_courier" > </th>                          
                            <th class="text-center">Update</th>
                           </tr>
                       </thead>
                       <tbody>
                           <tbody>
                                <?php  foreach($spare_parts as $key =>$row){ ?>
                               <tr style="text-align: center;<?php if(!is_null($row['remarks_defective_part_by_partner'])){ echo "color:red"; }?>">
                                    <td>
                                        <?php echo $sn_no; ?>
                                    </td>
                                    <td>
                                         <a  href="<?php echo base_url();?>service_center/booking_details/<?php echo urlencode(base64_encode($row['booking_id']));?>"  title='View'><?php echo $row['booking_id'];?></a>
                                    </td>
                                     <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                    <td>
                                        <?php if(!is_null($row['service_center_closed_date'])){  $age_shipped = date_diff(date_create($row['service_center_closed_date']), date_create('today'));   echo $age_shipped->days. " Days";} ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['parts_shipped']; ?>
                                    </td>
                                    <td style="word-break: break-all;">
                                        <?php echo $row['part_number']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['challan_approx_value']; ?>
                                    </td>

                                    <td>
                                        <?php if(!is_null($row['remarks_defective_part_by_partner'])){  echo $row['remarks_defective_part_by_partner']; } else { echo $row['remarks_by_partner'];} ?>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-control checkbox_address" onclick="remove_select_all()" name="download_address[]"  value="<?php echo $row['id'];?>" />
                                    </td>
                                    <td>

                                      <?php if(!$partner_on_saas){ ?>
                                             <input type="checkbox" class="form-control checkbox_challan" onclick="remove_select_all_challan()" name="download_challan[]"  value="<?php echo $row['challan_file'];?>" />
                                     <?php  }else{ ?>

                                      <input type="checkbox" class="form-control checkbox_challan" onclick="remove_select_all_challan()" name="download_challan[<?php echo $row['defective_return_to_entity_id'];  ?>][]"  value="<?php echo $row['id']?>" />

                                      <?php } ?>
                                       
                                    </td>  

                                    <td>
                                      <input type="checkbox" data-booking_partner_id="<?php echo $row['booking_partner_id'];?>"  data-sf_id="<?php echo $row['service_center_id'];?>" data-mobile="<?php echo $row['mobile'];?>" data-user_name="<?php echo $row['name'];?>" class="form-control checkbox_courier" onclick="remove_select_all_courier()" name="send_courier[]"  value="<?php echo $row['id'];?>" />
                                    </td>                                
                      
                                    <td>
                                         <a href="<?php echo base_url() ?>service_center/update_defective_parts/<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" style="background-color:#2C9D9C; border-color: #2C9D9C;" ><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>
                                    </td>

                                </tr>
                                <?php $sn_no++; } ?>
                            </tbody>
                        </table>
                      
                      <center> 
                          <input type= "submit" id="button_send" class="btn btn-danger" onclick='return check_checkbox()' style="text-align: center; background-color:#2C9D9C; border-color: #2C9D9C;"  data-toggle="modal" value ="Print Shipment Address / Challan File /  Send Dective Parts" >

                      </center>
                  </form>

                        </div>
                   
               </div>
            </div>
         </div>
      </div>
   </div>
<div class="custom_pagination" style="margin-left: 16px;" > <?php if(isset($links)) echo $links; ?></div>
</div>


<div id="courier_update" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <form id="idForm"  action="<?php echo base_url(); ?>employee/service_centers/do_multiple_spare_shipping"  method="POST" enctype="multipart/form-data" onsubmit="return submitForm();">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title">Send Bulk Courier</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="awb" class="col-md-4">AWB *</label>
                                    <div class="col-md-6">
                                        <input onblur="check_awb_exist()" type="text" class="form-control" id="awb_by_sf" name="awb_by_sf" value="" placeholder="Please Enter AWB" required="">
                                    </div>
                                                                    </div>
                                <div id="courier_charges_by_sfrow" class="form-group ">
                                    <label for="courier_charges_by_sf" class="col-md-4">Courier Charges</label>
                                    <div class="col-md-6">
                                        
                                        <input type="text" class="form-control" id="courier_charges_by_sf" name="courier_charges_by_sf" value="" placeholder="Please Enter Courier Charges" required="">
                                    </div>
                                                                    </div>
                                <div class="form-group ">
                                    <label for="awb" class="col-md-4">No Of Boxes *</label>
                                    <div class="col-md-6">
                                        <select class="form-control" id="defective_parts_shipped_boxes_count" name="defective_parts_shipped_boxes_count" required="">
                                            <option selected="" disabled="" value="">Select Boxes</option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                        <option value="5">5</option>
                                                                                        <option value="6">6</option>
                                                                                        <option value="7">7</option>
                                                                                        <option value="8">8</option>
                                                                                        <option value="9">9</option>
                                                                                        <option value="10">10</option>
                                                                                    </select>
                                    </div>
                                                                    </div>
                                <div class="form-group       " id="exist_courier_image_row">
                                    <label for="AWS Receipt" class="col-md-4">Courier Invoice *</label>
                                    <div class="col-md-6">
                                        <input id="aws_receipt" class="form-control" name="defective_courier_receipt" type="file" required="" value="" style="background-color:#fff;pointer-events:cursor">
                                                                                <input type="hidden" class="form-control" value="" id="exist_courier_image" name="exist_courier_image">
                                    </div>
                                                                    </div>
                                                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="courier" class="col-md-4">Courier Name *</label>
                                    <div class="col-md-6">
                                        <select class="form-control" id="courier_name_by_sf" name="courier_name_by_sf" required="">
                                            <option selected="" disabled="" value="">Select Courier Name </option>
                                             
                                               <?php foreach ($courier_details as $value1) { ?> 
                                            <option <?php if ((set_value("courier_name_by_sf") == $value1['courier_name'])) {
                                                echo "selected";
                                                } ?> value="<?php echo $value1['courier_code']; ?>"><?php echo $value1['courier_name']; ?></option>
                                            <?php } ?>
                                             
                                            </select>
                                    </div>
                                                                    </div>
                                <div class="form-group ">
                                    <label for="shipment_date" class="col-md-4">Shipment Date *</label>
                                    <div class="col-md-6">
                                        <div class="input-group input-append date">
                                            <input id="defective_part_shipped_date" class="form-control" name="defective_part_shipped_date" type="text" value="2019-07-03" required="" readonly="true" style="background-color:#fff;pointer-events:cursor">
                                            <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                                                    </div>
                                <div class="form-group ">
                                    <label for="courier" class="col-md-4">Weight *</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" style="width: 25%; display: inline-block;" id="defective_parts_shipped_weight_in_kg" name="defective_parts_shipped_kg" value="" placeholder="Weight" required=""> <strong> in KG</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="number" class="form-control" style="width: 25%; display: inline-block;" id="defective_parts_shipped_weight_in_gram" value="" name="defective_parts_shipped_gram" placeholder="Weight" required="">&nbsp;<strong>in Gram </strong>                                       
                                    </div>
                                                                    </div>
                                <div class="form-group ">
                                    <label for="remarks_defective_part" class="col-md-4">Remarks *</label>
                                    <div class="col-md-6">
                                        <textarea type="text" class="form-control" id="remarks" name="remarks_defective_part" placeholder="Please Enter Remarks" required=""></textarea>
                                    </div>
                                                                    </div>
                            </div>
                        </div>

                        <input type="hidden" name="courier_boxes_weight_flag" id="courier_boxes_weight_flag" value="0">
                        <input type="hidden" name="courier_charges_by_sf" id="courier_charges_by_sf" value="0">
                        <input type="hidden" name="sp_ids" id="spareids" value="">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
        </div>
        </form>
    </div>
</div>
 <div class="loader hide"></div>
 <style>
    .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
  }
</style>

<script>
    function submitForm(){
       event.preventDefault();
       $(".loader").removeClass('hide');
       if( $("#courier_charges_by_sf_hidden").val()!=0)
        {
            $("#courier_charges_by_sf").val( $("#courier_charges_by_sf_hidden").val())
        }
       var form_data = new FormData(document.getElementById("idForm"));

               $.ajax({
                   url: "<?php echo base_url()  ?>employee/service_centers/do_multiple_spare_shipping",
                   type: "POST",
                   data: form_data,
                   processData: false,  // tell jQuery not to process the data
                   contentType: false   // tell jQuery not to set contentType
                   }).done(function(response) {
                         console.log(response);
                          
                            $(".loader").addClass('hide');
                             swal({title: "Updated !", text: "Your courier details updated .", type: "success"},
                              function(){ 
                              location.reload();
                             }
                   );
                        
               });
 
    }


    $('#defective_part_shipped_date').daterangepicker({
        autoUpdateInput: false,
        singleDatePicker: true,
        showDropdowns: true,
        minDate: false,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
    
    $('#defective_part_shipped_date').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });
    
    $('#defective_part_shipped_date').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });

function check_checkbox(){
   
    var flag =0;
    //$('.checkbox_address').each(function (i) {
       
        var d_m = $('.checkbox_address:checked');
        if(d_m.length > 0){
            flag = 1;  
       }
       
       if(flag === 0){
           var d_m = $('.checkbox_challan:checked');
           if(d_m.length > 0){
               flag = 1;  
           }
       }

      if(flag === 0){
           var c_m_c = $('.checkbox_courier:checked');
           if(c_m_c.length > 0){
               flag = 1; 
           }
       }

    //});
        
    if(flag ===0 ){
        alert("Please Select Atleast One Checkbox");
        return false;
    }
}

$("#selectall_address").change(function(){
       var d_m = $('.checkbox_challan:checked');
       var d_mm = $('.checkbox_courier:checked');
        if (d_m.length > 0 || d_mm.length > 0) {
            $('.checkbox_challan').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
        }
       
       $(".checkbox_address").prop('checked', $(this).prop("checked"));
       $("#button_send").val("Print Shipment Address") ;
       $("#button_send").attr("type","submit") ;
       $("#button_send").removeAttr("data-target") ;

        
});

$("#selectall_challan_file").change(function () {
        var d_m = $('.checkbox_address:checked');
        var d_mm = $('.checkbox_courier:checked');
        if (d_m.length > 0 || d_mm.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('#selectall_address').prop('checked', false);
          //  $('#selectall_challan_file').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
        }
        $(".checkbox_challan").prop('checked', $(this).prop("checked"));
        $("#button_send").val("Challan File") ;
        $("#button_send").attr("type","submit") ;
        $("#button_send").removeAttr("data-target") ;
});
    


$("#selectall_send_courier").change(function () {
        var d_m = $('.checkbox_address:checked');
        var d_mm = $('.checkbox_challan:checked');
        if (d_m.length > 0 || d_mm.length > 0) {

            $('.checkbox_challan').prop('checked', false);
             $('.checkbox_address').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_address').prop('checked', false);
        }
        $(".checkbox_courier").prop('checked', $(this).prop("checked"));
        $("#button_send").val("Send Defective Parts") ;
        $("#button_send").attr("type","button") ;
        $("#button_send").attr("data-target","#courier_update") ;
    });


function remove_select_all(){
    $('#selectall_address').prop('checked', false); 
    $('#selectall_send_courier').prop('checked', false); 
    $('#selectall_challan_file').prop('checked', false); 
    var d_m = $('.checkbox_challan:checked');
    var d_m_d = $('.checkbox_courier:checked');
    if (d_m.length > 0 || d_m_d.length > 0) {
            $('.checkbox_challan').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
    }
}

function remove_select_all_challan(){
    $('#selectall_challan_file').prop('checked', false); 
    $('#selectall_send_courier').prop('checked', false); 
    $('#selectall_address').prop('checked', false);
    var d_m = $('.checkbox_address:checked');
    var d_m_d = $('.checkbox_courier:checked');
    if (d_m.length > 0 || d_m_d.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('.checkbox_courier').prop('checked', false);
            $('#selectall_address').prop('checked', false);
            $('#selectall_send_courier').prop('checked', false);
    }
}

function remove_select_all_courier(){
    $('#selectall_send_courier').prop('checked', false); 
    $('#selectall_challan_file').prop('checked', false);
    $('#selectall_address').prop('checked', false); 
    var d_m = $('.checkbox_address:checked');
    var d_m_d = $('.checkbox_challan:checked');
    if (d_m.length > 0 || d_m_d.length > 0) {
            $('.checkbox_address').prop('checked', false);
            $('.checkbox_challan').prop('checked', false);
            $('#selectall_address').prop('checked', false);
            $('#selectall_challan_file').prop('checked', false);
    }
}



$(".checkbox_challan").click(function(){
if($('.checkbox_challan:checkbox:checked').length > 0){
    $("#button_send").val("Challan File") ;
    $("#button_send").attr("type","submit") ;
    $("#button_send").removeAttr("data-target") ;
}
});

$(".checkbox_address").click(function(){
if($('.checkbox_address:checkbox:checked').length > 0){
    $("#button_send").val("Print Shipment Address") ;
    $("#button_send").attr("type","submit") ;
    $("#button_send").removeAttr("data-target") ;
}
});


$(".checkbox_courier").click(function(){
if($('.checkbox_courier:checkbox:checked').length > 0){
    $("#button_send").val("Send Defective Parts") ;
    $("#button_send").attr("type","button") ;
    $("#button_send").attr("data-target","#courier_update") ;

}
});

 
 $("#button_send").click(function(){
 yourArray=[];

      $(".checkbox_courier:checked").each(function(){
       yourArray.push($(this).val());
       });
 
$("#spareids").val(yourArray);
  
       
 });




 function check_awb_exist() {
        var awb = $("#awb_by_sf").val();
         var characterReg = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
            if (characterReg.test(awb) && awb != '') {
                awb = '';
                $("#awb_by_sf").val('');
                alert('Special Characters are not allowed in AWB.');
                return false;
            }  
        if (awb!='') {
            $.ajax({
                type: 'POST',
                beforeSend: function () {
    
                    $('body').loadingModal({
                        position: 'auto',
                        text: 'Loading Please Wait...',
                        color: '#fff',
                        opacity: '0.7',
                        backgroundColor: 'rgb(0,0,0)',
                        animation: 'wave'
                    });
    
                },
                url: '<?php echo base_url() ?>employee/service_centers/check_sf_shipped_defective_awb_exist',
                data: {awb: awb},
                success: function (response) {
                    console.log(response);
                    var data = jQuery.parseJSON(response);
                    if (data.code === 247) {
    
                        //$("#same_awb").css({"color": "green", "font-weight": "900"});
                        //  $("#same_awb").css("font-wight",900);
                        //alert("This AWB already used same price will be added");
                        //$("#same_awb").css("display", "block");
                        $('body').loadingModal('destroy');
    
                        
    
                        $("#defective_part_shipped_date").val(data.message[0].defective_part_shipped_date);
                        
                        $("#courier_name_by_sf").val("");
                        $("#courier_name_by_sf").attr('readonly',"readonly");
                        $("#courier_name_by_sf").css("pointer-events","none");
                        var courier = data.message[0]['courier_name_by_sf'].toLowerCase();
                        // $('#courier_name_by_sf option[value="'+data.message[0].courier_name_by_sf.toLowerCase()+'"]').attr("selected", "selected");
                        $('#courier_name_by_sf').val(courier).trigger('change');
                        if(data.message[0].courier_charge > 0){
                            $("#courier_charges_by_sf").val(data.message[0].courier_charge);
                            $("#courier_charges_by_sf_hidden").val(data.message[0].courier_charge);
                           // $("#courier_charges_by_sf").attr('readonly', "readonly");
                        }
                        
                        
                        
                        // $("#courier_charges_by_sf").css("display","none");
                        $('#defective_parts_shipped_boxes_count option[value="' + data.message[0]['box_count'] + '"]').attr("selected", "selected");
                        if (data.message[0]['box_count'] === 0) {
                            $('#defective_parts_shipped_boxes_count').val("");
                            
                        } else {
                            $('#defective_parts_shipped_boxes_count').val(data.message[0]['box_count']).trigger('change');
    
                        }
                        
                         $("#courier_boxes_weight_flag").val(data.message[0]['partcount'] );
                            $("#aws_receipt").removeAttr("required");
    
                        if (data.message[0].defective_courier_receipt) {
    
                            $("#exist_courier_image").val(data.message[0].defective_courier_receipt);
                            $("#aws_receipt").css("display", "none");
                        }
                       
                        //    alert(data.message[0]['partcount'])
                        var wt = Number(data.message[0]['billable_weight']);
                        if(wt > 0){
                            var wieght = data.message[0]['billable_weight'].split(".");
                            $("#defective_parts_shipped_weight_in_kg").val(wieght[0]).attr('readonly', "readonly");
                            $("#defective_parts_shipped_weight_in_gram").val(wieght[1]).attr('readonly', "readonly");
                        }
                        
                    } else {
                        $('body').loadingModal('destroy');
                        $("#aws_receipt").css("display", "block");
                        $("#courier_charges_by_sf").css("display", "block");
                        $("#same_awb").css("display", "none");
                        $("#exist_courier_image").removeAttr("readonly");
                        $("#courier_name_by_sf").val("");
                        $("#courier_name_by_sf").val("");
                        $("#courier_charges_by_sf").removeAttr('readonly');
                        $("#courier_charges_by_sf").val("");
                        $("#aws_receipt").attr("required","required");
                        $("#defective_part_shipped_date").val("");
                        $("#defective_parts_shipped_boxes_count").val("");
                        $("#defective_parts_shipped_weight_in_kg").removeAttr('readonly');
                        $("#defective_parts_shipped_weight_in_gram").removeAttr('readonly');
                        $("#defective_parts_shipped_weight_in_kg").val("");
                        $("#defective_parts_shipped_weight_in_gram").val("");
                        $("#remarks").val("");
                        $("#aws_receipt").css("display", "block");
                        $("#courier_boxes_weight_flag").val("0" );
                        
                    }
    
                }
            });
        }
    
    }




</script>
<style type="text/css">
  .sweet-alert {

    width: 700px !important;
    left: 46% !important;
  }
  .modal-lg {
    /* width: 1300px; */
    width: 95% !important;
}
.form-control{
    margin-bottom: 10px;
}
.input-group{
   margin-bottom: 10px; 
}
</style>
