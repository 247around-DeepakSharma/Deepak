<script src="<?php echo base_url()?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url()?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url()?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url()?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>

<style>
    #chargesTable_filter{
        display: none;
    }
</style>

<div id="page-wrapper" >
    <div class="row">
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading"><center style="font-size:130%;">SERVICE CENTER PAY OUT (CALL CHARGES)</center></div>
        </div>
        <div class="table_filter">
            <div class="row">
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="service_id">
                                <option selected="" disabled="">Select Product</option>
                                <?php foreach($appliance as $key => $val) { ?> 
                                <option value="<?php echo $key?>"><?php echo $val?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="category">
                                <option selected="" disabled="">Select Category</option>
                                <?php foreach($category as $val) { ?> 
                                <option value="<?php echo $val?>"><?php echo $val?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="capacity">
                                <option selected="" disabled="">Select Capacity</option>
                                <?php foreach($capacity as $val) { ?> 
                                <option value="<?php echo $val?>"><?php echo $val?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="service_category">
                                <option selected="" disabled="">Select Service Category</option>
                                <?php foreach($service_category as $val) { ?> 
                                <option value="<?php echo $val?>"><?php echo $val?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <table id="chargesTable" class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="jumbotron">S.N.</th>
                    <th class="jumbotron"style="text-align: center" >SC CODE</th>
                    <th class="jumbotron"style="text-align: center" >PRODUCT</th>
                    <th class="jumbotron"style="text-align: center" >CATEGORY</th>
                    <th class="jumbotron"style="text-align: center" >CAPACITY</th>
                    <th class="jumbotron"style="text-align: center" >SERVICE CATEGORY</th>
                    <th class="jumbotron"style="text-align: center" >VENDOR BASIC CHARGE</th>
                    <th class="jumbotron"style="text-align: center" >VENDOR TAX BASIC CHARGE</th>
                    <th class="jumbotron"style="text-align: center" >VENDOR TOTAL</th>
                    <th class="jumbotron"style="text-align: center" >CUSTOMER NET PAYABLE</th>
                    <th class="jumbotron"style="text-align: center" >PROOF OF DELIVERY</th>
                </tr>
            <tbody></tbody>
            </thead>
        </table>
    </div>

</div>

<script>
    
    var chargesTable = "";
    $(document).ready(function(){
        chargesTable = $('#chargesTable').DataTable({
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "pageLength": 50,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/service_centers/get_sf_data",
                "type": "POST",
                "data": function(d){
                    d.status = '0';
                    d.product = $('#service_id').val();
                    d.category = $('#category').val();
                    d.capacity = $('#capacity').val();
                    d.service_category = $('#service_category').val();
                    
                 }
            },
            "columnDefs": [
                {
                    "targets": [0,1,6,7],
                    "orderable": false
                }
            ]      
        });
        
        $('.filter_table').on('change', function(){
            chargesTable.ajax.reload();
        });
        
    });
</script>