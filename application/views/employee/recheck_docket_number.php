
<div id="page-wrapper">
    <div class="panel panel-info" style="margin-top:20px;">
        <div class="panel-heading">Recheck Docket Number</div>
            <div class="panel-body">
            <div class="col-md-12" style="padding: 0px;">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Invoive Id</th>
                            <th>AWB Number</th>
                            <th>Comapny Name</th>
                            <th>Courier Charges</th>
                            <th>Billable Weight</th>
                            <th>Actual Weight</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i = 1;
                            foreach ($courier_company_detail as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $value['courier_invoice_id']; ?></td>
                            <td><?php echo $value['awb_number']; ?></td>
                            <td><?php echo $value['company_name']; ?></td>
                            <td><?php echo $value['courier_charge']; ?></td>
                            <td><?php echo $value['billable_weight']; ?></td>
                            <td><?php echo $value['actual_weight']; ?></td>
                            <td>
                                <button type="button" class="btn btn-success btn-xs" onclick="recheck_docket_nember('<?php echo $value['id']; ?>', '<?php echo $value['awb_number']; ?>', '<?php echo $value['courier_charge']; ?>')">Recheck</button>
                                <button type="button" class="btn btn-danger btn-xs" onclick="open_reject_remark_model('<?php echo $value['id']; ?>')" data-toggle="modal" data-target="#rejectInvoiceModal">Reject</button>
                            </td>
                        </tr>
                        <?php
                            }
                            if(count($courier_company_detail) === 0){ ?>
                                <tr><td colspan="8"> No data found </td></tr>
                        <?php        
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--- Reject courier invoice model  --->
<div id="rejectInvoiceModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reject Remark <button type="button" class="close" data-dismiss="modal">&times;</button></h4>
            </div>
            <div class="modal-body">
                   <div class="row">
                       <input type="hidden" id="courier_invoice_id" value="">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="reject_remarks">Remark *</label>
                                <textarea class="form-control" id="reject_remarks" name="reject_remarks" placeholder="Enter Reject Remark...."></textarea>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="reject_courier_invoice()">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!---- End ----->

<script>
function recheck_docket_nember(id, awb_no, courier_charge){
    $.ajax({
        url: "<?php echo base_url() ?>employee/inventory/process_recheck_docket_number",
        type: "POST",
        data: {id:id, awb_no:awb_no, courier_charge:courier_charge}
    }).done(function (response) { 
        if(response){
            alert('Update Successfully.');
            location.reload();
        }
        else{
            alert('AWB number not found.');
        }
    });
}

function open_reject_remark_model(id){
    $("#courier_invoice_id").val(id);
}

function reject_courier_invoice(){
    $.ajax({
        url: "<?php echo base_url() ?>employee/inventory/reject_courier_invoice",
        type: "POST",
        data: {id:$("#courier_invoice_id").val(), reject_remark:$("#reject_remarks").val()}
    }).done(function(response){
        if(response){
            $('#rejectInvoiceModal').modal('hide');
            alert("Courier invoice successfully rejected");
            location.reload();
        }
        else{
            console.log(response);
            alert("Error occured while rejecting courier invoice");
        }
        
    });
}
</script>

