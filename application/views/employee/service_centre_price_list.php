<script type="text/javascript" src="<?php echo base_url();?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/jquery-ui-1.7.1.custom.min.js"></script>
<style type="text/css">
    table{
        width: 99%;
        padding: 0;
        margin: 0;
        border: 0;
        border-collapse: collapse;
    }
    th,td{
        border: 1px #f2f2f2 solid;
        text-align:center;
        vertical-align: center;
        font-size: 14;
        margin:0px; 
        padding:0px; 
    }

    th{
        height: 30px;
        background-color: #4CBA90;
        color: white;
    }


</style>

<div id="page-wrapper" style="width:140%;">
  <div class="">
      <div class="row">

        <div class="form-group" style="float:left;width:200px;padding-left:20px;">
          <label for="service_name" style="color:red;"><b>Select Appliance</b></label>
          <div>
            <select type="text" class="form-control"  id="service_id" name="service_id" 
                value = "<?php echo set_value('service_id'); ?>" 
              onChange="display_charges_for_particular_appliance(this.value);">
              <option>Select Appliance</option>
              <?php foreach ($services as $key => $values) { ?>
              <option  value=<?= $values->id; ?>>
                <?php echo $values->services; }    ?>
              </option>
              <?php echo form_error('service_id'); ?>
            </select>
          </div>
        </div>

        <div style="float:left;width:100%;margin-left:10px;margine-right:5px;">
          <h1 align="left">Service Centres Charges</h1>
          <table cellpadding="0" cellspacing="0" border-collapse="collapse">
            <thead>
            <tr>
              <th width="10%;">Category</th>
              <th width="10%;">Capacity</th>
              <th width="15%;">Service Category</th>
              <th width="5%;">Total Charges</th>
              <th width="5%;">Vendor Price</th>
              <th width="5%;">Around Markup</th>
              <th width="5%;">Service Charges</th>
              <th width="5%;">Service Tax</th>
            </tr>
            </thead>
          </table>
          <table cellpadding="0" cellspacing="0" border-collapse="collapse" id="prices">
          </table>
        </div>
    </div>
  </div>
</div>

<script>
    function display_charges_for_particular_appliance(service_id)
    {
       $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centre_charges/display_charges_for_particular_appliance/'+ service_id,
               success: function (data) {
                $('#prices').html(data);   
               }
            });
    }
</script>
