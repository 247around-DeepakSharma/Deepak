<style>
    .disabled_button{
        pointer-events:none;
    }    
    .line_break{
        word-break: break-all;
    }
</style>
<div role="tabpanel" class="tab-pane" id="estimate_cost_given">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <table id="estimate_cost_given_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead >
                                <tr>
                                    <th class="text-center" >No</th>
                                    <th class="text-center" data-orderable="false">Booking Id</th>
                                    <th class="text-center" data-orderable="false">Spare Pending On</th>
                                    <th class="text-center" data-orderable="false">User</th>
                                    <th class="text-center" data-orderable="false">Mobile</th>
                                    <th class="text-center" data-orderable="false">Service Center</th>
                                    <th class="text-center" data-orderable="false">Partner</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Requested Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>
                                    <th class="text-center" data-orderable="false">Part Type</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="true">Age Of Requested</th>
                                    <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                    <th class="text-center" data-orderable="false">Cancel Part</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div role="tabpanel" class="tab-pane" id="oow_part_shipped">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <table id="oow_part_shipped_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead >
                                <th class="text-center" >No</th>
                                <th class="text-center" data-orderable="false">Booking Id</th>
                                <th class="text-center" data-orderable="false">Spare Pending On</th>
                                <th class="text-center" data-orderable="false">User</th>
                                <th class="text-center" data-orderable="false">Mobile</th>
                                <th class="text-center" data-orderable="false">Service Center</th>
                                <th class="text-center" data-orderable="false">Partner</th>
                                <th class="text-center" data-orderable="false">Requested Part</th>
                                <th class="text-center" data-orderable="false">Parts Number</th>
                                <th class="text-center" data-orderable="false">Part Type</th>
                                <th class="text-center" data-orderable="false">Shipped Part</th>
                                <th class="text-center" data-orderable="false">Booking Type</th>
                                <th class="text-center" data-orderable="false">Estimate Cost</th>
                                <th class="text-center" data-orderable="true">Age Of Shipped</th>
                                <th class="text-center" data-orderable="false">Challan File</th>
                                <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                <th class="text-center" data-orderable="false">Sale Invoice ID</th>
                                <th class="text-center" data-orderable="false">Purchase Invoice PDF</th>
                                <th class="text-center" data-orderable="false">Create Purchase Invoice</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="col-md-12 col-md-offset-6">
                            <button onclick="open_create_invoice_form()" class="btn btn-md btn-primary">Create Purchase Invoice</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="estimate_cost_requested">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <table id="estimate_cost_requested_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead >
                                <tr>
                                    <th class="text-center" >No</th>
                                    <th class="text-center" data-orderable="false">Booking Id</th>
                                    <th class="text-center" data-orderable="false">Spare Pending On</th>
                                    <th class="text-center" data-orderable="false">User</th>
                                    <th class="text-center" data-orderable="false">Mobile</th>
                                    <th class="text-center" data-orderable="false">Service Center</th>
                                    <th class="text-center" data-orderable="false">Partner</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Requested Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>
                                    <th class="text-center" data-orderable="false">Part Type</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="true">Age Of Requested</th>
                                    <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                    <th class="text-center" data-orderable="false">Cancel Part</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane active" id="spare_parts_requested">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <table id="spare_parts_requested_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead >
                                <tr>
                                    <th class="text-center" >No</th>
                                    <th class="text-center" data-orderable="false">Booking Id</th>
                                    <th class="text-center" data-orderable="false">Spare Pending On</th>
                                    <th class="text-center" data-orderable="false">User</th>
                                    <th class="text-center" data-orderable="false">Mobile</th>
                                    <th class="text-center" data-orderable="false">Service Center</th>
                                    <th class="text-center" data-orderable="false">Partner</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Requested Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>   
                                    <th class="text-center" data-orderable="false">Part Type</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="true">Age Of Requested</th>
                                    <!-- <th class="text-center" data-orderable="false">Update</th>-->
                                    <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                    <?php if($this->session->userdata('user_group') == 'admin'  || $this->session->userdata('user_group') == 'inventory_manager' || $this->session->userdata('user_group') == 'developer'){ ?>
                                    <th class="text-center" data-orderable="false">Edit Booking</th>
                                    <th class="text-center" data-orderable="false">Approval</th>
                                    <?php } ?>
                                    
                                    <th class="text-center" data-orderable="false">Cancel Part</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div role="tabpanel" class="tab-pane " id="spare_parts_requested_approved">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <table id="spare_parts_requested_table_approved" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead >
                                <tr>
                                    <th class="text-center" >No</th>
                                    <th class="text-center" data-orderable="false">Booking Id</th>
                                    <th class="text-center" data-orderable="false">Spare Pending On</th>
                                    <th class="text-center" data-orderable="false">User</th>
                                    <th class="text-center" data-orderable="false">Mobile</th>
                                    <th class="text-center" data-orderable="false">Service Center</th>
                                    <th class="text-center" data-orderable="false">Partner</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Requested Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>   
                                    <th class="text-center" data-orderable="false">Part Type</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="true">Age Of Requested</th>
                                    <!-- <th class="text-center" data-orderable="false">Update</th>-->
                                    <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                    <th class="text-center" data-orderable="false">Cancel</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div role="tabpanel" class="tab-pane " id="spare_parts_requested_rejected">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <table id="spare_parts_requested_table_reject" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead >
                                <tr>
                                    <th class="text-center" >No</th>
                                    <th class="text-center" data-orderable="false">Booking Id</th>
                                    <th class="text-center" data-orderable="false">Spare Pending On</th>
                                    <th class="text-center" data-orderable="false">User</th>
                                    <th class="text-center" data-orderable="false">Mobile</th>
                                    <th class="text-center" data-orderable="false">Service Center</th>
                                    <th class="text-center" data-orderable="false">Partner</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Requested Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>   
                                    <th class="text-center" data-orderable="false">Part Type</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="true">Age Of Rejection</th>
                                    <!-- <th class="text-center" data-orderable="false">Update</th>-->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModal2" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title">Reject Parts</h4>
            </div>
            <div class="modal-body">
                <h4 style="padding: 3px;font-size: 1em;" id="status_label" class="modal-title"></h4>
                <div id="part_warranty_option"></div>
                <h4 style="padding: 3px;font-size: 1em;" id="remarks_label" class="modal-title">Remarks</h4>
                <textarea rows="3" class="form-control" id="textarea" placeholder="Enter Remarks"></textarea>
                <input style="margin-top:20px; display: none" type="number" name="charge" class="form-control" id="charges" placeholder="Enter Courier Charge" />
            </div>
            <input type="hidden" id="url"></input>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="reject_parts()" id="reject_btn">Send</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="defective_part_shipped_by_SF_approved">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_shipped_by_SF_approved_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">Spare Pending On</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Parts Number</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Defective Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" data-orderable="false">Age</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="defective_part_shipped_by_SF">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_shipped_by_sf_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">Spare Pending On</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                         <th class="text-center" data-orderable="false">Parts Number</th>
                                        <th class="text-center" data-orderable="false">Part Shipped Date</th>
                                        <th class="text-center" data-orderable="false">Courier Name</th>
                                        <th class="text-center" data-orderable="false">AWB</th>
                                        <th class="text-center" data-orderable="false">Courier Charges</th>
                                        <th class="text-center" data-orderable="false">SF Remarks</th>
                                        <th class="text-center" data-orderable="false">Courier Invoice</th>
                                        <th class="text-center" data-orderable="false">Challan File</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                        <th class="text-center" data-orderable="false">Reject Courier</th>
                                        <th class="text-center" data-orderable="false">Approve Courier</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="defective_part_pending">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_pending_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">Spare Pending On</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false"> Parts Number</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Partner Shipped Date</th>
                                        <th class="text-center" data-orderable="false">SF Received Date</th>
                                        <th class="text-center" data-orderable="false">Price</th>
                                        <th class="text-center" data-orderable="true">Age</th>
                                        <th class="text-center" data-orderable="true">Pickup Request </th>
                                        <th class="text-center" data-orderable="true">Pickup Schedule</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="defective_part_rejected_by_partner">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_rejected_by_partner_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">Spare Pending On</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Parts Number</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="true">Age Of shipped</th>
                                        <th class="text-center" data-orderable="false">SF Remarks</th>
                                        <th class="text-center" data-orderable="false">Defective Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Courier Invoice</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">Parts Shipped</th>
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="partner_shipped_part">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="partner_shipped_part_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">Spare Pending On</th>
                                        <th class="text-center" data-orderable="false">User</th>                                        
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Parts Number</th>
                                        <th class="text-center" data-orderable="false">Part Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="true">Age Of Shipped</th>
                                        <th class="text-center" data-orderable="false">Challan File</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Update</th>-->
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                        <th class="text-center" data-orderable="false">Part Lost & Required</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="sf_received_part">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="sf_received_part_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">Spare Pending On</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Parts Number</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Partner Shipped Date</th>
                                        <th class="text-center" data-orderable="false">SF Received Date</th>
                                        <th class="text-center" data-orderable="false">Price</th>
                                        <th class="text-center" data-orderable="true">Age Of Delivered</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <button type="submit" class="btn btn-success" onclick="genaerate_purchase_invoice()">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="close_model()">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="courier_lost" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal-title">Courier Lost</h4> 
            </div>
            <div class="modal-body">
                <textarea rows="3" class="form-control" id="lost_courier_reason" placeholder="Enter Remarks"></textarea>                
                <p id="remarks_err"></p>
            </div>
            <input type="hidden" id="spare_id" value="">            
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="lost_courier">Request Part</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    var spare_parts_requested_table;
     var spare_parts_requested_table_approved;
    var partner_shipped_part_table;
    var sf_received_part_table;
    var defective_part_pending_table;
    var defective_part_rejected_by_partner_table;
    var estimate_cost_requested_table;
    var estimate_cost_given_table;
    var oow_part_shipped_table;
    var defective_part_shipped_by_SF_table;
    
    $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $(document).ready(function() {
        
        oow_part_shipped_table = $('#oow_part_shipped_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 13, "desc" ]],//Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,11,12,13 ]
                    },
                    title: 'partner_shipped_oow_part'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '8', status: '<?php echo SPARE_OOW_SHIPPED; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
          }
        });
        
    
        
     estimate_cost_given_table = $('#estimate_cost_given_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 13, "desc" ]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11 ]
                    },
                    title: 'spare_cost_given'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '0', status: '<?php echo SPARE_OOW_EST_GIVEN; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4,9,10,11], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
          }
        });
        
       
    estimate_cost_requested_table = $('#estimate_cost_requested_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 13, "desc" ]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11 ]
                    },
                    title: 'cost_requested'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '0', status: '<?php echo SPARE_OOW_EST_REQUESTED; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4,5], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
            }
        });
    
    
    //datatables    
    spare_parts_requested_table = $('#spare_parts_requested_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            "order": [[ 13, "asc" ]],
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50', '100', '500', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,12,13 ],
                         modifier : {
                            // DataTables core
                            page : 'All',      // 'all',     'current'
                        }
                    },
                    title: 'spare_parts_requested'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '10', status: '<?php echo SPARE_PART_ON_APPROVAL; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
          }
        });
    
    
    spare_parts_requested_table.draw(false);
         //datatables    
    spare_parts_requested_table_approved = $('#spare_parts_requested_table_approved').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            "order": [[ 13, "asc" ]],
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50', '100', '500', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,12 ],
                         modifier : {
                            // DataTables core
                            page : 'All',      // 'all',     'current'
                        }
                    },
                    title: 'spare_parts_requested_approved'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '0', status: '<?php echo SPARE_PARTS_REQUESTED; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4,11,12], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
          }
        });
    
  //spare_parts_requested_rejected  
        spare_parts_requested_table_reject = $('#spare_parts_requested_table_reject').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode
           "order": [[ 13, "asc" ]],
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50', '100', '500', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,12 ],
                         modifier : {
                            // DataTables core
                            page : 'All',      // 'all',     'current'
                        }
                    },
                    title: 'spare_parts_requested_rejected'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '11', status: '<?php echo _247AROUND_CANCELLED; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4,11,12], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
          }
        });
    
    
    partner_shipped_part_table = $('#partner_shipped_part_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 12, "desc" ]],//Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10 ]
                    },
                    title: 'partner_shipped_part'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '1', status: '<?php echo SPARE_SHIPPED_BY_PARTNER; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
            }
        });
        
        sf_received_part_table = $('#sf_received_part_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[15, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13 ]
                    },
                    title: 'sf_received_part'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '2', status: '<?php echo SPARE_DELIVERED_TO_SF; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [1,5,9], //first column / numbering column
                    "orderable": true //set not orderable
                },
                {
                    "targets": [0], //first column / numbering column
                    "orderable": false //set not orderable
                },
                
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
           }
        });
        
        defective_part_pending_table = $('#defective_part_pending_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[14, "desc"]], 
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13]
                    },
                    title: 'defective_part_pending'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '9', status: '<?php echo DEFECTIVE_PARTS_PENDING; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [1,5,8,14], //first column / numbering column
                    "orderable": true //set not orderable
                },
                 {
                    "targets": [0,12,15,16], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
            }
        });
        
        defective_part_rejected_by_partner_table = $('#defective_part_rejected_by_partner_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[11, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       columns: [ 1,2,3,4,5,6,7,8,9,10,11 ]
                    },
                    title: 'defective_part_rejected'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '3', status: '<?php echo DEFECTIVE_PARTS_REJECTED; ?>', partner_id: '<?php echo $partner_id; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,3,4,5], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
            }
        });
        
        defective_part_shipped_by_sf_table = $('#defective_part_shipped_by_sf_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       columns: [ 1,2,3,4,5,6,7,8,9,10,11 ]
                    },
                    title: 'defective_part_shipped_by_sf'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '4', status: '<?php echo DEFECTIVE_PARTS_SHIPPED; ?>', partner_id: '<?php echo $partner_id; ?>', approved_defective_parts_by_admin:0}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4,5,6], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
            }
        });
        
        
        defective_part_shipped_by_SF_approved_table = $('#defective_part_shipped_by_SF_approved_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[13, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50 rows', '100 rows', '500 rows', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       ccolumns: [ 1,2,3,4,5,6,7,8,9]
                    },
                    title: 'defective_part_shipped_by_SF_approved'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '5', status: '<?php echo DEFECTIVE_PARTS_SHIPPED; ?>', partner_id: '<?php echo $partner_id; ?>', approved_defective_parts_by_admin:1}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4,5], //first column / numbering column
                    "orderable": false //set not orderable
                },
                {
                    "targets": [13], //first column / numbering column
                    "orderable": true //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
               $(".dataTables_filter").addClass("pull-right");
            }
        });
       
    });
    
    function open_create_invoice_form(){
        var spare_id = [];
        var partner_id_array = [];
        var data = [];
        $('.spare_id:checked').each(function (i) {
            spare_id[i] = $(this).val();
            var partner_id  = $(this).attr('data-partner_id');
            var booking_id  = $(this).attr('data-booking_id');
            partner_id_array.push(partner_id);
            data[i] =[];
            data[i]['spare_id'] = spare_id[i];
            data[i]['partner_id'] = partner_id;
            data[i]['booking_id'] = booking_id;
         
        });
    
        if(spare_id.length > 0){
            var unique_partner = ArrayNoDuplicate(partner_id_array);
            if(unique_partner.length > 1){
                alert("You Can not select multiple partner booking");
            } else {
                var html  = '<input type="hidden" name="partner_id" value="'+unique_partner[0]+'" />';
                for(k =0; k < data.length; k++){
                    html +='<div class="col-md-12" >';
                    html += '<div class="col-md-4 "> <div class="form-group col-md-12  "><label for="remarks">Booking ID *</label>';
                    html += '<input required type="text" class="form-control" style="font-size: 13px;"  id="bookingid_'+k+'" placeholder="Enter Booking ID" name="part['+data[k]["spare_id"]+'][booking_id]" value = "'+data[k]['booking_id']+'" >';
                    html += '</div></div>';
                    
                    html += '<div class="col-md-3 " style="width: 18%"><div class="form-group col-md-12  ">';
                    html += ' <label for="remarks">HSN Code *</label>';
                    html += '<input required type="text" class="form-control" style="font-size: 13px;"  id="hsncode_'+k+'" placeholder="HSN Code" name="part['+data[k]["spare_id"]+'][hsn_code]" value = "" >';
                    html += '</div></div>';
                    
                    html += '<div class="col-md-3 " style="width: 17%"><div class="form-group col-md-12  ">';
                    html += ' <label for="remarks">GST Rate *</label>';
                    html += '<input required type="number" class="form-control" style="font-size: 13px;"  id="gstrate'+k+'" placeholder="GST Rate" name="part['+data[k]["spare_id"]+'][gst_rate]" value = "" >';
                    html += '</div></div>';
                    
                    html += '<div class="col-md-4 " style="width: 30%"><div class="form-group col-md-12  ">';
                    html += ' <label for="remarks">Basic Amount *</label>';
                    html += '<input required type="number" step=".01" class="form-control" style="font-size: 13px;"  id="basic_amount'+k+'" placeholder="Enter Amount" name="part['+data[k]["spare_id"]+'][basic_amount]" value = "" >';
                    html += '</div></div>';
                    
                    html += '</div>';
                }
                $("#spare_inner_html").html(html);
                $('#purchase_invoice').modal('toggle'); 
            }
        } else {
            alert("Please Select Atleast One Checkbox");
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
    
            swal({
                     title: "Do You Want To Continue?",
                     type: "warning",
                     showCancelButton: true,
                     confirmButtonColor: "#DD6B55",
                     closeOnConfirm: true
    
                 },
                 function(){
                    
    
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
                         oow_part_shipped_table.ajax.reload(null, false);
    
                     } else {
                         swal("Oops", data, "error");
                         alert(data);
    
                     }
                      $('body').loadingModal('destroy');
    
                 }
               });
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

    
    function courier_lost_required(spare_part_id, booking_id){
       $("#lost_part_reason").val("");
       $("#courier_lost").modal();
       $("#spare_id").val(spare_part_id);
       $("#lost_courier_reason").val('');
       $("#remarks_err").html('');
    }
    
    $("#lost_courier").on('click',function(){
        lost_courier_reason = $("#lost_courier_reason").val();
        spare_id = $("#spare_id").val();    
        if(lost_courier_reason !=''){
           $("#remarks_err").html('');
           if(confirm('Are you sure you want to Request New Spare Part')){
              $.ajax({
                method:'POST',
                url:'<?php echo base_url(); ?>employee/spare_parts/lost_courier_request_new_spare_part_from_partner',
                data:{spare_part_id:spare_id,reason:lost_courier_reason},
                success:function(res){                    
                    var obj = JSON.parse(res);
                    if(obj.status === true){
                        partner_shipped_part_table.ajax.reload(null, false);
                        $("#courier_lost").hide();
                    }
                }
            });
          } 
        }else{
          $("#remarks_err").html("Remarks should not be blank.").css({'color':'red'});
        }
        
    });
    
    
    $(document).ready(function(){         
     spare_parts_requested_table.ajax.reload( function ( json ) { 
            $("#total_unapprove").html('(<i>'+json.unapproved+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
        } );
    });
    
    function uncheckedPickupScheduleCheckbox(sequence_id){
    
         var len = $(".pickup_request:checked").length;
         
         if(len > 0){
            $("#request_pickup").attr('disabled',false);    
         }else{
             $("#request_pickup").attr('disabled',true);   
         }
         
         $("#schedule_pickup").attr('disabled',true);
          
         $('.pickup_schedule').prop('checked', false);
         
        var service_center_id_arr = [];         
        $(".pickup_request:checked").each(function(i){            
            service_center_id = $(this).data("sf_id");   
            
            if(i === 0){
                 service_center_id_arr.push(service_center_id);
            } else {
                if ($.inArray(service_center_id, service_center_id_arr) !== -1) {                
                  service_center_id_arr.push(service_center_id);
              } else {
                   
                  $("#"+sequence_id).prop('checked', false);
                  alert("Do not allow to tick different vendor booking");
                  
                  return false;
              }
            }
        });
      
    }
    
    function uncheckedPickupRequest(sequence_id){
        
        var len = $(".pickup_schedule:checked").length;

        if(len > 0){
          $("#schedule_pickup").attr('disabled',false);    
        }else{
           $("#schedule_pickup").attr('disabled',true);   
        }

        $("#request_pickup").attr('disabled',true);

        $('.pickup_request').prop('checked', false);
          
        var service_center_id_arr = [];         
        $(".pickup_schedule:checked").each(function(i){
            service_center_id = $(this).data("sf_id");
                      
            if(i === 0){
                 service_center_id_arr.push(service_center_id);
            } else {
                if ($.inArray(service_center_id, service_center_id_arr) !== -1) {                
                  service_center_id_arr.push(service_center_id);
              } else {                  
                  $("#"+sequence_id).prop('checked', false);
                  alert("Do not allow to tick different vendor booking");
                  return false;
              }
            }
        });         
        
    }
    
</script>
