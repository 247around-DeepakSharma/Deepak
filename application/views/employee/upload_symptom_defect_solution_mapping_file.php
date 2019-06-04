<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <?php
                if ($this->session->userdata('file_error')) {
                    echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:15px;">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->userdata('file_error') . '</strong>
                    </div>';
                }

                if ($this->session->flashdata('file_success')) {
                    echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:15px;">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('file_success') . '</strong>
                    </div>';
                }
                ?>
                <h3 class="page-header">
                    <b> Upload Symptom Defect Solution File</b>
                </h3>

                <section>
                    <div class="col-md-6">
                        <form class="form-horizontal" onsubmit="return submitForm();" id="fileinfo" name="fileinfo"  method="POST" enctype="multipart/form-data">
                            <div class="form-group <?php
                            if (form_error('partner_id')) {
                                echo 'has-error';
                            }
                            ?>">
                                <label for="partner_id" class="col-md-3">Select Partner</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="partner_id" required="" name="partner_id"></select>
                                </div>
                                <?php echo form_error('partner_id'); ?>
                            </div>
                            <div class="form-group <?php
                            if (form_error('service_id')) {
                                echo 'has-error';
                            }
                            ?>">
                                <label for="excel" class="col-md-3">Select Appliance</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="service_id" required="" name="service_id">
                                        <option value="" selected="" disabled="">Select Appliance</option>
                                    </select>
                                </div>
                                <?php echo form_error('service_id'); ?>
                            </div>
                            <div class="form-group  <?php
                            if (form_error('excel')) {
                                echo 'has-error';
                            }
                            ?>">
                                <label for="excel" class="col-md-3">Upload File</label>
                                <div class="col-md-9">
                                    <input type="file" class="form-control"  name="file" >
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
                        <p style="font-size: 18px;"><b>Download Sample File. Use this file to upload inventory details.</b></p>
                        <a href="https://s3.amazonaws.com/<?php echo BITBUCKET_DIRECTORY; ?>/vendor-partner-docs/inventory_master_list_sample_file.xlsx" class="btn btn-info" target="_blank">Download Sample File</a>
                    </div>
                </section>
                <div class="col-md-12" style="margin-top:20px;">
                    <h3>File Upload History</h3>
                    <table id="datatable1" class="table table-striped table-bordered table-hover" style="width: 100%;">
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
        </div> </div>
</div>



<script>

    var table;
    $('#service_id').select2();
    function submitForm() {
        if ($('#partner_id').val()) {
            if ($('#service_id').val()) {
                var fd = new FormData(document.getElementById("fileinfo"));
                fd.append("label", "WEBUPLOAD");
                fd.append('partner_id', $('#partner_id').val());
                fd.append('service_id', $('#service_id').val());
                fd.append('file_type', '<?php echo SYMPTOM_DEFECT_SOLUTION_MAPPING_FILE; ?>');
                fd.append('redirect_url', 'upload_symptom_defect_solution_mapping_file');
                $.ajax({
                    url: "<?php echo base_url() ?>employee/booking_request/process_symptom_defect_solution_mapping_file",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false
                }).done(function (data) {
                    alert(data);
                });
                alert('File validation is in progress, please wait....');
            } else {
                alert("Please Select Partner ");
                return false;
            }

        } else {
            alert("Please Select Partner ");
            return false;
        }

    }

    $(document).ready(function () {
        show_upload_file_history();
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>employee/partner/get_partner_list',
            data: {is_wh: true},
            success: function (response) {
                $('#partner_id').html(response);
                $('#partner_id').select2();
            }
        });
    });

    function show_upload_file_history() {
        table = $('#datatable1').DataTable({
            processing: true,
            serverSide: true,
            order: [],
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            pageLength: 5,
            ajax: {
                url: "<?php echo base_url(); ?>employee/upload_booking_file/get_upload_file_history",
                type: "POST",
                data: function (d) {
                    d.file_type = '<?PHP echo SYMPTOM_DEFECT_SOLUTION_MAPPING_FILE; ?>';
                }
            },
            columnDefs: [
                {
                    "targets": [0, 1, 2, 3, 4],
                    "orderable": false
                }
            ]
        });
    }

    $('#partner_id').on('change', function () {
        get_appliance();
    });

    function get_appliance() {
        var partner_id = $('#partner_id').val();
        if (partner_id) {
            $.ajax({
                type: 'GET',
                url: '<?php echo base_url() ?>employee/partner/get_partner_specific_appliance',
                data: {is_option_selected: true, partner_id: partner_id},
                success: function (response) {
                    if (response) {
                        $('#service_id').html(response);
                    } else {
                        console.log(response);
                    }
                }
            });
        } else {
            alert('Please Select Partner');
        }
    }
</script>
<?php
if ($this->session->userdata('file_error')) {
    $this->session->unset_userdata('file_error');
}
?>
<?php
if ($this->session->userdata('file_success')) {
    $this->session->unset_userdata('file_success');
}
?>