<style>
    #datatable1_info{
        display: none;
    }

    #datatable1_filter{
        text-align: right;
    }
</style>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <b> Upload File</b>
                </h1>
                <section>
                    <div class="col-md-6">
                        <form class="form-horizontal" id="fileinfo" name="fileinfo"  method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" action="<?php echo base_url(); ?>employee/bulkupload/check_warranty_data">                            
                            <input type="hidden" name="redirect_url" id="redirect_url" value="check_warranty">
                            
                            <div class="form-group">
                                <label for="excel" class="col-md-3">Upload File</label>
                                <div class="col-md-9">
                                    <input type="file" class="form-control"  name="file" required="">
                                    <?php
                                    if (form_error('excel')) {
                                        echo 'File size or file type is not supported. Allowed extentions are "xls" or "xlsx". Maximum file size is 2 MB.';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">                                 
                                    <input type="submit"  class="btn btn-success btn-md" id="submit_btn" value ="Upload">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <p style="font-size: 18px;"><b>Download Sample File. Use this file to check Warranty Data.</b></p>
                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/warranty-docs/check_warranty_data_sample.xlsx" class="btn btn-info" target="_blank">Download Sample File</a>
                    </div>
                </section>
                <div class="col-md-12" style="margin-top:20px;">
                    <h3>File Upload History</h3>
                    <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>S.No.</th>  
                                <th>Booking Id</th>  
                                <!--<th>Product</th>-->
                                <!--<th>Booking Date</th>-->                                
                                <!--<th>Model</th>-->
                                <!--<th>DOP</th>-->
                                <th>Warranty Status</th>
                                <!--<th>Remarks</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(!empty($data)){
                                    $count = 1;
                                    foreach($data as $key => $rec)
                                    {
                                        $str = '<tr>';
                                        $str .= '<td>'.$count++.'</td>';
                                        $str .= '<td>'.$key.'</td>';
                                        $str .= '<td>'.$rec.'</td>';
//                                        $str .= '<td>'.$rec[6].'</td>';
//                                        $str .= '<td>'.$rec[3].'</td>';
//                                        $str .= '<td>'.$rec[5].'</td>';
//                                        $str .= '<td>'.$rec[8].'</td>';
//                                        $str .= '<td>'.$rec[9].'</td>';
                                        $str .= '</tr>';
                                        echo $str;
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#datatable1').DataTable({
            dom: 'Bfrtip',
            buttons: [
               'csv'
            ]
        });
        
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data: {},
            success: function (response) {
                $('#partner_id').html(response);
                $('#partner_id').select2();
                $('#partner_id').trigger("change");
            }
        });
    });
</script>