<?php
$tab_class = !empty($data_id) ? $data_id : "all";
if(is_numeric($this->uri->segment(3)) && !empty($this->uri->segment(3))){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} 
?>
<script type="text/javascript" src="<?php echo base_url();?>js/base_url.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/review_bookings.js"></script>      
<input type='hidden' name='arr_bookings' id='arr_bookings' value='<?= json_encode($bookings_data); ?>'>
<input type="hidden" name="comment_booking_id" value="" id="comment_booking_id">
<div class="" style="margin-top: 30px;">
         <div class="row">
                <?php if($status == 'Completed') { ?>
                    <div class="col-md-1 pull-right" style="margin-top:20px;">
                        <a href="javascript:void(0);" class="btn btn-primary pull-right download" name="download_complete_booking" value="Download" title="Download Complete Bookings List">Export</a>
                    </div>
                <?php } ?>                
                <?php if($status == 'Cancelled') { ?>
                    <div class="col-md-1 pull-right" style="margin-top:20px;">
                        <a href="javascript:void(0);" class="btn btn-primary pull-right download" name="download_cancelled_booking" value="Download" title="Download Cancelled Bookings List">Export</a>
                    </div>
                <?php } ?>
            <div class="col-md-3 pull-right" style="margin-top:20px;">
               
                <input type="search" class="form-control pull-right"  id="search_<?=$review_status?>_<?=$is_partner?>" placeholder="search" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>)">
            </div>
             
             <?php if($status == 'Completed') { ?>
             <div class="col-md-3 pull-right" style="margin-top:20px;">
              
                
                <select type="text" class="form-control"  id="state_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>" name="state" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>)">
                    <option value=""></option>
                    <?php foreach($states as $state) { ?>
                    <option value="<?= $state['state_code']; ?>" <?php if(!empty($state_selected) && $state['state_code'] == $state_selected) { echo 'selected';} ?>><?= $state['state']; ?></option>
                  
                    <?php } ?>
                </select>
               
                
            </div>
             <div class="col-md-3 pull-right" style="margin-top:20px;">
              
                
                <select type="text" class="form-control"  id="partner_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>" name="partner" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>)">
                    <option value=""></option>
                    <?php foreach($partners as $partner) { ?>
                    <option value="<?= $partner['id']; ?>" <?php if(!empty($partner_selected) && $partner['id'] == $partner_selected) { echo 'selected';}?>><?= $partner['public_name']; ?></option>
                  
                    <?php } ?>
                </select>
               
                
            </div>
                 
                 
            
             <?php } if($status == 'Cancelled') { 
              ?>
             <div class="col-md-3 pull-right" style="margin-top:20px;">
              
                
                <select type="text" class="form-control"  id="cancellation_reason_<?php echo $is_partner; ?>" name="cancellation_reason" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>)">
                    <option value=""></option>
                    <?php foreach($cancellation_reason as $reason) { ?>
                    <option value="<?= $reason['id']; ?>" <?php if(!empty($cancellation_reason_selected) && $reason['id'] == $cancellation_reason_selected) { echo 'selected';}?>><?= $reason['reason']; ?></option>
                  
                    <?php } ?>
                </select>
               
                
            </div>
             <div class="col-md-3 pull-right" style="margin-top:20px;">
              
                
                <select type="text" class="form-control"  id="state_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?>" name="state" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>)">
                    <option value=""></option>
                    <?php foreach($states as $state) { ?>
                    <option value="<?= $state['state_code']; ?>" <?php if(!empty($state_selected) && $state['state_code'] == $state_selected) { echo 'selected';} ?>><?= $state['state']; ?></option>
                    
                  
                    <?php } ?>
                </select>
               
                
            </div>
             <div class="col-md-3 pull-right" style="margin-top:20px;">
              
                
                <select type="text" class="form-control"  id="partner_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?>" name="partner" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>)">
                    <option value=""></option>
                    <?php foreach($partners as $partner) { ?>
                    <option value="<?= $partner['id']; ?>" <?php if(!empty($partner_selected) && $partner['id'] == $partner_selected) { echo 'selected';}?>><?= $partner['public_name']; ?></option>
                  
                    <?php } ?>
                </select>
               
                
            </div>
             
             <?php } ?>
             <h2 style="margin-left: 13px;" >
                  <b><?php echo $status; ?> Bookings</b>
               </h2>
               <form action="<?php echo base_url();?>employee/booking/checked_complete_review_booking" method="post">
                  <div class="col-md-12" style="font-size:82%;">
                      <table class="table table-bordered table-hover table-striped" id="completed_cancelled_review_table">
                        <thead>
                           <tr>
                              <th class="jumbotron" >S.N.</th>
                              <th class="jumbotron" >Booking Id</th>
