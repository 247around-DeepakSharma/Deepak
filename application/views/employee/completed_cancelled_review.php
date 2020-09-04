<?php
$tab_class = !empty($data_id) ? $data_id : "all";
// create strings to make Ids of Common controls Unique
// Distinguish Wrong call Area Bookings
$sub_id = ""; 
$sub_heading = "";
if(!empty($cancellation_reason_selected) && $cancellation_reason_selected == CANCELLATION_REASON_WRONG_AREA_ID){
    $sub_id = "_wrongarea";
    $sub_heading = " (Wrong Area Calls)";
}
if(is_numeric($this->uri->segment(3)) && !empty($this->uri->segment(3))){ $sn_no =  $this->uri->segment(3) +1; } else{ $sn_no = 1;} 
$arr_bookings = !empty($bookings_data) ? json_encode($bookings_data) : "";
?>
<script type="text/javascript" src="<?php echo base_url();?>js/base_url.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/review_bookings.js"></script>      
<input type='hidden' name='arr_bookings' id='arr_bookings' value='<?= $arr_bookings; ?>'>
<input type="hidden" name="comment_booking_id" value="" id="comment_booking_id">


<div class="" style="margin-top: 20px;">
        <!--Heading Panel-->
        <div class="row" style="margin-left:10px;">
            <div class="col-md-11" style="padding:0px;">
                <h2 style="font-size: 18x;padding:0px;" >
                    <b><?php echo $status; ?> Bookings<?php echo $sub_heading?></b>
                </h2>
            </div>            
            <?php if($status == 'Completed') { ?>
                <div class="col-md-1 pull-right">
                    <a href="javascript:void(0);" class="btn btn-primary pull-right download btn-sm" name="download_complete_booking" value="Download" title="Download Complete Bookings List" style="margin-top: 20px;">Export Data</a>
                </div>
            <?php } ?>                
            <?php if($status == 'Cancelled') { ?>
                <div class="col-md-1 pull-right" style="">
                    <a href="javascript:void(0);" class="btn btn-primary pull-right download btn-sm" name="download_cancelled_booking" value="Download" title="Download Cancelled Bookings List" style="margin-top: 20px;">Export Data</a>
                </div>
            <?php } ?>
        </div>
        <!--Filter Panel-->        
        <div class="row" style="margin-left:10px;border:1px solid #ddd;">
            <input type="hidden" name="sub_id" id="sub_id" value="<?php echo $sub_id; ?>">                 
            <div class="col-md-2 pull-right" style="padding:10px;width:12%">               
                <label for="search">Custom Search</label>
                <input type="search" class="form-control pull-right"  id="search_<?=$review_status?>_<?=$is_partner?><?=$sub_id?>" placeholder="search" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>','<?php echo $sort_on_selected; ?>')">
            </div>            
            <div class="col-md-2 pull-right" style="padding:10px;width:12%">                           
                <label for="request_type">Request Type</label>
                <select type="text" class="form-control"  id="request_type_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>" name="request_type" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>','<?php echo $sort_on_selected; ?>')">
                    <option value="">Choose Request Type</option>
                    <?php
                    foreach($request_types as $key => $request_type) { ?>
                        <option value="<?= $key ?>" <?php if(!empty($request_type_selected) && $key == $request_type_selected) { echo 'selected';}?>><?= $request_type; ?></option>                  
                    <?php } ?>
                </select>                           
            </div> 
             <?php if($status == 'Completed') { ?>
             <div class="col-md-2 pull-right" style="padding:10px;width:12%">                            
                <label for="state">State</label>
                <select type="text" class="form-control"  id="state_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>" name="state" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>','<?php echo $sort_on_selected; ?>')">
                    <option value=""></option>
                    <?php foreach($states as $state) { ?>
                    <option value="<?= $state['state_code']; ?>" <?php if(!empty($state_selected) && $state['state_code'] == $state_selected) { echo 'selected';} ?>><?= $state['state']; ?></option>                  
                    <?php } ?>
                </select>                
            </div>
             <div class="col-md-2 pull-right" style="padding:10px;width:12%">                
                <label for="partner">Partner</label>
                <select type="text" class="form-control"  id="partner_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>" name="partner" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>','<?php echo $sort_on_selected; ?>')">
                    <option value=""></option>
                    <?php foreach($partners as $partner) { ?>
                    <option value="<?= $partner['id']; ?>" <?php if(!empty($partner_selected) && $partner['id'] == $partner_selected) { echo 'selected';}?>><?= $partner['public_name']; ?></option>                  
                    <?php } ?>
                </select>                              
            </div>           
            <?php } if($status == 'Cancelled') { ?>
            <div class="col-md-2 pull-right" style="padding:10px;width:12%">                      
                <label for="cancellation_reason">Cancellation Reason</label>
                <?php if(empty($sub_heading)) { ?>             
                <select type="text" class="form-control"  id="cancellation_reason_<?php echo $is_partner; ?><?=$sub_id?>" name="cancellation_reason" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>','<?php echo $sort_on_selected; ?>')">
                    <option value=""></option>
                    <?php foreach($cancellation_reason as $reason) { ?>
                    <option value="<?= $reason['id']; ?>" <?php if(!empty($cancellation_reason_selected) && $reason['id'] == $cancellation_reason_selected) { echo 'selected';}?>><?= $reason['reason']; ?></option>
                  
                    <?php } ?>
                </select> 
                <?php } else { ?>
                    <select type="text" class="form-control"  id="cancellation_reason_<?php echo $is_partner; ?><?=$sub_id?>" name="cancellation_reason" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>','<?php echo $sort_on_selected; ?>')">
                    <option value="<?php echo CANCELLATION_REASON_WRONG_AREA_ID;?>" selected><?php echo CANCELLATION_REASON_WRONG_AREA; ?></option>                    
                </select> 
                <?php }?>
            </div>            
            <div class="col-md-2 pull-right" style="padding:10px;width:12%">                
                <label for="state">State</label>
                <select type="text" class="form-control"  id="state_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>" name="state" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>','<?php echo $sort_on_selected; ?>')">
                    <option value=""></option>
                    <?php foreach($states as $state) { ?>
                    <option value="<?= $state['state_code']; ?>" <?php if(!empty($state_selected) && $state['state_code'] == $state_selected) { echo 'selected';} ?>><?= $state['state']; ?></option>                  
                    <?php } ?>
                </select>               
            </div>
            <div class="col-md-2 pull-right" style="padding:10px;width:12%">
                <label for="partner">Partner</label>
                <select type="text" class="form-control"  id="partner_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>" name="partner" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>','<?php echo $sort_on_selected; ?>')">
                    <option value=""></option>
                    <?php foreach($partners as $partner) { ?>
                    <option value="<?= $partner['id']; ?>" <?php if(!empty($partner_selected) && $partner['id'] == $partner_selected) { echo 'selected';}?>><?= $partner['public_name']; ?></option>
                  
                    <?php } ?>
                </select>                             
            </div>                         
            <?php } ?>             
            <?php if($review_status == "Completed" || $review_status == "Cancelled"){ ?> 
            <div class="col-md-2 pull-right" style="padding:10px;width:13%">
                <label for="review">Review Age Range</label>
                <input type="number" min="0" id="review_age_min_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>" name="review_age_min" style="width:60px;height:28px;" value="<?php echo((!empty($min_review_age_selected) || !empty($max_review_age_selected)) ? $min_review_age_selected : '')?>"> - 
                <input type="number" min="0" id="review_age_max_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>" name="review_age_max"  style="width:60px;height:28px;" value="<?php echo((!empty($min_review_age_selected) || !empty($max_review_age_selected)) ? $max_review_age_selected : '')?>">                                
                <button class="btn btn-sm btn-primary" style="width:30px;padding:2px;margin-left:5px;height:28px;" onclick="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id; ?>','<?php echo $sort_on_selected; ?>')">OK</button>
            </div>
            <!--Sorting Drop Down-->
            <div class="col-md-2 pull-left" style="padding:10px;width:12%">
                <?php
                $age_order_selected = (!empty($sort_on_selected) && $sort_on_selected == 'initial_booking_date') ? 'selected' : "";
                $review_age_order_selected = (!empty($sort_on_selected) && $sort_on_selected == 'service_center_closed_date') ? 'selected' : "";
                $sort_order_asc = (!empty($sort_order_selected) && $sort_order_selected == 'desc') ? 'checked' : "";
                $sort_order_desc = (!empty($sort_order_selected) && $sort_order_selected == 'asc') ? 'checked' : "";
                ?>
                <input type="radio" name="sort_order" value="desc" style="margin-left:10px;margin-right:2px;" <?php echo $sort_order_asc; ?>> Asc
                <input type="radio" name="sort_order" value="asc" style="margin-left:20px;margin-right:2px;" <?php echo $sort_order_desc; ?>> Desc <br/>
                <select type="text" class="form-control"  id="sort_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>" name="sort_on" onchange="review_search('<?php echo $review_status ?>',<?php echo $is_partner; ?>,'<?php echo $sub_id ?>',this.value)">
                    <option value="">Select Sort Option</option>
                    <option value="initial_booking_date"  <?php echo $age_order_selected; ?>>Age</option>
                    <option value="service_center_closed_date"  <?php echo $review_age_order_selected; ?>>Review Age</option>
                </select>                
            </div>
<!--            <div class="col-md-1 pull-left" style="width:30px;padding:2px;height:28px;margin-top:35px;">
                <button class="btn btn-sm btn-primary">OK</button>
            </div>-->
            <?php } ?>
        </div>
               <form action="<?php echo base_url();?>employee/booking/checked_complete_review_booking" method="post">
                  <div class="col-md-12" style="font-size:82%;margin-top:10px;">
                      <table class="table table-bordered table-hover table-striped completed_cancelled_review_table" id="completed_cancelled_review_table">
                        <thead>
                           <tr>
                              <th class="jumbotron no-sort" >S.N.</th>
                              <th class="jumbotron no-sort" >Booking Id</th>
                              <th class="jumbotron no-sort" style="text-align: center;">Price Details</th>
                              <th class="jumbotron no-sort" >Amount Due</th>
                              <th class="jumbotron no-sort" >Amount Paid</th>
                              <th class="jumbotron" >Age</th>
                              <?php
                              if($review_status == "Completed" || $review_status == "Cancelled" || $review_status == "Completed_By_SF"){
                              ?>
                                <th class="jumbotron" title="Age after SF completed action from his side.">Review Age&nbsp;<i class="fa fa fa-info-circle"></i></th>
                              <?php
                              }
                              ?> 
                              <?php
                              if($review_status == "Completed"){
                              ?>
                                <th class="jumbotron no-sort" >Warranty Status</th>
                              <?php
                              }
                              ?>                              
                              <th class="jumbotron no-sort" >Admin Remarks</th>
                              <th class="jumbotron no-sort" >Vendor Remarks</th>
                              <th class="jumbotron no-sort" >Vendor Cancellation Reason</th>
                              <!--Direct Approval Checkbox not appears in case of Completed/Cancelled Review Tab-->
                              <?php if(($review_status != "Completed" && $review_status != "Cancelled"  && $review_status != "Completed_By_SF") || !empty($sub_id)){ ?>
                              <th class="jumbotron no-sort" ><input type="checkbox" id="selecctall" class="selecctall <?php echo $tab_class?>" data-id="<?php echo $tab_class?>" onchange="selectall_checkboxes(this)"/></th>
                              <?php } ?>
                              <th class="jumbotron no-sort" >Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php $count =1;$initial_offset = $offset; foreach ($charges as $key => $value) { ?>
                            <tr id="<?php echo  "row_".$value['booking_id'] ?>">
                              <?php $offset++ ;?>
                              <td style="text-align: left;white-space: inherit;font-size:80%"><?php echo $offset; ?>
                                <?php if (isset($value['booking'][0]['is_upcountry']) && $value['booking'][0]['is_upcountry'] == 1) { ?><i style="color:red; font-size:20px;" class="fa fa-road" aria-hidden="true"></i><?php } ?>
                              </td>
                              
                              <td  style="text-align: left;white-space: inherit;"><?php if(isset($value['booking'][0]['vendor_name'])){ echo $value['booking_id']." <br/><br/>".$value['booking'][0]['vendor_name']; } ?><?php if(!empty($value['sf_purchase_invoice'])) { echo "<br/><br/><a href='https://s3.amazonaws.com/".BITBUCKET_DIRECTORY."/misc-images/".$value['sf_purchase_invoice']."' target=\"_blank\">Invoice</a>"; }?>
                                 
                                  <input type="hidden" name="booking_id[]" value="<?php echo $value['booking_id']; ?>" id="<?php echo "booking_id".$count; ?>">
                                  <input type="hidden" name="approved_by" value='<?php echo _247AROUND ?>'  id="approved_by">
                                  <input type="hidden" name="booking_request_type[]" value="<?=$value['request_type']?>"  class="booking_request_type_<?=$value['booking_id']?>">
                                  <input type="hidden" name="booking_warranty_status[]" value=''  class="booking_warranty_status_<?=$value['booking_id']?>">
                              </td>

                            <input type="hidden" class="form-control" id="partner_id" name="partner_id[<?php echo $value['booking_id']; ?>]" value = "<?php if(isset($value['booking'][0]['partner_id'])){ echo $value['booking'][0]['partner_id']; } ?>" >

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
                              <td style="text-align: center;white-space: inherit;"><strong><?php if(isset($value['booking'][0]['amount_due'])){ echo $value['booking'][0]['amount_due']; } ?></strong></td>
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $value['amount_paid']; ?></strong></td>
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $value['age'];?></strong></td>
                              <?php if($review_status == "Completed" || $review_status == "Cancelled"  || $review_status == "Completed_By_SF"){ ?>
                              <td style="text-align: center;white-space: inherit;"><strong><?php echo $value['review_age'] ?></strong></td>
                              <?php } ?>
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
                              <?php if(($review_status != "Completed" && $review_status != "Cancelled"  && $review_status != "Completed_By_SF") || !empty($sub_id)){ ?>
                              <td><input id="approved_close" type="checkbox"  class="checkbox1 <?php echo $tab_class;?> <?php echo "app_".$value['booking_id'];?>" name="approved_booking[]" value="<?php echo $value['booking_id']; ?>"
                                         <?php if($status == _247AROUND_COMPLETED){?> onchange="is_sn_correct_validation('<?php echo $value['booking_id']?>')"<?php } ?>></input></td>
                              <?php } ?>
                              <td>
                                 <?php echo "<a class='btn btn-sm btn-primary' style='margin-top:5px;' "
                                    . "href=" . base_url() . "employee/booking/viewdetails/$value[booking_id] target='_blank' title='view'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                                    ?>
                              <a style="margin-top:5px;" target='_blank'  href="<?php echo base_url(); ?>employee/booking/get_complete_booking_form/<?php echo $value['booking_id']; ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil" aria-hidden="true" title="Complete Booking"></i></a>
                              <button style="margin-top:5px;" type="button" id="<?php echo "remarks_".$count;?>" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" onclick="open_admin_remarks_modal('<?php echo $value['booking_id']; ?>')"><i class="fa fa-times" aria-hidden="true" title="Reject"></i></button>
                              <a style="margin-top:5px;position: relative;" class="btn btn-success btn-sm" id='<?php echo 'comment_' . $count; ?>' href="javascript:void(0);" name="save-remarks" onclick="save_remarks('<?php echo $value['booking_id']; ?>')"><i class="fa fa-comment"></i>
                                      <?php
                                      if (isset($bookings_comment_count[$value['booking_id']]) && $bookings_comment_count[$value['booking_id']] > 0) {
                                          ?>
                                          <span class='comment_count'><?php echo $bookings_comment_count[$value['booking_id']]; ?></span>                        
                                          <?php
                                        }
                                      ?>
                                  </a>
<!--                                Added link that is redirecting to the Admin Booking cancellation form.-->
                                <?php if($status == _247AROUND_CANCELLED){
                                    $url_cancel_form =  base_url() . "employee/booking/get_cancel_form/".$value['booking_id'];
                                    echo "<a class='btn btn-danger btn-sm' style='margin-top:5px;' href='".$url_cancel_form."' target='_blank' title='Cancel Booking'><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                                } ?>
 
                              <button style="margin-top:5px;" type="button"  class="btn btn-danger btn-sm open-adminremarks_transfer" ><a style="color:white;" target="_blank" href="<?php echo base_url(); ?>employee/vendor/get_reassign_vendor_form/<?php echo $value['booking_id']; ?>">Transfer</a></button>
 
                              </td>
                           
                            </tr>
                           <?php $count++; } ?>
                        </tbody>
                     </table>
                     <?php if(($review_status != "Completed" && $review_status != "Cancelled"  && $review_status != "Completed_By_SF") || !empty($sub_id)){ ?>
                     <?php if(!empty($charges)){?>
                     <div class="col-md-12">
                        <center><input type="submit" value="Approve Bookings" id="btn-approve-booking" onclick="return checkValidationForBlank_review()" style=" background-color: #2C9D9C;
                           border-color: #2C9D9C;"  class="btn btn-md btn-success"></center>
                     </div>
                     <?php }} ?>
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
                    // Pagination for Wrong area Calls Tab
                    if(!empty($sub_id)){
                        $tab = "#tabs-6";
                    }
                    if($review_status == "Completed"){
                        $tab = "#tabs-2";
                    }
                    else if($review_status == "Completed_By_SF"){
                        $tab = "#tabs-5";
                    }
             }
             for($i=0;$i<=$total_pages;$i++){
                 $offset = $per_page*$i;
                 if(!empty($review_status)){
                 ?>
                 <a id="link_<?php echo $i;?>" style="background: #d7eaea;padding: 5px;" onclick="load_view('employee/booking/review_bookings_by_status/<?php echo  $review_status?>/<?php echo $offset;?>/<?php echo $is_partner; ?>/0/<?php echo $cancellation_reason_selected; ?>/<?php echo $partner_selected;?>/<?php echo $state_selected; ?>/<?php echo $request_type_selected; ?>/<?php echo $min_review_age_selected; ?>/<?php echo $max_review_age_selected; ?>/<?php echo $sort_on_selected; ?>/<?php echo $sort_order_selected; ?>','<?php echo $tab ?>','link_<?php echo $i;?>')"><?php echo $i+1; ?></a>
                 <?php
                }                
            }
             ?>
                 </div>

    <div id="model_remarks_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="modal-title-<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>">Modal Header</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <center><img id="loader_gif_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>" src="<?php echo base_url(); ?>images/loadring.gif" style="display: none;"></center>
                        <center><p id="remarks_msg_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>" style="color : red;"></p></center>
                    </div>
                    <input type="hidden" name="modal_booking_id" id="modal_booking_id_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>" value="">
                    <select  class="form-control"  id="select_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>">
                        <option disabled="" selected=""></option>
                    </select>
                </div>
                <input type="hidden" id="id_no">
                <input type="hidden" value='<?php echo _247AROUND; ?>' id="admin_id_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>">
                <input type="hidden" value="<?php echo $status; ?>" id="internal_boking_status_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>" class="internal_boking_status_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>">
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="send_remarks_multitab('<?= $review_status ?>','<?= $is_partner ?>')" id="btn_send_remarks_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>">Send</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
                </div>
            </div>
        </div>
    </div>
