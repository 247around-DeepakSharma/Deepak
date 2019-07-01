<div id="page-wrapper" >
<div class="container" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
            <?php echo validation_errors(); ?>
            
            </div>
        </div>
        <?php }?>
                 <?php if ($this->session->userdata('failed')) { ?>
             <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <center><strong><?php echo $this->session->userdata('failed') ?></strong></center>
                        
                    </div>
             <?php } ?>
        
           
                 <?php if ($this->session->userdata('success')) { ?>
             <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>


                        <center>  <strong><?php echo $this->session->userdata('success') ?></strong></center>
                      
                    </div>
             <?php } ?>
        
             <?php
//             
               $this->session->unset_userdata('success');
               $this->session->unset_userdata('failed');
//            
             ?>
        
<div class="panel  " >
    <div 
        class=" ">
        <h1>  Appliance Table</h1>
        <button type="button" class="btn btn-primary" id="submit_btn" onClick="window.location.href = '<?php echo base_url();?>employee/service_centre_charges/add_new_appliance_name';return false;" style="float:right"   value="Add"  >Add</button><!--
        
-->        </div>
    </div>
    <div class="panel-body"  >
        <div class="row">
            <div class="col-md-12">
                <table class="table  table-striped table-bordered" id="appliancedatat">
                    <thead>
                        <tr>
                            <th>Appliance</th>
                            <th>Status</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                            foreach ($appliance_name as $key => $row)  
                            {  
                               ?>
                        <tr>
                            <td><?php echo $row->services;?></td>
                            <td id='row<?=$row->id?>'>
                                <?php
                                    $str = "<button class='btn btn-success status' style='width:80px;' value='0' id='btn".$row->id."' onClick='return changeStatus(this.id, this.value);'>Active</button>";
                                    if(empty($row->isBookingActive))
                                    {
                                        $str = "<button class='btn btn-warning status' style='width:80px;' value='1' id='btn".$row->id."' onClick='return changeStatus(this.id, this.value);'>Deactive</button>";
                                    }
                                    echo $str;
                                ?>
                            </td>
                         
                            
                            <td>
                                <button id='<?php echo "updatebtn".$key;?>' class="btn btn-primary" onclick="loadupdatemodel('<?php echo $key;?>')" 
                                        
                                        value="update" data-services="<?php echo $row->services; ?>" 
                                        data-id="<?php echo $row->id; ?>">Update</button>
                                
                            </td>
                        </tr>
                        <?php }  
                            ?>  
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="updatemyModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!--                                          <h4 class="modal-title">Add New Entity</h4>-->
            </div>
            <div class="modal-body">
                <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centre_charges/update_appliance_name"  method="POST" enctype="multipart/form-data">
                    <div class="panel panel-info" >
                        <div class="panel-heading">Update Appliance Name</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!--                            <div class="col-md-6 col-md-offset-2">-->
                               
                                <div class="form-group <?php if( form_error('appliance') ) { echo 'has-error';} ?>">
                                    <label for="appliance" class="col-md-4">Appliance Name *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="appliance" name="appliance" value="<?php echo set_value('appliance'); ?>" placeholder="Enter Appliance Name" required>
                                    <?php echo form_error('appliance'); ?>
                                    </div>
                                   
                                </div>
  
                                    <input type="hidden" name="rowid" id="rowid" value="">
                                </div>
                                <!--                        </div>-->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-md-offset-4" style="margin-bottom: 50px;">
                        <center>
                        <input type="Submit" class="btn btn-primary" id="submit_btn" value="Submit" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    
    <script>
       function loadupdatemodel(key){
           
           var appliance = $("#updatebtn"+key).attr('data-services');
           var id = $("#updatebtn"+key).attr('data-id');
           
          
          
           
           //$("#service").val(service).change();
           $("#rowid").val(id);
           $("#appliance").val(appliance);
           $("#updatemyModal").modal('toggle');
           
       }
       
       function changeStatus(btnId, status)
       {
           var statusFlag = "Deactivate";
           if(status == '1')
           {
               statusFlag = "Activate";
           }
           
           if(!confirm("Are you sure, You want to "+statusFlag+" service ?"))
           {
               return false;
           }
           
           var id = btnId.substr(3);
           $.post('<?php echo base_url(); ?>employee/service_centre_charges/update_service_status',{id : id, status : status}, function(data){
               if(data == '1')
               {
                    if(status == '1')
                    {
                        $("#row"+id).html("<button class='btn btn-success status' style='width:80px;' value='0' id='btn"+id+"' onClick='return changeStatus(this.id, this.value);'>Active</button>");
                        
                    }
                     else
                    {
                        $("#row"+id).html("<button class='btn btn-warning status' style='width:80px;' value='1' id='btn"+id+"' onClick='return changeStatus(this.id, this.value);'>Deactive</button>");
                        
                    }
                }
           });
       };
    </script>
    <script>
        



$('#appliancedatat').DataTable({
"processing": true, 
"serverSide": false,  
 "dom": 'lBfrtip',
                "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',
                    title: 'Appliances', 
                    exportOptions: { 
                       columns: [0],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'current',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            "order": [], 
            "pageLength": 25,
            "ordering": false,
           "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "deferRender": true   


});



    </script>

    <style>
        
        @media (min-width: 1200px){
.container {
    width: 100% !important;
}

.dataTables_filter{


    float: right !important;
    margin-top: -30px !important;
}

    </style>