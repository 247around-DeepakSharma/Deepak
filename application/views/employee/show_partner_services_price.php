<?php if (isset($partners)) { ?>
    <div id="page-wrapper" >
        <div class="container-fluid" >
            <div class="panel panel-info" style="margin-top:20px;">
                <div class="panel-heading"><h3>Show Partner Price </h3> </div>
                <div class="panel-body">
                    <table class="table  table-striped table-bordered">
                        <tr>
                            <th style="width: 22%"> 
                                <select class="form-control" id="partners" name="partners" >
                                    <option selected disabled>Select Partner</option>
                                    <?php foreach ($partners as $key => $value) { ?>

                                        <option value="<?php echo $value['price_mapping_id'] ?>"> <?php echo $value['source']; ?></option>

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

        $('#partners').on('change', function () {
            var partner = $(this).val();
            if (partner) {

                $.ajax({
                    type: 'POST',
                    url: '<?php echo base_url() ?>employee/service_centre_charges/get_partner_data',
                    data: 'partner=' + partner,
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
            postdata['price_mapping_id'] = $('#partners').val();
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
            }
        });

        $('#service_category, #partners').on('change', function () {
            var postdata = {};
            postdata['price_mapping_id'] = $('#partners').val();
            postdata['service_id'] = $('#service_id').val();
            postdata['service_category'] = $('#service_category').val();

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

        <table class="table table-striped table-bordered" >
            <th>No.</th>
            <th>Service Category</th>
            <th>Category</th>
            <th>Capacity</th>
            <th>Customer Total</th>
            <th>Partner Payable Basic</th>
            <th>Customer Net Payable</th>
            <th>Vendor Total</th>
            <th>POD</th>
            <th>Upcountry</th>
            <tbody>
                <?php $i = 1;
                foreach ($price_data as $key => $value) { ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $value['service_category'] ?></td>
                        <td><?php echo $value['category'] ?></td>
                        <td><?php echo $value['capacity'] ?></td>
                        <td><?php echo $value['customer_total'] ?></td>
                        <td><?php echo $value['partner_payable_basic'] ?></td>
                        <td><?php echo $value['customer_net_payable'] ?></td>
                        <td><?php echo $value['vendor_total'] ?></td>
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

