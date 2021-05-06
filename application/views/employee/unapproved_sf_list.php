<style>
    body {
        background-color: #f8f8f8;
    }

    table {
        background-color: #fff;
    }
    .download_btn{
        background-color: #2a3f54;
        border-color: #2a3f54;
    }
    #datatable1_filter{
        float: right;
    }
</style>

<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">

<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
<div  id="page-wrapper">
    <div class="row">
        <?php
        if ($this->session->userdata('error')) {
            echo '<div class="alert alert-danger alert-dismissible" role="alert" style="margin-top: 55px;">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <strong>' . $this->session->userdata('error') . '</strong>
                                        </div>';
        }
        ?>
        <?php
        if ($this->session->flashdata('success')) {

            echo '<div class="alert alert-success alert-dismissible" role="alert" style="margin-top:10px;">
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>' . $this->session->flashdata('success') . '</strong>
                   </div>';
        }
        ?>
    </div>
    <h1>Approve Vendors</h1>
    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="x_content">
                        <table id="datatable1" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">S.N</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Address</th>
                                    <th class="text-center">Pincode</th>
                                    <th class="text-center">State</th>
                                    <th class="text-center">District</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($records)) {
                                    $i = 1;
                                    foreach ($records as $record) {
//                                        if (!$record['active']) {
//                                            continue;
//                                        }
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i; ?> </td>
                                            <td class="text-center"><?php echo $record['company_name']; ?></td>
                                            <td class="text-center"><?php echo $record['address']; ?></td>
                                            <td class="text-center"><?php echo $record['pincode']; ?></td>
                                            <td class="text-center"><?php echo $record['state']; ?></td>
                                            <td class="text-center"><?php echo $record['district']; ?></td>
                                            <td class="text-center">
                                                <?php if (!$record['is_approved']) { ?>
                                                    <button class="btn btn-primary" type="button" id="btn_<?php echo $record['id']; ?>" onclick="approve_sf('<?php echo $record['id']; ?>')"><i class="fa fa-active"></i> Approve</button>
                                                    <span id="loader_<?php echo $record['id']; ?>"></span>
                                        <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="7">No Data Found.</td>
                                    </tr>
                                <?php } ?>  
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#datatable1').DataTable({
            "paging": true,
            "ordering": true,
            "info": true,
            'columnDefs': [{
                    'targets': [2, 6], // column index (start from 0)
                    'orderable': false, // set orderable false for selected columns
                }]
        });
    });
    
    function approve_sf(sf_id) {
        if (sf_id !== '' || sf_id !== undefined) {
            if (!confirm('Are you sure to approve this Vendor ?')) {
                return false;
            }
            $('#btn_'+sf_id).hide();
            $('#loader_'+sf_id).html("<img src='<?php echo base_url(); ?>images/loader.gif' style='width:30px'>");
            $.ajax({
                type: 'POST',
                url: '<?php echo base_url('employee/vendor/approve_vendor'); ?>',
                data: {sf_id: sf_id},
                success: function (response) {
                    $('#loader_'+sf_id).html('');
                    var response = JSON.parse(response);
                    alert(response.message);
                    if (response.result == 0) {
                        $('#btn_'+sf_id).show();                          
                        return false;
                    }
                    else
                    {
                        $('#loader_'+sf_id).html("<img src='<?php echo base_url(); ?>images/ok.png' style='width:30px'>");           
                    }
                }
            });
        }
    }
</script>
<style>
    #datatable1 td{
        text-align: center !important;
    }
    #datatable1 td:nth-child(2){
        color: blue !important;
    }
    #datatable1 td:nth-child(8){
        padding-left: 82px !important;
    }
    .pagination
    {
        float: right;
    }
</style>
