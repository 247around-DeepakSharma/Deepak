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
                        <center><strong><?php echo $this->session->userdata('success') ?></strong></center>
                        
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
       <h1> Service Category List</h1>
        <button type="button" class="btn btn-primary" id="submit_btn" onClick="window.location.href = '<?php echo base_url();?>employee/service_centre_charges/add_service_category';return false;" style="float:right"   value="Add"  >Add</button><!--
        
-->        </div>
    </div>
    <div class="panel-body"  >
        <div class="row">
            <div class="col-md-12">
                <table class="table  table-striped table-bordered" id="datatableservicecate">
                    <thead>
                        <tr>
                            <th>Appliance</th>
                            <th>Service Category</th>
                            <th>Product</th>
                            <th>Date</th>
                            <th>Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                            foreach ($service_category_data as $key => $row)  
                            {  
                               ?>
                        <tr>
                            <td><?php echo $row->services;?></td>
                            <td><?php echo $row->service_category;?></td>
                            <td><?php echo $row->product_or_services;?></td>
                            <td><?php echo date('jS M, Y', strtotime($row->create_date));?></td>
                            <td>
                                <button id='<?php echo "updatebtn".$key;?>' class="btn btn-primary" onclick="loadupdatemodel('<?php echo $key;?>')" 
                                        
                                        value="update" data-service_id="<?php echo $row->service_id; ?>"  
                                        data-service_category='<?php echo $row->service_category; ?>'  
                                        data-product_or_services="<?php echo $row->product_or_services; ?>"   
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
                <form name="myForm" class="form-horizontal" id ="engineer_form" action="<?php echo base_url();?>employee/service_centre_charges/update_service_category"  method="POST" enctype="multipart/form-data">
                    <div class="panel panel-info" >
                        <div class="panel-heading">Update Appliance Details</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!--                            <div class="col-md-6 col-md-offset-2">-->
                                    <div class="form-group <?php if( form_error('service') ) { echo 'has-error';} ?>">
                                    <label for="service" class="col-md-4">Appliance *</label>
                                    <div class="col-md-6">
                                        <select name="service" class="form-control" id="services"   required>
                                            <option selected disabled="">Please Select Appliance</option>
                                          
                                        </select>
                                        <?php echo form_error('service'); ?>
                                    </div>
                                    
                                </div>
                                
                                <div class="form-group <?php if( form_error('product') ) { echo 'has-error';} ?>">
                                    <label for="product" class="col-md-4">Product Type *</label>
                                    <div class="col-md-6">
                                        <select name="product" class="form-control" id="product" required>
                                            <option selected disabled>Select Product</option>
                                            <option <?php if(set_value('product') == "Service"){echo 'selected';} ?>>Service</option>
                                           <option <?php if(set_value('product') == "Product") {echo 'selected';} ?>>Product</option>
                                        </select>
                                        <?php echo form_error('product'); ?>
                                    </div>
                                   
                                </div>
                                 
                                
                                <div class="form-group <?php if( form_error('category') ) { echo 'has-error';} ?>">
                                    <label for="category" class="col-md-4">Service Category *</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="category" name="category" value="<?php echo set_value('category'); ?>" placeholder="Enter Service Category" required>
                                    <?php echo form_error('category'); ?>
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








<script type="text/javascript">
    




$('#datatableservicecate').DataTable({
"processing": true, 
"serverSide": false,  
 "dom": 'lBfrtip',
                "buttons": [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span>  Export',
                    pageSize: 'LEGAL',
                    title: 'Service Category List', 
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
            "order": [], 
            "pageLength": 25,
            "ordering": false,
           "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
            "deferRender": true   


});





</script>















    
    <script>
       function loadupdatemodel(key){
           var service = $("#updatebtn"+key).attr("data-service_id");
           var category = $("#updatebtn"+key).attr("data-service_category");
           var product = $("#updatebtn"+key).attr('data-product_or_services');
           var id = $("#updatebtn"+key).attr('data-id');
           
           $.ajax({
                type: 'POST',
                url: '<?php echo base_url(); ?>employee/service_centre_charges/get_services',
                data: {service_id: service},
                success: function (data) {
                 console.log(data);
                $("#services").html(data).change();
                   
                  }
           });
          
           
           //$("#service").val(service).change();
           $("#rowid").val(id);
           $("#category").val(category);
           $("#product").val(product).change();
           $("#updatemyModal").modal('toggle');
           
       }
       
    
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