<script src="<?php echo base_url(); ?>js/base_url.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/app.js"></script>
<script src="<?php echo base_url(); ?>js/buyback_app/controller/controllers.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.loading.css">
<script src="<?php echo base_url();?>js/jquery.loading.js"></script>
<div class="right_col" role="main" ng-app="viewBBOrder">
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel" style="height: auto;" ng-controller="assignCP">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_title">
                    <h2>
                        <i class="fa fa-bars"></i> Upload Tracking File <!--<small>Float left</small>-->
                    </h2>
                    <a style="float: right;" href="<?php echo base_url()?>buyback/buyback_process/download_tracking_sample_file" class="btn btn-success" class="" data-value="Download">Download Sample File</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" >
                    <?php if ($this->session->userdata('tracking_error')) {
                            echo '<br><br><div class="alert alert-danger alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                               <span aria-hidden="true">&times;</span>
                           </button>
                           <strong>' . $this->session->userdata('tracking_error') . '</strong>
                       </div>';
                        }else if($this->session->userdata('tracking_success')){
                                echo '<br><br><div class="alert alert-success alert-dismissible" role="alert" style="width: 60%;margin-left: 20%;margin-top: -49px;">

                               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                               <strong>' . $this->session->userdata('tracking_success') . '</strong>
                           </div>';
                        }
                        ?>
                    <form class="form-horizontal"  id="fileinfo" onsubmit="return validation()" action="<?php echo base_url() ?>buyback/buyback_process/process_tracking_number_file" name="fileinfo"  method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <div class="col-md-4">
                            <input type="file" class="form-control"  name="tracking_file" id="tracking_file">
                        </div>
                        <input type= "submit"  class="btn btn-success" id="submit_btn" value ="Upload">
                    </div>
                </form>
                    <div class="col-md-12" style="margin-top:20px;">
                        <h3>File Upload History</h3>
                        <table id="datatable_tracking" class="table table-striped table-bordered table-hover" style="width: 100%;">
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
</div>
<script>
    var table;

        $(document).ready(function () {

            //datatables
            table = $('#datatable_tracking').DataTable({
                processing: true, //Feature control the processing indicator.
                serverSide: true, //Feature control DataTables' server-side processing mode.
                order: [], //Initial no order.
                pageLength: 5,
                // Load data for the table's content from an Ajax source
                ajax: {
                    url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                    type: "POST",
                    data: {file_type: 'Buyback_Tracking_File'}
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
    function validation(){
       var fileName =  $('#tracking_file').val();
       if(fileName){
            var fileNameArray = fileName.split(".");
            if(fileNameArray[fileNameArray.length-1] === "xlsx" || fileNameArray[fileNameArray.length-1] === "xls"){
                return true;
            }
            else{
                alert("Please Upload only Excel File");
                return false;
            }
        }
        else{
            alert("Please Upload tracking excel file");
                return false ;
        }
    }
</script>
<?php 
if($this->session->userdata('tracking_success')){$this->session->unset_userdata('tracking_success');}
if($this->session->userdata('tracking_error')){$this->session->unset_userdata('tracking_error');}
?>
