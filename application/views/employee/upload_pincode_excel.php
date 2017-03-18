<div id="page-wrapper">
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
                                    <div class="col-md-12 col-md-offset-2">
                                        <center>
                                            <input type= "submit"  class="btn btn-danger btn-md" value ="Upload" >
                                           <!--  <input type="hidden" value="https://s3.amazonaws.com/bookings-collateral/vendor-pincodes/<?php echo $pincode_mapping_file[0]['file_name']; ?>" id="fileUrl"></input> -->

                                            <a href="<?php echo base_url()?>employee/vendor/download_pincode_latest_file" class="btn btn-primary btn-md">Download latest File</a> 
                                            <a href="<?php echo base_url()?>employee/vendor/download_unique_pincode_excel" class="btn btn-primary btn-md">Get Unique Pincode</a> 
                                            
                                        </center>
                                    </div>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class="col-md-6">
                                    Total Pincode&nbsp;:&nbsp;&nbsp;<b><?php echo $total_pincode?></b>
                                </div>
                                <div class="col-md-6">
                                    <strong>Uploaded By : </strong><?php echo $latest_file[0]['full_name']?>
                                </div>
                                <br><br>
                                <div class='col-md-12'>
                                    <b><i>Last Pincode Added</i></b>
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
                </div>
            </div>
        </div>
    </div>
</div>


