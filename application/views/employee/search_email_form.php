<div id="page-wrapper" >
     
    <div class="container col-md-12" >
        <div class="panel panel-info" >
            <div class="panel-heading" >
                Search Email: 
            </div>
            <div class="panel-body">
                <div class="col-md-4">
                <form name="myForm" id="myForm" class="form-horizontal" action="<?php echo base_url(); ?>employee/vendor/seach_by_email"  method="POST" >
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="email_id" value="" id="email_id" />
                    </div>
                    <div class="col-md-2">
                        <input type="submit" class="btn btn-primary" value="Search">
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
   

<?php if (isset($data) && !empty($data)) { ?>
<?php //print_r($data); die(); ?>
        <div class="container col-md-12" >
            <div class="panel panel-info" >
                <div class="panel-heading" >Email Detail</div>
                <div class="panel-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Email Type</th>
                            </tr>
                        </thead>
                        <tbody>
              <?php for($i=0; $i<count($data); $i++){ ?>
                        <tr>
              <?php foreach ($data[$i] as $key => $value){ ?>
                        <td><?php echo $value; ?></td>
               <?php } ?>
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
