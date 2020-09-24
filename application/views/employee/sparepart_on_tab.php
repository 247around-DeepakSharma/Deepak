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
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Partner</th>
                                    <th class="text-center" data-orderable="false">Requested Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>
                                    <th class="text-center" data-orderable="false">Part Type</th>
                                    <th class="text-center" data-orderable="false">Requested Quantity</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="true">Age Of Requested</th>
                                    <?php if($this->session->userdata('user_group') == 'admin'  || $this->session->userdata('user_group') == 'inventory_manager' || $this->session->userdata('user_group') == 'developer' || $this->session->userdata('user_group') == 'employee'){ ?>
                                    <th class="text-center" data-orderable="false">Edit Model No.</th>
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

<div role="tabpanel" class="tab-pane" id="oow_part_shipped">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <div class="col-md-12  ">
                            <button onclick="open_create_invoice_form()" class="btn btn-md btn-primary" style="float: right;" id="btn_create_invoice" name="btn_create_invoice">Create Purchase Invoice</button>
                        </div>
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
                                <th class="text-center" data-orderable="false">Quantity</th>
                                <th class="text-center" data-orderable="false">Shipped Part</th>
                                <th class="text-center" data-orderable="false">Booking Type</th>
                                <th class="text-center" data-orderable="false">Estimate Cost</th>
                                <th class="text-center" data-orderable="true">Age Of Shipped</th>
                                <th class="text-center" data-orderable="false">Courier Charges</th>
                                <th class="text-center" data-orderable="false">Challan File</th>
                                <th class="text-center" data-orderable="false">Sale Invoice ID</th>
                                <th class="text-center" data-orderable="false">Purchase Invoice PDF </th>
                                <th class="text-center" data-orderable="false">Defective Front Part Image</th>
                                <th class="text-center" data-orderable="false">Defective Back Part Image</th>
                                <th class="text-center" data-orderable="false">Create Purchase Invoice</th>
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
                                    <th class="text-center" data-orderable="false">Requested Quantity</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="true">Age Of Requested</th>
                                    <?php if($this->session->userdata('user_group') == 'admin'  || $this->session->userdata('user_group') == 'inventory_manager' || $this->session->userdata('user_group') == 'developer' || $this->session->userdata('user_group') == 'employee'){ ?>
                                    <th class="text-center" data-orderable="false">Edit Model No.</th>
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
<div role="tabpanel" class="tab-pane active" id="spare_parts_requested">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <div class="row">       
                            <div class="col-md-1 pull-right">       
                                <a class="btn btn-success" id="show_spare_list">Show</a><span class="badge" title="show spare data"></span>     
                            </div>      
                            <div class="col-md-5 pull-right">       
                                <select class="form-control" name="appliance_wise_parts_requested" id="appliance_wise_parts_requested">     
                                    <option value="" selected="selected" disabled="">Select Services</option>       
                                    <?php foreach($services as $val){ ?>        
                                    <option value="<?php echo $val->id?>"><?php echo $val->services?></option>      
                                    <?php } ?>      
                                </select>       
                            </div>      
                            <div class="col-md-5 pull-right">       
                                <select class="form-control" name="partner_wise_parts_requested"  id="partner_wise_parts_requested">        
                                    <option value="" selected="selected" disabled="">Select Partners</option>       
                                    <?php       
                                        foreach($partners as $val){ ?>      
                                            <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>       
                                    <?php } ?>      
                                </select>       
                            </div>      
                        </div>      
                        <hr/>
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
                                    <th class="text-center" data-orderable="false">Requested Quantity</th>
                                    <!-- Symptom -->
                                    <th class="text-center" data-orderable="false">Symptom</th>
                                    <th class="text-center" data-orderable="false">Defect Pic</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="false">Warranty Status</th>
                                    <th class="text-center" data-orderable="true">Age Of Requested</th>
                                    <!-- <th class="text-center" data-orderable="false">Update</th>
                                    <th class="text-center" data-orderable="false">Is Defective Parts Required</th>-->
                                    <?php if($this->session->userdata('user_group') == 'admin'  || $this->session->userdata('user_group') == 'inventory_manager' || $this->session->userdata('user_group') == 'developer'){ ?>
                                    <th class="text-center" data-orderable="false">Edit Booking</th>
                                    <th class="text-center" data-orderable="false">Approval</th>
                                    <th class="text-center" data-orderable="false">Edit Model No.</th>
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

                        <div class="row" style="margin-top: 10px !important;">       
                            <div class="col-md-1 pull-right">       
                                <a class="btn btn-success" id="show_spare_list2">Show</a><span class="badge" title="show spare data"></span>     
                            </div>      
                            <div class="col-md-5 pull-right">       
                                <select class="form-control" name="appliance_wise_parts_requested" id="appliance_wise_parts_requested2">     
                                    <option value="" selected="selected" disabled="">Select Services</option>       
                                    <?php foreach($services as $val){ ?>        
                                    <option value="<?php echo $val->id?>"><?php echo $val->services?></option>      
                                    <?php } ?>      
                                </select>       
                            </div>      
                            <div class="col-md-5 pull-right">       
                                <select class="form-control" name="partner_wise_parts_requested"  id="partner_wise_parts_requested2">        
                                    <option value="" selected="selected" disabled="">Select Partners</option>       
                                    <?php       
                                        foreach($partners as $val){ ?>      
                                            <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>       
                                    <?php } ?>      
                                </select>       
                            </div>      
                        </div> 

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
                                    <th class="text-center" data-orderable="false">Quantity</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                     <th class="text-center" data-orderable="false">Approval Date</th>
                                    <th class="text-center" data-orderable="false">Approval Agent</th>
                                    <th class="text-center" data-orderable="false">Age Of Requested</th>
                                    <!-- <th class="text-center" data-orderable="false">Update</th>-->
                                    <?php if($this->session->userdata('user_group') == 'admin'  || $this->session->userdata('user_group') == 'inventory_manager' || $this->session->userdata('user_group') == 'developer'){ ?>
                                    <th class="text-center" data-orderable="false">Edit Model No.</th>
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

<div role="tabpanel" class="tab-pane " id="spare_parts_requested_rejected">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <div class="row">
                            <div class="col-md-12">
                                <span class="text-info text-bold" style="font-size:15px;float:right;"><i class="fa fa-info-circle" aria-hidden="true"></i> Note : Only In-Warranty parts can be reopened.</span>
                            </div>
                        </div>
                        
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
                                    <th class="text-center" data-orderable="false">State</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Requested Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>
                                    <th class="text-center" data-orderable="false">Quantity</th>  
                                    <th class="text-center" data-orderable="false">Part Type</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <th class="text-center" data-orderable="false">Age Of Rejection</th>
                                    <th class="text-center" data-orderable="false">Spare Cancellation Reason</th>
                                    <th class="text-center" data-orderable="false">Open</th>
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
                <h4 style="padding: 3px;font-size: 1em;" id="remarks_label" class="modal-title">Remarks *</h4>
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
<div role="tabpanel" class="tab-pane" id="courier_approved_defective_parts">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="courier_approved_defective_parts_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
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
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Requested Parts Number</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Shipped Parts Number</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Defective/Ok Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" data-orderable="false">Age</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <!--<th class="text-center" data-orderable="false">IS Defective Parts Required</th>-->
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
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Requested Parts Number</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Shipped Parts Number</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Defective/Ok Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Status</th>
                                        <th class="text-center" data-orderable="false">Age</th>
                                        <!--    <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <!--  <th class="text-center" data-orderable="false">IS Defective Parts Required</th>-->
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
<div role="tabpanel" class="tab-pane" id="return_defective_parts_from_wh_to_partner">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="return_defective_parts_from_wh_to_partner_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">Spare Pending On</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Requested Parts Number</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Shipped Parts Number</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Age</th>
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
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Part Shipped Date</th>
                                        <th class="text-center" data-orderable="false">Consumption Reason</th>
                                        <th class="text-center" data-orderable="false">Courier Name</th>
                                        <th class="text-center" data-orderable="false">AWB</th>
                                        <th class="text-center" data-orderable="false">Courier Charges</th>
                                        <th class="text-center" data-orderable="false">SF Remarks</th>
                                        <th class="text-center" data-orderable="false">Courier Invoice</th>
                                        <th class="text-center" data-orderable="false">Challan File</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <!--<th class="text-center" data-orderable="false">Is Defective Parts Required</th>-->
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
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Requested Parts Number</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Parts Number</th>
                                        <th class="text-center" data-orderable="false">Consumption Reason</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Partner Shipped Date</th>
                                        <th class="text-center" data-orderable="false">SF Received Date</th>
                                        <th class="text-center" data-orderable="false">Price</th>
                                        <th class="text-center" data-orderable="true">Age</th>
                                        <th class="text-center" data-orderable="false">Pickup Request </th>
                                        <th class="text-center" data-orderable="false">Pickup Schedule</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                       <?php if ($this->session->userdata('user_group') == "inventory_manager" || $this->session->userdata('user_group') == "admin" || $this->session->userdata('user_group') == "developer" || $this->session->userdata('user_group') == "accountmanager") { ?>
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                        <th class="text-center" data-orderable="false">Mark Courier Lost</th>
                                        <th class="text-center" data-orderable="false">Generate Invoice</th>
                                       <?php } ?>
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

<div role="tabpanel" class="tab-pane" id="defective_part_pending_oot">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1 pull-right" style="padding: 10px;">       
                <a class="btn btn-success" id="download_spare_oot">Download</a><span class="badge" title="Download spare data"></span>     
            </div> 
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_oot_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" data-orderable="false">No.</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">SF Name</th>
                                        <th class="text-center" data-orderable="false">SF Status</th>
                                        <th class="text-center" data-orderable="false">Partner Name</th>
                                        <th class="text-center" data-orderable="false">Spare Status</th>
                                        <th class="text-center" data-orderable="false">Spare Warranty Status</th>
                                        <th class="text-center" data-orderable="false">Service Center Closed Date</th>
                                        <th class="text-center" data-orderable="false">Booking Request Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Model Number</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Number</th>
                                        <th class="text-center" data-orderable="false">Spare Part Shipped Date</th>
                                        <th class="text-center" data-orderable="true">Spare Shipped Age</th>
                                        <th class="text-center" data-orderable="false">NRN Status</th>
                                        <th class="text-center" data-orderable="false">TAT</th>
                                        <th class="text-center" data-orderable="false">Partner AWB Number</th>
                                        <th class="text-center" data-orderable="false">SF AWB Number</th>                                 
                                        <th class="text-center" data-orderable="false">Parts Charge</th>
                                        <th class="text-center" data-orderable="false">AWB Number Warehouse Dispatch Defective To Partner</th>
                                        <th class="text-center" data-orderable="false">Spare Lost</th>
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

<div role="tabpanel" class="tab-pane" id="defective_part_rejected_by_wh">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="defective_part_rejected_by_wh_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
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
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Part Charges </th>
                                        <th class="text-center" data-orderable="false">Requested Parts Number</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Shipped Parts Number</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="true">Age Of shipped</th>
                                        <th class="text-center" data-orderable="false">SF Remarks</th>
                                        <th class="text-center" data-orderable="false">Defective/Ok Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Rejected By</th>
                                        <th class="text-center" data-orderable="false">Courier Invoice</th>
                                        <th class="text-center" data-orderable="false">Rejected Image</th>
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
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Part Charges </th>
                                        <th class="text-center" data-orderable="false">Requested Parts Number</th>
                                        <th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Shipped Parts Number</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="true">Age Of shipped</th>
                                        <th class="text-center" data-orderable="false">SF Remarks</th>
                                        <th class="text-center" data-orderable="false">Defective/Ok Parts Rejection Reason</th>
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
                                        <th class="text-center" data-orderable="false">Requested Parts Number</th>
                                        <th class="text-center" data-orderable="false">Part Type</th>
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Number </th>
                                        <th class="text-center" data-orderable="false">AWB Number </th>
                                        <th class="text-center" data-orderable="false">Courier Company </th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="true">Age Of Shipped</th>
                                        <th class="text-center" data-orderable="false">Challan File</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Update</th>-->
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">Is Defective Parts Required</th>
                                        <th class="text-center" data-orderable="false">Part Lost & Required</th>
                                        <th class="text-center" data-orderable="false">Mark RTO Case</th>
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
                                        <th class="text-center" data-orderable="false">Requested Quantity</th>
                                        <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                        <th class="text-center" data-orderable="false">Requested Parts Number</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Parts Number</th>
                                        <th class="text-center" data-orderable="false">AWB Number</th>
                                        <th class="text-center" data-orderable="false">Booking Type</th>
                                        <th class="text-center" data-orderable="false">Partner Shipped Date</th>
                                        <th class="text-center" data-orderable="false">SF Received Date</th>
                                        <th class="text-center" data-orderable="false">Price</th>
                                        <th class="text-center" data-orderable="true">Age Of Delivered</th>
                                        <!--                                        <th class="text-center" data-orderable="false">Cancel Part</th>-->
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                        <th class="text-center" data-orderable="false">Mark RTO Case</th>
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
<div role="tabpanel" class="tab-pane" id="total_parts_shipped_to_sf">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1 pull-right" style="padding: 10px;">       
                <a class="btn btn-success" id="download_total_shipped_part">Download</a><span class="badge" title="Download spare data"></span>     
            </div> 
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="total_part_shipped_to_sf_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" data-orderable="false">No.</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">SF Name</th>
                                        <th class="text-center" data-orderable="false">SF Status</th>
                                        <th class="text-center" data-orderable="false">Partner Name</th>
                                        <th class="text-center" data-orderable="false">Spare Status</th>
                                        <th class="text-center" data-orderable="false">Spare Warranty Status</th>
                                        <th class="text-center" data-orderable="false">NRN Status</th>
                                        <th class="text-center" data-orderable="false">Service Center Closed Date</th>
                                        <th class="text-center" data-orderable="false">Booking Request Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Model Number</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Number</th>
                                        <th class="text-center" data-orderable="false">Spare Part Shipped Date</th>
                                        <th class="text-center" data-orderable="true">Spare Shipped Age</th>
                                        <th class="text-center" data-orderable="false">Partner AWB Number</th>
                                        <th class="text-center" data-orderable="false">SF AWB Number</th>                                 
                                        <th class="text-center" data-orderable="false">Parts Charge</th>
                                        <th class="text-center" data-orderable="false">AWB Number Warehouse Dispatch Defective To Partner</th>
                                        <th class="text-center" data-orderable="false">Spare Lost</th>
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
<div role="tabpanel" class="tab-pane" id="courier_lost_spare_parts">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <hr/>
                        <table id="courier_lost_spare_parts_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th class="text-center" data-orderable="false">No</th>
                                    <th class="text-center" data-orderable="false">Booking Id</th>
                                    <th class="text-center" data-orderable="false">Spare Pending On</th>
                                    <th class="text-center" data-orderable="false">User</th>
                                    <th class="text-center" data-orderable="false">Mobile</th>
                                    <th class="text-center" data-orderable="false">Service Center</th>
                                    <th class="text-center" data-orderable="false">Partner</th>
                                    <th class="text-center" data-orderable="false">State</th>
                                    <th class="text-center" data-orderable="false">Model Number</th>
                                    <th class="text-center" data-orderable="false">Requested Part</th>
                                    <th class="text-center" data-orderable="false">Parts Number</th>   
                                    <th class="text-center" data-orderable="false">Part Type</th>
                                    <th class="text-center" data-orderable="false">Requested Quantity</th>
                                    <th class="text-center" data-orderable="false">Shipped Part</th>
                                    <th class="text-center" data-orderable="false">Shipped Quantity</th>
                                    <th class="text-center" data-orderable="false">AWB Number</th>
                                    <th class="text-center" data-orderable="false">Booking Type</th>
                                    <th class="text-center" data-orderable="false">Part Status</th>
                                    <!--<th class="text-center" data-orderable="false">Warranty Status</th>-->
                                    <th class="text-center" data-orderable="true">Age Of Requested</th>
                                    <th class="text-center" data-orderable="false">Approve</th>
                                    <th class="text-center" data-orderable="false">Reject</th>
                                    <th class="text-center" data-orderable="false">Mark RTO Case</th>
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

<div role="tabpanel" class="tab-pane" id="auto_acknowledged_spare">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="auto_acknowledged_spare_table" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                <thead >
                                    <tr>
                                        <th class="text-center" data-orderable="false">No.</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">SF Name</th>
                                        <th class="text-center" data-orderable="false">SF Status</th>
                                        <th class="text-center" data-orderable="false">Partner Name</th>
                                        <th class="text-center" data-orderable="false">Spare Status</th>
                                        <th class="text-center" data-orderable="false">Spare Warranty Status</th>
                                        <th class="text-center" data-orderable="false">NRN Status</th>
                                        <th class="text-center" data-orderable="false">Service Center Closed Date</th>
                                        <th class="text-center" data-orderable="false">Booking Request Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Model Number</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Type</th>
                                        <th class="text-center" data-orderable="false">Shipped Part Number</th>
                                        <th class="text-center" data-orderable="false">Spare Part Shipped Date</th>
                                        <th class="text-center" data-orderable="true">Spare Shipped Age</th>
                                        <th class="text-center" data-orderable="false">Partner AWB Number</th>
                                        <th class="text-center" data-orderable="false">SF AWB Number</th>                                 
                                        <th class="text-center" data-orderable="false">Parts Charge</th>
                                        <th class="text-center" data-orderable="false">Acknowledged</th>
                                        <th class="text-center" data-orderable="false">Acknowledged Date</th>
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
                <button type="submit" class="btn btn-success" id="btn_purchase_invoice" name="btn_purchase_invoice" onclick="genaerate_purchase_invoice()">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="btn_create_invoice.disabled=false;close_model()">Close</button>
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
<div id="ApproveCourierLostSparePartModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="approve_courier_lost_spare_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Approve Courier Spare Lost </h4>
            </div>
            <div class="modal-body" >
                <div class="row">
                    <div class="col-md-12">
                        <textarea name="remarks" class="form-control" id="approve_courier_spare_part_remarks" rows="4" placeholder="Enter Remarks"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <input type="submit" id="approve_courier_spare_part_btn" name="approve-part" value="Approve" class="btn btn-primary form-control" style="margin-top:2px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="RejectCourierLostSparePartModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="reject_courier_lost_spare_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Reject Courier Spare Lost </h4>
            </div>
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>
<div id="RtoCaseSparePartModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" id="rto_case_spare_model">
        <!-- Modal content-->
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">RTO Case </h4>
            </div>
            <div class="modal-body" >
            </div>
        </div>
    </div>
</div>
<div class="loader hide"></div>
<style>
    .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 99999999;
    background: url('<?php echo base_url();  ?>images/loading_new.gif') 50% 50% no-repeat rgba(249,249,249,0.62);
  }
</style>
<script>
    var spare_parts_requested_table;
     var spare_parts_requested_table_approved;
    var partner_shipped_part_table;
    var sf_received_part_table;
    var defective_part_pending_table;
    var defective_part_oot_table;
    var defective_part_rejected_by_partner_table;
    var estimate_cost_requested_table;
    var estimate_cost_given_table;
    var oow_part_shipped_table;
    var defective_part_shipped_by_sf_table;
    var courier_lost_spare_parts_table;
    
    var defective_part_rejected_by_wh_table;
    var return_defective_parts_from_wh_to_partner_table;
    
    $("#invoice_date").datepicker({dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true});
    $(document).ready(function() {
        
        oow_part_shipped_table = $('#oow_part_shipped_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 14, "desc" ]],//Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,11,12,13,14,15,16,17 ]
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
                    "targets": [0,1,2,3,4,13], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
            $("#total_oow_shipped_part_pending").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
          }
        });
        
    
        
     estimate_cost_given_table = $('#estimate_cost_given_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 14, "desc" ]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14 ]
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
                    "targets": [14], //first column / numbering column
                    "orderable": true //set not orderable
                },
                {
                    "targets": [0,1,2,3,4,9,10,11,13], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
            $("#total_quote_given").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
          }
        });
        
       
    estimate_cost_requested_table = $('#estimate_cost_requested_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 14, "desc" ]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14 ]
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
                $("#total_req_quote").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
    
    
    //datatables    
    spare_parts_requested_table = $('#spare_parts_requested_table').on('xhr.dt', function (e, settings, json, xhr) {
            if (typeof json !== 'undefined' && typeof json["bookings_data"] !== 'undefined' && json["bookings_data"].length > 0) {
                var arr_bookings_data = json["bookings_data"];
                for (var rec_bookings_data in arr_bookings_data) {
                    $.ajax({
                        method:'POST',
                        url:"<?php echo base_url(); ?>employee/booking/get_warranty_data",
                        data:{'bookings_data': arr_bookings_data[rec_bookings_data]},
                        success:function(response){
                            $(".warranty-loader").hide();
                            var warrantyData = JSON.parse(response);                        
                            $.each(warrantyData, function(index, value) {
                                $(".warranty-"+index).html(value);
                            });
                        }                            
                    }); 
                } 
            }            
        }).DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order:[[ 17, "desc" ]],
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,12,13,15,16,17],
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
                data: function(d){
                    d.type =  '10';     
                    d.status =  '<?php echo SPARE_PART_ON_APPROVAL; ?>';        
                    d.partner_id =  '<?php echo $partner_id; ?>';       
                    d.partner_wise_parts_requested =  $('#partner_wise_parts_requested').val();     
                    d.appliance_wise_parts_requested =  $('#appliance_wise_parts_requested').val();     
                },  
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [17], //first column / numbering column
                    "orderable": true //set not orderable
                },
                {
                    "targets": [0,1,2,3,4,11,12,13,14,15], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {                
                $(".dataTables_filter").addClass("pull-right");
                $("#total_unapprove").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            },      
        });
    
    
    spare_parts_requested_table.draw(false);
    
    
    courier_lost_spare_parts_table = $('#courier_lost_spare_parts_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order:[[ 18, "desc" ]],
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,12,13,14,15,16,17,18,19,20,],
                         modifier : {
                            // DataTables core
                            page : 'All',      // 'all',     'current'
                        }
                    },
                    title: 'courier_lost_spare_parts'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: function(d){
                    d.type =  '12';     
                    d.status =  '<?php echo InProcess_Courier_Lost; ?>';        
                    d.partner_id =  '<?php echo $partner_id; ?>';       
                },  
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [18], //first column / numbering column
                    "orderable": true //set not orderable
                }                
            ],
            "fnInitComplete": function (oSettings, response) {                
                $(".dataTables_filter").addClass("pull-right");  
                $("#total_courier_lost").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});              
            },      
        });
    
    
    courier_lost_spare_parts_table.draw(false);
    
         //datatables    
    spare_parts_requested_table_approved = $('#spare_parts_requested_table_approved').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            "order": [],
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,12,13,14,15,16 ],
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
                data:function(d){
                    d.type =  '0';     
                    d.status =  '<?php echo SPARE_PARTS_REQUESTED; ?>';        
                    d.partner_id =  '<?php echo $partner_id; ?>'; 
                    d.approved =  '1';       
                    d.partner_wise_parts_requested =  $('#partner_wise_parts_requested2').val();     
                    d.appliance_wise_parts_requested =  $('#appliance_wise_parts_requested2').val(); 
                },
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [16], //first column / numbering column
                    "orderable": true //set not orderable
                },
                {
                    "targets": [0,1,2,3,4,11,12,13,14], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
            $("#total_approved_spare").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
          }
        });
    
          
        spare_parts_requested_table_reject = $('#spare_parts_requested_table_reject').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            "order": [], 
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, -1 ],[ '50', '100', '500', 'All' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,13,14,15 ],
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
                    "targets": [15], //first column / numbering column
                    "orderable": true //set not orderable
                },
                {
                    "targets": [0,1,2,3,4,12,13,14], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
            $(".dataTables_filter").addClass("pull-right");
            $("#total_rejected_spare").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
          }
        });
    
    
    partner_shipped_part_table = $('#partner_shipped_part_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[ 17, "desc" ]],//Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17 ]
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
                $("#total_partner_shipped_part").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        sf_received_part_table = $('#sf_received_part_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[19, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19 ]
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
                    "targets": [1,5,19], //first column / numbering column
                    "orderable": true //set not orderable
                },
                {
                    "targets": [0], //first column / numbering column
                    "orderable": false //set not orderable
                },
                
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
                $("#total_sf_received_part").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
           }
        });
        
        defective_part_pending_table = $('#defective_part_pending_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[17, "desc"]], 
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17]
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
                    "targets": [1,3,17], //first column / numbering column
                    "orderable": true //set not orderable
                }
                
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
                $("#total_all_defective").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        defective_part_rejected_by_partner_table = $('#defective_part_rejected_by_partner_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[15, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17 ]
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
                $("#total_defective_rejected_partner").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        
        
        defective_part_rejected_by_wh_table = $('#defective_part_rejected_by_wh_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[15, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18 ]
                    },
                    title: 'defective_part_rejected_wh'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '13', status: '<?php echo DEFECTIVE_PARTS_REJECTED_BY_WAREHOUSE; ?>', partner_id: '<?php echo $partner_id; ?>'}
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
                $("#total_defective_rejected_by_wh").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        
        defective_part_shipped_by_sf_table = $('#defective_part_shipped_by_sf_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14 ]
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
                $("#total_courier_audit").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        
        defective_part_shipped_by_SF_approved_table = $('#defective_part_shipped_by_SF_approved_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[16, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       ccolumns: [ 1,2,3,4,5,6,7,8,9,10]
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
                    "targets": [16], //first column / numbering column
                    "orderable": true //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
               $(".dataTables_filter").addClass("pull-right");
               $("#total_defective_received_by_wh").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        courier_approved_defective_parts_table = $('#courier_approved_defective_parts_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[16, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       ccolumns: [ 1,2,3,4,5,6,7,8,9,10]
                    },
                    title: 'defective_part_shipped_by_SF_approved'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '17', status: '<?php echo DEFECTIVE_PARTS_SHIPPED; ?>', partner_id: '<?php echo $partner_id; ?>', approved_defective_parts_by_admin:1}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4,5], //first column / numbering column
                    "orderable": false //set not orderable
                },
                {
                    "targets": [16], //first column / numbering column
                    "orderable": true //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
               $(".dataTables_filter").addClass("pull-right");
               $("#total_in_transit").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        /*
        * @uses: Return Defective From Warehouse To Partner
        */
        return_defective_parts_from_wh_to_partner_table = $('#return_defective_parts_from_wh_to_partner_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[14, "desc"]], //Initial no order.
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                       ccolumns: [ 1,2,3,4,5,6,7,8,9,10]
                    },
                    title: 'return_defective_parts_from_wh_to_partner'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '14', awb_by_wh: ' IS NOT NULL', defective_parts_shippped_date_by_wh:'IS NOT NULL'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [0,1,2,3,4,5], //first column / numbering column
                    "orderable": false //set not orderable
                },
                {
                    "targets": [14], //first column / numbering column
                    "orderable": true //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
               $(".dataTables_filter").addClass("pull-right");
               $("#total_defective_return_to_partner").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        
        /*
        * @desc: Used to load the Defective/Ok Part (OOT) tab data
        */
          defective_part_oot_table = $('#defective_part_oot_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[14, "desc"]], 
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,20,21]
                    },
                    title: 'out_of_tat_defective_part_pending'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '15', status: '<?php echo DEFECTIVE_PARTS_PENDING_OOT; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [], //first column / numbering column
                    "orderable": true //set not orderable
                },
                 {
                  "targets": [14], //first column / numbering column
                    "orderable": true //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
                $("#total_defective_oot").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        
        /*
        * @desc: Used to load the Defective/Ok Part (OOT) tab data
        */
          total_part_shipped_to_sf_table = $('#total_part_shipped_to_sf_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[15, "desc"]], 
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,20]
                    },
                    title: 'total_part_shipped_to_sf'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '16', status: '<?php echo TOTAL_PARTS_SHIPPED_TO_SF; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [], //first column / numbering column
                    "orderable": true //set not orderable
                },
                 {
                  "targets": [15], //first column / numbering column
                    "orderable": true //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
                $("#total_part_shipped_to_sf").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
        
       /*
        * @desc: It's used list the auto acknowledge spare parts to SF.
        * @response: json 
        */
        
          auto_acknowledged_spare_table = $('#auto_acknowledged_spare_table').DataTable({
            processing: true, //Feature control the processing indicator.
            serverSide: true, //Feature control DataTables' server-side processing mode.
            order: [[15, "desc"]], 
            pageLength: 50,
            dom: 'Blfrtip',
            lengthMenu: [[ 50, 100, 500, 1000, -1 ],[ '50', '100', '500', '1000' ]],
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]
                    },
                    title: 'auto_acknowledged_spare_parts'
                }
            ],
            // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url(); ?>employee/spare_parts/get_spare_parts_tab_details",
                type: "POST",
                data: {type: '18', status: '<?php echo SPARE_DELIVERED_TO_SF; ?>'}
            },
            //Set column definition initialisation properties.
            columnDefs: [
                {
                    "targets": [], //first column / numbering column
                    "orderable": true //set not orderable
                },
                 {
                  "targets": [15], //first column / numbering column
                    "orderable": true //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
                $("#total_auto_acknowledge_spare").html('(<i>'+response.recordsFiltered+'</i>)').css({"font-size": "14px;", "color": "red","background-color":"#fff"});
            }
        });
        
       
    });
     
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
    
    /*
     * @desc: Download the OOT spare data. 
     */
     $("#download_spare_oot").click(function (){
        $('#download_spare_oot').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
        
        $.ajax({
                type: 'POST',
				dataType: 'json',
                url: '<?php echo base_url(); ?>employee/spare_parts/download_spare_oot_data',
                data: { download_flag :true},
                success: function (data) {
                     $('#download_spare_oot').html("Download").attr('disabled',false);
                    var obj = JSON.parse(JSON.stringify(data));
                    if(obj['status']){
                        window.location.href = obj['msg'];
                    }else{
                        alert('File Download Failed. Please Refresh Page And Try Again...')
                    }
                }
            });
     });
     
     
    /*
     * @desc: Download the Total shipped part to SF data. 
     */
     $("#download_total_shipped_part").click(function (){
        $('#download_total_shipped_part').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/spare_parts/download_total_spare_shipped_part_sf_data',
                data: { download_flag :true},
                success: function (data) {
                    $('#download_total_shipped_part').html("Download").attr('disabled',false);
                    var obj = JSON.parse(data); 
                    if(obj['status']){
                        window.location.href = obj['msg'];
                    }else{
                        alert('File Download Failed. Please Refresh Page And Try Again...')
                    }
                }
            });
     });
    
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
                         oow_part_shipped_table.ajax.reload(null, false);
    
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
    $(document).on('click', '.open_spare_part', function(){

        var spare_id = $(this).data('spareid');
        var status = '<?php echo SPARE_PARTS_REQUESTED;  ?>';
        var new_booking_id = $(this).data('bookingid');
        swal({
        title: "Are you sure?",
        text: "Your rejected spare will be opened again!",
        inputPlaceholder: "Enter remarks",
        type: "input",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, open it!",
        closeOnConfirm: false
    }, function (inputValue) {
        
        
        if (inputValue === false) return false;
         if (inputValue === "") {
         swal.showInputError("You need to write something!");
         return false
         }
        $(".loader").removeClass('hide');
        //if (!inputValue) return;
        $.ajax({
            url: "<?php  echo base_url(); ?>employee/spare_parts/copy_booking_details_by_spare_parts_id",
            type: "POST",
            data: {
                spare_parts_id: spare_id,
                status:status,
                new_booking_id:new_booking_id,
                spare_update:true,
                open_remark:inputValue
            },
            success: function (response) {
                console.log(response);
                response=$.trim(response);
                response.trim();
                if (response=='success') {
                   $(".loader").addClass('hide');
                  swal("Done!", "It was succesfully opened!", "success");  
                }else{
                  $(".loader").fadeOut("slow");
                  $(".loader").addClass('hide');
                  swal("Error Occured!", "Error in opening the spare request. Either booking is already closed by service center or some network issue.", "error");
                }
                
                spare_parts_requested_table_reject.ajax.reload(null, false);  
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $(".loader").addClass('hide');
                swal("Network Error!", "This is caused due to network problem . Please try again !", "error");
            }
        });
    });

 
});   

   
// For select2 in approved tab //
    $('#partner_wise_parts_requested,#partner_wise_parts_requested2').select2({        
       placeholder:'Select Partner',        
       allowClear: true,
       width: '100%'
    });     
