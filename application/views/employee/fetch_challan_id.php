<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
           
            <div class="col-md-6 ">
                <h1 class="page-header"><b>Fetch Challan ID</b></h1>
            </div>
        </div>
        <div class="row" >
            <div class="form-group col-md-6">
                <label for="state" class="col-sm-2">Select</label>
                <div class="col-md-10">
                    <select class="form-control" name ="service_center" id="vendor" onchange="get_challan_id()">
                        <option disabled selected >Service Center</option>
                        <?php
                            foreach ($service_center as $vendor) {
                            ?>
                        <option value = "<?php echo $vendor['sc_code']?>">
                            <?php echo $vendor['name'];?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-md-offset-3"><img src="" id="loader_gif" /></div>
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12 ">
                <div class="form-group col-md-6">
                <div id="challand_id"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $("#vendor").select2();
    
    function get_challan_id(){
        var sc_code =  $("#vendor").val();
        $("#challand_id").html("");
        $.ajax({
          type: 'POST',
          url: '<?php echo base_url(); ?>employee/vendor/get_challan_id/'+sc_code,
          success: function (challanID) {

            $('#loader_gif').attr('src', '');
            $("#challand_id").html("<span style='font-weight:bold'>Challan ID: </span>" + challanID);
         }
       });
    }
</script>