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
                        <center> <strong><?php echo $this->session->userdata('success') ?></strong></center>
                       
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
        class="panel-heading">
       <h1>Appliance Table</h1> 
        <button type="button" class="btn btn-primary" id="submit_btn" onClick="window.location.href = '<?php echo base_url();?>employee/service_centre_charges/add_new_category';return false;" style="float:right"   value="Add"  >Add</button><!--
        
-->        </div>
    </div>
    <div class="panel-body"   >
        <div class="row">
            <div class="col-md-12">
                <table class="table  table-striped table-bordered" id="datatablemappingview">
                    <thead>
                        <tr>
                            <th>Service ID</th>
                            <th>Category</th>
                            <th>Capacity</th>
                            <th>Date</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                            foreach ($appliance_data as $key => $row)  
                            {  
                               ?>
                        <tr>
                            <td><?php echo $row->services;?></td>
                            <td><?php echo $row->category;?></td>
                            <td><?php echo $row->capacity;?></td>
                            <td><?php echo date('jS M, Y', strtotime($row->create_date));?></td>
                            <td>
                                <button id='<?php echo "updatebtn".$key;?>' class="btn btn-primary" onclick="loadupdatemodel('<?php echo $key;?>')" 
                                        
                                        value="update" data-service_id="<?php echo $row->service_id; ?>"  
                                        data-category="<?php echo $row->category; ?>"  
                                        data-capacity="<?php echo $row->capacity; ?>"   
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
                <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centre_charges/update_appliance"  method="POST" enctype="multipart/form-data">
                    <div class="panel panel-info" >
                        <div class="panel-heading">Update Appliance Details</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!--                            <div class="col-md-6 col-md-offset-2">-->
                                    <div class="form-group <?php if( form_error('service_id') ) { echo 'has-error';} ?>">
                                        <label for="service_id" class="col-md-4">Appliance *</label>
                                        <div class="col-md-6">
                                            <select name="service_id" class="form-control" id="service_id" required>
                                                <option selected disabled>Select Appliance</option>
                                               
                                            </select>
                                            <?php echo form_error('service_id'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if( form_error('category') ) { echo 'has-error';} ?>">
                                        <label for="category" class="col-md-4">Category *</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="category" name="category"  placeholder="Enter Category" >
                                            <?php echo form_error('category'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group <?php if( form_error('capacity') ) { echo 'has-error';} ?>">
                                        <label for="capacity" class="col-md-4">Capacity *</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="capacity" name="capacity" value="" placeholder="Enter Capacity" >
                                            <?php echo form_error('capacity'); ?>
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
           var service = $("#updatebtn"+key).attr("data-service_id");
           var category = $("#updatebtn"+key).attr("data-category");
           var capacity = $("#updatebtn"+key).attr('data-capacity');
           var id = $("#updatebtn"+key).attr('data-id');
           
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/service_centre_charges/get_services',
                data: {service_id: service},
                success: function (data) {
                console.log(data);
                $("#service_id").html(data).change();
                   
                  }
           });
          
           
           $("#service_id").val(service_id).change();
           $("#rowid").val(id);
           $("#category").val(category);
           $("#capacity").val(capacity);
           $("#updatemyModal").modal('toggle');
           
       }
    </script>


    <script type="text/javascript">
    




$('#datatablemappingview').DataTable({
"processing": true, 
"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
"serverSide": false,  
 "dom": 'lBfrtip',
                "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',

                    title: 'Appliance Table', 
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
            "language":{ 
                "processing": "<div class='spinner'>\n\
                                    <div class='rect1' style='background-color:#db3236'></div>\n\
                                    <div class='rect2' style='background-color:#4885ed'></div>\n\
                                    <div class='rect3' style='background-color:#f4c20d'></div>\n\
                                    <div class='rect4' style='background-color:#3cba54'></div>\n\
                                </div>"
            },
            select: {
                style: 'multi'
            },
            
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "ordering": false,

            "order": [], 
            "pageLength": 25,
            "ordering": false,
                
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