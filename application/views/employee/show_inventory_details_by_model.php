<style>
    .select2-container{
        width: 100%!important;
    }
    .select2-container .select2-selection--single{
        height: 35px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 33px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 31px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            
            <?php if(!empty($inventory_details)) { ?>
            <div class="x_panel">
                <div class="x_title">
                    <h3>Spare Part Details For Model Number <strong><?php echo array_unique(array_column($inventory_details, 'model_number'))[0] ;?></strong></h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="inventory_stock_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>HSN</th>
                                    <th>GST Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1; foreach ($inventory_details as $value) { ?>
                                <tr>
                                    <td><?php echo $sn; ?></td>
                                    <td><?php echo $value['services']; ?></td>
                                    <td><?php echo $value['type']; ?></td>
                                    <td><?php echo $value['part_name']; ?></td>
                                    <td><?php echo $value['part_number']; ?></td>
                                    <td><?php echo $value['size']; ?></td>
                                    <td><?php echo $value['price']; ?></td>
                                    <td><?php echo $value['hsn_code']; ?></td>
                                    <td><?php echo $value['gst_rate']; ?></td>
                                    
                                </tr>
                                <?php $sn++;} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php }else { ?> 
            <div class="alert alert-danger text-center">
                <p>No Details Found</p>
            </div>
            <?php } ?>
        </div>

        <!--Modal start-->
        <div id="modal_data" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="open_model"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal end -->
    </div>
</div>