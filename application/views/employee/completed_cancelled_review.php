<?php if(is_numeric($this->uri->segment(3)) && !empty($this->uri->segment(3))){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} ?>
<script type="text/javascript" src="<?php echo base_url();?>js/base_url.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/review_bookings.js"></script>      
<div class="" style="margin-top: 30px;">
         <div class="row">
            <div class="col-md-3 pull-right" style="margin-top:20px;">
                <input type="search" class="form-control pull-right"  id="search" placeholder="search" onchange="review_search('<?php echo $status ?>',<?php echo $is_partner; ?>)">
            </div>
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
                              <th class="jumbotron" >Admin Remarks</th>
                              <th class="jumbotron" >Vendor Remarks</th>
                              <th class="jumbotron" >Vendor Cancellation Reason</th>
                              <th class="jumbotron" ><input type="checkbox" id="selecctall" /></th>
                              <th class="jumbotron" >Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $count =1;$initial_offset = $offset; foreach ($charges as $key => $value) { ?>
                            <tr id="<?php echo  "row_".$value['booking_id'] ?>">
                              <?php $offset++ ;?>
                              <td style="text-align: left;white-space: inherit;font-size:80%"><?php echo $offset; ?></td>
                              
                              <td  style="text-align: left;white-space: inherit;"><?php echo $value['booking_id']." <br/><br/>".$value['booking'][0]['vendor_name']; ?>
                                 
                                  <input type="hidden" name="booking_id[]" value="<?php echo $value['booking_id']; ?>" id="<?php echo "booking_id".$count; ?>">
                                  <input type="hidden" name="approved_by" value='<?php echo _247AROUND ?>'  id="approved_by">
                              </td>

                            <input type="hidden" class="form-control" id="partner_id" name="partner_id[<?php echo $value['booking_id']; ?>]" value = "<?php echo $value['booking'][0]['partner_id'];?>" >

                              <td style="text-align: left;white-space: inherit; <?php if($value['unit_details'][0]['mismatch_pincode'] == 1){ echo "background-color:red;";}?>">
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
                                              <a target="_blank" href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY;?>/engineer-uploads/<?php echo $value1['serial_number_pic'];?>"> 
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
                                             <span id="<?php echo "upcountry".$count;?>"><?php if($key1 ==0){ echo $value1['upcountry_charges'];} ?></span>
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
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $value1['amount_paid']; ?></strong></td>
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
                              <td><input id="approved_close" type="checkbox"  class="checkbox1 <?php echo "app_".$value['booking_id'];?>" name="approved_booking[]" value="<?php echo $value['booking_id']; ?>"
                                         <?php if($status == _247AROUND_COMPLETED){?> onchange="is_sn_correct_validation('<?php echo $value['booking_id']?>')"<?php } ?>></input></td>
                              <td>
                                 <?php echo "<a class='btn btn-sm btn-primary' "
                                    . "href=" . base_url() . "employee/booking/viewdetails/$value[booking_id] target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                              <a style="margin-top:5px;" target='_blank'  href="<?php echo base_url(); ?>employee/booking/get_complete_booking_form/<?php echo $value['booking_id']; ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil" aria-hidden="true" title="Edit"></i></a>
                              <button style="margin-top:5px;" type="button" id="<?php echo "remarks_".$count;?>" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2"><i class="fa fa-times" aria-hidden="true" title="Reject"></i></button></td>
                           
                            </tr>
                           <?php $count++; } ?>
                        </tbody>
                     </table>
                     <?php if(!empty($charges)){?>
                     <div class="col-md-12">
                        <center><input type="submit" value="Approve Bookings" onclick="return checkValidationForBlank_review() style=" background-color: #2C9D9C;
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
                    if($status == "Completed"){
                        $tab = "#tabs-2";
                    }
             }
             for($i=0;$i<=$total_pages;$i++){
                 $offset = $per_page*$i;
                 ?>
    <a id="link_<?php echo $i;?>" style="background: #d7eaea;padding: 5px;" onclick="load_view('employee/booking/review_bookings_by_status/<?php echo  $status?>/<?php echo $offset;?>','<?php echo $tab ?>','link_<?php echo $i;?>')"><?php echo $i+1; ?></a>
                 <?php
             }
             ?>
                 </div>

   <div id="myModal2" class="modal fade" role="dialog">
      <div class="modal-dialog">
         <!-- Modal content-->
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <h4 class="modal-title" id="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
               <textarea rows="8" class="form-control" id="textarea"></textarea>
            </div>
            <input type="hidden" id="id_no">
            <input type="hidden" value='<?php echo _247AROUND; ?>' id="admin_id">
            <input type="hidden" value="<?php echo $status; ?>" id="internal_boking_status">
            <div class="modal-footer">
               <button type="button" class="btn btn-success" onclick="send_remarks()">Send</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
         </div>
      </div>
   </div>

<script>
   $(document).ready(function(){
        $("#selecctall").change(function(){
            var isChecked = document.getElementById('selecctall').checked;
            $(".checkbox1").prop('checked', $(this).prop("checked"));
            if(isChecked){
                var outputArray = []; 
                $('.checkbox1').each(function() {
                      outputArray.push(is_sn_correct_validation($(this).val(),'Yes'));
                 })
                  if(outputArray.includes('no')){
                        alert("Review Booking Listing Contains Booking WIth Wrong Serial number All Wrong Serial number booking will be auto unselected");
                  }
          }
         });
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
    </script>