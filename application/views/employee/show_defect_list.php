<style>
    #defect_list_filter{
        float: right;
    }
</style>
<div  id="page-wrapper">
    <div class="row">
        <div class="row">
            <h1 class="col-md-6 col-sm-12 col-xs-12"><b>Defect List</b></h1>
        
            <?php if($this->session->userdata('user_group') != 'closure'){?>
                <div class="col-md-6 col-sm-12 col-xs-12" style="margin-top: 30px;margin-bottom: 10px;">
                    <a href="<?php echo base_url();?>employee/booking_request/add_new_defect"><input class="btn btn-primary pull-right" type="Button" value="Add New Defect"></a>
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
            <div class="defect_listing container-fluid">
                <table id="defect_list" class="table table-bordered table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Partner</th>
                            <th>Service</th>
                            <th>Defect</th>
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

<!--Modal start [ update spart part defect ]-->
<div id="update_defect" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title" id="modal_title_action">Update Defect </h4>
          </div>
          <div class="modal-body">

              <form class="form-horizontal">
                 <div class="row">
                     <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label col-md-4" for="model_partner_id">Partner *</label>
                              <div class="col-md-6">
                                  <select class="form-control" id="model_partner_id" name="model_partner_id" onchange='get_appliance()'>
                                      <option selected disabled>Select Partner</option>
                                  </select>
                              </div>
                          </div>
                      </div> 
                     <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label col-md-4" for="model_service_id">Service *</label>
                              <div class="col-md-6">
                                  <select class="form-control" id="model_service_id" name="model_service_id" onchange='get_defects()'>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="control-label col-md-4" for="model_defect">Defect *</label>
                              <div class="col-md-6">
                                  <select class="form-control" id="model_defect" name="model_defect">
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <input type="hidden" id="defect_list">
                      <button type="button" class="btn btn-success" onclick="update_defect()">Update</button>
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
        $('#defect_list').DataTable({
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
                    title: 'defect_list',
                    exportOptions: {
                       columns: [0,1,2,3],
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
                "url": "<?php echo base_url(); ?>employee/booking_request/get_defect_data",
                "type": "POST",
                "data": {"id": id}
                
            },
            "columnDefs": [
                {
                    "targets": [0,4], 
                    "orderable": false 
                }
            ]
            
        });
    });
    
    function update_status(status, id){
        var status_val = ((status == 0)?"Active":"Deactive");
        var cnfrm = confirm("Are you sure, you want to make this defect "+status_val+" ?");
        if(!cnfrm){
            return false;
        }
        var active = 0;
        if(status == "0"){
           active = 1; 
        }
        var data = {};
        data.active = active;
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/booking_request/update_defect', 
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
        var partner_id=$("#model_partner_id option:selected").val();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url() ?>employee/booking/get_appliances/0',
            async: false,
            data: {'partner_id':partner_id},
            success: function (response) {
                response=JSON.parse(response);
                if((response.length != 0) && (response.services)){
                    $('#model_service_id').html(response.services);
                }
                else {
                    $('#model_service_id').html('<option selected disabled>Select Service</option>');
                    $('#model_service_id option:eq(0)').attr('selected', true);
                }
            }
        });
       
    }
    
    function get_defects(){
	var service_id=$("#model_service_id").val();
        var partner_id=$("#model_partner_id").val();
        $.ajax({
           type: 'POST',
           url: '<?php echo base_url() ?>employee/booking_request/get_defects',
           async: false,
           data: {'where':{'service_id':service_id, 'partner_id':partner_id}, 'is_option_selected':false},
           success: function (response) {
               if(response){
                   $('#model_defect').html(response);
                   $("#model_defect option[value=all]").remove();
                }
           }
        });
    }
    
    function get_partner() {
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            async: false,
            data:{'is_wh' : 1},
            success: function (response) {
                $('#model_partner_id').html(response);
                //$('#model_partner_id').select2();
            }
        });
    }
    
    function update_defect_data(btn, id) {
        var data = JSON.parse($(btn).attr("data-id"));
        $("#defect_list").val(id);
        $("#model_partner_id").val(data.partner_id);
        get_appliance();
        $("#model_service_id").val(data.service_id);
        get_defects();
        $("#model_defect option:contains('"+data.defect+"')").attr('selected', true);
        $('#update_defect').modal('toggle');
    }
    
    function update_defect(){
        if(!$("#model_partner_id").val()){
            alert("Please Select Partner");
        }
        else if(!$("#model_service_id").val()){
           alert("Please Select Appliance"); 
        }
        else if(!$("#model_defect option:selected").text()){
           alert("Please Select Defect"); 
        }
        else{
            var data = {};
            data.partner_id = $("#model_partner_id").val();
            data.service_id = $("#model_service_id").val();
            data.defect = $.trim($("#model_defect option:selected").text());
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking_request/update_defect', 
                data:{data:data, id : $("#defect_list").val()},
                success: function (response) {
                    if(response){
                        alert("Updated Successfully");
                        location.reload();
                    }
                    else{
                        alert("Defect Already Exist");
                    }
                }
            }); 
        }
    }
    
    get_partner();
</script>
