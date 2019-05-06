<style>
    .dropdown-menu{
        font-size: 13px;
        left:-60px;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="clearfix"></div>
<div class="row" >
    <div class="col-md-12 col-sm-12 col-xs-12" >
        <div class="x_panel" style="height: auto;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_title" style="border-bottom: 0px solid #FFF;">
                    <h2>
                    Search Result
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                     <form action="#" method="POST" id="reAssignForm" name="reAssignForm">
                    <table id="search_datatable1" class="table table-striped table-bordered table-responsive" style="width: 100%; margin-bottom: 100px;background-color: #fff;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Tracking ID</th>
                                <th>Appliance</th>
                                <th>Category/Size</th>
                                <th>City</th>
                                <th>Order Date</th>
                                <th>Delivery Date</th>
                                <th>Current Status</th>
                                <th>Exchange Value</th>
                                <th>SF Charge</th>
                                <th>CP Claimed Price</th>
                                <th>Assign CP</th>
                                <th style="display:none">Assign Collection Partner</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                               $assign_collection_partner = "";
                                foreach($list as $key => $value){ ?>
                            <tr>
                                <td><?php echo ($key +1);?></td>
                                <td><a href="<?php echo base_url();?>buyback/buyback_process/view_order_details/<?php echo $value->partner_order_id;?>">
                                    <?php echo $value->partner_order_id;?></a>
                                </td>
                                <td><?php echo $value->tracking_id;?></td>
                                <td><?php echo $value->services;?></td>
                                <td><?php echo $value->category;?></td>
                                <td><?php echo $value->city;?></td>
                                <td><?php echo $value->order_date;?></td>
                                <td><?php echo $value->delivery_date;?></td>
                                <td><?php echo $value->current_status;?></td>
                                <td><?php echo $value->partner_basic_charge;?></td>
                                <td><?php echo ($value->cp_basic_charge + $value->cp_tax_charge);?></td>
                                <td><?php echo ($value->cp_claimed_price);?></td>
                                <td>
                                    <select name="assign_cp_id[<?php echo $value->partner_order_id; ?>]" ui-select2  class="assign_cp_id"  class="form-control" 
                                        data-placeholder="Select CP" style="width:200px;">
                                        <option value="" selected disabled>Select CP</option>   
                                         <?php foreach ($shop_list as $key => $val) { ?>
                                             <option value="<?php echo $val['id']?>" <?php if($value->assigned_cp_id == $val['cp_id']) { echo "selected";$assign_collection_partner=$val['name'];}?>><?php echo $val['cp_name']?></option>  
                                        <?php } ?>
                                        </select>
                                </td>
                                <td style="display:none">
                                    <?php echo $assign_collection_partner;?>
                                </td>
                                 <td>
                                    <div class="dropdown">
                                        <button class='btn btn-default dropdown-toggle' type='button' id='menu1' data-toggle='dropdown'>Actions<span class='caret'></span></button>
                                        <ul class='dropdown-menu' role='menu' aria-labelledby='menu1'>
                                            <li role='presentation'><a role='menuitem' tabindex='-1' onclick="showDialogueBox('<?php echo base_url();?>buyback/buyback_process/update_received_bb_order/<?php echo (rawurlencode($value->partner_order_id)  . "/" . rawurlencode($value->service_id) . "/" . rawurlencode($value->city) . "/" . rawurlencode($value->assigned_cp_id))?>')">Received</a></li>
                                            <li role='presentation'><a role='menuitem' tabindex='-1' onclick="showDialogueBox('<?php echo base_url();?>buyback/buyback_process/update_not_received_bb_order/<?php echo (rawurlencode($value->partner_order_id)  . "/" . rawurlencode($value->service_id) . "/" . rawurlencode($value->city) . "/" . rawurlencode($value->assigned_cp_id))?>')">Not Received</a></li>
                                            <li role='presentation'><a role='menuitem' tabindex='-1' href='<?php echo base_url();?>buyback/buyback_process/update_bb_report_issue_order_details/<?php echo (rawurlencode($value->partner_order_id)  . "/" . rawurlencode($value->service_id) . "/" . rawurlencode($value->city) . "/" . rawurlencode($value->assigned_cp_id). "/" . rawurlencode($value->current_status))?>' target='_blank'>Broken/Wrong Product</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php }
                                ?>
                        </tbody>
                   </table>
                    </form>
                    <div class="col-md-12 text-center">
                            <a href="javascript:void(0);" class="btn btn-md  btn-success" onclick="reAssign()">ReAssign CP</a>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="myModal"  class="modal fade" data-keyboard="false" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Not Assigned</h4>
            </div>
            <div class="modal-body">
                <div id="open_model">
                    <table class="table table-bordered table-hover table-responsive">
                        <thead>
                        <th>S.No.</th>
                        <th>Order ID</th>   
                        <th>Message</th>   
                        </thead>
                        <tbody id="error_td">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(".assign_cp_id").select2({
             allowClear: true
    });

    $(document).ready(function () {
        table = $('#search_datatable1').DataTable({
            "pageLength":'500',
            "lengthMenu": [[100, 200, 500,-1], [100, 200, 500, "All"]],
            dom: 'Bfrtip',
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {

                        columns: [1,2,3,4,5,6,7,8,10,12]
                    },
                    
                    title: 'buyback_order'
                }
            ],
            select: true
         
        });
    });
    
    function showDialogueBox(url){
        swal({
                title: "Do You Want To Continue?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: false
            },
            function(){
                window.location.href = url;
            });
    }
</script>
