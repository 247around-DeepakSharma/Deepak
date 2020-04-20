<style>
    .select2-container--default .select2-selection--single{
        border-radius: 0px!important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        top: 7px!important;
    }
    .alert{
        padding: 5px!important;
    }
    label{
        font-size: 12px;
    }
</style>

<div class="right_col" role="main">
    <div class="price_charges_file">
        <div class="page-title">
            <div class="title_left">
                <h3>Buyback charges List</h3>

            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                    <div class="x_content">
                        <section class="filterPriceList">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="cp_id" class="col-md-12 col-sm-12 col-xs-12">CP Name</label>
                                    <select class="form-control label-control col-md-12 col-sm-12 col-xs-12" id="cp_id">
                                        <option selected="" disabled="">Select CP</option>
                                        <?php foreach ($cp_list as $value) { ?> 
                                            <option value="<?php echo $value['cp_id'] ?>"><?php echo $value['cp_name'] ?> )</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-12 col-xs-12">
                                    <label for="service_id" class="col-md-12 col-sm-12 col-xs-12">Appliance</label>
                                    <select class="form-control label-control col-md-12 col-sm-12 col-xs-12" id="service_id">
                                        <option selected="" disabled="">Select Appliance</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-12 col-xs-12">
                                    <label for="physical_condition" class="col-md-12 col-sm-12 col-xs-12">Physical Condition</label>
                                    <select class="form-control label-control col-md-12 col-sm-12 col-xs-12" id="physical_condition">
                                        <option selected="" disabled="">Select Physical Condition</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-12 col-xs-12">
                                    <label for="working_condition" class="col-md-12 col-sm-12 col-xs-12">Working Condition</label>
                                    <select class="form-control label-control col-md-12 col-sm-12 col-xs-12" id="working_condition">
                                        <option selected="" disabled="">Select Working Condition</option>
                                    </select>
                                </div>
                            </div>
                        </section>
                        <div class="col-md-12"><center><img id="loader_gif" src="" style="display: none; margin-top: 30px;"></center></div>
                        <section id="filterPriceListData" style="margin-top: 20px;"></section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#cp_id").select2();
    $("#service_id").select2();
    $("#physical_condition").select2();
    $("#working_condition").select2();

    $('#cp_id').on('change', function () {
        var cp_id = $('#cp_id').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>buyback/buyback_process/get_bb_cp_appliance',
            data: {'cp_id': cp_id},
            success: function (response) {
                //console.log(response);
                $('#service_id').val('val', "");
                $('#service_id').val('Select Appliance').change();
                $('#service_id').select2().html(response);
                $('#working_condition').val('val', "");
                $('#working_condition').val('Select Working Condition').change();
                $('#working_condition').select2().html('<option selected disabled  >Select Working Condition</option>');

            }
        });
    });

    $('#service_id').on('change', function () {
        var postdata = {};
        postdata['cp_id'] = $('#cp_id').val();
        postdata['service_id'] = $('#service_id').val();
        if (postdata) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>buyback/buyback_process/get_bb_charges_physical_condition',
                data: postdata,
                success: function (html) {
                    $('#physical_condition').val('val', "");
                    $('#physical_condition').val('Select Physical Condition').change();
                    $('#physical_condition').select2().html(html);
                    $('#working_condition').val('val', "");
                    $('#working_condition').val('Select Working Condition').change();
                    $('#working_condition').select2().html('<option selected disabled  >Select Working Condition</option>');
                }
            });
        }
    });
    
    $('#physical_condition').on('change', function () {
        var postdata = {};
        postdata['cp_id'] = $('#cp_id').val();
        postdata['service_id'] = $('#service_id').val();
        postdata['physical_condition'] = $('#physical_condition').val();
        if (postdata) {
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>buyback/buyback_process/get_bb_charges_working_condition',
                data: postdata,
                success: function (html) {
                    $('#working_condition').val('val', "");
                    $('#working_condition').val('Select Working Condition').change();
                    $('#working_condition').select2().html(html);
                }
            });
        }
    });

    $('#working_condition, #cp_id').on('change', function () {
        var postdata = {};
        postdata['cp_id'] = $('#cp_id').val();
        postdata['service_id'] = $('#service_id').val();
        postdata['physical_condition'] = $('#physical_condition').val();
        postdata['working_condition'] = $('#working_condition').val();

        if (postdata) {
            $('#loader_gif').css('display', 'inherit');
            $('#loader_gif').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url() ?>buyback/buyback_process/get_bb_price_list',
                data: postdata,
                success: function (data) {
                    $('#loader_gif').attr('src', "");
                    $('#loader_gif').css('display', 'none');
                    $("#filterPriceListData").html(data);
                }
            });
        }
    });
</script>