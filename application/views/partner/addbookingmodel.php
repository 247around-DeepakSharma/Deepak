<!-- Modal -->
<div id="myModal1" class="modal fade" role="dialog">
   <div class="modal-dialog modal-lg ">
      <!-- Modal content-->
      <div class="modal-content" style="width:120%">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Please Verify All Booking Details</h4>
         </div>
         <div class="modal-body col-md-12">
            <table class="table table-bordered">
               <tbody>
                   <tr>
                     <th>Order ID</th>
                     <td>
                        <p id="b_order_id"></p>
                     </td>
                     <th>Source</th>
                     <td id="b_source"></td>
                  </tr>
                  <tr>
                     <th>Name</th>
                     <td>
                        <p id="user_name"></p>
                     </td>
                     <th>Email</th>
                     <td id="b_email"></td>
                  </tr>
                  <tr>
                     <th>Primary Contact No.</th>
                     <td id="p_contact_no"></td>
                     <th >Alternate Contact No.</th>
                     <td id="b_alt_contact_no"></td>
                  </tr>
                  <tr>
                     <th>Booking Address</th>
                     <td id="b_address" ></td>
                     <th>Booking city</th>
                     <td id="b_city" ></td>
                  </tr>
                  <tr>
                     <th >Booking Pincode</th>
                     <td id="b_pincode" ></td>
                     <th>Landmark</th>
                     <td id="b_landmark" ></td>
                  </tr>
                  <tr>
                     <th >Booking Date</th>
                     <td id="b_date" ></td>
                     <th>Booking Time Slot</th>
                     <td id="b_timeslot" ></td>
                  </tr>
             
                  <tr>
                     <th>Problem Description</th>
                     <td id="bremarks"></td>
                    
                  </tr>
               </tbody>
            </table>
            <div class="preview_booking panel panel-info " id="preview_booking1">
               <div class="panel-body">
                  <div class="row">
                     
                        <div class="col-md-6">
                           <table class="table table-bordered">
                              <tbody>
                                 <tr>
                                    <th>Brand</th>
                                    <td id="bbrand_1"></td>
                                    <th>Category</th>
                                    <td id="bcategory_1"></td>
                                 </tr>
                                 <tr>
                                    <th>Capacity</th>
                                    <td id="bcapacity_1"></td>
                                    <th>Model NUmber</th>
                                    <td id="bmodel_1"></td>
                                 </tr>

                                  <tr>
                                   
                                    <th>Purchase Year</th>
                                    <td id="bpurchase_year_1"></td>
                                    <th>Purchase Month</th>
                                    <td id="bpurchase_month_1"></td>
                                 </tr>

                                 <tr>
                                   
                                   
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-md-6" id="bpriceList_1"></div>
                  </div>
               </div>
            </div>
           
            
             <div class="clone_m"></div>

             <div class="col-md-6">Price To be Paid: <span id="bgrand_total_charge"></span> Rs</div>
         </div>

         <div class="modal-footer">

            <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>

         </div>
      </div>
   </div>
</div>