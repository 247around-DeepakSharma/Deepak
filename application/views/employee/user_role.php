<style>
    #navigation_table_filter{
        float:right;
    }
</style>
<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>

<div class="container" style="width:100%">
     <div class="table_filter">
            <div class="row">
                <div class="col-md-4">
                    <div class="item form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <select class="form-control filter_table" id="entity_type_data" style='margin-top:8%;'>
                                <option value="" selected="selected" disabled="">Select Entity Type</option>
                                <option value="All">All</option>
                                <?php foreach($entity_type_data as $val){ ?>
                                <option value="<?php echo $val['entity_type']?>"><?php echo $val['entity_type']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
     </div>
    <button type="button" style="float:right;margin:10px;" class="btn btn-info" data-toggle="modal" data-target="#add_new_heading">Add New Heading In Menu</button>
<!--    <div class="right_holder" style="float:right;margin-right:10px;">
        <lable>Entity Type</lable>
        <select class="form-control " id="serachInput" style="border-radius:3px;">
            <option value="all">All</option>
            <option value="247Around">247Around</option>
            <option value="Partner">Partner</option>
        </select>            
    </div>-->
    <table class="table table-bordered" id="navigation_table">
    <thead>
      <tr>
        <th>S.N</th>
         <th>Entity Type</th>
        <th>Title</th>
        <th>Link</th>
        <th>Parents</th>
        <th>Groups</th>
        <th>Is Active</th>
      </tr>
    </thead>
    <tbody>
     <?php
     $index = 0;
     foreach($header_navigation as $id=>$headerMenuData){
         $parentString='';
         $groupIDString ='';
         $isActiveString ='';
         $linkString ='';
         $parentName = array();
         if($headerMenuData['parent_ids'] != '' && !empty($header_navigation["id_".$headerMenuData['parent_ids']])){ 
            $parentString = $header_navigation["id_".$headerMenuData['parent_ids']]['title'];
         }
          if($headerMenuData['groups'] != ''){
            $groupIDArray = explode(",",$headerMenuData['groups']);
            $groupIDString .= '<select style="width:100%" name="roles_group_'.$headerMenuData['id'].'[]" ui-select2 id='.$headerMenuData['id'].' onchange="updateUserGroup(this.id)"   class="form-control roles_group" data-placeholder="Select Role Group" multiple>';
            $groupIDString .= '<option value="" ></option>';
            $tempArray = array();
            if($headerMenuData['entity_type'] == 'Partner'){
                 $tempArray = $partners_roles_group;
            }
            else{
                $tempArray = $roles_group;
            }
            foreach($tempArray as $groupTitle){
                $selected = '';
                if(in_array($groupTitle['groups'], $groupIDArray)){
                    $selected = "Selected";
                }
                $groupIDString .= '<option '.$selected.' value ='.$groupTitle['groups'].'>'.$groupTitle['groups'].'</option>';
            }
            $groupIDString  .= '</select>';
         }
         if($headerMenuData['is_active'] != ''){
                    if($headerMenuData['is_active'] == 1){
                        $active_select = "Selected";
                        $deactive_Select = "";
                    }
                    else{
                        $active_select = "";
                        $deactive_Select = "Selected";
                    }
              $isActiveString .= '<select style="width:100%" name="is_active" id="isActive_'.$headerMenuData['id'].'" onchange="updateIsActive(this.id)"   class="form-control">';
              $isActiveString .= '<option '.$active_select.' value =1>Yes</option>';
              $isActiveString .= '<option '.$deactive_Select.' value =0>No</option>';
              $isActiveString .= '</select>';
         }
         if($headerMenuData['link'] != ''){
              $linkString .= '<a class="btn  btn-success" target="_blank" href='.base_url().$headerMenuData['link'].'>Link</a>';
         }
         ?>
        <tr>
            <td><?php echo $index+1; ?></td>
            <td><?php echo $headerMenuData['entity_type']; ?></td>
            <td><?php echo $headerMenuData['title']; ?></td>
            <td><?php echo $linkString?></td>
            <td><?php echo $parentString ?></td>
            <td><?php echo $groupIDString ?></td>
            <td><?php echo $isActiveString?></td>
            </tr>
        <?php
        $index++;
     }
     ?>
    </tbody>
  </table>

</div>
<div class="modal fade" id="add_new_heading" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Before Adding the heading please add Parents</h4>
        </div>
        <div class="modal-body">
            <form class="form-horizontal" action="<?php echo base_url() ?>employee/login/add_new_nav_heading" method="post">
    <div class="form-group">
        <label for="title" class="control-label col-xs-2">Title *</label>
        <div class="col-xs-10">
            <input type="text" class="form-control" id="title" name="title" placeholder="Add Title">
        </div>
    </div>
                 <div class="form-group">
        <label for="title_icon" class="control-label col-xs-2">Title Icon</label>
        <div class="col-xs-10">
            <input type="text" class="form-control" id="title_icon" name="title_icon" placeholder="title_icon">
        </div>
    </div>
                <div class="form-group">
        <label for="level" class="control-label col-xs-2">Level *</label>
        <div class="col-xs-10">
            <input type="text" class="form-control" id="level" name="level" placeholder="Level">
        </div>
    </div>
    <div class="form-group">
        <label for="link" class="control-label col-xs-2">Link</label>
        <div class="col-xs-10">
            <input type="text" class="form-control" id="link" name="link" placeholder="Add Link">
        </div>
    </div>
                <div class="form-group">
        <label for="link" class="control-label col-xs-2">Nav&nbsp;Type&nbsp;*</label>
        <div class="col-xs-10">
            <input type="text" class="form-control" id="nav_type" name="nav_type" placeholder="Add Nav Type">
        </div>
    </div>
    <div class="form-group " <?php if($saas_flag){ ?> style="display: none;" <?php } ?> >
        <label for="parent" class="control-label col-xs-2">Entity *</label>
        <div class="col-xs-10">
            <select class="form-control roles_group_add_new" id="entity_type" name="entity_type" onchange="get_parent_roles(this.value)">
                <option value="" >NULL</option>
                <option value="247Around">247Around</option>
                <option value="Partner">Partner</option>
            </select> 
        </div>
    </div>
    <div class="form-group ">
        <label for="parent" class="control-label col-xs-2">Parent *</label>
        <div class="col-xs-10" id="parent_select" >
            <select class="form-control roles_group_add_new" id="add_parents" name="add_parents" disabled="">
                
            </select>
            
        </div>
    </div>
     <div class="form-group">
        <label for="parent" class="control-label col-xs-2">Roles *</label>
        <div class="col-xs-10" >
            <select class="form-control roles_group_add_new" name="roleGroups[]" multiple="" id="group_select" disabled="">
                
            </select>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" onclick="return validate_submit()">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
</form>
        </div>
      </div>
      
    </div>
  </div>
<script>
     $(".roles_group").select2();
    $(".roles_group_add_new").select2();
    var saas_flag = "<?=((isset($saas_flag) && $saas_flag) ? $saas_flag : 0) ?>";
    if(saas_flag == 1) {
        $("#entity_type ").val("247Around");
        get_parent_roles("247Around");
    }
   // var table = $('#navigation_table').DataTable();
  var table=  $('#navigation_table').DataTable({
                    "pageLength": 50,
                    "lengthMenu": [[10, 25, 50,100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                    drawCallback: function() {
                       $('.roles_group').select2();
                    }
                  }) 
       $("#entity_type_data").change(function() {
        var entity_type_data=this.value;
        if(entity_type_data!=='All')
        {
                table
                .columns( 1 )
                .search( entity_type_data )
                .draw();
        }
        else
        {
             location.reload();
        }
    } );
        $("#serachInput").change(function () {
            if($('#serachInput').val() !== 'all'){
                table
                    .columns( 1 )
                    .search($('#serachInput').val())
                    .draw();
            }
            else{
                location.reload();
            }
    } );
    //$('#serachInput').select2();
    
    function updateUserGroup(headerID){
        var rolesGroup = $("#"+headerID).val();
         var url =  '<?php echo base_url();?>employee/login/update_role_group_for_header_navigation';
        $.ajax({
            type: 'POST',
            url: url,
            data: {headerID: headerID, rolesGroup: rolesGroup},
            success: function (response) {
                alert(response);
            }
            });
    }
    function updateIsActive(headerID){
        var id = headerID.split("_")[1];
        var is_active = $("#isActive_"+id).val();
        var url =  '<?php echo base_url();?>employee/login/activate_deactivate_header_navigation';
        $.ajax({
            type: 'POST',
            url: url,
            data: {headerID: id, is_active: is_active},
            success: function (response) {
                alert(response);
            }
            });
    }
    function validate_submit(){
        var title = $("#title").val();
        var nav_type = $("#nav_type").val();
        var add_parents = $("#add_parents").val();
        var level = $("#level").val();
        if(!(title || nav_type ||add_parents ||level)){
            alert("All fields are mandatry except link");
            return false;
        }
        else{
            return true;
        }
    }
    function get_parent(entity_type){
        var url =  '<?php echo base_url();?>employee/login/get_header_navigation_parent_by_entity';
        $.ajax({
            type: 'POST',
            url: url,
            data: {entity_type: entity_type},
            success: function (response) {
                $("#parent_select").html(response);
            }
            });
    }
    function get_roles(entity_type){
         var url =  '<?php echo base_url();?>employee/login/get_header_navigation_roles_by_entity';
        $.ajax({
            type: 'POST',
            url: url,
            data: {entity_type: entity_type},
            success: function (response) {
                $("#group_select").prop('disabled', false);
                $("#group_select").html(response);
            }
            });
    }
    function get_parent_roles(entity_type){
       get_parent(entity_type);
       get_roles(entity_type);
    }
    </script>
    <style>
        .select2{
            width: 100% !important;
        }
        </style>