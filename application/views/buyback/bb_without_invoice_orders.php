<style>
    .dropdown-menu{
        font-size: 13px;
        left:-60px;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="right_col" role="main">
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
                    <table id="search_datatable1" class="table table-striped table-bordered table-responsive" style="width: 100%; margin-bottom: 100px;background-color: #fff;">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Order ID</th>
                                <th>Admin Remarks</th>
                                <th>Order Date</th>
                                <th>Delivery Date</th>
                                <th>Current Status</th>
                                <th>Internal Status</th>
                                <th>Remarks</th>
                                <th>Last Update Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach($list as $key => $value){ ?>
                            <tr>
                                <td><?php echo ($key +1);?></td>
                                <td><a target="_blank" href="<?php echo base_url();?>buyback/buyback_process/view_order_details/<?php echo $value['partner_order_id'];?>">
                                    <?php echo $value['partner_order_id'];?></a>
                                </td>
                                <td><?php echo $value['admin_remarks'];?></td>
                                <td><?php echo date("d-M-Y", strtotime($value['order_date']));?></td>
                                <td><?php echo date("d-M-Y", strtotime($value['delivery_date']));?></td>
                                <td><?php echo $value['current_status'];?></td>
                                <td><?php echo $value['internal_status'];?></td>
                                <td><?php echo $value['remarks'];?></td>
                                <td><?php echo date("d-M-Y", strtotime($value['update_date']));?></td>
                            </tr>
                            <?php }
                                ?>
                        </tbody>
                   </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script>

    $(document).ready(function () {
        table = $('#search_datatable1').DataTable({
            "pageLength":'25',
            dom: 'Bfrtip',
            buttons: [
                'pageLength',
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7,8]
                    },
                    
                    title: 'buyback_order'
                }
            ],
            select: true
         
        });
    });
</script>
