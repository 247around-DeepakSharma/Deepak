<style>
    #recheck_docket_number_table_filter, #ingnored_docket_number_table_filter{
        text-align: right;
    }
</style>
<style type="text/css">
    #booking_form .form-group label.error {
    margin:4px 0 5px !important;
    width:auto !important;
    }
    #tabs ul{
    margin:0px;
    padding:0px;
    }
    #tabs li{
    list-style: none;
    float: left;
    position: relative;
    top: 0;
    margin: 1px .2em 0 0;
    border-bottom-width: 0;
    padding: 0;
    white-space: nowrap;
    border: 1px solid #2c9d9c;
    background: #d9edf7 url(images/ui-bg_glass_75_e6e6e6_1x400.png) 50% 50% repeat-x;
    font-weight: normal;
    color: #555555;
    border-top-right-radius: 4px;
    border-top-left-radius: 4px;
    border-bottom: 0px;
    }
    #tabs a{
    float: left;
    padding: .5em 1em;
    text-decoration: none;
    }
    .col-md-12 {
    padding: 10px;
    }
    .select2-container{
    width:100% !important;
    }
    .vertical-align{
    height:100%;
    padding-top: 1%
    }
    #warehouse_datatable_filter{
    text-align: right;
    }
    .form-horizontal .control-label{
    text-align: left;
    }
    .custom-sf-list{
    padding: 10px;        
    background-color: #fff;        
    } 
    .hide{
        display: none;
    }
    .radio-inline input[type=radio] {
    position: absolute;   
    margin-left: 10px;
}
</style>
 <div id="page-wrapper">
    <div class="row">
        <div class="clear"></div>
        <div class="panel panel-info">
            <div class="panel-heading" style="padding: 6px 7px 0px 10px;"><h4>Courier Company Invoice Detail</h4></div>
            <div id="tabs" style="border:0px solid #fff;float:left;">
                <div class="col-md-12" style="">
                    <ul>
                        <li style="background:#fff"><a id="1" href="#tabs-1" onclick="load_form(this.id, 'vendor')"><span class="panel-title">Recheck Docket No</span></a></li>
                        <li><a id="2" href="#tabs-1" onclick="load_form(this.id, 'partner')"><span class="panel-title">Ignored Docket Number</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
            <div class="panel-body">
                <div class="col-md-12 ">
                    <div id="container_1" class="form_container">
                        <table class="table table-bordered table-hover table-striped" id="recheck_docket_number_table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Invoice Id</th>
                                    <th>AWB Number</th>
                                    <th>Company Name</th>
                                    <th>Billable Weight</th>
                                    <th>Actual Weight</th>
                                    <th>Courier Charges</th>
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
                                    <td><?php echo $value['billable_weight']; ?></td>
                                    <td><?php echo $value['actual_weight']; ?></td>
                                    <td><?php echo $value['courier_charge']; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-xs" onclick="recheck_docket_nember('<?php echo $value['id']; ?>', '<?php echo $value['awb_number']; ?>', '<?php echo $value['courier_charge']; ?>')">Recheck</button>
                                        <button type="button" class="btn btn-warning btn-xs" onclick="open_reject_remark_model('<?php echo $value['id']; ?>')" data-toggle="modal" data-target="#rejectInvoiceModal">Ignore</button>
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
                    <div id="container_2" class="form_container" style="display:none">
                        <table class="table table-bordered table-hover table-striped" id="ingnored_docket_number_table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Invoice Id</th>
                                    <th>AWB Number</th>
                                    <th>Company Name</th>
                                    <th>Courier Charges</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $i = 1;
                                    foreach ($ignored_invoice_detail as $key => $value) {
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $value['courier_invoice_id']; ?></td>
                                    <td><?php echo $value['awb_number']; ?></td>
                                    <td><?php echo $value['company_name']; ?></td>
                                    <td><?php echo $value['courier_charge']; ?></td>
                                    <td><?php echo $value['reject_remarks']; ?></td>
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
        <div class="clear"></div>
       </div>
    </div>


<!--- Reject courier invoice model  --->
<div id="rejectInvoiceModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ignore Remark <button type="button" class="close" data-dismiss="modal">&times;</button></h4>
            </div>
            <div class="modal-body">
                   <div class="row">
                       <input type="hidden" id="courier_invoice_id" value="">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="reject_remarks">Remark *</label>
                                <textarea class="form-control" id="reject_remarks" name="reject_remarks" placeholder="Enter Ignore Remark...."></textarea>
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
$(document).ready(function(){
    $("#recheck_docket_number_table, #ingnored_docket_number_table").DataTable();
}); 

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
    if($("#reject_remarks").val()){
        $.ajax({
            url: "<?php echo base_url() ?>employee/inventory/reject_courier_invoice",
            type: "POST",
            data: {id:$("#courier_invoice_id").val(), reject_remark:$("#reject_remarks").val()}
        }).done(function(response){
            if(response){
                $('#rejectInvoiceModal').modal('hide');
                alert("Courier invoice successfully ignored");
                location.reload();
            }
            else{
                console.log(response);
                alert("Error occured while rejecting courier invoice");
            }
        });
    }
    else{
        alert("Please Enter Reject Remark");
    }
}

    function load_form(tab_id){
        var total_div  = 3;
        for(var i =1;i<=total_div;i++){
            if(i != tab_id){
                document.getElementById("container_"+i).style.display='none';
                document.getElementById(i).style.background='#d9edf7';
            }
            else{
                document.getElementById("container_"+i).style.display='block';
                document.getElementById(i).style.background='#fff';
            }
        }
    }
</script>

