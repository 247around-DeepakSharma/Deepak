<style type="text/css">
    #parttype_table_filter{
        float: right;
    }
</style>
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0 40px;">
            <div class="x_panel">
                <div class="x_title">
                    <h3>
                        Part Type Return Mapping
                    </h3>
                    <hr>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="x_content_header">
                        <section>
                            <div class="row">
                                <div class="form-inline">
                                    <div class="form-group col-md-4">
                                        <select class="form-control" id="partner_id">
                                            <option value="" disabled="">Select Partner</option>
                                        </select>
                                    </div>                                   
                                    <div class="form-group col-md-4">
                                        <select class="form-control" id="service_id">
                                            <option value="" disabled="">Select Appliance</option>
                                        </select>
                                    </div>
                                    <button class="btn btn-success col-md-2" id="get_part_type_return_data">Submit</button>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="parttype_list">
                        <table id="parttype_table" class="table table-responsive table-hover" width="100%"></table>
                        <button class="btn btn-success col-md-2" id="add_part_type">Add Part Type</button>
                        <button class="btn btn-success col-md-2" id="edit_part_type">Edit Part Type</button>
                    </div>
                </div>
            </div>
        </div>
</div>
<script>

    var actionType = null,
        appliance_id = null,
        partner_id = null;


    $(document).ready(function(){
        $('#service_id').select2({
            allowClear: true,
            placeholder: 'Select Appliance'
        });
        get_partner_list();
        $('#parttype_table').hide();
        $('#add_part_type').hide();
        $('#edit_part_type').hide();

        actionType = '<?php echo $action; ?>';
    });

    function get_partner_list(){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/partner/get_partner_list',
            data:{is_wh:false},
            success: function (response) {
                $("#partner_id").html(response);
                var option_length = $('#partner_id').children('option').length;
                if(option_length == 2){
                 $("#partner_id").change();   
                }
                $("#on_partner_id").html(response);
                var option_length = $('#on_partner_id').children('option').length;
                if(option_length == 2){
                 $("#on_partner_id").change();   
                }
                $('#partner_id').select2();
            }
        });
    }

    function get_appliance(partner_id){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/service_centre_charges/get_partner_data',
            data:{partner:partner_id},
            success: function (response) {
                $('#service_id').html(response);
                $('#service_id').select2();
            }
        });
    }

    function updateCheckboxValue(data){
        ( $(data).val() == 0 ) ? $(data).val(1) : $(data).val(0);
    }

    function add_part_type(){
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url()?>employee/inventory/get_parttype_data/add',
                data: {partner_id : partner_id, appliance_id: appliance_id},
                success : function(response) {
                    var data = JSON.parse(response);
                    if(Array.isArray(data)){
                        $('#add_part_type').show();
                        var tableData = [];
                        data.forEach(function(row,index){
                            var tmpArr = [];
                            tmpArr.push(index+1,"<span>"+row['part_type']+"</span>"+ "<span style='display:none'>"+row['id']+"</span>","<input type='checkbox'>");
                            tableData.push(tmpArr);
                        });
                        $('#parttype_table').show();
                        $('#parttype_table').dataTable({
                            destroy : true,
                            data : tableData,
                            columns: [
                                { title: "S.No" },
                                { title: "Part Type" },
                                { title: "Is Return" },
                            ]
                        });
                    }else{
                        alert('No data found');
                    }
                }
            });
    }

    function edit_part_type(){
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url()?>employee/inventory/get_parttype_data/edit',
                data: {partner_id : partner_id, appliance_id: appliance_id},
                success : function(response) {
                    var data = JSON.parse(response);
                    if(Array.isArray(data)){
                        $('#edit_part_type').show();
                        var tableData = [];
                        data.forEach(function(row,index){
                            var tmpArr = [];
                            var isReturn = '0';
                            var isChecked = '';
                            if(row['is_return'] == 1){
                                isReturn = '1';
                                isChecked = 'checked';
                            }
                            tmpArr.push(index+1,"<span>"+row['part_type']+"</span>"+ "<span style='display:none'>"+row['id']+"</span>","<input type='checkbox' value='" + isReturn +"' " + isChecked +" onClick= 'updateCheckboxValue(this)'>");
                            tableData.push(tmpArr);
                        });
                        $('#parttype_table').show();
                        $('#parttype_table').dataTable({
                            destroy : true,
                            data : tableData,
                            columns: [
                                { title: "S.No" },
                                { title: "Part Type" },
                                { title: "Is Return" },
                            ]
                        });
                    }else{
                        alert('No data found');
                    }
                }
            });
    }

    function sendAddDataTOServer(data){
        $('#add_part_type').html("Processing...").attr("disabled", true);
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url()?>employee/inventory/add_part_type_mapping',
                data: {dataToAdd : data},
                success : function(response) {
                    $('#add_part_type').html("Add Part Type").attr("disabled", false);
                    
                    if(response == 'success'){
                        alert("Details Added Successfully");
                    }else{
                        alert("Something went wrong, Please Try Again");
                    }
                }
            });
    }

    function sendEditDataTOServer(data){
        $('#edit_part_type').html("Processing...").attr("disabled", true);
        $.ajax({
                type: 'POST',
                url: '<?php echo base_url()?>employee/inventory/update_part_type_mapping',
                data: {dataToEdit : data},
                success : function(response) {
                    $('#edit_part_type').html("Edit Part Type").attr("disabled", false);
                    
                    if(response == 'success'){
                        alert("Details Edited Successfully");
                    }else{
                        alert("Something went wrong, Please Try Again");
                    }
                }
            });
    }


    $('#partner_id').on('change',function(){
        partner_id = $('#partner_id').val();
        if(partner_id){
            get_appliance(partner_id);
        }else{
            alert('Please Select Partner');
        }
    });

    $('#get_part_type_return_data').on('click',function(){
        partner_id = $('#partner_id').val();
        appliance_id = $('#service_id').val();
        if(partner_id && appliance_id) {

            if(actionType == 'add'){
                add_part_type();
            }else{
                edit_part_type();
            }
        }else{
            alert('Please Select Partner And Appliance Both');
        }
    });

    $("#add_part_type").click(function () {

        var dataToSend = [];
        //Loop through all checked CheckBoxes in GridView.
        $("#parttype_table input[type=checkbox]:checked").each(function () {
            var spans = $(this).closest("tr").find('span');
            var part_type = spans.eq(0).text();
            var inventory_id = spans.eq(1).text();
            var tmpArr = {"partner_id" : partner_id , "appliance_id": appliance_id , "inventory_id": inventory_id,"part_type":part_type,"is_return": 1};
            dataToSend.push(tmpArr);
        });

        if(dataToSend.length > 0) {
            sendAddDataTOServer(dataToSend);
        }else{
            alert("No Part Type Seleted");
        }

        return false;
    });

    $("#edit_part_type").click(function () {

        var dataToSend = [];
        //Loop through all checked CheckBoxes in GridView.
        $("#parttype_table input[type=checkbox]").each(function () {
            var value = $(this).val();
            var spans = $(this).closest("tr").find('span');
            var part_type = spans.eq(0).text();
            var mappingId = spans.eq(1).text();
            var tmpArr = {"part_type":part_type,"id": mappingId, "is_return" : value};
            dataToSend.push(tmpArr);
        });

        if(dataToSend.length > 0) {
            sendEditDataTOServer(dataToSend);
        }else{
            alert("No Part Type Seleted");
        }

        return false;
    });

</script>