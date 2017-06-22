<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js"></script>
<script src="https://editor.datatables.net/extensions/Editor/js/dataTables.editor.min.js"></script>
<script src="https://editor.datatables.net/extensions/Editor/js/editor.bootstrap.min.js"></script>
<div class="right_col" role="main">
    <!--        <div class="page-title">
        <div class="title_left">
            <h3>Order Details</h3>
        </div>
        </div>-->
    <div class="clearfix"></div>
    <div class="row" >
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="x_panel" style="height: auto;">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_title">
                        <h2>
                            <i class="fa fa-bars"></i> CP Shop Address <!--<small>Float left</small>-->
                        </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="datatable" class="table table-striped table-bordered" >
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Partner Name</th>
                                    <th>CP Name</th>
                                    <th>Contact Person</th>
                                    <th>Mobile</th>
                                    <th>Alt Mobile</th>
                                    <th>Shop Address1</th>
                                    <th>Shop Address2</th>
                                    <th>City</th>
                                    <th>Pincode</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">

        var table;

        $(document).ready(function () {
            table = $('#datatable').DataTable({
                "processing": true, //Feature control the processing indicator.
                "serverSide": true, //Feature control DataTables' server-side processing mode.
                "order": [], //Initial no order.
                "pageLength": 50,
                
                // Load data for the table's content from an Ajax source
                "ajax": {
                    "url": "<?php echo base_url(); ?>buyback/collection_partner/get_cp_shop_address_data",
                    "type": "POST"

                },
                //Set column definition initialisation properties.
                "columnDefs": [
                    {
                        "targets": [0], //first column / numbering column
                        "orderable": false, //set not orderable
                    },
                ],
                
            });

        });

        function activate_deactivate(shop_id, is_acitve) {
       
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>buyback/collection_partner/activate_deactivate_cp/'+ shop_id +"/"+ is_acitve,
                
                success: function (data) {
                   if(data === "Success"){
                       table.ajax.reload(null,false);
                   } else {
                       alert("There is some issues to Activate/De-Activate. Please Contact Developer Team");
                   }
                    
                }
            });
        }

    </script>
