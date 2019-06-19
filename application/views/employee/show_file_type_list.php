<style>
    #file_type_list_filter{
        float: right;
    }
</style>
<div  id="page-wrapper">
    <div class="row">
        <h1 class="col-md-6 col-sm-12 col-xs-12"><b>File Types List</b></h1>
    </div>
    <?php
    if ($this->session->userdata('success')) {
        echo '<div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('success') . '</strong>
                    </div>';
    }
    if ($this->session->userdata('error')) {
        echo '<div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('error') . '</strong>
                    </div>';
    }
    ?>
    <hr>
    <div style="margin: 30px 10px;" class="row">
        <button class="btn" onclick="show_file_type()" style="background-color: #337ab7;color: #fff;margin-bottom: 10px;">Add File Type</button>
        <form name="file_type_form" class="form-horizontal" id ="file_type_form" action="<?php echo base_url() ?>employee/booking/process_file_type" method="POST" enctype="multipart/form-data" onsubmit="return process_file_type_validations()" style="display:none;">
                <?php
                if(isset($query[0]['id'])){
                    if($query[0]['id']){
                    ?>
                    <input type="hidden" id="partner_id" name="partner_id" value=<?php echo  $query[0]['id']?>>
                    <?php
                    }
                }
                ?>

        <div class="clonedInput panel panel-info " id="clonedInput">                      
            <div class="panel-heading">
                <p style="color: #000;"><b>Add File Type</b></p>
                <div class="clone_button_holder1" style="float:right;margin-top: -31px;">
                    <button class="clone btn btn-sm btn-info">Add</button>
                    <button class="remove btn btn-sm btn-info">Remove</button>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group ">
                            <div class="col-md-4 form-group <?php if (form_error('file_type')) {
                                echo 'has-error';
                                } ?>">
                                <label for="file_type" class="col-md-4">Support&nbsp;File&nbsp;Type&nbsp;*</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="file_type[]" id="file_type_1" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 32">
                                    <?php echo form_error('file_type'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 form-group <?php if (form_error('max_allowed_size')) {
                                echo 'has-error';
                                } ?>">
                                <label for="max_allowed_size" class="col-md-6">Maximum&nbsp;Allowed&nbsp;Size</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="max_allowed_size[]" id="max_allowed_size_1" onkeypress="return (event.charCode > 47 && event.charCode < 58)">
                                    <?php echo form_error('max_allowed_size'); ?>
                                </div>
                            </div>
                            <div class="col-md-4 form-group <?php if (form_error('allowed_type')) {
                                echo 'has-error';
                                } ?>">
                                <label for="allowed_type" class="col-md-4">Allowed&nbsp;Type</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="allowed_type[]" id="allowed_type_1">
                                        <option selected="" disabled="" value="">Select File Type</option>
                                        <option value="image/jpg">image/jpg</option>
                                        <option value="image/jpeg">image/jpeg</option>
                                        <option value="image/png">image/png</option>
                                        <option value="pdf">pdf</option>
                                        <option value="video/mp4">video/mp4</option>
                                        <option value="video/avi">video/avi</option>
                                    </select>
                                    <?php echo form_error('allowed_type'); ?>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>   
        </div>
        <div class="cloned"></div>

        <div class="form-group " style="text-align:center">
            <input type="submit" class="btn btn-primary" id="save" value="Submit">
        </div>
    </form>
    <hr>
    <?php
        if(!empty($file_type)){
            ?>
        <div class="row">
            <div class="file_listing container-fluid">
                <table id="file_type_list" class="table table-bordered table-responsive table-hover">
                    <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Support File Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $index = 0;
                            foreach($file_type as $value){
                                $index ++;
                            ?>
                        <tr>
                            <td><?php echo $index; ?></td>
                            <td><?php echo $value['file_type'] ?></td>
                            <td><?php if($value['is_active']) { ?>
                                <button type="button" class="btn btn-info btn-sm" onclick="activate_deactive('<?php echo $value['id'] ?>','0','Deactivate')"   value='' style="background: #ff4d4d;border: #ff4d4d;width: 79px;">Deactivate</button>
                           <?php } else {?>
                                <button type="button" class="btn btn-info btn-sm" onclick="activate_deactive('<?php echo $value['id'] ?>','1','Activate')"  value='' style="background: #468245;border: #468245; width: 79px;">Activate</button>
                           <?php } ?>
                            
                                <button type="button" class="btn btn-info btn-sm" onclick="create_edit_file_type_form('<?=$value['file_type']?>','<?=$value['max_allowed_size']?>','<?=$value['allowed_type']?>',<?=$value['id']?>)" data-toggle="modal"  id="edit_button">Update</button>
                            </td>
                        </tr>
                
                    <?php  }  ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
        }
        ?>
    </div>
