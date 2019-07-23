<script src="https://cdn.datatables.net/plug-ins/1.10.12/api/sum().js"></script>
<style>
.dropdown-submenu {
    position: relative;
}
.dropdown-submenu .dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: -1px;
}
#bank_transaction_datatable1_filter{
    text-align: right;
}

</style>
<?php if(isset($invoice_array)){ ?>
<p><h2>Invoices Generated</h2></p>

<form class="form-horizontal" method="POST" action="<?php echo base_url()?>employee/invoice/get_add_new_transaction/<?php if(!empty($invoice_array)){ ?><?php echo $invoice_array[0]['vendor_partner'];?>/<?php echo $invoice_array[0]['vendor_partner_id']; } ?>" >
 <input type="text" name="selected_amount_collected" id="selected_amount_collected" value="" hidden />
         <input type="text" name="selected_tds" id="selected_tds" value="" hidden />
  <table class="table table-bordered  table-hover table-striped data"  >
   <thead>
      <tr >
         
         <th>Invoice Id</th>
         <th>Type</th>
         <th>Sub-Category</th>         
         <th>Bookings/ Parts</th>
         <th>Invoice Period</th>
         <th>Due Date</th>
         <th>Total Invoice</th>
         <th>Service Charges</th>
        <?php if(!empty($invoice_array) && $invoice_array[0]['vendor_partner'] == "vendor"){ ?>
         <th>Additional Service Charges</th>
        <?php } ?>
         <th>Parts / Stands</th>
        <?php if(!empty($invoice_array) && $invoice_array[0]['vendor_partner'] == "vendor"){ ?>
         <th>TDS Amount</th>
        <?php } ?>
        <?php if(!empty($invoice_array) && $invoice_array[0]['vendor_partner'] == "partner"){ ?>
         <th>Upcountry Charges</th>
         <th>Courier Charges</th>
        <?php } ?>
         <th>Penalty</th>
         <th>GST Amount</th>
         <th>Amount to be Paid By 247Around</th>
         <th>Amount to be Paid By Partner</th>
         <th>Amount Paid</th> 
         <th>Remarks</th> 
         <th>Select<input align="center" type="checkbox" id="selecctall_amt"/></th>
         <th>Action</th>
<!--         <th>Update</th>
         <th>Resend</th>-->
        
      </tr>
   </thead>
   <tbody>
   
      <?php
       $count = 1;
       $sum_no_of_booking = 0;
       $sum_of_total_service_charges = 0;
       $sum_total_additional_service_charge = 0;
       $sum_total_parts_cost = 0;
       $sum_tds = 0;
       $pay_by_247 =0;
       $pay_by_partner = 0;
      

       if(!empty($invoice_array)){

         foreach($invoice_array as $key =>$invoice) {?>

      <tr <?php if($invoice['settle_amount'] == 1){ ?> style="background-color: #90EE90; " <?php } ?>>
         
         <td><?php echo $invoice['invoice_id']; ?>
             <p style="margin-top:15px;">
                 <a  href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/invoices-excel/<?php echo $invoice['invoice_file_main']; ?>">Main</a>
             </p> <p style="margin-top:15px;">
             <a  href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/invoices-excel/<?php echo $invoice['invoice_detailed_excel']; ?>">Detail</a>
             </p> <p style="margin-top:15px;">
             <a  href="javascript:void(0);" class="get_invoice_payment_history" data-id="<?php echo $invoice['invoice_id'];?>">History</a>
             </p>
         </td>
        
         <td style="max-width: 56px; word-wrap:break-word;"><?php echo $invoice['type']; ?></td>
         <td style="max-width: 56px; word-wrap:break-word;"><?php echo $invoice['sub_category']; ?></td>
         <td ><?php echo $invoice['num_bookings']."/".$invoice['parts_count']; ?></td>
         <td><?php echo date("jS M, Y", strtotime($invoice['invoice_date'])). " <br/><br/> ".date("jS M, Y", strtotime($invoice['from_date'])). " to ". date("jS M, Y", strtotime($invoice['to_date'])); ?></td>
          <td><?php echo $invoice['due_date'];?></td>
         <td><?php echo $invoice['total_amount_collected'];?></td>
         <td><?php echo (sprintf("%.2f",($invoice['total_service_charge'] + $invoice['service_tax']))); $sum_of_total_service_charges +=  $invoice['total_service_charge'] + $invoice['service_tax']; ?></td>
         
         <?php if($invoice["vendor_partner"] == "vendor"){ ?>
            <td><?php echo sprintf("%.2f",$invoice['total_additional_service_charge']); $sum_total_additional_service_charge += $invoice['total_additional_service_charge'];?></td>
         <?php } ?>
            
         <td><?php echo (sprintf("%.2f",($invoice['parts_cost'] + $invoice['vat']))); $sum_total_parts_cost +=($invoice['parts_cost'] + $invoice['vat']); ?></td>
         
         <?php if($invoice["vendor_partner"] == "vendor"){ ?>
         <td id="<?php echo 'tds_'.$count; ?>"><?php echo sprintf("%.2f",$invoice['tds_amount']); $sum_tds +=$invoice['tds_amount'];?></td>
         <?php } ?>
         
         <?php if($invoice["vendor_partner"] == "partner"){ ?>
         <td id="<?php echo 'upcountry_'.$count; ?>"><?php if($invoice['type'] == "Cash" && $invoice['vendor_partner'] == "vendor") { echo "-".sprintf("%.2f",$invoice['upcountry_price']);} else { echo sprintf("%.2f",$invoice['upcountry_price']); } ?></td>
         <td id="<?php echo 'courier_charges_'.$count; ?>"><?php echo sprintf("%.2f",$invoice['courier_charges']); ?></td>
         <?php } ?>
         
         <td id="<?php echo 'penalty_'.$count; ?>"><?php echo "-".sprintf("%.2f",$invoice['penalty_amount']); ?></td>
         <td id="<?php echo 'gst_'.$count; ?>"><?php echo sprintf("%.2f",$invoice['igst_tax_amount'] + $invoice['cgst_tax_amount'] + $invoice['sgst_tax_amount']); ?></td>
         <td id="<?php echo 'pay_247'.$count; ?>" ><?php  if($invoice['amount_collected_paid'] < 0){ echo sprintf("%.2f",$invoice['amount_collected_paid']); $pay_by_247 += ($invoice['amount_collected_paid'] );} else {echo "0.00"; } ?></td>
         <td id="<?php echo 'pay_partner'.$count; ?>"><?php if($invoice['amount_collected_paid'] > 0){ echo sprintf("%.2f",$invoice['amount_collected_paid']); $pay_by_partner += $invoice['amount_collected_paid'];} else {echo "0.00";} ?></td>
        
         <td id="<?php echo 'amount_paid_'.$count; ?>"><?php echo sprintf("%.2f",$invoice['amount_paid']) ?></td>
         <td><?php echo $invoice['remarks']; ?></td>
        
         <td ><?php if($invoice['settle_amount'] == 0){ ?><input type="checkbox" class="checkbox_amt form-control" name ="invoice_id[]" value="<?php echo $invoice['invoice_id'] ?>" id="<?php echo 'checkbox_'.$count; ?>" onclick="sum_amount()" />
             
             <input type="hidden" class ="in_disable" name="<?php echo "tds_amount[".$invoice['invoice_id']."] "; ?>" id="<?php echo "intdsAmount_".$count; ?>" value="<?php if($invoice['amount_paid'] > 0 ) { echo "0.00";} else { echo $invoice['tds_amount'];} ?>"/>
             <input type="hidden" class ="in_disable"    name="<?php echo "amount_collected[".$invoice['invoice_id']."] "; ?>" id="<?php echo "inAmountCollected_".$count; ?>" value="<?php if($invoice['amount_collected_paid'] > 0) {echo $invoice['amount_collected_paid'] - $invoice['amount_paid'];} else { echo $invoice['amount_collected_paid'] + $invoice['amount_paid'];}?>"/>
            
            <?php } ?>
            
        

         </td>
         <td>
             <?php if($invoice['vendor_partner'] == "vendor") { ?>
             
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                    <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                      <li><a <?php if($invoice['amount_paid'] > 0 ) { echo 'style="color:#33333385;"'; } else { echo 'href="'.base_url().'employee/invoice/insert_update_invoice/'.$invoice['vendor_partner'].'/'.$invoice['invoice_id'].'"'; } ?>>Update</a></li>
                      <li class="divider"></li>
                      <li><a href="<?php echo base_url()?>employee/invoice/sendInvoiceMail/<?php echo $invoice['invoice_id']; ?>">Resend Invoice</a></li>
                      <li class="divider"></li>
                      <li class="dropdown-submenu">
                        <a class="custom_dropdown-submenu"  href="#">ReGenerate<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                          <li><a <?php if($invoice['amount_paid'] > 0 ) { echo 'style="color:#33333385;"'; } else{ echo 'href="'.base_url().'employee/invoice/regenerate_invoice/'.$invoice['invoice_id'].'/final"'; } ?>>Final</a></li>
                          <li class="divider"></li>
                          <li><a <?php if($invoice['amount_paid'] > 0 ) { echo 'style="color:#33333385;"'; } else{ echo 'href="'.base_url().'employee/invoice/regenerate_invoice/'.$invoice['invoice_id'].'/draft"'; } ?>>Draft</a></li>
                        </ul>
                      </li>
                      <li class="divider"></li>
                      <li><a href="<?php echo base_url(); ?>employee/invoice/view_invoice/<?php echo $invoice['vendor_partner']; ?>/<?php echo $invoice['invoice_id']; ?>" target="_blank">View Invoice</a></li>
                    </ul>
                </div>
            
<!--             <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" <?php //if($invoice['amount_paid'] > 0 ) { echo "disabled"; } ?>>Action
                <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li><a href="<?php //echo base_url();?>employee/invoice/regenerate_invoice/<?php //echo $invoice['invoice_id'];?>/final">Final</a></li>
                  <li class="divider"></li>
                  <li><a href="<?php //echo base_url();?>employee/invoice/regenerate_invoice/<?php //echo $invoice['invoice_id'];?>/draft">Draft</a></li>
                </ul>
              </div>-->
            <?php }else{?>
                
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Action
                    <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a <?php if($invoice['amount_paid'] > 0 ) { echo 'style="color:#33333385;"'; } else { echo 'href="'.base_url().'employee/invoice/insert_update_invoice/'.$invoice['vendor_partner'].'/'.$invoice['invoice_id'].'"'; } ?> >Update</a></li>
                      <li class="divider"></li>
                      <li><a href="<?php echo base_url()?>employee/invoice/sendInvoiceMail/<?php echo $invoice['invoice_id']; ?>">Resend Invoice</a></li>
                      <li class="divider"></li>
                      <li><a href="<?php echo base_url(); ?>employee/invoice/view_invoice/<?php echo $invoice['vendor_partner']; ?>/<?php echo $invoice['invoice_id']; ?>" target="_blank">View Invoice</a></li>
                    </ul>
                </div>
            <?php } ?>
         </td>
<!--         <td>
             <a href="<?php //echo base_url()?>employee/invoice/insert_update_invoice/<?php //echo $invoice['vendor_partner'];?>/<?php //echo $invoice['invoice_id'];?>" <?php //if($invoice['amount_paid'] > 0 ) { echo "disabled"; } ?> class="btn btn-sm btn-info" >Update</a>
         </td>-->
         
          <?php  $count = $count+1;  ?>
<!--         <td class="col-md-6">
             <a href="<?php //echo base_url()?>employee/invoice/sendInvoiceMail/<?php //echo $invoice['invoice_id']; ?>" class="btn btn-sm btn-primary">Resend Invoice</a>
         </td>-->

        </tr>
      <?php }} ?>


        <tr style="font-weight: bold;">
         <td><b>Total</b></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td><?php echo sprintf("%.2f",$sum_of_total_service_charges); ?></td>
         <td><?php echo sprintf("%.2f",$sum_total_additional_service_charge); ?></td>
         <td><?php echo sprintf("%.2f",$sum_total_parts_cost); ?></td>
         <td><?php echo sprintf("%.2f",$sum_tds); ?></td>
         <td></td>
         <td></td>
         <td><?php echo sprintf("%.2f",$pay_by_247); ?></td>
         <td><?php echo sprintf("%.2f",$pay_by_partner); ?></td>
         <td></td>
         <td id="final_amount_selected"></td>
         <td><input type="submit" class="form-control btn btn-sm btn-primary" value="Pay"></td>
         <td> </td>
        
       
         
      </tr>
   </tbody>
   </tbody>
</table>
         <!--Invoice Payment History Modal-->
    <div id="invoiceDetailsModal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-body">
                  <div id="open_model"></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>
      </div>
        
    </div>
<!-- end Invoice Payment History Modal -->

<!--Invoice Defective Part Pending Booking with Age Modal-->
    <div id="defective_part_pending_booking_age" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-body">
                  <table class="table table-bordered  table-hover table-striped data">
                      <thead>
                        <th>SN</th>
                        <th>Booking ID</th>
                        <th>Shipped Part Type</th>
                        <th>Pending Age</th>
                        <th>Challan Approx Value</th>
                      </thead>
                      <tbody id="defective-model">
                          
                      </tbody>
                  </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              </div>
            </div>
      </div>
        
    </div>
<!-- end Invoice Defective Part Pending Booking with Age Moda -->

      </form>
  <script type="text/javascript">
     $(".in_disable").prop('disabled', true);
     
      function sum_amount(){
              var total_amount_collected = 0;
              var total_tds = 0;
              var total_amount_paid = 0;
              $(".in_disable").prop('disabled', true);
              $("input[type=checkbox]:checked").each(function(i) {
              div = this.id .split('_');
              $("#intdsAmount_"+div[1]).prop('disabled', false);
              $("#inAmountCollected_"+div[1]).prop('disabled', false);
              $("#inAmountPaid__"+div[1]).prop('disabled', false);
              var amount_paid = $('#amount_paid_'+ div[1]).text();
              var tds_amount = $('#tds_'+ div[1]).text();
              if(amount_paid > 0){
                  tds_amount = 0;
              } 
              var amount_partner_to_be_pay = $('#pay_partner'+ div[1]).text();
              if(amount_partner_to_be_pay > 0){
                  amount_paid = -Math.abs(amount_paid);
              }
              var pay = Number($('#pay_247'+ div[1]).text()) + Number(amount_partner_to_be_pay) + Number(amount_paid);
            
              
              total_amount_collected += Number(pay);
              total_tds += Number(tds_amount);
              
              //total_amount_paid += Number(Math.abs(amount_paid));
        
              });
              
              $('#selected_amount_collected').val(total_amount_collected.toFixed(2));
              $('#selected_tds').val(total_tds.toFixed(2));
              document.getElementById("final_amount_selected").innerHTML = Math.abs(total_amount_collected.toFixed(2));
              if(Number($('#selected_amount_collected').val())>0){
                    $('#final_amount_selected').css("color","#3f9c4e;");
              }
              else if(Number($('#selected_amount_collected').val()) === 0){
                    $('#final_amount_selected').css("color", "#333333;");
              }
              else{
                    $('#final_amount_selected').css("color","#ea1d27f2;"); 
              }
             // document.getElementById("final_tds_selected").innerHTML = total_tds.toFixed(2);
              
      }
  </script>
  
  <script>
      $('.get_invoice_payment_history').on('click',function(){
          var invoice_id = $(this).attr('data-id');
          if(invoice_id){
              $.ajax({
                  type:"POST",
                  url: "<?php echo base_url(); ?>employee/invoice/get_invoice_payment_history",
                  data: {'invoice_id':invoice_id},
                  success:function(response){
                      //console.log(response);
                      $("#open_model").html(response);   
                      $('#invoiceDetailsModal').modal('toggle');
                  }
              });
          }
      });
  </script>
<?php } ?>
  
  <?php if($invoicing_summary['vendor_partner'] == "vendor") { ?>
      
      <br>
     <h2>Document Status</h2>
      <table class="table table-bordered  table-hover table-striped data"  >
          <thead>
          <tr>
             <th class="text-center">PAN</th>     
             <th class="text-center">GST</th>
             <th class="text-center">Bank Details Verification</th>
             <th class="text-center">Contract</th>
             <th class="text-center">Temporary Status</th>
             <th class="text-center">Permanent Status</th>
             <th class="text-center">Defective Parts Not Shipped by SF</th>
             <th class="text-center">Go to vendor Details</th>
          </tr>
       </thead>
       <tbody>
           <tr>
               <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['pan_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
               <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['gst_no'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
               <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['is_verified']) && $invoicing_summary['is_verified'] == '1'){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
                <td class="text-center">
                 
                   <img src="<?php echo  base_url(); ?><?php if(!empty($invoicing_summary['contract_file'])){ echo "images/ok.png";} else { echo "images/red_cross.png";} ?>" style="width:15px; height: 15px;" /> 
               </td>
               <td class="text-center">
                   <?php if($invoicing_summary['on_off'] == 1){ echo "On"; }else{ echo "Off"; } ?> 
               </td>
               <td class="text-center">
                   <?php if($invoicing_summary['active'] == 1){ echo "Active"; }else{ echo "Deactive"; } ?>
               </td>
               <td class="text-center">
                  <?php if($invoicing_summary['count_spare_part'] > 0){ ?>
                      <a href="javascript:void(0)" onclick="get_defective_spare_count_details()">  <?php print_r($invoicing_summary['count_spare_part']);?> </a>
                 <?php  } else { echo "0"; } ?>
 
               </td>
               <td class="text-center">
                 
                   <a href="<?php echo base_url()?>/employee/vendor/editvendor/<?php echo $invoicing_summary['id'] ?>" target="_blank" class="btn btn-sm btn-primary" >Click here</a>
               </td>

           </tr>
       </tbody>
      </table>
      
 <?php }?>
  
    <?php //if(isset($bank_statement)) { ?>
    <br>
     <p><h2>Bank Transactions</h2></p>
     <table class="table table-bordered  table-hover table-striped data"  id="bank_transaction_datatable1">
        <thead>
          <tr>
             <th>No #</th>
             <th>Transaction Date</th>
             <th>Description</th>
             <th>Amt Received from Vendor</th>         
             <th>Amt Paid to Vendor</th>
             <th>TDS Deducted</th>
             <th>Invoices</th>
             <th>Bank Name / Mode</th>
             <th>Transaction Id</th>
          </tr>
       </thead>
       <tfoot>
           <tr>
               <th>Total</th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
               <th></th>
           </tr>
       </tfoot>
      </table>
    <br><br>  
    <h2><u>Final Summary</u></h2>
    <br>
    <?php 
 
        $final_settlement = $invoicing_summary['final_amount'];
    ?>
    <p><h4>Vendor has to pay to 247around = Rs. <?php if($final_settlement >= 0){ echo sprintf("%.2f",$final_settlement);} else { echo 0;} ?></h4></p>
    <p><h4>247around has to pay to vendor = Rs. <?php if($final_settlement < 0){ echo abs(sprintf("%.2f",$final_settlement));} else {echo 0;} ?></h4></p>
    <hr/>
    <?php if(isset($unbilled_amount)){ ?> 
     <h2><u>Un-billed Amount (Invoice is not generated)</u></h2>
     <p><h4>Vendor has to pay to 247around = Rs. <?php if($unbilled_amount[0]['unbilled_amount'] >= 0){ echo sprintf("%.2f",$unbilled_amount[0]['unbilled_amount']);} else { echo 0;} ?></h4></p>
    <p><h4>247around has to pay to vendor = Rs. <?php if($unbilled_amount[0]['unbilled_amount'] < 0){ echo abs(sprintf("%.2f",$unbilled_amount[0]['unbilled_amount']));} else { echo 0;} ?></h4></p>
    
    <?php } 
    //} 
    ?>


<style>
#advance_text {
    -ms-transform: rotate(330deg); /* IE 9 */
    -webkit-transform: rotate(330deg); /* Safari */
    transform: rotate(330deg); /* Standard syntax */
    color: red;
    font-weight: bold;
}
    
    
</style>

