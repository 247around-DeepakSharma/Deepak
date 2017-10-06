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
                <h3 style="color: #6b6220;text-align: center;"><?php if(($this->session->userdata('final_msg'))){
                             echo  $this->session->userdata('final_msg');
                             $this->session->unset_userdata('final_msg');
                    } ?></h3>
            </div>
            <div class="panel-body">
                    <div class="col-lg-12">
                    <div  id="success"></div> 
                        <form class="form-inline" action="<?php echo base_url()?>employee/vendor/process_upload_pin_code_vendor" method="POST" enctype="multipart/form-data">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="file" class="form-control"  name="file" required="">
                                    </div>
                               
                                <div class="form-group">
                                        <input type="hidden" name="vendorID" value="<?php echo $vendorID;?>">
                                        <input type= "submit"  class="btn btn-success btn-md" value ="Upload" >
                                </div>
                               </div>
                            
                            <div class='col-md-6'>                    
<!--                                <div class='col-md-12'>
                                    <b><i>Last Pincode Added</i></b>
                                    <div class="pull-right">Total Pincode&nbsp;:&nbsp;&nbsp;<b><?php echo $total_pincode?></b></div>
                                </div><hr>-->
                                <div class="col-md-12">
                                    <table class='table table-condensed table-bordered'>
                                        <thead>
                                            <tr>
                                                <td style="background: #D3D3D3;font-weight: bold">Vendor Name</td>
                                                <td style="background: #D3D3D3;font-weight: bold">Appliance</td>
                                                <td style="background: #D3D3D3;font-weight: bold">Brand</td>
                                                <td style="background: #D3D3D3;font-weight: bold">Area</td>
                                                <td style="background: #D3D3D3;font-weight: bold">Pincode</td>
                                                <td style="background: #D3D3D3;font-weight: bold">Region</td>
                                                <td style="background: #D3D3D3;font-weight: bold">City</td>
                                                <td style="background: #D3D3D3;font-weight: bold">State</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                    <?php if(!empty($latest_vendor_pincode[0])){ foreach($latest_vendor_pincode[0] as $key=>$value){
                                        echo '<td>'.$value.'</td>';
                                    } }?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    
                    <div class="col-md-12" style="margin-top:20px;">
                        <h3>File Upload History</h3>
                        <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Download</th>
                                    <th>Uploaded By</th>
                                    <th>Uploaded Date</th>
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
            table = $('#datatable1').DataTable({
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
<?php $this->session->unset_userdata('file_error'); ?>
<?php $this->session->unset_userdata('success_msg'); ?>


