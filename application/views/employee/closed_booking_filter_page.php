<div class="table_filter">
    <div class="row">
        <div class="col-md-3">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" id="partner_id">
                        <option value="" selected="selected" disabled="">Select Partner</option>
                        <?php foreach($partners as $val){ ?>
                        <option value="<?php echo $val['id']?>"><?php echo $val['public_name']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" id="sf_id">
                        <option value="" selected="selected" disabled="">Select Service Center</option>
                        <?php foreach($sf as $val){ ?>
                        <option value="<?php echo $val['id']?>"><?php echo $val['name']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" id="appliance">
                        <option value="" selected="selected" disabled="">Select Services</option>
                        <?php foreach($services as $val){ ?>
                        <option value="<?php echo $val->id?>"><?php echo $val->services?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" id="ratings">
                        <option value="" selected="selected" disabled="">Select Rating</option>
                        <option value="a">Booking With Ratings</option>
                        <option value="b">Booking Without Ratings</option>
                        <option value="c">All</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <select class="form-control filter_table" id="city">
                        <option value="" selected="selected" disabled="">Select City</option>
                        <?php foreach($cities as $val){ ?>
                        <option value="<?php echo $val['city']?>"><?php echo $val['city']?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="item form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <input type="text" class="form-control" id="closed_date" placeholder="Completion Date">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#partner_id').select2({
           placeholder: "Select Partner",
           allowClear: true
       });
       $('#sf_id').select2({
           placeholder: "Select Service Center",
           allowClear: true
       });
       $('#appliance').select2({
           placeholder: "Select Appliance",
           allowClear: true
       });
       $('#city').select2({
           placeholder: "Select City",
           allowClear: true
       });
       $('#ratings').select2({
           placeholder: "Select Rating",
           allowClear: true
       });
        $('.filter_table').on('change', function(){
               datatable1.ajax.reload();
           });
           
        $('#closed_date').daterangepicker({
               autoUpdateInput: false,
               locale: {
                   cancelLabel: 'Clear'
               }
           });
           $('#closed_date').on('apply.daterangepicker', function (ev, picker) {
               $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
               datatable1.ajax.reload();
           });
           
           $('#closed_date').on('cancel.daterangepicker', function(ev, picker) {
               $(this).val('');
               datatable1.ajax.reload();
           });
</script>