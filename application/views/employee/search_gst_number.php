<div id="page-wrapper" >
     
    <div class="container col-md-12" >
        <div class="panel panel-info" >
            <div class="panel-heading" >
                Search GST Number: 
            </div>
            <div class="panel-body">
                
                  <form class="form-horizontal"  method="POST" action="<?php echo base_url(); ?>employee/vendor/seach_gst_number">
                    <div class="form-group">
                        <label for="gst_number" class="col-md-1"> GSTIN</label>
                        <div class="col-md-4">
                        <input type="text" class="form-control" name="gst_number" value="" id="gst_number" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="excel" class="col-md-1">Use Api</label>
                        <div class="col-md-4">
                            <input type="checkbox" id="api_check" name="api_check">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-1"> </label>
                        <div class="col-md-4">
                            <input type="submit" class="btn btn-primary" value="Search"> 
                        </div>
                    </div>
                </form>
             
            </div>
        </div>
    </div>
   

<?php if (isset($data) && !empty($data)) { ?>
<?php //print_r($data); die(); ?>
        <div class="container col-md-12" >
            <div class="panel panel-info" >
                <div class="panel-heading" >GSTIN Detail</div>
                <div class="panel-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>GST Number</th>
                                <th>Entity</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
             <?php   foreach ($data as $value){ ?>
                    <tr>
                        <td><?php echo $value['gst_number']; ?></td>
                        <td><?php echo $value['entity']; ?></td>
                        <td><?php echo $value['name']; ?></td>
                        <td><?php echo $value['gst_status']; ?></td>
                        <td><?php echo $value['gst_type']; ?></td>
                    </tr>
               <?php } ?>
                   </tbody>
                     </table>
                </div>
                </div>
            </div>
        </div>
   
<?php } ?>
</div>
<?php if($this->session->userdata('success')) {$this->session->unset_userdata('success');} ?>
