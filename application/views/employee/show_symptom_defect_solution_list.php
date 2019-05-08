<style>
    #mapping_list_filter{
        float: right;
    }
</style>
<div  id="page-wrapper">
    <div class="row">
        <div class="row">
            <h1 class="col-md-6 col-sm-12 col-xs-12"><b>Symptom Defect Solution Mapping</b></h1>
        
            <?php if($this->session->userdata('user_group') != 'closure'){?>
                <div class="col-md-6 col-sm-12 col-xs-12" style="margin-top: 30px;margin-bottom: 10px;">
                    <a href="<?php echo base_url();?>employee/booking_request/add_symptom_defect_solution"><input class="btn btn-primary pull-right" type="Button" value="Add Symptom Defect Solution Mapping"></a>
                </div>
            <?php }?>
        </div>
        <?php
        if ($this->session->flashdata('success_msg')) {
            echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('success_msg') . '</strong>
                    </div>';
        }
        if ($this->session->flashdata('error_msg')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->flashdata('error_msg') . '</strong>
                    </div>';
        }
        ?>
        <hr>
        <div class="row">
            <div class="mapping_listing container-fluid">
                <table id="mapping_list" class="table table-bordered table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Service</th>
                            <th>Request Type</th>
                            <th>Symptom</th>
                            <th>Defect</th>
                            <th>Solution</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        
</div>

<!--Modal start [ update spart part symptom ]-->
<div id="update_symptom" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title" id="modal_title_action">Update Symptom Defect Solution Mapping </h4>
          </div>
          <div class="modal-body">

              <form class="form-horizontal">
                 <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label col-md-4" for="model_service_id">Service *</label>
                              <div class="col-md-6">
                                  <select class="form-control" id="model_service_id" name="model_service_id" onchange="getPriceTags();get_symptoms();get_defects();get_solutions();">;
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label col-md-4" for="model_request_type">Request Type *</label>
                              <div class="col-md-8">
                                  <select class="form-control" id="model_request_type" name="model_request_type">
                                      <option selected disabled>Select Request Type</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label col-md-4" for="model_symptom">Symptom *</label>
                              <div class="col-md-6">
                                  <select class="form-control" id="model_symptom" name="model_symptom">
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label col-md-4" for="model_defect">Defect *</label>
                              <div class="col-md-8">
                                  <select class="form-control" id="model_defect" name="model_defect">
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label col-md-4" for="model_solution">Solution *</label>
                              <div class="col-md-6">
                                  <select class="form-control" id="model_solution" name="model_solution">
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <input type="hidden" id="symptom_solution_mapping">
                      <button type="button" class="btn btn-success" onclick="update_symptom_solution_mapping()">Update</button>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>
<!--Modal end-->

<?php if ($this->session->flashdata('success_msg')) {$this->session->unset_userdata('success_msg');} ?>
<?php if ($this->session->flashdata('error_msg')) {$this->session->unset_userdata('error_msg');} ?>
<script type="text/javascript">
    var id = '<?php echo $id;?>';
    $(document).ready(function () {
         
        //datatables
        $('#mapping_list').DataTable({
            "processing": true, 
            "serverSide": true,
            "order": [],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50,100, 500, -1], [10, 25, 50, 100, 500, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Export',
                    pageSize: 'LEGAL',
                    title: 'symptom_defect_solution_data',
                    exportOptions: {
                       columns: [0,1,2,3,4,5],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "ajax": {
                "url": "<?php echo base_url(); ?>employee/booking_request/get_symptom_defect_solution_mapping",
                "type": "POST",
                "data": {"id": id}
                
            },
            "columnDefs": [
                {
                    "targets": [0,6], 
                    "orderable": false 
                }
            ]
            
        });
    });
    
    function update_status(status, id){
        var status_val = ((status == 0)?"Active":"Deactive");
        var cnfrm = confirm("Are you sure, you want to make this mapping "+status_val+" ?");
        if(!cnfrm){
            return false;
        }
        var is_active = 0;
        if(status == "0"){
           is_active = 1; 
        }
        var data = {};
        data.is_active = is_active;
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking_request/update_symptom_defect_solution', 
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
                   $("#model_service_id option[value=all]").remove();
                   //$('#model_service_id').select2();
                }else{
                   //console.log(response);
                }
           }
        });
    }
    
    function get_symptoms(){
        var service_id=$("#model_service_id").val();
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url() ?>employee/booking_request/get_symptoms',
           async: false,
           data: {'where':{'service_id':service_id}, 'is_option_selected':false},
           success: function (response) {
               if(response){
                   $('#model_symptom').html("");
                   $('#model_symptom').append(response);
                   $("#model_symptom option[value=all]").remove();
                   //$('#model_service_id').select2();
                }else{
                   //console.log(response);
                }
           }
        });
    }
    
    function get_defects(){
        var service_id=$("#model_service_id").val();
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url() ?>employee/booking_request/get_defects',
           async: false,
           data: {'where':{'service_id':service_id}, 'is_option_selected':false},
           success: function (response) {
               if(response){
                   $('#model_defect').html("");
                   $('#model_defect').append(response);
                   $("#model_defect option[value=all]").remove();
                }
           }
        });
    }
    
    function get_solutions(){
        var service_id=$("#model_service_id").val();
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url() ?>employee/booking_request/get_solutions',
           async: false,
           data: {'where':{'service_id':service_id}, 'is_option_selected':false},
           success: function (response) {
               if(response){
                   $('#model_solution').html("");
                   $('#model_solution').append(response);
                   $("#model_solution option[value=all]").remove();
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
                if(request_type){
                    $("#model_request_type").val(request_type).trigger("change");
                }
            }
        });
    }
    
    function update_symptom_defect_solution(btn, id) {
        var data = JSON.parse($(btn).attr("data-id"));
        $("#symptom_solution_mapping").val(id);
        $("#model_service_id").val(data.service).trigger("change");
        getPriceTags(data.request_type);
        $("#model_request_type").val(data.request_type);
        get_symptoms();
        get_defects();
        get_solutions();
        $("#model_symptom").val(data.symptom);
        $("#model_defect").val(data.defect);
        $("#model_solution").val(data.solution);
        $('#update_symptom').modal('toggle');
    }
    
    function update_symptom_solution_mapping(){
        if(!$("#model_request_type").val()){
            alert("Please Select Request Type");
        }
        else if(!$("#model_service_id").val()){
           alert("Please Select Appliance"); 
        }
        else if(!$("#model_symptom").val()){
           alert("Please Select Symptom"); 
        }
        else if(!$("#model_defect").val()){
           alert("Please Select Defect"); 
        }
        else if(!$("#model_solution").val()){
           alert("Please Select Solution"); 
        }
        else{
            var data = {};
            data.request_id = $("#model_request_type").val();
            data.product_id = $("#model_service_id").val();
            data.symptom_id = $("#model_symptom").val();
            data.defect_id = $("#model_defect").val();
            data.solution_id = $("#model_solution").val();
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking_request/update_symptom_defect_solution', 
                data:{data:data, id : $("#symptom_solution_mapping").val()},
                success: function (response) {
                    if(response){
                        alert("Updated Successfully");
                        location.reload();
                    }
                    else{
                        alert("Data Already Exist");
                    }
                }
            }); 
        }
    }
    get_appliance();
    /*get_symptoms();
    get_defects();
    get_solutions();*/
</script>