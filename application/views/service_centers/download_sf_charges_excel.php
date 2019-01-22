<script src="<?php echo base_url()?>assest/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url()?>assest/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url()?>assest/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url()?>assest/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>

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
                            <select class="form-control filter_table" id="service_id" onchange="get_category()">
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
                            <select class="form-control filter_table" id="category" onchange="get_capacity()">
                                <option selected="" disabled="">Select Category</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="capacity" onchange="get_service_category()">
                                <option selected="" disabled="">Select Capacity</option>
                               
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="service_category" onchange="service_category_changed()">
                                <option selected="" disabled="">Select Service Category</option>
                               
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
                    <th class="jumbotron">No</th>
<!--                    <th class="jumbotron"style="text-align: center" >SC CODE</th>-->
                    <th class="jumbotron"style="text-align: center" >PRODUCT</th>
                    <th class="jumbotron"style="text-align: center" >BRAND</th>
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
    
    $("#service_id").select2({
        placeholder: "Select Product",
        allowClear:true
    });
    $("#category").select2({
        placeholder: "Select Category",
        allowClear:true
    });
    $("#capacity").select2({
        placeholder: "Select Capacity",
        allowClear:true
    });
    $("#service_category").select2({
        placeholder: "Select Service",
        allowClear:true
    });
    

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
                    "targets": [0,1,7,8],
                    "orderable": false
                }
            ]      
        });
        
//        $('.filter_table').on('change', function(){
//            chargesTable.ajax.reload();
//        });
        $("input[type=Search]").attr("placeholder", "Search Brand");
    });
    
    function get_category(){
        $.ajax({
            url:'<?php echo base_url(); ?>employee/service_centers/get_service_price_category',
            method : 'post',
            data : {service_id : $("#service_id").val()},
            success : function(response){
                $("#category").html(response);
                chargesTable.ajax.reload();
            }
        });
    }
    
    function get_capacity(){
        $.ajax({
            url:'<?php echo base_url(); ?>employee/service_centers/get_service_price_capacity',
            method : 'post',
            data : {service_id : $("#service_id").val(), category : $("#category").val()},
            success : function(response){
                $("#capacity").html(response);
                chargesTable.ajax.reload();
            }
        });
    }
    
    function get_service_category(){
        $.ajax({
            url:'<?php echo base_url(); ?>employee/service_centers/get_service_price_service_category',
            method : 'post',
            data : {service_id : $("#service_id").val(), category : $("#category").val(), capacity : $("#capacity").val()},
            success : function(response){
                $("#service_category").html(response);
                chargesTable.ajax.reload();
            }
        });
    }
    
    function service_category_changed(){
        chargesTable.ajax.reload();
    }
</script>