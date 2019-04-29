<?php if (isset($partners)) { ?>
    <div id="page-wrapper" >
        <div class="container-fluid" >
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading">
                    <h3>Show Partner Price </h3> 
                </div>
                <div class="panel-body">
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th style="width: 22%"> 
                                <select class="form-control" id="partners" name="partners" >
                                    <option selected disabled>Select Partner</option>
                                    <?php foreach ($partners as $key => $value) { ?>
                                        <option value="<?php echo $value['partner_id'] ?>"> <?php echo $value['source']; ?></option>
                                    <?php } ?>
                                </select>
                            </th>

                            <th>
                                <select class="form-control"  id="service_id" name="service_id" >
                                    <option selected disabled >Select Appliance</option>
                                </select>
                            </th>

                            <th>
                                <select class="form-control" id="service_category" name="service_category" >
                                    <option selected disabled  >Select Service Category</option>
                                </select>
                            </th>
                            
                            <th>
                                <select class="form-control" id="brand" name="brand" >
                                    <option selected disabled  >Select Brand</option>
                                </select>
                            </th>


                        </tr>
                    </table>
                    <div class="col-md-12"><center><img id="loader_gif" src="" style="display: none;"></center></div>
                    <table class="table table-striped table-bordered" id="partners_price"></table>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $("#partners").select2();
        $("#service_id").select2();
        $("#service_category").select2();
        $("#brand").select2();
        
        var partner_id_with_source_code = <?php echo $source_code;?>;
        $('#partners').on('change', function () {
            var partner = $(this).val();
            if (partner) {
                var postData = {};
                postData['partner'] = partner;
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>employee/service_centre_charges/get_partner_data',
                    data: postData,
                    success: function (html) {
                        $('#service_id').val('val', "");
                        $('#service_id').val('Select Appliance').change();
                        $('#service_id').select2().html(html);
                        $('#service_category').val('val', "");
                        $('#service_category').val('Select Service Category').change();
                        $('#service_category').select2().html('<option selected disabled  >Select Service Category</option>');
                    }
                });
            }
            else {
                $('#service_id').html('<option value="">Select Partner first</option>');
                $('#service_category').html('<option value="">Select Appliance first</option>');
            }
        });

        $('#service_id').on('change', function () {
            var postdata = {};
            postdata['partner_id'] = $('#partners').val();
            postdata['service_id'] = $('#service_id').val();
            if (postdata) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>employee/service_centre_charges/get_partner_data',
                    data: postdata,
                    success: function (html) {
                        $('#service_category').val('val', "");
                        $('#service_category').val('Select Service Category').change();
                        $('#service_category').select2().html(html);
                    }
                });
                
                if($('#service_id').val()){
                    var post = {};
                    post['service_id'] = $('#service_id').val();
                    post['source_code'] = partner_id_with_source_code[$('#partners').val()];
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo base_url() ?>employee/booking/getBrandForService',
                        data: post,
                        success: function (response) {
                            var data1 = jQuery.parseJSON(response);
                            $("#brand").html(data1.brand);
                        }
                    });
                }
            }
        });

        $('#service_category, #partners,#brand').on('change', function () {
            var postdata = {};
            postdata['partner_id'] = $('#partners').val();
            postdata['service_id'] = $('#service_id').val();
            postdata['service_category'] = $('#service_category').val();
            postdata['brand'] = $('#brand').val();

            if (postdata) {
                $('#loader_gif').css('display', 'inherit');
                $('#loader_gif').attr('src', "<?php echo base_url(); ?>images/loader.gif");
                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>employee/service_centre_charges/show_partner_price',
                    data: postdata,
                    success: function (data) {
                        $('#loader_gif').attr('src', "");
                        $('#loader_gif').css('display', 'none');
                        $("#partners_price").html(data);
                       
                    }
                });
            }
        });

    </script>

<?php } ?>

<?php if (isset($price_data)) {
    if (!empty($price_data)) { ?>

        <table class="table table-striped table-bordered"  id="partner_price_data">
            <thead>
            <th>No.</th>
            <th>Service Category</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Capacity</th>
            <th>Customer Total</th>
            <th>Partner Payable Basic</th>
            <th>Customer Net Payable</th>
            <th>Vendor Total</th>
            <th>Vendor Basic Percentage</th>
            <th>POD</th>
            <th>Upcountry</th>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($price_data as $key => $value) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $value['service_category'] ?></td>
                        <td><?php echo $value['brand'] ?></td>
                        <td><?php echo $value['category'] ?></td>
                        <td><?php echo $value['capacity'] ?></td>
                        <td><?php echo $value['customer_total'] ?></td>
                        <td><?php echo $value['partner_payable_basic'] ?></td>
                        <td><?php echo $value['customer_net_payable'] ?></td>
                        <td><?php echo $value['vendor_total'] ?></td>
                        <td><?php echo $value['vendor_basic_percentage'] ?></td>
                        <td><?php echo $value['pod'] ?></td>
                        <td><?php echo $value['is_upcountry'] ?></td>
                    </tr>
            <?php $i++;
        } ?>

            </tbody>
        </table>


    <?php } else { ?> 
        <div class="alert alert-danger text-center" id="data-not-found" style="margin: 10px;"> No data found</div>
    <?php }
} ?> 
<script>
    $(document).ready(function(){  
        $('#partner_price_data').DataTable({
            "dom": 'Bfrtip',
         
           "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
             
                "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',
                    title: 'Model List', 
                    exportOptions: { 
                       columns: [0,1,2,3,4,5,6,7,8,9,10],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
        });
        
        $("#partner_price_data_filter").css("float", "right");
    });
</script>
