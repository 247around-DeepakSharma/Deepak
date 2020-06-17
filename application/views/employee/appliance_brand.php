<table class="table table-bordered table-condensed table-hover">
    <thead><tr><th width="5%">S.No.</th><th width="35%">Appliance</th><th width="60%">Brands</th></tr></thead>
    <tbody>
        <?php foreach ($appliances as $key => $appliance) {
            $appliance_id = $this->reusable_model->get_search_result_data('services', 'id', ['trim(lower(services))' => trim(strtolower($appliance))], NULL, NULL, NULL, NULL, NULL)[0]['id'];
            $appliance_brands = $this->reusable_model->get_search_result_data('appliance_brands', 'id, brand_name', ['service_id' => $appliance_id], NULL, NULL, ['brand_name' => SORT_ASC], NULL, NULL);
        ?>
            <tr>
                <td width="5%"><?php echo ++$key; ?>.</td>
                <td width="35%">
                    <input type="hidden" name="appliance_id[]" value="<?php echo $appliance_id; ?>">
                    <input type="text" class="form-control" name="appliance[]" readonly value="<?php echo $appliance; ?>">
                </td>
                <td width="60%">
                    <select name="brands[<?php echo $appliance_id; ?>][]" class="brand" id="brand_<?php echo $key; ?>" multiple>
                        <option value=""></option>
                        <?php if(!empty($appliance_brands)) { 
                            foreach ($appliance_brands as $brand) { ?>
                            <option <?php echo (!empty($sf_brands[$appliance_id]) && in_array($brand['id'], $sf_brands[$appliance_id]) ? 'selected' : ''); ?> value="<?php echo $brand['id'].'-'.$brand['brand_name']; ?>"><?php echo $brand['brand_name']; ?></option>
                        <?php } } ?>
                    </select>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<script>
    $('.brand').select2({
        multiple: true,
        placeholder: 'Select Brand'
    });
    
    $('.brand').on('change', function() {
        if($(this).val().includes('all')) {
            $(this).children('option').hide();
            $(this).children("option[value='all']").show();
        }
    });   
</script>
<style>
    .select2 {
        width:100% !important;
    }
</style>