
<div id="page-wrapper" >
    <div class="col-md-12" style="border-bottom: 1px solid #ccc; margin-bottom: 30px;">
        <div class="col-md-6">
            <h2 class="page-header" style="border: none;">Spare Invoice List</h2>
        </div>
        <div class="col-md-6">
            <button onclick="open_create_invoice_form()" style="margin-top: 45px;float: right;" class="btn btn-md btn-primary" id="btn_create_invoice" name="btn_create_invoice">create</button>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped data" id="invoice_table">
            <thead>
                <tr>
                    <th>SNo</th>
                    <th>Booking ID</th>
                    <th>Booking Type</th>
                    <th>Part Type</th>
                    <th>Spare Status</th>
                    <th>Partner Name</th>
                    <th>Purchase Price</th>
                    <th>Sell Price</th>
                    <th>Purchase Invoice ID</th>
                    <th>Purchase Invoice PDF</th>
                    <th>Sale Invoice ID</th>
                    <th>Create Purchase Invoice </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($spare as $key => $value) { ?>
                <tr>
                    <td><?php echo $key+1; ?></td>
                    <td><a  target="_blank" href="<?php echo base_url();?>employee/booking/viewdetails/<?php echo $value->booking_id;?>"  title='View'><?php echo $value->booking_id; ?></a></td>
                    <td><?php echo $value->request_type; ?></td>
                    <td><?php  if($value->part_warranty_status==SPARE_PART_IN_OUT_OF_WARRANTY_STATUS){echo REPAIR_OOW_TAG;}else{ echo REPAIR_IN_WARRANTY_TAG;}  ?></td>
                    <td><?php echo $value->status; ?></td>
                    <td><?php  echo $value->public_name; ?></td>
                    <td><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $value->purchase_price; ?></td>
                    <td><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $value->sell_price; ?></td>
                    <td><?php echo $value->invoice_id; ?></td>
                    <td class="text-center">
                        <?php if(!empty($value->invoice_pdf)){ ?>
                        <a target="_blank" href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $value->invoice_pdf;  ?>">
                            <img style="width:27px;" src="<?php echo base_url();?>images/invoice_icon.png"; /></a>
                    <?php } ?>
                    </td>
                    <td><?php if(!empty($value->sell_invoice_id)){ echo $value->sell_invoice_id; } else { ?>
                        <a href="<?php echo base_url();?>employee/invoice/generate_oow_parts_invoice/<?php echo $value->id; ?>" id="btn_sell_invoice_<?php echo $value->id; ?>" onclick="disable_btn(this.id)" class="btn btn-md btn-success">Generate Sale Invoice</a>
                    <?php } ?></td>
                    <td><input type="checkbox" class="form-control spare_id" name="spare_id[]" data-partner_id="<?php echo $value->booking_partner_id; ?>" data-invoice_id ="<?php echo $value->invoice_id?>" data-spare_id="<?php echo $value->id; ?>" value="<?php echo $value->id; ?>" /></td>
                </tr>
                <?php }?>
            </tbody>
            
        </table>
                
    </div>