<!--                              <th class="jumbotron" >Service Center </th>-->
                              <th class="jumbotron" style="text-align: center;">Price Details</th>
                              <th class="jumbotron" >Amount Due</th>
                              <th class="jumbotron" >Amount Paid</th>
                              <th class="jumbotron" >Age</th>
                              <?php
                              if($review_status == "Completed"){
                              ?>
                                <th class="jumbotron" >Warranty Status</th>
                              <?php
                              }
                              ?>                              
                              <th class="jumbotron" >Admin Remarks</th>
                              <th class="jumbotron" >Vendor Remarks</th>
                              <th class="jumbotron" >Vendor Cancellation Reason</th>
                              <th class="jumbotron" ><input type="checkbox" id="selecctall" class="selecctall <?php echo $tab_class?>" data-id="<?php echo $tab_class?>"/></th>
                              <th class="jumbotron" >Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $count =1;$initial_offset = $offset; foreach ($charges as $key => $value) { ?>
                            <tr id="<?php echo  "row_".$value['booking_id'] ?>">
                              <?php $offset++ ;?>
                              <td style="text-align: left;white-space: inherit;font-size:80%"><?php echo $offset; ?></td>
                              
                              <td  style="text-align: left;white-space: inherit;"><?php echo $value['booking_id']." <br/><br/>".$value['booking'][0]['vendor_name']?><?php if(!empty($value['sf_purchase_invoice'])) { echo "<br/><br/><a href='https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$value['sf_purchase_invoice']."' target=\"_blank\">Invoice</a>"; }?>
                                 
                                  <input type="hidden" name="booking_id[]" value="<?php echo $value['booking_id']; ?>" id="<?php echo "booking_id".$count; ?>">
                                  <input type="hidden" name="approved_by" value='<?php echo _247AROUND ?>'  id="approved_by">
                                  <input type="hidden" name="booking_request_type[]" value="<?=$value['request_type']?>"  class="booking_request_type_<?=$value['booking_id']?>">
                                  <input type="hidden" name="booking_warranty_status[]" value=''  class="booking_warranty_status_<?=$value['booking_id']?>">
                              </td>

                            <input type="hidden" class="form-control" id="partner_id" name="partner_id[<?php echo $value['booking_id']; ?>]" value = "<?php echo $value['booking'][0]['partner_id'];?>" >

                            <td style="text-align: left;white-space: inherit; <?php if(isset($value['unit_details'][0]['mismatch_pincode'])){ if($value['unit_details'][0]['mismatch_pincode'] == 1){ echo "background-color:red;";} }?>">
                                 <table  class="table table-condensed">
                                    <thead>
                                        <th class="jumbotron" >Brand</th>
                                       <th class="jumbotron" >Category/Capacity</th>
                                       <th class="jumbotron" >Model No</th>
                                       <th class="jumbotron" >Serial Number</th>
                                       <th class="jumbotron" >Tags</th>
                                       <th class="jumbotron" >Service Charge</th>
                                       <th class="jumbotron" >Additional Service Charge</th>
                                       <th class="jumbotron" >Parts Cost</th>
                                       <th class="jumbotron" >Upcountry Charges</th>
                                       <th class="jumbotron" >IS Broken</th>
                                       <th class="jumbotron" >Vendor Status</th>
                                    </thead>
                                    <tbody>
                                       <?php foreach ($value['unit_details'] as $key1 => $value1) {
                                            $style = "";
                                           
                                            if($value1['customer_net_payable'] > 0 && $value1['internal_status'] == "Completed" 
                                                   && ($value1['service_charge'] + $value1['additional_service_charge'] + $value1['parts_cost']) ==0 ){
                                                $style = "background-color:#FF8080";
                                                
                                            } else if($value1['internal_status'] == "Completed" && $value1['customer_net_payable'] ==0  && 
                                                    ($value1['service_charge'] + $value1['additional_service_charge'] + $value1['parts_cost']) > 0){
                                                 $style = "background-color:#4CBA90";
                                            }
   
                                               ?>
                                       <tr style="<?php echo $style?>">
                                            <td><span class="<?php echo "brand".$count; ?>"><?php echo $value1['appliance_brand']; ?></span></td>
                                           <td><span class="<?php echo "category".$count; ?>"><?php echo $value1['appliance_category']."/". $value1['appliance_capacity']; ?></span></td>
                                           <td><span class="<?php echo "model_number".$count; ?>"><?php echo $value1['model_number']; ?></span></td>
                                           <td>
                                              <?php if(!empty($value1['serial_number_pic'])) {?>
                                               <input type="hidden" style="display:none;" value="<?php echo $value1['is_sn_correct'] ?>" class=<?php echo "sn_".$value['booking_id']; ?>>
                                              <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/<?php echo SERIAL_NUMBER_PIC_DIR;?>/<?php echo $value1['serial_number_pic'];?>"> 
                                                  <span class="<?php if($value1['is_sn_correct']==IS_SN_CORRECT){ echo "text-danger ";}else{ echo "text-info ";}?><?php echo "serial_number".$count; ?>"><?php echo $value1['serial_number']; ?></span></a>
                                              <?php } else {
                                                  if($value1['serial_number']){ ?>
                                                      <input type="hidden" style="display:none;" value="<?php echo $value1['is_sn_correct'] ?>" class=<?php echo "sn_".$value['booking_id']; ?>>
                                                      <?php
                                                   }
                                              ?>
                                               <span class="<?php if($value1['is_sn_correct']==IS_SN_CORRECT){ echo "text-danger ";}else{ echo "text-info ";}?><?php echo "serial_number".$count; ?>"><?php echo $value1['serial_number']; ?></span>
                                               <?php
                                               } ?>
                                          </td>
                                          <td><span class="<?php echo "price_tags".$count; ?>"><?php echo $value1['price_tags']; ?></span></td>
                                          <td>
                                             <span id="<?php echo "service_charge".$count; ?>"><?php echo $value1['service_charge']; ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "additional_charge".$count; ?>"><?php echo $value1['additional_service_charge']; ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "parts_cost".$count;?>"><?php echo $value1['parts_cost']; ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "upcountry".$count;?>"><?php  echo $value1['upcountry_charges']; ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "broken".$count;?>"><?php if($value1['is_broken'] == 1){ echo "Yes";} else{ echo "No";} ?></span>
                                          </td>
                                          <td>
                                             <span id="<?php echo "internal_status".$count; ?>"><?php echo $value1['internal_status']; ?></span>
                                          </td>
                                       </tr>
                                       <?php } ?>
                                    </tbody>
                                 </table>
                              </td>
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $value['booking'][0]['amount_due']; ?></strong></td>
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $value['amount_paid']; ?></strong></td>
                              <?php
                                $now = time();
                                $initial_booking_date = strtotime($value['booking'][0]['initial_booking_date']);
                                $datediff = $now - $initial_booking_date;
                                $booking_age = 0;
                                if($datediff >= 0){
                                    $booking_age =  ceil($datediff / (60 * 60 * 24));
                                }
                                
                              ?>
                              
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $booking_age ?></strong></td>
                              <?php if($review_status == "Completed"){ ?>
                              <td class="warranty-<?= $value['booking_id']?>">--</td>
                              <?php } ?>
                              <td style="text-align: left;white-space: inherit;">
                                 <p id="<?php echo "admin_remarks_".$count; ?>"><?php echo $value['admin_remarks']; ?></p>
                              </td>
                              <input type="hidden" id="<?php echo "admin_remarks".$count;?>" value="<?php echo $value['admin_remarks'];?>"></input>
                              <td style="text-align: left;white-space: inherit;font-size:90%">
                                 <p id="<?php echo "service_center_remarks".$count; ?>"><?php echo $value['service_center_remarks']; ?></p>
                              </td>
                              <td style="text-align: left;white-space: inherit;font-size:90%">
                                 <p id="<?php echo "cancellation_reason".$count; ?>"><?php echo $value['cancellation_reason']; ?></p>
                              </td>
                              <td><input id="approved_close" type="checkbox"  class="checkbox1 <?php echo $tab_class;?> <?php echo "app_".$value['booking_id'];?>" name="approved_booking[]" value="<?php echo $value['booking_id']; ?>"
                                         <?php if($status == _247AROUND_COMPLETED){?> onchange="is_sn_correct_validation('<?php echo $value['booking_id']?>')"<?php } ?>></input></td>
                              <td>
                                 <?php echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "employee/booking/viewdetails/$value[booking_id] target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                              <a style="margin-top:5px;" target='_blank'  href="<?php echo base_url(); ?>employee/booking/get_complete_booking_form/<?php echo $value['booking_id']; ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil" aria-hidden="true" title="Edit"></i></a>
                              <button style="margin-top:5px;" type="button" id="<?php echo "remarks_".$count;?>" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" onclick="open_admin_remarks_modal('<?php echo $value['booking_id']; ?>')"><i class="fa fa-times" aria-hidden="true" title="Reject"></i></button>
                              <a style="margin-top:5px;" class="btn btn-success" id='<?php echo 'comment_'.$count; ?>' href="javascript:void(0);" name="save-remarks" onclick="save_remarks('<?php echo $value['booking_id']; ?>')"><i class="fa fa-comment"></i></a>
                              </td>
                           
                            </tr>
                           <?php $count++; } ?>
                        </tbody>
                     </table>
                     <?php if(!empty($charges)){?>
                     <div class="col-md-12">
                        <center><input type="submit" value="Approve Bookings" onclick="return checkValidationForBlank_review()" style=" background-color: #2C9D9C;
                           border-color: #2C9D9C;"  class="btn btn-md btn-success"></center>
                     </div>
                     <?php } ?>
                       </div>
               </form>
            
         </div>
      </div>
<div class = 'msg_holder' style="float:left;"> <?php echo "<p>Showing ".(($initial_offset)+1)." to ". ($offset)." of ".$total_rows." entries</p>"; ?></div>
<div class="link_holder" style="float:right;">
 <?php

             if($is_partner){
                 $tab = "#tabs-4";
             }
             else{
                    $tab = "#tabs-3";
                    if($review_status == "Completed"){
                        $tab = "#tabs-2";
                    }
                    else if($review_status == "Completed_By_SF"){
                        $tab = "#tabs-5";
                    }
             }
             for($i=0;$i<=$total_pages;$i++){
                 $offset = $per_page*$i;
                 ?>
    <a id="link_<?php echo $i;?>" style="background: #d7eaea;padding: 5px;" onclick="load_view('employee/booking/review_bookings_by_status/<?php echo  $review_status?>/<?php echo $offset;?>/<?php echo $is_partner; ?>/0/<?php echo $cancellation_reason_selected; ?>/<?php echo $partner_selected;?>/<?php echo $state_selected; ?>','<?php echo $tab ?>','link_<?php echo $i;?>')"><?php echo $i+1; ?></a>
                 <?php
             }
             ?>
                 </div>

   <div id="model_remarks_<?=$review_status?>_<?=$is_partner?>" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="modal-title-<?=$review_status?>_<?=$is_partner?>">Modal Header</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <center><img id="loader_gif_<?=$review_status?>_<?=$is_partner?>" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                </div>
                <input type="hidden" name="modal_booking_id" id="modal_booking_id_<?=$review_status?>_<?=$is_partner?>" value="">
                <textarea rows="8" class="form-control textarea" id="textarea_<?=$review_status?>_<?=$is_partner?>"></textarea>
            </div>
            <input type="hidden" id="id_no">
            <input type="hidden" value='<?php echo _247AROUND; ?>' id="admin_id_<?=$review_status?>_<?=$is_partner?>">
            <input type="hidden" value="<?php echo $status; ?>" id="internal_boking_status_<?=$review_status?>_<?=$is_partner?>" class="internal_boking_status_<?=$review_status?>_<?=$is_partner?>">
            <div class="modal-footer">
               <button type="button" class="btn btn-success" onclick="send_remarks_multitab('<?=$review_status?>','<?=$is_partner?>')" id="btn_send_remarks_<?=$review_status?>_<?=$is_partner?>">Send</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
         </div>
      </div>
   </div>
   <div id="commentModal_<?=$review_status?>_<?=$is_partner?>" class="modal fade" role="dialog">
      <div class="modal-dialog" style=" height: 90% !important;">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-body">
                <div id="commentbox_<?=$review_status?>_<?=$is_partner?>"></div>
            </div>
         </div>
      </div>
   </div>
<script>
    $('.download').on('click', function() {
        var download_btn = $(this).attr('name');
        
        if(download_btn == 'download_complete_booking') {
            var partner_id = $('#partner_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>').val();
            var state_id = $('#state_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>').val();
            
            window.open("<?php echo base_url(); ?>employee/booking/download_review_bookings_data?partner_id="+partner_id+"&state_id="+state_id+"&is_partner=<?php echo $is_partner; ?>&review_status=<?php echo $review_status;?>", '_blank');
        }
        
        if(download_btn == 'download_cancelled_booking') {
            var partner_id = $('#partner_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?>').val();
            var state_id = $('#state_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?>').val();
            var cancellation_reason_id = $('#cancellation_reason_<?php echo $is_partner; ?>').val();

            window.open("<?php echo base_url(); ?>employee/booking/download_review_bookings_data?partner_id="+partner_id+"&state_id="+state_id+"&is_partner=<?php echo $is_partner; ?>&review_status=<?php echo $review_status;?>&cancellation_reason_id="+cancellation_reason_id, '_blank');
        }
    });

    $('#cancellation_reason_<?php echo $is_partner; ?>').select2({
       placeholder: 'Cancellation Reason'
    }); 
    $('#state_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?>').select2({
       placeholder: 'State'
    }); 
    $('#partner_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?>').select2({
       placeholder: 'Partner'
    });    
    $('#partner_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>').select2({
       placeholder: 'Partner'
    });    
    $('#state_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>').select2({
       placeholder: 'State'
    });    
   
   $(document).ready(function(){
        $(".selecctall").change(function(){
            var dataId = $(this).attr('data-id');
            var isChecked = $("."+dataId).prop("checked");
            
            $("."+dataId).prop('checked', $(this).prop("checked"));
            if(isChecked){
                var outputArray = []; 
                $('.'+dataId).each(function() {
                      outputArray.push(is_sn_correct_validation($(this).val(),'Yes'));
                 })
                  if(outputArray.includes('no')){
                        alert("Review Booking Listing Contains Booking WIth Wrong Serial number All Wrong Serial number booking will be auto unselected");
                  }
                  }
         });
        
        // this ajax fetches the warranty status of showed bookings
        var bookings_data = $('#arr_bookings').val();
        var arr_bookings_data = JSON.parse(bookings_data);
        for (var rec_bookings_data in arr_bookings_data) {
            $.ajax({
                method:'POST',
                url:"<?php echo base_url(); ?>employee/booking/get_warranty_data",
                data:{'bookings_data': arr_bookings_data[rec_bookings_data]},
                success:function(response){
                    var warrantyData = JSON.parse(response);
                    $.each(warrantyData, function(index, value) {
                        $(".warranty-"+index).html(value);
                        $(".booking_warranty_status_"+index).val(value);
                    });
                }                            
            }); 
        }        
   });
     function is_sn_correct_validation(booking_id,bulkalert){
       if(bulkalert !== 'Yes'){
           bulkalert = false;
       }
       temp = true;
       booking_sn_div_id =  "sn_"+booking_id;
       current_div_booking =  "app_"+booking_id;
        $('.'+booking_sn_div_id).each(function() {
            if($(this).val() == 0){
                temp = false;
                $("."+current_div_booking).prop("checked", false);
            }
        })
        if(!temp && !bulkalert){
                alert("Booking "+ booking_id + " Contains Wrong Serial number It can not be approved in Bulk Approval, Booking automatic will be unselected");
        }
        if(!temp){
            return 'no';
        }
        else{
             return 'yes';
        }
   }
   function checkValidationForBlank_review(){
    var is_checked = $('.checkbox1:checkbox:checked');
    if(is_checked.length != 0){
        return true;
    }
    else{
        alert("Please Select any booking");
        return false;
    }
    }
    function save_remarks(booking_id) {
        $('#comment_booking_id').val(booking_id);
        getcommentbox(1, booking_id);
        $('#commentModal_<?=$review_status?>_<?=$is_partner?>').modal(); 
           
    }
    
    function open_admin_remarks_modal(booking_id) {
        $('.modal-title').text("");
        $('.textarea').text("");
        $('#model_remarks_<?=$review_status?>_<?=$is_partner?>').modal();     
        $('#modal_booking_id_<?=$review_status?>_<?=$is_partner?>').val(booking_id);
        $('#modal-title-<?=$review_status?>_<?=$is_partner?>').html(booking_id);
    }
    
    function getcommentbox(type_val, booking_id){
        $.ajax({
            method: 'POST',
            data: {comment_type:type_val},
            url: '<?php echo base_url(); ?>employee/booking/get_comment_section/'+booking_id+'/'+type_val,
            success: function (response) {
                if(type_val == 2){
                    document.getElementById("commentbox").remove();
                    document.getElementById("booking_hostory_template").innerHTML = '<div id="commentbox"></div>';                         
                  //  document.getElementById("spare_parts_commentbox").innerHTML = response;
                }else{
                   // alert(response);
                    document.getElementById("commentbox_<?=$review_status?>_<?=$is_partner?>").innerHTML = response;                        
                   // document.getElementById("spare_parts_commentbox").remove();
                   // document.getElementById("spare_parts_template").innerHTML = '<div id="spare_parts_commentbox"> </div>';
                }

            }
        });
    }
    
    function load_comment_area(){
        $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#comment_section').show();
        $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#update_section').hide();
        //document.getElementById("comment_section").style.display='block';
        $('#commnet_btn').hide();
    }
    
    function load_update_area(data="", key){
       // document.getElementById("update_section").style.display='block';
        $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#update_section').children('#comment2').val(data);
        $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#update_section').show();
        $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#comment_section').hide();
        //document.getElementById("").innerHTML=data;
        $('#comment_id').attr("value",key);
        $('#commnet_btn').hide();
    }
    
    function addComment() {
        var prethis = $(this);
        var comment_type = 1;
        var comment = $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#comment_section').children('#comment').val();
        var booking_id = $('#comment_booking_id').val();
  
        if(comment != '') {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/addComment',
             beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: {comment_type : comment_type, comment: comment, booking_id: booking_id},
            success: function (response) { 
                if(response === "error"){
                    alert('There is some issue. Please refresh and try again');
                } else {
                    document.getElementById("commentbox_<?=$review_status?>_<?=$is_partner?>").innerHTML = response;
                   // document.getElementById("spare_parts_commentbox").innerHTML = response;
                }   
            }
            
        });
        } else {
        alert("Please enter comments");
        }
    }
    
    function editComment(key){
       document.getElementById("comment_section").style.display='none';
       // document.getElementById("comment").innerHTML=data;
        $('#commnet_btn').hide();
        var comment = $("#comment_text_"+key).text();
        load_update_area(comment, key);
    }
    
    function updateComment() {
        var prethis = $(this);
        var comment_type = 1;
        var comment = $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#update_section').children('#comment2').val();
        var comment_id= $("#comment_id").val();
        var booking_id= $('#comment_booking_id').val();
         if(comment != '') {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking/update_Comment',
             beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: {comment: comment, comment_id: comment_id, booking_id: booking_id, comment_type: comment_type},
            success: function (response) {
                if(response === "error"){
                    alert('There is some issue. Please refresh and try again');
                } else {
                    document.getElementById("commentbox_<?=$review_status?>_<?=$is_partner?>").innerHTML = response;
                 //   document.getElementById("spare_parts_commentbox").innerHTML = response;
                } 
            }
            
        });
        } else {
            alert("Please enter comments");
        }
    }
    
    
     function deleteComment(comment_id) {
                
            var comment_type = 1; 
            var check = confirm("Do you want to delete this comment?");
            if(check == true){
                var comment_id = comment_id;
                var booking_id= $('#comment_booking_id').val();
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url(); ?>employee/booking/deleteComment',
                    data: {comment_id: comment_id, booking_id:booking_id ,comment_type : comment_type},
                    success: function (response) {
                        if(response === "error"){
                            alert('There is some issue. Please refresh and try again');
                        } else {
                            document.getElementById("commentbox_<?=$review_status?>_<?=$is_partner?>").innerHTML = response;
                         //   document.getElementById("spare_parts_commentbox").innerHTML = response;  
                        } 
                    }
                    
                });
            }
        }    
        
        function cancel(){
            $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#comment_section').hide();
            $("#commentbox_<?=$review_status?>_<?=$is_partner?>").children('form').next('div').children('#update_section').hide();
       //     $('#comment_section').css('display', 'none');
        //    $('#update_section').css('display', 'none');
            
            $('#commnet_btn').show();
//            var type_val = 1;   
//            getcommentbox(type_val);        
        }  
   </script> 