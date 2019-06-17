<style>
    .select2.select2-container.select2-container--default{
        width: 100%!important;
    }

</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Engineer Vise Call List</h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="x_content_header">
                        <section class="fetch_engineer_call_data">
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-4">
                                        <select class="form-control" id="engineer_id">
                                            <option value="">Select Engineer</option>
                                            <?php if(!empty($engineers)) { foreach($engineers as $engineer) { ?>
                                            <option value="<?= $engineer['id']; ?>"><?= $engineer['name']; ?></option>
                                            <?php } } ?>
                                        </select>
                                    </div> 
                                    <div class="form-group col-md-4">
                                        <select class="form-control" id="status_id">
                                            <option value="">Select Status</option>
                                            <?php if(!empty($status)) { foreach($status as $name) { ?>
                                            <option value="<?= $name; ?>"><?= $name; ?></option>
                                            <?php } } ?>
                                        </select>
                                    </div> 
                                    <div class="form-group col-md-4">
                                        <button class="btn btn-success btn-sm col-md-2" id="get_engineer_call_data">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="engineer_call_list">
                        <table id="engineer_vise_call_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Booking ID</th>
                                    <th>User/Phone</th>
                                    <th>Address</th>
                                    <th>Appliance</th>
                                    <th>Booking Date</th>
                                    <th>Age</th>
                                    <th>Partner</th>
                                    <th>Brands</th>
                                    <th>Escalation</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
         
    </div>
</div>
<style>
    #engineer_vise_call_table_filter, .dataTables_length, .dt-buttons, .dataTables_info, .dataTables_paginate {
        display:none;
    }
</style>
<script>

    $('#engineer_id').select2({
        allowClear: true,
        placeholder :'Select Engineer'
    });
    $('#status_id').select2({
        allowClear: true,
        placeholder :'Select Status'
    });
    
    var engineer_vise_call_table;

    $(document).ready(function () {
        get_engineer_vise_list();
    });
    
    $('#get_engineer_call_data').on('click',function(){
        var engineer_id = $('#engineer_id').val();
        // alert(engineer_id);
        if(engineer_id){
             
            engineer_vise_call_table.ajax.reload();
        }else{
            alert("Please Select Engineer");
        }
    });
    
    function get_engineer_vise_list(){
       
        engineer_vise_call_table = $('#engineer_vise_call_table').DataTable({
            "processing": true,
            "serverSide": true,
               "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "language": {
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>",
                "emptyTable":     "No Data Found"
            },
            "order": [],
            "pageLength": 25,
            "dom": 'lBfrtip',
            "ordering": false,
              "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>   Export',
                    pageSize: 'LEGAL',
                    title: 'Inventory List',
                    exportOptions: {
                       columns: [0,1,2,3,4,5,6,7,8,9],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                url: "<?php echo base_url(); ?>employee/vendor/get_engineer_vise_call_details",
                type: "POST",
                data: function(d){
                   // console.log(d); 
                    var entity_details = get_entity_details();
                    d.engineer_id = entity_details.engineer_id,
                    d.status = entity_details.status
                }
            },
            "deferRender": true
        });
    }
    
    function get_entity_details() {
        var data = {
            'engineer_id': $('#engineer_id').val(),
            'status' : $('#status_id').val(),
        };
        
        return data;
    }
    
</script>