<div id="purchase_invoice" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title">Generate Purchase Invoice</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id ="purchase_invoice_form" action="#"  method="POST" >
                    <div class="col-md-12" >
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="Claimed Price">Invoice ID *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="invoice_id" placeholder="Enter Invoice ID" name="invoice_id" value = "" onblur="check_invoice_id(this.id)" required>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">Invoice Date *</label>
                                <input type="text" class="form-control" style="font-size: 13px; background-color:#fff;" placeholder="Select Date" id="invoice_date" name="invoice_date" required readonly='true' >
                            </div>
                        </div>
                    </div>
                    <div id="spare_inner_html"></div>
                    <div class="col-md-12 ">
                        <div class="col-md-12 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">Remarks *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="gst_rate" placeholder="Enter Remarks" name="remarks" value = "" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id="btn_purchase_invoice" name="btn_purchase_invoice" onclick="genaerate_purchase_invoice()">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="btn_create_invoice.disabled=false;close_model()">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});

 function open_create_invoice_form(){
        $('#btn_create_invoice').attr('disabled',true);
        var spare_id = [];
        var partner_id_array = [];
        var invoice_id_array = [];
        var data = [];
        $('.spare_id:checked').each(function (i) {
            spare_id[i] = $(this).val();
            var partner_id  = $(this).attr('data-partner_id');
            var invoice_id  = $(this).attr('data-invoice_id');
            partner_id_array.push(partner_id);
            invoice_id_array.push(invoice_id);
            data[i] =[];
            data[i]['spare_id'] = spare_id[i];
            data[i]['partner_id'] = partner_id;
         
        });
        var unique_partner = ArrayNoDuplicate(partner_id_array);
        var unique_invoice = ArrayNoDuplicate(invoice_id_array);
        var flag = true;
        if(unique_invoice.length > 1){
          flag = false;
          $('#btn_create_invoice').attr('disabled',false);
          alert("You Can not select different invoice id.");  
          return false;
        }
        
        if(unique_partner.length > 1){
             flag = false;
             $('#btn_create_invoice').attr('disabled',false);
             alert("You Can not select multiple partner booking");
             return false;
         } 
          if(flag){
            $.ajax({
                 method:'POST',
                 dataType: "json",
                 url:'<?php echo base_url(); ?>employee/inventory/get_spare_invoice_details',
                 data: { spare_id_array : spare_id },
                 success:function(data){ 
                     if(data.length > 0){
                         $("#invoice_id").val(data[0]['invoice_id']);
                         $("#invoice_date").val(data[0]['invoice_date']);
                       var html  = '<input type="hidden" name="partner_id" value="'+unique_partner[0]+'" />';
                       for(k =0; k < data.length; k++){
                            html +='<div class="col-md-12" >';
                            html += '<div class="col-md-4 "> <div class="form-group col-md-12  "><label for="remarks">Booking ID *</label>';
                            html += '<input required type="text" class="form-control" style="font-size: 13px;"  id="bookingid_'+k+'" placeholder="Enter Booking ID" name="part['+data[k]["spare_id"]+'][booking_id]" value = "'+data[k]['booking_id']+'" >';
                            html += '</div></div>';

                            html += '<div class="col-md-3 " style="width: 18%"><div class="form-group col-md-12  ">';
                            html += ' <label for="remarks">HSN Code *</label>';
                            html += '<input required type="text" class="form-control" style="font-size: 13px;"  id="hsncode_'+k+'" placeholder="HSN Code" name="part['+data[k]["spare_id"]+'][hsn_code]" value = "'+data[k]["hsn_code"]+'" >';
                            html += '</div></div>';

                            html += '<div class="col-md-3 " style="width: 17%"><div class="form-group col-md-12  ">';
                            html += ' <label for="remarks">GST Rate *</label>';
                            html += '<input required type="number" class="form-control" style="font-size: 13px;"  id="gstrate'+k+'" placeholder="GST Rate" name="part['+data[k]["spare_id"]+'][gst_rate]" value = "'+data[k]["gst_rate"]+'" >';
                            html += '</div></div>';

                            html += '<div class="col-md-4 " style="width: 30%"><div class="form-group col-md-12  ">';
                            html += ' <label for="remarks">Basic Amount *</label>';
                            html += '<input required type="number" step=".01" class="form-control" style="font-size: 13px;"  id="basic_amount'+k+'" placeholder="Enter Amount" name="part['+data[k]["spare_id"]+'][basic_amount]" value = "'+data[k]["invoice_amount"]+'" >';
                            html += '</div></div>';
                            html += '</div>';
                       }  
                       
                    $("#spare_inner_html").html(html);
                    $('#purchase_invoice').modal('toggle'); 
                    }
                    else {
                        $('#btn_create_invoice').attr('disabled',false);
                    }
                     
                 }
            });  
        }
            
    }
    
    function ArrayNoDuplicate(a) {
        var temp = {};
        for (var i = 0; i < a.length; i++)
            temp[a[i]] = true;
        var r = [];
        for (var k in temp)
            r.push(k);
        return r;
    }
    
    function genaerate_purchase_invoice(){
        $('#btn_purchase_invoice,#btn_create_invoice').attr('disabled',true);
            swal({
                     title: "Do You Want To Continue?",
                     type: "warning",
                     showCancelButton: true,
                     confirmButtonColor: "#DD6B55",
                     closeOnConfirm: true
    
                 },
                 function(isConfirm) {
                    if (isConfirm) {
                    
    
         var fd = new FormData(document.getElementById("purchase_invoice_form"));
             fd.append("label", "WEBUPLOAD");
                $.ajax({
                 type: "POST",
                 beforeSend: function(){
                      swal("Thanks!", "Please Wait..", "success");
                         $('body').loadingModal({
                         position: 'auto',
                         text: 'Loading Please Wait...',
                         color: '#fff',
                         opacity: '0.7',
                         backgroundColor: 'rgb(0,0,0)',
                         animation: 'wave'
                       });
    
                  },
                 data:fd,
                 processData: false,
                 contentType: false,
                 url: "<?php echo base_url() ?>employee/invoice/generate_spare_purchase_invoice",
                 success: function (data) {
                     console.log(data);
                     if(data === 'Success'){
                       
                        $('#purchase_invoice').modal('toggle'); 
                        $("#invoice_id").val("");
                        $("#invoice_date").val("");
                        $("#parts_cost").val("");
                        $("#gst_rate").val("");
                        $("#hsn_code").val("");
                        $("#remarks").val("");
                        swal("Thanks!", "Booking updated successfully!", "success");
                        location.reload();
    
                     } else {
                         swal("Oops", data, "error");
                         alert(data);
                         $('#btn_purchase_invoice,#btn_create_invoice').attr('disabled',false);
                     }
                      $('body').loadingModal('destroy');
    
                    }
                });
            } 
            else {
                 $('#btn_purchase_invoice,#btn_create_invoice').attr('disabled',false);
            }
         });
     }
     
     function check_invoice_id(id){
    
        var invoice_id = $('#'+id).val().trim();
        if(invoice_id){

            $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>check_invoice_id_exists/'+invoice_id,
                data:{is_ajax:true},
                success:function(res){
                    //console.log(res);
                    var obj = JSON.parse(res);
                    if(obj.status === true){

                        alert('Invoice number already exists');
                        $("#invoice_id").val('');
                    }
                }
            });
            
        }
    }
    
    function disable_btn(id){
        $("#"+id).attr('disabled',true);
    }
</script>
