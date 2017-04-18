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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sn_no='1'; foreach ($spare_parts as $value) {  
                                        if($value['status'] == 'Spare Parts Requested'){
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
                                    
                                        <td class="text-center"><a href="#" disabled class="btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a></td>
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

                                        <td class="text-center"><a href="#" disabled class="btn btn-sm btn-primary" ><i class="fa fa-edit"></i></a></td>
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
