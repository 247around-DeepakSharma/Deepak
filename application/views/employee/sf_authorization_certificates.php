<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
        <div class="x_title" style="border-bottom: none;">
            <h2>Vendor Authorization Certificate List</h2>
            <div class="clearfix"></div>

        </div>

        <div class="x_content">
            <div class="table-responsive">
                <?php if (!empty($service_centers)) { ?>

                    <table id="auth_cert" class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Vendor name</th>
                                <th>Validation</th>
                                <th>Document</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($service_centers as $service_center) { ?>

                                <tr>
                                    <td><?php echo $service_center['company_name']; ?></td>
                                    <td><?php echo $service_center['auth_certificate_validate_year']; ?></td>
                                    <td> <?php if ($service_center['auth_certificate_file_name'] != NULL) { ?> <a href="<?php echo S3_WEBSITE_URL . 'authorization_certificate/' . $service_center['auth_certificate_file_name']; ?>" target="_blank">certificate</a> <?php } ?></td>
                                    <td>
                                        <?php
                                        $financial_year = '';
                                        $current_month = date('m');
                                        if ($current_month > 3) {
                                            $financial_year = date('Y') . '-' . (date('Y') + 1);
                                        } else {
                                            $financial_year = (date('Y') - 1) . '-' . date('Y');
                                        }

                                        if ($service_center['has_authorization_certificate'] == 0 && $service_center['auth_certificate_file_name'] == NULL && $service_center['auth_certificate_validate_year'] == NULL) {
                                            ?>
                                            <button class="btn btn-default send_certificate" data-vendor-id="<?php echo $service_center['id']; ?>">Send Certificate</button>

                                        <?php } else if ($service_center['has_authorization_certificate'] == 1 && $service_center['auth_certificate_file_name'] != NULL && $service_center['auth_certificate_validate_year'] != $financial_year) {
                                            ?>
                                            <button class="btn btn-default send_certificate" data-vendor-id="<?php echo $service_center['id']; ?>">Send Certificate</button>
                                        <?php } ?>
                                    </td>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#auth_cert').DataTable({
            "order": [[2, "desc"]]
        });
    });

    $(document).on('click', '.send_certificate', function () {
        var ele = $(this);
        var ele_parent = $(this).parent();
        ele_parent.html('Sending....');
        var vendor_id = $(this).attr('data-vendor-id');

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url('employee/SF_authorization_certificate/send_auth_certificate'); ?>',
            data: {vendor_id: vendor_id},
            dataType: "json",
            success: function (response) {
                var result = response;
                if (parseInt(result.success) === 1) {
                    window.location.reload();
                } else {
                    alert('Request failed.')
                    ele_parent.html(ele);
                }
            },
            error: function (jqXHR, exception) {
                ele_parent.html(ele);
            }

        });
    });
    function send_certificate(vendor_id) {

    }
</script>