<div id="commentModal_<?=$review_status?>_<?=$is_partner?>" class="modal fade" role="dialog">
      <div class="modal-dialog" style=" height: 90% !important;">
         <!-- Modal content-->
         <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <br />
          </div>
            <div class="modal-body">
                <div id="commentbox_<?=$review_status?>_<?=$is_partner?>"></div>
            </div>
         </div>
      </div>
   </div>



<style>
    .comment_count
    {
        position: absolute;
        top: -7px;
        right: -7px;
        width: 20px;
        height: 20px;
        background: #df4848;
        border-radius: 10px;
    }
    .select2-container--default{
      width:100% !important;
    }
</style>
<script>
    $('.download').on('click', function() {
        var download_btn = $(this).attr('name');
        
        if(download_btn == 'download_complete_booking') {
            var partner_id = $('#partner_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?><?php echo $sub_id?>').val();
            var state_id = $('#state_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?><?php echo $sub_id?>').val();
            var request_type = $('#request_type_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>').val();
            var review_age_min = $('#review_age_min_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>').val();
            var review_age_max = $('#review_age_max_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>').val();

            window.open("<?php echo base_url(); ?>employee/booking/download_review_bookings_data?partner_id="+partner_id+"&state_id="+state_id+"&request_type="+request_type+"&review_age_min="+review_age_min+"&review_age_max="+review_age_max+"&is_partner=<?php echo $is_partner; ?>&review_status=<?php echo $review_status;?>", '_blank');
        }
        
        if(download_btn == 'download_cancelled_booking') {
            var partner_id = $('#partner_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?><?php echo $sub_id?>').val();
            var state_id = $('#state_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?><?php echo $sub_id?>').val();
            var cancellation_reason_id = $('#cancellation_reason_<?php echo $is_partner; ?><?php echo $sub_id?>').val();
            var request_type = $('#request_type_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>').val();
            var review_age_min = $('#review_age_min_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>').val();
            var review_age_max = $('#review_age_max_<?php echo $is_partner; ?>_<?php echo $review_status;?><?=$sub_id?>').val();

             window.open("<?php echo base_url(); ?>employee/booking/download_review_bookings_data?partner_id="+partner_id+"&state_id="+state_id+ "&request_type="+request_type+"&review_age_min="+review_age_min+"&review_age_max="+review_age_max+"&is_partner=<?php echo $is_partner; ?>&review_status=<?php echo $review_status;?>&cancellation_reason_id="+cancellation_reason_id, '_blank');
        }
    });

    $('#cancellation_reason_<?php echo $is_partner; ?><?php echo $sub_id; ?>').select2({
       placeholder: 'Cancellation Reason'
    }); 
    $('#state_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?><?php echo $sub_id; ?>').select2({
       placeholder: 'State'
    }); 
    $('#partner_cancelled_<?php echo $is_partner; ?>_<?php echo $review_status;?><?php echo $sub_id; ?>').select2({
       placeholder: 'Partner'
    });    
    $('#partner_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>').select2({
       placeholder: 'Partner'
    });    
    $('#state_completed_<?php echo $is_partner; ?>_<?php echo $review_status;?>').select2({
       placeholder: 'State'
    });  
    $('#request_type_<?php echo $is_partner; ?>_<?php echo $review_status;?><?php echo $sub_id; ?>').select2({
       placeholder: 'Request Type'
    });
   
   $(document).ready(function(){
       $('.completed_cancelled_review_table').each(function( index ) {
            $(this).DataTable().destroy();
       });
       console.log($.fn.dataTable.isDataTable(".completed_cancelled_review_table"));
       <?php if(($review_status == "Completed" || $review_status == "Cancelled")){ ?>
            $('.completed_cancelled_review_table').DataTable({
                "paging": false,
                "info":     false,
                "searching": false,
                "ordering": true,
                columnDefs: [{
                  orderable: false,
                  targets: "no-sort"
                }]
            });
       <?php } ?>
        
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
        $("#btn-approve-booking").css("pointer-events", "none");
        $("#btn-approve-booking").css("opacity", "0.5");
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
        // CRM-6300 Cancellation reason dropdwon
        function open_admin_remarks_modal(booking_id) {
            $('.modal-title').text("");
            $('.textarea').text("");
            $('#model_remarks_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>').modal();     
            $('#modal_booking_id_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>').val(booking_id);
            $('#modal-title-<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>').html(booking_id);
            // fill cancellation reason in cancellation remark popup dropdown
            $.ajax({
                type:'POST',
                url:'<?php echo base_url(); ?>employee/booking/get_cancellation_reasons',
                data:{reason_of:'247around'},
                success:function(data){
                    if(data){
                        $("#select_<?= $review_status ?>_<?= $is_partner ?><?= $sub_id ?>").html(data);
                    }
                }
            });
        }


    function open_admin_remarks_modal_assign(booking_id) {
        $('#modal_title_assign').text("");
        $('#textarea_assign').text("Transfer Booking to Other SF");
        $('#modal_booking_id_reassign').val(booking_id);

        $('#booking_reassign').modal();     
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

    $('#service_center_reassign').select2();
    $('#reason_of_reassign').select2();

    
    
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


    function selectall_checkboxes(obj) {
        var dataId = $(obj).attr('data-id');
        var isChecked = $("."+dataId).prop("checked");

        $("."+dataId).prop('checked', $(obj).prop("checked"));
        if(isChecked){
            var outputArray = []; 
            $('.'+dataId).each(function() {
                outputArray.push(is_sn_correct_validation($(this).val(),'Yes'));
            })
            if(outputArray.includes('no')){
                alert("Review Booking Listing Contains Booking WIth Wrong Serial number All Wrong Serial number booking will be auto unselected");
            }
        }
     }       
        


   </script> 