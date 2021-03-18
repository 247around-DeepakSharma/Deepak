<style>
    #warranty_plans_filter{
        text-align: right;
    }
    
    .spinner {
        margin: 0px auto;
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 10px;
    }

    .spinner > div {
        height: 100%;
        width: 6px;
        display: inline-block;

        -webkit-animation: sk-stretchdelay 1.2s infinite ease-in-out;
        animation: sk-stretchdelay 1.2s infinite ease-in-out;
    }

    .spinner .rect2 {
        -webkit-animation-delay: -1.1s;
        animation-delay: -1.1s;
    }

    .spinner .rect3 {
        -webkit-animation-delay: -1.0s;
        animation-delay: -1.0s;
    }

    .spinner .rect4 {
        -webkit-animation-delay: -0.9s;
        animation-delay: -0.9s;
    }

    .spinner .rect5 {
        -webkit-animation-delay: -0.8s;
        animation-delay: -0.8s;
    }

    @-webkit-keyframes sk-stretchdelay {
        0%, 40%, 100% { -webkit-transform: scaleY(0.4) }  
        20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes sk-stretchdelay {
        0%, 40%, 100% { 
            transform: scaleY(0.4);
            -webkit-transform: scaleY(0.4);
        }  20% { 
            transform: scaleY(1.0);
            -webkit-transform: scaleY(1.0);
        }
    }
    
    #warranty_plans_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
    }
    
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
    .form-horizontal .control-label {
        text-align: left;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="title">
            <div class="row">
                <div class="col-md-6">
                    <h3>Warranty Plan List</h3>
                </div>
                <div class="col-md-6">
                    <a href="<?php echo base_url(); ?>employee/warranty/add_warranty_plan" class="btn btn-success pull-right" style="margin-top: 10px;" id="add_model" title="Add New Warranty Plan"><i class="fa fa-plus" style="margin-right:5px"></i>Add New Warranty Plan</a>
                </div>
            </div>
        </div>
        <hr>
        <div class="filter_box">
            <div class="row">
                <div class="form-inline">
                    <div class="form-group col-md-3">
                        <select class="form-control" id="model_partner_id">
                            <option value="0" selected>Select Partner</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <select class="form-control" id="model_service_id">
                            <option value="0" selected>Select Product</option>
                        </select>
                    </div>
<!--                    <div class="form-group col-md-2">
                        <label class="checkbox-inline"><input type="checkbox" value="1" id="show_all_inventory">Show All</label>
                    </div>-->
                    <button class="btn btn-success col-md-2" id="get_warranty_plan_data">Submit</button>
                </div>
            </div>
        <hr>
        <?php if($this->session->flashdata('error')){ ?>
        <div class="error_msg_div">
            <div class="alert alert-warning alert-dismissible" role="alert" style="margin-top:15px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><span id="success_msg"><?php echo $this->session->flashdata('error'); ?></span></strong>
            </div>
        </div>
        <?php } ?>
        
        <?php if($this->session->flashdata('success')){ ?>
        <div class="success_msg_div">
            <div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong><span id="success_msg"><?php echo $this->session->flashdata('success'); ?></span></strong>
            </div>
        </div>
        <?php } ?>
        
        <div class="model-table">
            <table class="table table-bordered table-hover table-striped" id="warranty_plans">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Plan Name</th>
                        <th>Plan Description</th>
                        <th>Start Period</th>
                        <th>End Period</th>
                        <th>Partner</th>
                        <th>Product</th>
                        <th>Warranty Type</th>
                        <th>Warranty Period</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Edit</th>
                        <th>Model</th> 
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    
</div>
<script>
    var warranty_plan_table;
    var entity_type = '';
    var entity_id = '';
    var time = moment().format('D-MMM-YYYY');
    
    $('#model_partner_id').select2({
     //   allowClear: true,
     //   placeholder: 'Select Partner'
    });
    $('#model_service_id').select2({
      //  allowClear: true,
      //  placeholder: 'Select Appliance'
    });
    $(document).ready(function(){
        
        get_partner_list();
        get_warranty_plan_list();
    });
    
    $('#get_warranty_plan_data').on('click',function(){
        var model_partner_id = $('#model_partner_id').val();
        if(model_partner_id && model_partner_id != 0){
        warranty_plan_table.ajax.reload(null, false);
        //   $('#warranty_plans').DataTable().ajax.reload();
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_warranty_plan_list(){
        warranty_plan_table = $('#warranty_plans').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            select: {
                style: 'multi'
            },
            "order": [], 
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Warranty_plan_List',
                    exportOptions: {
                       columns: [1,2,3,4,5,6,7,8,9],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/warranty/get_warranty_plan_list",
                "type": "POST",
                data: function(d){
                    d.partner_id = $('#model_partner_id').val();
                    d.service_id = $('#model_service_id').val();
                }
            },
            "deferRender": true       
        });
    }
    
    
    function change_warranty_plan_status(id, status, row_index)
    {
        var temp = warranty_plan_table.row(row_index).data();
      //  alert(status);
        if(status == 1)
        {
            var url = '<?php echo base_url(); ?>employee/warranty/activate_plan';
            temp[9] = 'Active';
            temp[10] = "<button class='btn btn-warning btn-sm' onclick='change_warranty_plan_status("+ id +",0,"+row_index+")'>Inactive</button>";
            var success_msg = "Warranty plan activated successfully!";
            var error_msg = "Something went wrong while activating warranty plan!";
        }
        else
        {
            var url = '<?php echo base_url(); ?>employee/warranty/deactivate_plan';
            temp[9] = 'Inactive';
            temp[10] = "<button class='btn btn-success btn-sm' onclick='change_warranty_plan_status("+ id +",1,"+row_index+")'>Active</button>";
            var success_msg = "Warranty plan deactivated successfully!";
            var error_msg = "Something went wrong while deactivating warranty plan!";
        }    
        
        $.ajax({
        type: 'POST',
        url: url,
        data: {plan_id : id}
      })
      .done (function(data) {
         // alert(temp);
          warranty_plan_table.row(row_index).data(temp).invalidate();
          alert(success_msg);
        //  location.reload();
      })
      .fail(function(jqXHR, textStatus, errorThrown){
          alert(error_msg);
       //   location.reload();
       })
    }
    
    
    
    //function to get partrner list in dropdown
    function get_partner_list()
    {
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url(); ?>employee/warranty/get_partner_list_dropdown',
           data: {}
         })
         .done (function(data) {
             $('#model_partner_id').append(data);
         })
         .fail(function(jqXHR, textStatus, errorThrown){
             alert("Something went wrong while loading partner list!");
          })
    }

    //function to load product list in dropdown on basis of partner selected
    $('#model_partner_id').change(function(){
        var partner_id = $(this).val();
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url(); ?>employee/warranty/get_partner_service_list_dropdown',
           data: {partner_id : partner_id}
         })
         .done (function(data) {
             $('#model_service_id').html(data);
         })
         .fail(function(jqXHR, textStatus, errorThrown){
             alert("Something went wrong while loading partner service list!");
          })
    })
    
    
    function warranty_plan_details(plan_id){
        window.location.href = "<?php echo base_url(); ?>employee/warranty/warranty_plan_details/"+plan_id;
    }
    // redirecting to model according to plan id
    function plan_model_mapping(plan_id){
        window.location.href = "<?php echo base_url(); ?>employee/warranty/plan_model_mapping/"+plan_id;
    }

</script>