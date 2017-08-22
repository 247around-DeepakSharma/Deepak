<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">

<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="clearfix"></div>
<div class="row" >
    <div class="col-md-12 col-sm-12 col-xs-12" >
        <div class="x_panel" style="height: auto;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_title" style="border-bottom: 0px solid #FFF;">
                    <h2>
                    <i class="fa fa-bars"></i> Search Result:- <!--<small>Float left</small>-->
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                     <form action="#" method="POST" id="reAssignForm" name="reAssignForm">
                    <table class="table table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Order ID</th>
                                <th>Appliance</th>
                                <th>Category/Size</th>
                                <th>City</th>
                                <th>Order Date</th>
                                <th>Delivery Date</th>
                                <th>Current Status</th>
                                <th>Exchange Value</th>
                                <th>SF Charge</th>
                                <th>Assign CP</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php 
                                foreach($list as $key => $value){ ?>
                            <tr>
                                <td><?php echo ($key +1);?></td>
                                <td><a href="<?php echo base_url();?>buyback/buyback_process/view_order_details/<?php echo $value->partner_order_id;?>">
                                    <?php echo $value->partner_order_id;?></a>
                                </td>
                                <td><?php echo $value->services;?></td>
                                <td><?php echo $value->category;?></td>
                                <td><?php echo $value->city;?></td>
                                <td><?php echo $value->order_date;?></td>
                                <td><?php echo $value->delivery_date;?></td>
                                <td><?php echo $value->current_status;?></td>
                                <td><?php echo $value->partner_basic_charge;?></td>
                                <td><?php echo ($value->cp_basic_charge + $value->cp_tax_charge);?></td>
                                <td>
                                    <select name="assign_cp_id[<?php echo $value->partner_order_id; ?>]" ui-select2  class="assign_cp_id"  class="form-control" 
                                        data-placeholder="Select CP" style="width:200px;">
                                        <option value="" selected disabled>Select CP</option>   
                                         <?php foreach ($shop_list as $key => $value) { ?>
                                                
                                        <option value="<?php echo $value['id']?>" ><?php echo $value['cp_name']?></option>   
                                <?php } ?>
                                        </select>
                                </td>
                            </tr>
                            <?php }
                                ?>
                        </tbody>
                        
                    </table>
                    </form>
                    <div class="col-md-12 text-center">
                           
                             <a href="javascript:void(0);" class="btn btn-md  btn-success" onclick="reAssign()"  >ReAssign CP</a>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(".assign_cp_id").select2({
             allowClear: true
        });
</script>