<script>
function get_defective_spare_count_details(){
    var vendor_id = $("#invoice_id").val();
    
    $.ajax({
        type:"POST",
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
        url: "<?php echo base_url(); ?>employee/invoice/get_pending_defective_parts_list/" + vendor_id,
        success:function(response){
            console.log(response);
            if(response === "DATA NOT FOUND"){
                $('body').loadingModal('destroy');
                alert("DATA NOT FOUND");
            } else {
               $("#defective-model").html(response);   
               $('#defective_part_pending_booking_age').modal('toggle'); 
               $('body').loadingModal('destroy');
            }
            
        }
    });
    
}
</script>
<script>
var bank_transaction_datatable;
$(document).ready(function(){
    
    $('.dropdown-submenu a.custom_dropdown-submenu').on("click", function(e){
      $(this).next('ul').toggle();
      e.stopPropagation();
      e.preventDefault();
    });
  
    /****** this is used to check/uncheck all checkboxes on select all checkbox *****/
    $("#selecctall_amt").change(function(){
        $(".checkbox_amt").prop('checked', $(this).prop("checked"));
        sum_amount();
    });
    
    $(".checkbox_amt").change(function(){
        if ($('.checkbox_amt:checked').length === $('.checkbox_amt').length) {
           $("#selecctall_amt").prop('checked', true);
        }
        else{
            $("#selecctall_amt").prop('checked', false);
        }
    });
    
    bank_transaction_datatable = $('#bank_transaction_datatable1').DataTable({
                "processing": true, 
                "serverSide": true,
                "dom": 'lBfrtip',
                "buttons": [
                    {
                        extend: 'excel',
                        text: 'Export',
                        exportOptions: {
                            columns: [ 1,2,3,4,5,6,7,8 ]
                        },
                        title: 'bank_transactions',
                    },
                ],
                "language":{ 
                    "processing": "<div class='spinner'>\n\
                                        <div class='rect1' style='background-color:#db3236'></div>\n\
                                        <div class='rect2' style='background-color:#4885ed'></div>\n\
                                        <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                        <div class='rect4' style='background-color:#3cba54'></div>\n\
                                    </div>"
                },
                "order": [], 
                "pageLength": 10,
                "lengthMenu": [[10, 25, -1], [10, 25, "All"]],
                "ordering": false,
                "ajax": {
                    "url": "<?php echo base_url(); ?>employee/accounting/get_payment_summary_searched_data",
                    "type": "POST",
                    data: function(d){ 
                        d.request_type = "invoice_summary_bank_transaction";
                        d.vendor_partner = '<?php echo $vendor_partner; ?>';
                        d.vendor_partner_id = '<?php echo $vendor_partner_id; ?>';
                    }
                },
                "footerCallback": function ( row, data, start, end, display ) { 
                        var api = this.api();
                        nb_cols = api.columns().nodes().length;
                        
                        var j = 3;
                        while(j < nb_cols){
                            var pageTotal = api.column( j, { page: 'current'} ).data().reduce( function (a, b, c) {
                                //console.log(c);
                                console.log(api.column( 0, { page: 'current'} ).data()[c].toString());
                                var str = api.column( 0, { page: 'current'} ).data()[c].toString();
                                if(str.includes("Advance")){
                                    return "";
                                }
                                else{
                                    var rsum = parseFloat(Number(a) + Number(b)).toFixed(2);
                                    if(rsum == "NaN"){
                                        return "";
                                    }
                                    else{
                                        return rsum;
                                    }
                                }
                            }, 0 );
                       
                            $( api.column( j ).footer() ).html(pageTotal);
                            j++;
                        } 
                }
                
    });
   
});

function bd_update(btn, id){ 
    if ($(btn).siblings(".text").is(":hidden")) {
        var prethis = $(btn);
        var bd_description = $(btn).siblings("input").val();
        $(btn).siblings(".text").text($(btn).siblings("input").val());
        
        $.ajax({
            url: "<?php echo base_url() ?>employee/invoice/update_bank_transaction_description",
            type: "POST",
            beforeSend: function(){
                
                 prethis.html('<i class="fa fa-circle-o-notch fa-lg" aria-hidden="true"></i>');
             },
            data: { description: bd_description, id: id},
            success: function (data) {
                if(data){
                    prethis.siblings("input").remove();
                    prethis.siblings(".text").show();
                    prethis.html('<i class="fa fa-pencil fa-lg" aria-hidden="true"></i>');
                } else {
                    alert("There is a problem to update");
                    alert(data);
                }
                
            }
        });
    }
    else {
        var text = $(btn).siblings(".text").text();
        $(btn).before("<input type=\"text\" class=\"form-control\" value=\"" + text + "\">");
        $(btn).html('<i class="fa fa-check fa-lg" aria-hidden="true"></i>');
        $(btn).siblings(".text").hide();
    }
}
</script>
