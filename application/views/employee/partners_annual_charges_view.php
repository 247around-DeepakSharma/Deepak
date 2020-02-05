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
                    <table id="annual_charges_report" class="table  table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Sn.</th>
                                <th class="text-center">Partner Name</th>
                                <th class="text-center">Invoice ID</th>
                                <th class="text-center">From Date</th>
                                <th class="text-center">To Date</th>
                                <th class="text-center">Amount Paid</th>
                                <!--<th class="text-center">Last cash Invoice for Installation Service</th>-->
                                <th class="text-center">Invoice Date<br>(<small>Last cash Invoice for Installation Service)</small></th>
                                <th class="text-center">Amount Paid</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                            $StartRowCount=0;
                            $totalAmount=0;
                            $TotalCashInoviceInst=0;
                            
                                foreach ($annual_charges_data as $row)  
                                {  //print_r($row);
                                ?>
                            <tr>
                                <td class="text-center"><?php echo ++$StartRowCount; ?></td>
                                <td class="text-center"><a target="_blank" href="<?php echo base_url();?>employee/invoice/invoice_summary/partner/<?php echo $row->vendor_partner_id;?>"><?php echo $row->public_name;?></td>
                                <td class="text-center"><a target="_blank" href="<?php echo S3_WEBSITE_URL;?>invoices-excel/<?php echo $row->invoice_file_main;?>"><?php echo $row->invoice_id;?></a></td>
                                <td class="text-center"><?php echo date('jS M, Y', strtotime($row->from_date));?></td>
                                <td class="text-center"><?php echo date('jS M, Y', strtotime($row->to_date));?></td>
                                <td class="text-center"><?php echo "<i class='fa fa-inr'></i> ".$row->amount_collected_paid;?></td>
                                <?php /*<td class="text-center"><?php if(isset($last_inst_cash_invoce[$row->vendor_partner_id])) {echo "<a target='_blank' href='".S3_WEBSITE_URL."invoices-excel/".$last_inst_cash_invoce[$row->vendor_partner_id]['invoice']."'>".$last_inst_cash_invoce[$row->vendor_partner_id]['invoice']."</a>";} ?></td>*/ ?>
                                <?php /*<td class="text-center"><?php if(isset($last_inst_cash_invoce[$row->vendor_partner_id])) {echo $last_inst_cash_invoce[$row->vendor_partner_id]['invoice'];} ?></td> */ ?>
                                <td class="text-center"><?php if(isset($last_inst_cash_invoce[$row->vendor_partner_id])) {echo date('d-M-Y', strtotime($last_inst_cash_invoce[$row->vendor_partner_id]['invoice_date']));} ?></td>
                                <td class="text-center"><?php if(isset($last_inst_cash_invoce[$row->vendor_partner_id])) {echo "<i class='fa fa-inr'></i> ".$last_inst_cash_invoce[$row->vendor_partner_id]['amount'];$TotalCashInoviceInst=$TotalCashInoviceInst+$last_inst_cash_invoce[$row->vendor_partner_id]['amount'];} ?></td>
                            </tr>
                            <?php
                                $totalAmount=$totalAmount+$row->amount_collected_paid;                            
                                } 
                                $totalAmount=number_format((float)$totalAmount, 2, '.', '');
                                $TotalCashInoviceInst=number_format((float)$TotalCashInoviceInst, 2, '.', '');
                                ?> 
                            <?php
                            if($StartRowCount > 0)
                            {
                            ?>
                            <tfoot>
                            <tr><td></td><td></td><td></td><td></td><td class="text-center"><strong>Total Amount</strong></td><td class="text-center"><strong><i class='fa fa-inr'></i> <?php echo $totalAmount; ?></strong></td><td></td><td><strong><i class='fa fa-inr'></i> <?php echo $TotalCashInoviceInst; ?></strong></td></tr>
                            </tfoot>
                            <?php
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
<script>
    $(document).ready(function() {
    $('#annual_charges_report').DataTable({
    "processing": true, 
    "serverSide": false,  
    "dom": 'lBfrtip',
    "buttons": [
    {
        extend: 'excel',
        text: '<span class="fa fa-file-excel-o"></span>  Export',
        title: 'annual_charges_<?php echo date('Ymd-His'); ?>',
        footer: true
    }  
    ],            
    "order": [],            
    "ordering": true,     
    "deferRender": true,
    //"searching": false,
    //"paging":false
    "pageLength": 10,
     "language": {                
        "emptyTable":     "No Data Found",
        "searchPlaceholder": "Search by any column."
    },
    });
    });
</script>
<style>
#annual_charges_report_filter label
{
    float: right !important;
}
#annual_charges_report_filter .input-sm
{
    width: 272px !important;    
}
.dataTables_length label
{
    float:left;
}
.dt-buttons
{
    float:left;
    margin-left:85px;
}
.paging_simple_numbers
{
    width: 45%;
    float: right;
    text-align: right;
}
.dataTables_info
{
    width: 45%;
    float: left;
    padding-top: 30px;
}
</style>