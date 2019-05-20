<style>
    #datatable1_filter,#datatable1_length,#datatable1_info{
        display: none;
    }
</style>
<div>
    <div class="container-fluid">
        <div class="panel panel-info" style="margin-top:20px;">
            <div class="panel-heading">
                <h3 style='text-align:center'>Upload Vendor Pincode Mapping Excel </h3>
                <h3 style="color: #6b6220;text-align: center;"><?php 
                            echo $this->session->userdata('final_msg');
                    ?></h3>
            </div>
            <div class="panel-body">
                    <div class="col-lg-12">
                    <div  id="success"></div> 
                        <form class="form-inline" action="<?php echo base_url()?>employee/vendor/process_upload_pin_code_vendor" method="POST" enctype="multipart/form-data">
                            <div class="col-md-6" style="width:30%">
                                    <div class="form-group">
                                        <input type="file" class="form-control"  name="file" required="">
                                    </div>
                               
                                <div class="form-group">
                                        <input type="hidden" name="vendorID" value="<?php echo $vendorID;?>">
                                        <input type= "submit"  class="btn btn-success btn-md" value ="Upload" >
                                </div>
                               </div>
                            
                            <div class='col-md-6' style="width:70%">
                                <p style="font-size: 18px;"><b>Please write Appliance Name Only From Below List</b></p>
                                    <table class='table table-condensed table-bordered'>
                                        <thead>
                                            <?php
                                            foreach($services as $index=>$serviceName){
                                                if($index%5== 0){
                                                    echo "<tr>";
                                                }
                                            ?>
                                           <td><?php echo$serviceName['services']; ?></td>
                                            <?php
                                           if(($index+1)%5== 0){
                                                    echo "</tr>";
                                                }
                                            }
                                            ?>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                            </div>
                        </form>
                    
                    <div class="col-md-12" style="margin-top:20px;">
                        <h3>File Upload History</h3>
                        <table id="datatable_upload_pincode" class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Download</th>
                                    <th>Uploaded By</th>
                                    <th>Uploaded Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var table;

        $(document).ready(function () {

            //datatables
            table = $('#datatable_upload_pincode').DataTable({
                processing: true, //Feature control the processing indicator.
                serverSide: true, //Feature control DataTables' server-side processing mode.
                order: [], //Initial no order.
                pageLength: 5,
                // Load data for the table's content from an Ajax source
                ajax: {
                    url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                    type: "POST",
                    data: {file_type: '<?php echo 'vendor_pincode_'.$vendorID; ?>'}
                },
                //Set column definition initialisation properties.
                columnDefs: [
                    {
                        "targets": [0,1,2,3], //first column / numbering column
                        "orderable": false //set not orderable
                    }
                ]
            });
        });
</script>
<style>
    #datatable_upload_pincode_filter{
            float: right;
    }
    </style>
<?php if($this->session->userdata('file_error')){$this->session->unset_userdata('file_error');} ?>
<?php if($this->session->userdata('success_msg')){$this->session->unset_userdata('success_msg');} ?>
<?php if($this->session->userdata('final_msg')){$this->session->unset_userdata('final_msg');} ?>


