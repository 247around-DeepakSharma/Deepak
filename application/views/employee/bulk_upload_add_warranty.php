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
            <div class="col-md-12">
                <center><img id="loader_gif_title" src="<?php echo base_url(); ?>images/loader.gif" style="display: none;"></center>
            </div>
        </div>
         <?php
            if ($this->session->userdata('file_error')) {
                echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:10px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>' . $this->session->userdata('file_error') . '</strong>
                    </div>';
            }
        ?> 
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
                                    <input type="file" class="form-control"  name="file" required="" accept=".xlsx, .xls, .csv">
                                    <?php
                                    if (form_error('file')) {
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
                        <p style="font-size: 18px;"><b>Download Sample File. Use this file to upload Warranty Data.</b></p>
                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/warranty-docs/warranty_plans_sample.xlsx" class="btn btn-info" target="_blank">Download Sample File</a>
                    
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
                                        $str .= '<td>'.(!empty($rec[0]) ? $rec[0] : '--').'</td>';
                                        $str .= '<td>'.(!empty($rec[1]) ? $rec[1] : '--').'</td>';
                                        $str .= '<td>'.(!empty($rec[2]) ? $rec[2] : '--').'</td>';
                                        $str .= '<td>'.(!empty($rec[3]) ? $rec[3] : '--').'</td>';
                                        $str .= '<td>'.(!empty($rec[4]) ? $rec[4] : '--').'</td>';
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
        $('#loader_gif_title').hide();
        $('#datatable1').DataTable({
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', text: 'Export', title: 'Add Warranty'}               
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
        $('#loader_gif_title').show();
    }
</script>
<?php if($this->session->userdata('file_error')){$this->session->unset_userdata('file_error');} ?>