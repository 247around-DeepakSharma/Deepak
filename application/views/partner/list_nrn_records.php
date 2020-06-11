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
</style>

<link rel="stylesheet" href="<?php echo base_url(); ?>css/jquery.loading.css">

<script src="<?php echo base_url(); ?>js/jquery.loading.js"></script>
<div class="right_col" role="main">
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
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <a href="<?php echo base_url('partner/add_nrn_details'); ?>" class="btn btn-primary">Add New TR Detail</a>
        </div>
    </div>
    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="x_content">
                        <table id="datatable1" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">S.N</th>
                                    <th class="text-center">Booking ID</th>
                                    <th class="text-center">ASM NAME</th>
                                    <th class="text-center">Distributor</th>
                                    <th class="text-center">Booking Date</th>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Model No</th>
                                    <th class="text-center">Serial No</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($records)) {
                                    $i = 1;
                                    foreach ($records as $record) {
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i; ?> </td>
                                            <td class="text-center"><?php echo $record['booking_id']; ?></td>
                                            <td class="text-center"><?php echo $record['asm_name']; ?></td>
                                            <td class="text-center"><?php echo $record['distributor_name']; ?></td>
                                            <td class="text-center"><?php echo $record['booking_date']; ?></td>
                                            <td class="text-center"><?php echo $record['product_id']; ?></td>
                                            <td class="text-center"><?php echo $record['product_model_no']; ?></td>
                                            <td class="text-center"><?php echo $record['product_serial_no']; ?></td>
                                            <td class="text-center"><?php echo $record['physical_status']; ?></td>
                                            <td class="text-center"><button class="btn btn-primary" type="button" onclick="editNRNRecord('<?php echo $record['nrn_id']; ?>')"><i class="fa fa-pencil"></i></button></td>
                                        </tr>
                                        <?php
                                        $i++;
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
</div>
<script type="text/javascript">
        $(document).ready(function () {
            $('#datatable1').DataTable({
                "dom": 'Bl<"#toolbar">frtip',
                "paging": true,
                "ordering": false,
                "info": true,
                buttons: [{
                     extend: 'excelHtml5',
                     text: 'Export',
                     title: 'TR History records',
                     exportOptions: {
                        columns: [ 1,2,3,4,5,6,7,8 ]
                     }
                 }]
            });
            $("div#toolbar").html('<center><b>TR Detail History</b></center>');
        });
    function editNRNRecord(nrn_id) {
        if (nrn_id !== '' || nrn_id !== undefined) {
            window.location.href = '<?php echo base_url('partner/edit_nrn_details/edit'); ?>/' + nrn_id;
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
#toolbar{
    width:30%;
    float: left;
    font-size: 18px;
}
#datatable1_length{
    width: 25% !important;
}
#datatable1_filter{
    width: 30% !important;
}
.dt-buttons{
    float: left;
    width: 10%;
    padding-bottom: 5px;
}
</style>
