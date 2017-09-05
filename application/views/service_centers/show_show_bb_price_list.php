<style>
    .select2-container--default .select2-selection--single{
        border-radius: 0px!important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        top: 0px!important;
    }
    .alert{
        padding: 5px!important;
    }
    label{
        font-size: 12px;
    }
</style>

<div class="right_col" role="main" id="page-wrapper">
    <div class="price_charges_file">
        <div class="page-title">
            <div class="title_left">
                <h3>Buyback charges List</h3>
            </div>
        </div>
        <hr>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">

                    <div class="x_content">
                        <section class="filterPriceList">
                            <div class="row">
                                <div class="col-md-3 col-sm-12 col-xs-12">
                                    <select class="form-control label-control col-md-9 col-sm-12 col-xs-12" id="service_id">
                                        <option selected="" disabled="">Select Appliance</option>
                                        <?php foreach($appliance_list as $appliace){ ?>
                                        <option value="<?php echo $appliace['service_id']?>"><?php echo $appliace['services']?></option>
                                        <?php }?>
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
    var cp_id = '<?php echo $this->session->userdata('service_center_id')?>';
    
    $("#service_id").select2();
    
    $(document).ready(function(){
        var postdata = {};
        postdata['cp_id'] = cp_id;
        postdata['service_id'] = '';
        postdata['physical_condition'] = '';
        postdata['working_condition'] = '';
        postdata['is_hide_field'] = true;

        sd(postdata);
    });

    $('#service_id').on('change', function () {
        var postdata = {};
        postdata['cp_id'] = cp_id;
        postdata['service_id'] = $('#service_id').val();
        postdata['physical_condition'] = '';
        postdata['working_condition'] = '';
        postdata['is_hide_field'] = true;
        sd(postdata);
    });

    function sd(postdata){
        $('#loader_gif').css('display', 'inherit');
        $('#loader_gif').attr('src', "<?php echo base_url(); ?>images/loadring.gif");
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/service_centers/get_bb_price_list',
            data: postdata,
            success: function (data) {
                $('#loader_gif').attr('src', "");
                $('#loader_gif').css('display', 'none');
                $("#filterPriceListData").html(data);
            }
        });
    }
</script>