<div id="page-wrapper" >
    <div class="container" >
        <?php if(validation_errors()){?>
        <div class="panel panel-danger" style="margin-top:10px;margin-bottom:-10px;">
            <div class="panel-heading" style="padding:7px 0px 0px 13px">
                <?php echo validation_errors(); ?>
            </div>
        </div>
        <?php }?>
        <?php if ($this->session->userdata('failed')) { ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <strong><?php echo $this->session->userdata('failed') ?></strong>
        </div>
        <?php } ?>
        <?php if ($this->session->userdata('success')) { ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <strong><?php echo $this->session->userdata('success') ?></strong>
        </div>
        <?php } ?>
        <?php
            //             
                           $this->session->unset_userdata('success');
                           $this->session->unset_userdata('failed');
            //            
                         ?>
        <div class="panel panel-info" >
            <div class="panel-heading">
                Annual Charges Table
            </div>
        
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                   <table class="table table-bordered  table-hover table-striped data" id="datatable"  >
                        <thead>
                            <tr>
                                <th class="text-center">Sn.</th>
                                <th class="text-center">Partner Name</th>
                                <th class="text-center">Invoice ID</th>
                                <th class="text-center">From Date</th>
                                <th class="text-center">To Date</th>
                                <th class="text-center">Amount Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                $total_amount=0;
                                $rowcount=0;
                                foreach ($annual_charges_data as $row)  
                                {  //print_r($row);
                                   ?>
                            <tr>
                                <td class="text-center"><?php echo ++$rowcount; ?></td>
                                <td class="text-center"><a target="_blank" href="<?php echo base_url();?>employee/invoice/invoice_summary/partner/<?php echo $row->vendor_partner_id;?>"><?php echo $row->public_name;?></td>
                                <td class="text-center"><a target="_blank" href="<?php echo S3_WEBSITE_URL;?>invoices-excel/<?php echo $row->invoice_file_main;?>"><?php echo $row->invoice_id;?></a></td>
                                <td class="text-center"><?php echo date('d-M-Y', strtotime($row->from_date));?></td>
                                <td class="text-center"><?php echo date('d-M-Y', strtotime($row->to_date));?></td>
                                <td class="text-center"><?php echo "<i class='fa fa-inr'></i> ".$row->amount_collected_paid;?></td>
                            </tr>
                            <?php
                            $total_amount=$total_amount+$row->amount_collected_paid;
                            $total_amount=number_format((float)$total_amount, 2, '.', '');
                            }  
                                ?>  
                            <tr><td></td><td></td><td></td><td></td><td class="text-center">Total Amount</td><td class="text-center"><i class='fa fa-inr'></i> <?php echo $total_amount; ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var time = moment().format('D-MMM-YYYY');
    $('#datatable').DataTable(
       {     
        dom: 'Bfrtip',
        "buttons": [
                {
                    extend: 'excel',
                    text: 'Export',
                    title: 'annual_charges_'+time                    
                },
            ],
        
        "paging":   false,
        "ordering": false,
        "info":     false,
        "searching": false
      //"aLengthMenu": [[15, 18, 25, -1], [5, 10, 25, "All"]],
        //"iDisplayLength": 5
       } 
        );
} );
    </script>