// For select2 in approved tab //
    $('#appliance_wise_parts_requested,#appliance_wise_parts_requested2').select2({      
           placeholder:'Select Appliance',      
           allowClear: true ,
           width: '100%'
    });    

    $('#show_spare_list').click(function(){     
        spare_parts_requested_table.ajax.reload(null, false);       
    }); 
// For show filtered data in ajax reload  //
    $('#show_spare_list2').click(function(){     
        spare_parts_requested_table_approved.ajax.reload(null, false);       
    }); 
    
    function disable_btn(id){
        $("#"+id).attr('disabled',true);
    }
    var courier_lost_spare_id;
    function approve_courier_lost_spare(spare_id) {
        courier_lost_spare_id = spare_id;
        $('#ApproveCourierLostSparePartModal').modal({backdrop: 'static', keyboard: false});
    }
    
    function reject_courier_lost_spare(spare_id) {
        
        $.ajax({
            method:'POST',
            url: '<?php echo base_url(); ?>employee/spare_parts/reject_courier_lost_spare',
            data: {spare_id}
        }).done(function (data){
            $("#reject_courier_lost_spare_model").children('.modal-content').children('.modal-body').html(data);   
            $('#RejectCourierLostSparePartModal').modal({backdrop: 'static', keyboard: false});
        });
        
        
    }
    
    $('#approve_courier_spare_part_btn').on('click', function(data) {
        var remarks = $('#approve_courier_spare_part_remarks').val();
        if(remarks == '' || remarks == null) {
            alert("Please enter remarks.");
            return false;
        }        
       
        $('#approve_courier_spare_part_btn').attr("disabled", true);
        $('#approve_courier_spare_part_btn').val("Please wait...");
        
        $.ajax({
            method:'POST',
            url:'<?php echo base_url(); ?>employee/spare_parts/approve_courier_lost_spare',
            data:{remarks:remarks, courier_lost_spare_id:courier_lost_spare_id}
        }).done(function(data){
            courier_lost_spare_parts_table.ajax.reload(null, false);  
            $('#ApproveCourierLostSparePartModal').modal('hide');
            $('#approve_courier_spare_part_remarks').val('');
            alert('Spare part has been approved successfully.');
        });
    });
    
    var rto_case_spare_part_id;
    var tab_type;
    function handle_rto_case(spare_id, type) {
        rto_case_spare_part_id = spare_id;
        tab_type = type;
        
        $.ajax({
            method:'POST',
            url: '<?php echo base_url(); ?>employee/spare_parts/rto_case_spare',
            data: {spare_id}
        }).done(function (data){
            $("#rto_case_spare_model").children('.modal-content').children('.modal-body').html(data);   
            $('#RtoCaseSparePartModal').modal({backdrop: 'static', keyboard: false});
        });
    }
    
        $(document).ready(function(){
        $('.panel .form-control').on('keypress keyup', function (event) {
            var regex = new RegExp("^[a-zA-Z0-9 ,-]+$");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            $(this).val($(this).val().replace(/[^a-z0-9 ,-\s]/gi, ''));
            if (!regex.test(key)) {
               event.preventDefault();
               return false;
            }
        });
    });
</script>
<style>
    #partner_wise_parts_requested2 .select2-container{
        width: 572px !important;
    }
    .select2 select2-container select2-container--default select2-container--above{
        width: 572px !important;
    }
</style>