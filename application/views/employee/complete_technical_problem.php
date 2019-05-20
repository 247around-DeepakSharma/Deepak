<div class = "container">
    <div class="panel-group">
        <div class="panel panel-info">
            <div class="panel-heading">
             <h4>   Symptom List
                <a class="btn btn-primary pull-right btn-md" href ="<?php echo base_url();?>employee/booking_request/add_new_completion_technical_problem">Add Symptom</a>
             </h4>
            </div>
            <div class="panel-body">
                <table class = "table table-condensed table-bordered table-striped table-responsive">
                    <tr>
                        <th>S No</th>
                        <th>Service</th>
                        <th>Request Type</th>
                        <th>Symptom</th>
                        <th>Update</th>
                    </tr>
                    <?php
                        if (!empty($data)) {
                            foreach ($data as $key => $row) {
                            $jsonData = json_encode(array("service" => $row['service_id'], "request_type"=> $row['request_type_id'], "symptom" => $row['symptom']));    
                        ?>
                    <tr>
                        <td><?php echo ($key +1) ?></td>
                        <td><?php echo $row['services'];?>
                        <td><?php echo $row['service_category']; ?></td>
                        <td><?php echo $row['symptom'];?></td>
                        <td>
                            <a href="#" class="btn btn-md btn-success" data-id='<?php echo $jsonData; ?>' onclick="update_spare_sympton(this, <?php echo $row['id']; ?>)">Update</a>
                            <a href="#" class="btn btn-md btn-warning" onclick="update_status(<?php echo $row['active']; ?>, <?php echo $row['id']; ?>)"><?php if($row['active'] == "0"){ echo "Active"; }else{ echo "Deactive"; } ?></a>
                        </td>
                    </tr>
                    <?php }
                        } else { ?>
                    <tr>
                        <td>"no data found"</td>
                    </tr>
                    <?php
                        }
                        ?>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<!--Modal start [ update spart part symptom ]-->
      <div id="update_symptom" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title_action">Spare Request Symptom </h4>
                </div>
                <div class="modal-body">

                    <form class="form-horizontal">
                       <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="model_service_id">Service*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="model_service_id" name="model_service_id" onchange="getPriceTags()">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="model_request_type">Request Type*</label>
                                    <div class="col-md-7 col-md-offset-1">
                                        <select class="form-control" id="model_request_type" name="model_request_type">
                                            <option selected disabled>Select Request Type</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3" for="model_symptom">Symptom*</label>
                                    <div class="col-md-9 col-md-offset-1" style="padding: 0px; margin-left: 58px;">
                                        <textarea id="model_symptom" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="symptom_completion_request">
                            <button type="button" class="btn btn-success" onclick="update_spare_symptom()">Update</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--Modal end-->

<script>
    function update_status(status, id){
        var is_active = 0;
        if(status == "0"){
           is_active = 1; 
        }
        var data = {};
        data.active = is_active;
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking_request/update_symptom_completion_request', 
            data:{data:data, id : id},
            success: function (response) {
                if(response){
                   alert("Status Updated Successfully");
                   location.reload();
                }
                else{
                    alert("Error Occured while Updating Status");
                }
            }
        }); 
    }
    
    function get_appliance(){
            $.ajax({
               type: 'GET',
               url: '<?php echo base_url() ?>employee/booking/get_service_id',
               data: {'is_option_selected':false},
               success: function (response) {
                   if(response){
                       $('#model_service_id').append(response);
                       //$('#model_service_id').select2();
                    }else{
                       //console.log(response);
                    }
               }
            });
    }
    
    function getPriceTags(request_type){
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/service_centre_charges/get_service_category_request_type',
            data: {'service_id':$("#model_service_id").val()},
            success: function (response) {
                //console.log(response);
                $('#model_request_type').html("");
                $('#model_request_type').append("<option selected disabled>Select Service category</option>").change();
                $('#model_request_type').append(response);
                $('#model_request_type').select2();
                if(request_type){
                    $("#model_request_type").val(request_type).trigger("change");
                }
            }
        });
    }
    
    function update_spare_sympton(btn, id){
        var data = JSON.parse($(btn).attr("data-id"));
        $("#symptom_completion_request").val(id);
        $("#model_service_id").val(data.service).trigger("change");
        getPriceTags(data.request_type);
        $("#model_symptom").text(data.symptom);
        $('#update_symptom').modal('toggle');
    }
    
    function update_spare_symptom(){
        if(!$("#model_request_type").val()){
            alert("Please Select Request Type");
        }
        else if(!$("#model_symptom").val()){
           alert("Please Enter Completion Request Symptom"); 
        }
        else{
            var data = {};
            data.request_type = $("#model_request_type").val();
            data.symptom = $("#model_symptom").val();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking_request/update_symptom_completion_request', 
                data:{data:data, id : $("#symptom_completion_request").val()},
                success: function (response) {
                    if(response){
                        alert("Completion Request Symptom Updated Successfully");
                        location.reload();
                    }
                    else{
                        alert("Symptom Already Exist");
                    }
                }
            }); 
        }
    }
    get_appliance();
</script>