</div>
<!--File Type Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="text-align: center;margin: 0px;">
                <button type="button" class="close btn-primary well" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit File Type</h4>
            </div>
            <div class="modal-body">
                <form name="edit_file_type_form" action="<?php echo base_url().'employee/booking/edit_file_type'?>" class="form-horizontal" id ="edit_file_type_form" method="POST" enctype="multipart/form-data" onsubmit="return edit_file_type_validations()">
                    <input type="hidden" id="file_type_id" name="file_type_id" >
                    <div class="row">
                        <div class="col-md-6">
                            <label for="file_type1" class="col-md-4">Support&nbsp;File&nbsp;Type&nbsp;*</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="file_type1" id="file_type1" onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode > 47 && event.charCode < 58) || event.charCode == 32">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="max_allowed_size1" class="col-md-6">Maximum&nbsp;Allowed&nbsp;Size</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="max_allowed_size1" id="max_allowed_size1" onkeypress="return (event.charCode > 47 && event.charCode < 58)">
                            </div>
                        </div> 
                    </div>
                    <div class="row" style="margin-top:5px;">
                        <div class="col-md-6">
                            <label for="allowed_type1" class="col-md-4">Allowed&nbsp;Type</label>
                            <div class="col-md-8">
                                <select class="form-control" name="allowed_type1" id="allowed_type1">
                                    <option selected="" disabled="" value="">Select File Type</option>
                                    <option value="image/jpg">image/jpg</option>
                                    <option value="image/jpeg">image/jpeg</option>
                                    <option value="image/png">image/png</option>
                                    <option value="pdf">pdf</option>
                                    <option value="video/mp4">video/mp4</option>
                                    <option value="video/avi">video/avi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class=" btn btn-success">Update</button>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--File Type Modal ends-->
<?php if ($this->session->userdata('success')) {$this->session->unset_userdata('success');}
if ($this->session->userdata('error')) {$this->session->unset_userdata('error');}
?>
<script>
    $(document).ready(function () {
         
        //datatables
        $('#file_type_list').DataTable({
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Export',
                    pageSize: 'LEGAL',
                    title: 'file_type_list',
                    exportOptions: {
                       columns: [0,1],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "columnDefs": [
                {
                    "targets": [0,2], 
                    "orderable": false 
                }
            ]
            
        });
    });
    
    var regex = /^(.+?)(\d+)$/i;
    var cloneIndex = $(".clonedInput").length +1;
    
    function clone(){
       $(this).parents(".clonedInput").clone()
           .appendTo(".cloned")
           .attr("id", "file_type" +  cloneIndex)
           .find("*")
           .each(function() {
               var id = this.id || "";
               var match = id.match(regex) || [];
               //console.log(match.length);
               if (match.length === 3) {
                   this.id = match[1] + (cloneIndex);
               }
           })
            .on('click', 'button.clone', clone)
            .on('click', 'button.remove', remove);
    
            $('#file_type_'+cloneIndex).val("");
            $('#max_allowed_size_'+cloneIndex).val("");
            $('#allowed_type_'+cloneIndex).val("");
       cloneIndex++;
       return false;
    }  
    function remove(){
        if($('div.clonedInput').length > 1) {
            $(this).parents(".clonedInput").remove();
        }
        return false;
    }
    $("button.clone").on("click", clone);
    
    $("button.remove").on("click", remove);
    
    function show_file_type(){
       $('#file_type_form').toggle();
    }
    function activate_deactive(id,action,status){
        var cnfrm = confirm("Are you sure, you want to "+status+" this ?");
        if(!cnfrm){
            return false;
        }
        if(id){
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/booking/activate_deactivate_type/'+id+'/'+action,
                success: function (data) {
                    alert(data);
                    location.reload();
                }
            });
        }
    }
    function create_edit_file_type_form(file_type,max_allowed_size,allowed_type,id){
        $('#file_type1').val(file_type);
        $('#max_allowed_size1').val(max_allowed_size);
        $('#allowed_type1 option[value="'+allowed_type+'"]').prop("selected",true);
        $('#file_type_id').val(id);
        $("#myModal").modal("show");
    }
    function process_file_type_validations (){
        $('.file_type').each(function() {
            var id = (this.id).split("_")[2];
            file_type = $("#file_type_"+id).val();
            if(file_type){ 
               return true;
            }
            else{
                alert('Please add all mandatory fields!!');
                return false;
            }
        });
        return true;
    }
    function edit_file_type_validations(){
        file_type = $("#file_type1").val();
        if(file_type){ 
             return true;
        }
        else{
            alert('Please add all mandatory fields!');
            return false;
        }
    }
</script>
