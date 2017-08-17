<style>
    #datatable1_filter,#datatable1_length,#datatable1_info{
        display: none;
    }
</style>

<div id="page-wrapper">
   <div class="container-fluid">
      <div class="row">
         <div class="col-lg-12">
             <?php if($this->session->userdata('error')) {
               echo '<div class="alert alert-danger alert-dismissible" role="alert">
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                   </button>
                   <strong>' . $this->session->userdata('error'). '</strong>
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
               
            <h1 class="page-header">
               <b> Upload Delivered Products for paytm Excel</b>
            </h1>
            
            <form class="form-horizontal" action="<?php echo base_url()?>employee/bookings_excel/upload_booking_for_paytm" method="POST" enctype="multipart/form-data">
                <div class="form-group  <?php if( form_error('excel') ) { echo 'has-error';} ?>">
                  <label for="excel" class="col-md-1">Delivered Products For Paytm</label>
                  <div class="col-md-4">
                     <input type="file" class="form-control"  name="file" >
                      <?php if( form_error('excel') ) { echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';} ?>
                  </div>
                   <input type= "submit"  class="btn btn-danger btn-md" value ="Upload" >    
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

<script>
//$("input").tagsinput('services');
</script>
<script type="text/javascript">

        var table;

        $(document).ready(function () {

            //datatables
            table1 = $('#datatable1').DataTable({
                processing: true, //Feature control the processing indicator.
                serverSide: true, //Feature control DataTables' server-side processing mode.
                order: [], //Initial no order.
                pageLength: 5,
                // Load data for the table's content from an Ajax source
                ajax: {
                    url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                    type: "POST",
                    data: {file_type: '<?php echo _247AROUND_PAYTM_DELIVERED; ?>'}
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
 <?php $this->session->unset_userdata('error'); ?>