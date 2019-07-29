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
                        <form class="form-horizontal" id="fileinfo" name="fileinfo"  method="POST" enctype="multipart/form-data" onsubmit="return validateForm()" action="<?php echo base_url(); ?>employee/bulkupload/add_warranty_data">                            
                            <input type="hidden" name="redirect_url" id="redirect_url" value="add_warranty">
                            <div class="form-group <?php
                            if (form_error('partner_id')) {
                                echo 'has-error';
                            }
                            ?>">
                                <label for="partner_id" class="col-md-3">Select Partner</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="partner_id" name="partner_id" required=""></select>
                                </div>
                                <?php echo form_error('partner_id'); ?>
                            </div>
                            <div class="form-group  <?php
                            if (form_error('excel')) {
                                echo 'has-error';
                            }
                            ?>">
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
                   
                </section>
                <div class="col-md-12" style="margin-top:20px;">
                    <h3>File Upload History</h3>
                    <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>S.No.</th>                                
                                <th>Plan Name</th>                                
                                <th>Plan Desc</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Remarks</th>
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
                                        $str .= '<td>'.$rec[0].'</td>';
                                        $str .= '<td>'.$rec[1].'</td>';
                                        $str .= '<td>'.$rec[2].'</td>';
                                        $str .= '<td>'.$rec[3].'</td>';
                                        $str .= '<td>'.$rec[4].'</td>';
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
                <?php if(isset($partner_id)) { ?> 
                    $('#partner_id').val('<?php echo $partner_id?>');
                <?php }?>
                $('#partner_id').trigger("change");
            }
        });
    });
    
    function validateForm()
    {
        if($('#partner_id').val() === null)
        {
            alert("Please Select Partner");
            return false;
        }
    }
</script>