<style>
    .select2.select2-container.select2-container--default{
        width: 100%!important;
    }
    .alternate_spare_list{
        float: right;  
        font-size: 22px;
        color: #131212cc;
        padding: 5px;
    }
    .pull-right {
        padding: 5px;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <div>
                        <h3>Download Sale and Purchase MSL Invoice</h3>
                         
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="x_content">
                    <div class="x_content_header">
                        <section class="fetch_inventory_data">
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-3">
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                    </div> 
                                    <button class="btn btn-success btn-sm col-md-2" id="get_inventory_data">Submit</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="inventory_stock_list">
                        <table id="inventory_stock_table" class="table table-bordered table-responsive">
                            <thead>
                                <tr>
                        <th>S.No</th>
                        <th>Invoice Id</th>
                        <th>Invoice Date</th>
                        <th>Invoice Type</th>
                        <th>Part Number</th>
                        <th>Description</th>
                        <th>HSN Code</th>
                        <th>Quantity</th>
                        <th>Settled Quantity</th>
                        <th>Rate</th>
                        <th>Taxable Value</th>
                        <th>GST Rate</th>
                        <th>GST Tax Amount</th>
                        <th>Total Amount</th>
                        <th>Type</th>
                        <th>From GST Number</th>
                        <th>To GST Number</th>
                        <th>Sub Category</th>
                                    
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

    var inventory_stock_table;

    $(document).ready(function () {
        get_partner();
        get_inventory_list();
        
 
    });

 
    $('#get_inventory_data').on('click',function(){
        var partner_id = $('#partner_id').val();
        if(partner_id){
            inventory_stock_table.ajax.reload();
        }else{
            alert("Please Select Partner");
        }
    });
    
    function get_inventory_list(){
        inventory_stock_table = $('#inventory_stock_table').DataTable({
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
                    title: 'Download_Sale_and_Purchase_MSL_Invoice',
                    exportOptions: {
                       columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'all',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                url: "<?php echo base_url(); ?>employee/inventory/get_list_sale_purchage_invoice_data",
                type: "POST",
                data: function(d){
                    var entity_details = get_entity_details();
                    d.partner_id = entity_details.partner_id
                }
            },
            "deferRender": true,
            "fnInitComplete": function (oSettings, response) {
            
                $(".dataTables_filter").addClass("pull-right");
            }
        });
    }
    
    function get_entity_details(){
    
        var data = {
            'partner_id':$('#partner_id').val()
        };
        
        return data;
    }
    
    function get_partner() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data:{'is_wh' : 1},
            success: function (response) {
                $('#partner_id').html(response);
                var option_length = $('#partner_id').children('option').length;
                if(option_length == 2){
                 $("#partner_id").change();   
                }
                $('#partner_id').select2();
            }
        });
    }
       
</script>
