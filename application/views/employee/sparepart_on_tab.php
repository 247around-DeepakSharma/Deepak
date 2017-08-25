<div role="tabpanel" class="tab-pane active" id="spare_parts_requested">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="today_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Booking Status</th>
                                        <th class="text-center" data-orderable="false">Update</th>
                                        <th class="text-center" data-orderable="false">Reject</th>
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($spare_parts as $key => $value) {  
                                        if($value['status'] == 'Spare Parts Requested'){
                                    ?>
                                    <tr id="<?php echo $value['booking_id']."_1";?>">
                                        <td class="text-center"><?php echo ($key +1); ?></td>
                                        <td class="text-center"><a 
                                          href="<?php echo base_url(); ?>employee/booking/viewdetails/<?php echo $value['booking_id']; ?>" target='_blank' title='view'><?php echo $value['booking_id'];?></a>
                                        </td>
                                        <td class="text-center"><?php echo $value['name'];?></td>
                                        <td class="text-center"><?php echo $value['booking_primary_contact_no'];?></td>
                                        <td class="text-center"><?php echo $value['sc_name'];?></td>
                                        <td class="text-center"><?php echo $value['source'];?></td>
                                        <td class="text-center"><?php echo $value['parts_requested'];?></td>
                                        <td class="text-center"><?php echo $value['parts_shipped'];?></td>
                                        <td class="text-center"><?php echo $value['current_status'];?></td> 

                                        <td class="text-center"><a href="<?php echo base_url(); ?>employee/inventory/update_spare_parts/<?php echo $value['id'];?>" class="btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a></td>
                                        
                                        <td class="text-center"><button type="button" id="<?php echo $value['booking_id'];?>" data-id="<?php echo base_url(); ?>employee/inventory/cancel_spare_parts/<?php echo $value['id']."/".$value['booking_id'];?>" class="btn btn-primary btn-sm open-adminremarks" data-toggle="modal" data-target="#myModal2">Reject</button></td>
                                        <td>
                                          
                                            <a class="btn btn-md <?php if($value['defective_part_required'] == '0'){ echo 'btn-primary';} else{ echo 'btn-danger'; }?>" href="<?php echo base_url();?>employee/inventory/update_is_defective_parts_rquired/<?php if($value['defective_part_required'] == '0'){ echo '1';} else{ echo '0'; }?>/<?php echo $value['id'];?>/<?php echo $value['booking_id'];?>"><?php if($value['defective_part_required'] == '0'){ echo "Def. Part Required";} else{ echo "Def. Part Not Required"; }?></a>
                                        </td>
                                   </tr>
                             
                                    <?php }}?>
                                </tbody>
                                
                            </table>
                        </form>
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
               <textarea rows="8" class="form-control" id="textarea"></textarea>
            </div>
            <input type="hidden" id="url"></input>
            <div class="modal-footer">
               <button type="button" class="btn btn-success" onclick="reject_parts()">Send</button>
               <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
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
                            <table id="today_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
										<th class="text-center" data-orderable="false">Defective Parts</th>
                                        <th class="text-center" data-orderable="false">Defective Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Booking Status</th>
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no='1'; foreach ($spare_parts as $value) { 
                                        if($value['status'] == 'Defective Part Shipped By SF'){
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $sn_no; ?></td>
                                        <td class="text-center"><a 
                                          href="<?php echo base_url(); ?>employee/booking/viewdetails/<?php echo $value['booking_id']; ?>" target='_blank' title='view'><?php echo $value['booking_id'];?></a>
                                        </td>
                                        <td class="text-center"><?php echo $value['name'];?></td>
                                        <td class="text-center"><?php echo $value['booking_primary_contact_no'];?></td>
                                        <td class="text-center"><?php echo $value['sc_name'];?></td>
                                        <td class="text-center"><?php echo $value['source'];?></td>
                                        <td class="text-center"><?php echo $value['parts_requested'];?></td>
                                        <td class="text-center"><?php echo $value['parts_shipped'];?></td>
										<td class="text-center"><?php echo $value['defective_part_shipped'];?></td>
                                        <td class="text-center"><?php echo $value['remarks_defective_part_by_partner'];?></td> 
                                        <td class="text-center"><?php echo $value['current_status'];?></td> 
                                        <td>
                                          
                                            <a class="btn btn-md <?php if($value['defective_part_required'] == '0'){ echo 'btn-primary';} else{ echo 'btn-danger'; }?>" href="<?php echo base_url();?>employee/inventory/update_is_defective_parts_rquired/<?php if($value['defective_part_required'] == '0'){ echo '1';} else{ echo '0'; }?>/<?php echo $value['id'];?>/<?php echo $value['booking_id'];?>"><?php if($value['defective_part_required'] == '0'){ echo "Def. Part Required";} else{ echo "Def. Part Not Required"; }?></a>
                                        </td>
                                   </tr>
                             
                                    <?php $sn_no++; }}?>
                                </tbody>
                                
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
                            <table id="today_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Defective Parts Rejection Reason</th>
                                        <th class="text-center" data-orderable="false">Booking Status</th>
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php $sn_no='1';  foreach ($spare_parts as $value) { 
                                        if($value['status'] == 'Defective Part Pending'){
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $sn_no; ?></td>
                                        <td class="text-center"><a 
                                          href="<?php echo base_url(); ?>employee/booking/viewdetails/<?php echo $value['booking_id']; ?>" target='_blank' title='view'><?php echo $value['booking_id'];?></a>
                                        </td>
                                        <td class="text-center"><?php echo $value['name'];?></td>
                                        <td class="text-center"><?php echo $value['booking_primary_contact_no'];?></td>
                                        <td class="text-center"><?php echo $value['sc_name'];?></td>
                                        <td class="text-center"><?php echo $value['source'];?></td>
                                        <td class="text-center"><?php echo $value['parts_requested'];?></td>
                                        <td class="text-center"><?php echo $value['parts_shipped'];?></td>
                                        <td class="text-center"><?php echo $value['remarks_defective_part_by_partner'];?></td> 
                                        <td class="text-center"><?php echo $value['current_status'];?></td> 
                                        <td>
                                          
                                            <a class="btn btn-md <?php if($value['defective_part_required'] == '0'){ echo 'btn-primary';} else{ echo 'btn-danger'; }?>" href="<?php echo base_url();?>employee/inventory/update_is_defective_parts_rquired/<?php if($value['defective_part_required'] == '0'){ echo '1';} else{ echo '0'; }?>/<?php echo $value['id'];?>/<?php echo $value['booking_id'];?>"><?php if($value['defective_part_required'] == '0'){ echo "Spare Def Required";} else{ echo "Def. Part Not Required"; }?></a>
                                        </td>
                                   </tr>
                             
                                    <?php $sn_no++; }}?>
                                </tbody>
                               
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="shipped">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="today_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Booking Status</th>
                                        <th class="text-center" data-orderable="false">Update</th>
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no='1'; foreach ($spare_parts as $value) {  
                                        if($value['status'] == 'Shipped'){
                                    ?>
                                    
                                    <tr>
                                        <td class="text-center"><?php echo $sn_no; ?></td>
                                        <td class="text-center"><a 
                                          href="<?php echo base_url(); ?>employee/booking/viewdetails/<?php echo $value['booking_id']; ?>" target='_blank' title='view'><?php echo $value['booking_id'];?></a>
                                        </td>
                                        <td class="text-center"><?php echo $value['name'];?></td>
                                        <td class="text-center"><?php echo $value['booking_primary_contact_no'];?></td>
                                        <td class="text-center"><?php echo $value['sc_name'];?></td>
                                        <td class="text-center"><?php echo $value['source'];?></td>
                                        <td class="text-center"><?php echo $value['parts_requested'];?></td>
                                        <td class="text-center"><?php echo $value['parts_shipped'];?></td>  
                                        <td class="text-center"><?php echo $value['current_status'];?></td> 

                                        <td class="text-center"><a href="<?php echo base_url(); ?>employee/inventory/update_spare_parts/<?php echo $value['id'];?>" class="btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a></td>
                                        <td>
                                          
                                            <a class="btn btn-md <?php if($value['defective_part_required'] == '0'){ echo 'btn-primary';} else{ echo 'btn-danger'; }?>" href="<?php echo base_url();?>employee/inventory/update_is_defective_parts_rquired/<?php if($value['defective_part_required'] == '0'){ echo '1';} else{ echo '0'; }?>/<?php echo $value['id'];?>/<?php echo $value['booking_id'];?>"><?php if($value['defective_part_required'] == '0'){ echo "Def. Part Required";} else{ echo "Def. Part Not Required"; }?></a>
                                        </td>
                                    </tr>
                             
                                        <?php $sn_no++; }}?>
                                </tbody>
                               
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div role="tabpanel" class="tab-pane" id="delivered">
    <div class="container-fluid">
        <div class="row" >
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <form   id="form1" onsubmit="return submitForm('form1');" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <table id="today_datatable" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="margin-top:10px;">
                                
                                <thead >
                                    <tr>
                                        <th class="text-center" >No</th>
                                        <th class="text-center" data-orderable="false">Booking Id</th>
                                        <th class="text-center" data-orderable="false">User</th>
                                        <th class="text-center" data-orderable="false">Mobile</th>
                                        <th class="text-center" data-orderable="false">Service Center</th>
                                        <th class="text-center" data-orderable="false">Partner</th>
                                        <th class="text-center" data-orderable="false">Requested Part</th>
                                        <th class="text-center" data-orderable="false">Shipped Part</th>
                                        <th class="text-center" data-orderable="false">Booking Status</th>
                                        <th class="text-center" data-orderable="false">IS Defective Parts Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no='1'; foreach ($spare_parts as $value) { 
                                        if($value['status'] == 'Delivered'){
                                    ?>
                                    
                                    <tr>
                                        <td class="text-center"><?php echo $sn_no; ?></td>
                                        <td class="text-center"><a 
                                          href="<?php echo base_url(); ?>employee/booking/viewdetails/<?php echo $value['booking_id']; ?>" target='_blank' title='view'><?php echo $value['booking_id'];?></a>
                                        </td>
                                        <td class="text-center"><?php echo $value['name'];?></td>
                                        <td class="text-center"><?php echo $value['booking_primary_contact_no'];?></td>
                                        <td class="text-center"><?php echo $value['sc_name'];?></td>
                                        <td class="text-center"><?php echo $value['source'];?></td>
                                        <td class="text-center"><?php echo $value['parts_requested'];?></td>
                                        <td class="text-center"><?php echo $value['parts_shipped'];?></td>
                                        <td class="text-center"><?php echo $value['current_status'];?></td> 
                                        <td>
                                          
                                            <a class="btn btn-md <?php if($value['defective_part_required'] == '0'){ echo 'btn-primary';} else{ echo 'btn-danger'; }?>" href="<?php echo base_url();?>employee/inventory/update_is_defective_parts_rquired/<?php if($value['defective_part_required'] == '0'){ echo '1';} else{ echo '0'; }?>/<?php echo $value['id'];?>/<?php echo $value['booking_id'];?>"><?php if($value['defective_part_required'] == '0'){ echo "Def. Part Required";} else{ echo "Def. Part Not Required"; }?></a>
                                        </td>
                                   </tr>
                             
                                        <?php $sn_no++; }}?>
                                </tbody>
                               
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
