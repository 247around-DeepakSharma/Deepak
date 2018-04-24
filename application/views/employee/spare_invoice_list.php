<div id="page-wrapper" >
    <div class="col-md-12">
        <h2 class="page-header">Spare Invoice List</h2>
    </div>
    <div class="col-md-12">
        <table class="table table-bordered table-hover table-striped data" id="invoice_table">
            <thead>
                <tr>
                    <th>SNo</th>
                    <th>Booking ID</th>
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
                    <td><?php echo $value->booking_id; ?></td>
                    <td><?php echo $value->public_name; ?></td>
                    <td><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $value->purchase_price; ?></td>
                    <td><i class="fa fa-inr" aria-hidden="true"></i> <?php echo $value->sell_price; ?></td>
                    <td><?php echo $value->purchase_invoice_id; ?></td>
                    <td class="text-center">
                        <?php if(!empty($value->incoming_invoice_pdf)){ ?>
                        <a target="_blank" href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $value->incoming_invoice_pdf;  ?>">
                            <img style="width:27px;" src="<?php echo base_url();?>images/invoice_icon.png"; /></a>
                    <?php } ?>
                    </td>
                    <td><?php echo $value->sell_invoice_id; ?></td>
                    <td><input type="checkbox" class="form-control spare_id" name="spare_id[]" value="<?php echo $value->id; ?>" /></td>
                </tr>
                <?php }?>
            </tbody>
            
        </table>
        <div class="col-md-12 col-md-offset-6">
            <button onclick="open_create_invoice_form()" class="btn btn-md btn-primary">create</button>
        </div>
        
    </div>
     <div id="myModal2" class="modal fade" role="dialog">
      <div class="modal-dialog">
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
                                <input type="text" class="form-control" style="font-size: 13px;"  id="invoice_id" placeholder="Enter Invoice ID" name="invoice_id" value = "" required>
                            </div>
                        </div>
                         <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">Invoice Date *</label>
                                 
                                <input type="text" class="form-control" style="font-size: 13px; background-color:#fff;" placeholder="Select Date" id="invoice_date" name="invoice_date" required readonly='true' >
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">Parts Qty *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="parts_count" placeholder="Enter Parts Quantity" name="parts_count" value = "" required>
                            </div>
                        </div>
                       
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">Parts Charge(With GST) *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="parts_cost" placeholder="Enter Parts Charge" name="parts_charge" value = "" required>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">GST Rate *</label>
                                <input type="text" class="form-control" style="font-size: 13px;" id="gst_rate" placeholder="Enter GST Rate" name="gst_rate" value = "" required>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group col-md-12  ">
                                <label for="remarks">HSN Code *</label>
                                <input type="text" class="form-control" style="font-size: 13px;"  id="gst_rate" placeholder="Enter HSN Code" name="hsn_code" value = "" >
                            </div>
                        </div>
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
               <button type="submit" class="btn btn-success" onclick="genaerate_purchase_invoice()">Submit</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
    $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
function genaerate_purchase_invoice(){
   
       swal({
                title: "Do You Want To Continue?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: true
                
            },
            function(){
                 var spare_id = [];
            $('.spare_id:checked').each(function (i) {
                spare_id[i] = $(this).val();
            });

    
    var fd = new FormData(document.getElementById("purchase_invoice_form"));
        fd.append("label", "WEBUPLOAD");
        for (var i = 0; i < spare_id.length; i++) {
         fd.append('spare_id[]', spare_id[i]);
        }
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
                    $('#myModal2').modal('toggle'); 
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
                    
                }
                 $('body').loadingModal('destroy');
                
            }
          });
          });
}

function open_create_invoice_form(){
    var spare_id = [];
    $('.spare_id:checked').each(function (i) {
        spare_id[i] = $(this).val();
    });
    console.log(spare_id);
    console.log(spare_id.length);
    if(spare_id.length > 0){
         $('#myModal2').modal('toggle'); 
    } else {
        alert("Please Select Atleast One Checkbox");
    }
    
}
</script>
