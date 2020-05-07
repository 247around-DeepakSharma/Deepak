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

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($service_centers as $service_center) { ?>

                                <tr>
                                    <td><?php echo $service_center['company_name']; ?></td>
                                    <td><?php echo $service_center['auth_certificate_validate_year']; ?></td>
                                    <td> <?php if ($service_center['auth_certificate_file_name'] != NULL) { ?> <a href="<?php echo S3_WEBSITE_URL . 'authorization_certificate/' . $service_center['auth_certificate_file_name']; ?>" target="_blank">certificate</a> <?php } ?></td>

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
        "order": [[ 2, "desc" ]]
    });
    });
</script>
