<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="right_col" role="main">
    <h1 class="col-md-6 col-sm-12 col-xs-12"><b> Brand Collateral </b></h1>
    <div class="clearfix"></div>
    <div style='margin:3%;'>
        <form method="POST" action="#">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group" id="partner_holder">

                        <select class="form-control" name="partner" id="partner" onchange='get_appliance()'>
                            <option selected disabled value="option_holder">Select Partner</option>
                            <?php
                                foreach($partnerArray as $partnerID=>$partnerName){
                                    echo ' <option value="'.$partnerID.'">'.$partnerName.'</option>';
                                }
                                ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group" >

                        <select class="form-control" id="service_id" required="" name="service_id" onchange='get_brand();'>
                            <option selected disabled  >Select Service</option>
                        </select>
                    </div>
                    <?php echo form_error('service_id'); ?>
                </div>
                <div class="col-md-2">
                    <div class="form-group" >

                        <select class="form-control" id="brand" required="" name="brand">
                            <option selected disabled  >Select Brand</option>
                        </select>
                    </div>
                    <?php echo form_error('brand'); ?>
                </div>
                <div class="col-md-2" style="width: 450px">
                    <div class="form-group" >

                        <select class="form-control" id="request_type" required="" name="request_type[]" multiple="multiple" style="width: 430px">
                            <option disabled  >Select Service Category</option>
                            <option value="Installation"  >Installation</option>
                            <option value="Repair"  >Repair</option>
                        </select>
                    </div>
                    <?php echo form_error('request_type'); ?>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <button type="button" class="btn btn-small btn-primary" id="search" onclick="validform()">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="x_panel" style="height: auto;display:none;">
        <table id="brand_collateral_partner" class="table table-striped table-bordered">
            <thead>
               <tr>
                    <th>S.No.</th>
                    <th>Document Type</th>
                    <th>Model</th>
                    <th>Category</th>
                    <th>Capacity</th>
                    <th>File</th>
                    <th>Description</th>
                    <!--<th>Delete <button onclick="delete_collatrals()"><i class="fa fa-trash" aria-hidden="true"></i></button></th>-->
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
        
</div>
<style>
    #brand_collateral_partner_filter{
        float: right;
    }
</style>
<script>
    function validform(){
        var partner = $("#partner option:selected").val();
        var service =  $("#service_id option:selected").val();
        var brand =  $("#brand option:selected").val();
        var request_type =  $("#request_type").val();
        
        if(partner==='option_holder')
        {
           alert("Please Select Partner ");
           return false;
        }
        else if(service==='Select Service')
        {
           alert("Please Select Service ");
           return false;
        }
        else if(brand==='Select Brand')
        {
           alert("Please Select Brand ");
           return false;
        }
        else if(request_type==null || request_type=='')
        {
           alert("Please Select Service Category ");
           return false;
        }
        else {
            $(".x_panel").css("display","block");
            ad_table.ajax.reload( function ( json ) {} );
        }
    }
     function getMultipleSelectedCheckbox(fieldName){
        var checkboxes = document.getElementsByName(fieldName);
        var vals = "";
        length = checkboxes.length;
        for (var i=0;i<length;i++) 
        {
            if (checkboxes[i].checked) 
            {
                vals += "'"+checkboxes[i].value+"',";
            }
        }
        return vals;
    }
    function delete_collatrals(){
        collatrelsID = getMultipleSelectedCheckbox("coll_id[]");
        if(collatrelsID){
            $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/deactivate_brand_collateral',
            data: {collateral_id:collatrelsID},
            success: function (data) {
                alert(data);
            }
        });
        }
    }
    function get_appliance(){
        var partner_id=$("#partner").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/booking/get_appliances/0',
            data: {'partner_id':partner_id},
            success: function (response) {
                response=JSON.parse(response);
                if(response.services){
                    $('#service_id').html(response.services);
                }
            }
        });
       
    }
    //This funciton is used to get Distinct Brands for selected service for Logged Partner
    function get_brand(){
        var partner_id=$("#partner").val();
        var service_id =  $("#service_id").val();

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_brands_from_service',
            data: {service_id: service_id,partner_id:partner_id},
            success: function (data) {
                //First Resetting Options values present if any
                $('#brand').html("<option selected disabled  >Select Brand</option>");
                $('#brand').append(data);
            }
        });
    }
    
    var ad_table;
        ad_table = $('#brand_collateral_partner').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "deferLoading": 0,
            "lengthMenu": [[10, 25, 50,100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Export',
                    pageSize: 'LEGAL',
                    title: 'partner_brand_collateral',
                    exportOptions: {
                       columns: [0,1,2,3,4,6,7],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": baseUrl+"/employee/partner/brandCollateralPartner",
                "type": "POST",
                "data": function(d){
                  d.partner_id = $("#partner option:selected").val();
                  d.service_id = $("#service_id option:selected").val();
                  d.brand = $("#brand option:selected").val();
                  d.request_type = $("#request_type").val();
               }
            },
            "columnDefs": [
                {
                    "targets": [0,5,6,7], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
        });
        $("#partner,#service_id,#brand").select2();
        $("#request_type").select2({
                placeholder: "Select Service Category",
                allowClear: true
        });
</script>