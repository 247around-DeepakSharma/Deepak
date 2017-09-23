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
            </div>
            <div class="panel-body">
                    <div class="col-lg-12">
                    <div  id="success"></div>
                        <?php if(isset($error) && $error !==0) {
                            echo '<div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $error . '</strong>
                            </div>';
                            }
                            ?>
                        <?php if(isset($sucess) && $sucess !==0) {
                            echo '<div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>' . $sucess . '</strong>
                            </div>';
                            }
                            
                            ?>
                            <?php if($this->session->flashdata('file_error')) {
                                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                    </button>
                                <strong>' . $this->session->flashdata('file_error') . '</strong>
                               </div>';
                            }
                            ?>
                            <?php if($this->session->flashdata('success_msg')) {
                                echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                    </button>
                                <strong>' . $this->session->flashdata('success_msg') . '</strong>
                               </div>';
                            }
                            ?>
                        <form class="form-horizontal" action="<?php echo base_url()?>employee/vendor/process_pincode_excel_upload_form" method="POST" enctype="multipart/form-data">
                            <div class="col-md-6">
                                <div class="form-group col-md-12 <?php if( form_error('excel') ) { echo 'has-error';} ?>">
                                    <label for="excel" class="col-md-4">Pincode Excel</label>
                                    <div class="col-md-8">
                                        <input type="file" class="form-control"  name="file" >
                                        <?php if( form_error('excel') ) { echo 'File size or file type is not supported. Allowed extensions are "xls" or "xlsx". Maximum file size is 2 MB.';} ?>
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    <div class="col-md-12 col-md-offset-4">
                                        <input type= "submit"  class="btn btn-danger btn-md" value ="Upload" >
                                        <a href="<?php echo base_url()?>employee/vendor/download_unique_pincode_excel" class="btn btn-primary btn-md">Get Unique Pincode</a> 
                                            
                                    </div>
                                </div>
                                <div class="info_text"><p class="alert alert-danger"><i class="fa fa-info-circle" aria-hidden="true"></i>  Zipped CSV file Must Be name as  <strong>vendor_pincode_mapping.csv</strong></p></div>
                            </div>
                            
                            <div class='col-md-6'>
<!--                                <div class="col-md-12">
                                    Total Pincode&nbsp;:&nbsp;&nbsp;<b><?php //echo $total_pincode?></b>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table-bordered table-hover table-responsive">
                                        <tbody>
                                            <tr>
                                                <td><strong>Uploaded By</strong></td>
                                                <td><?php //if(!empty($pincode_mapping_file_list)){echo $pincode_mapping_file_list[0]['agent_name'];} ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Upload Date</strong></td>
                                                <td><?php //if(!empty($pincode_mapping_file_list)){echo date('d-m-Y',  strtotime($pincode_mapping_file_list[0]['upload_date']));} ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br><br>-->
                                <div class='col-md-12'>
                                    <b><i>Last Pincode Added</i></b>
                                    <div class="pull-right">Total Pincode&nbsp;:&nbsp;&nbsp;<b><?php echo $total_pincode?></b></div>
                                </div><hr>
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
                    data: {file_type: '<?php echo _247AROUND_VENDOR_PINCODE; ?>'}
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


