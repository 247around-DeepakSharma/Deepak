<style>
    #appliance_model_details_filter{
        text-align: right;
    }
    #appliance_model_details_processing{
            position: absolute;
            z-index: 999999;
            width: 100%;
            background: rgba(0,0,0,0.5);
            height: 100%;
            top: 10px;
            left:6%;
    }
    
    .dataTables_length{
        width: 12%;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Model Mapping List</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="inventory-table">
                        <table class="table table-bordered table-hover table-striped" id="appliance_model_details">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appliance</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Capacity</th>
                                    <th>Model Number</th>
                                    <th>Status</th>
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
<script>
    var appliance_model_details_table;
    
    $(document).ready(function(){ 
        appliance_model_details_table = $('#appliance_model_details').DataTable({
            "processing": true, 
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6]
                    },
                    title: 'mapped_model_number',
                },
            ],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            "order": [], 
            "pageLength": 25,
            "ordering": false,
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/inventory/get_partner_mapped_model_details",
                "type": "POST",
                data: {partner_id: '<?php echo $this->session->userdata('partner_id'); ?>'}
            },
        });
    });
    
   
</script>