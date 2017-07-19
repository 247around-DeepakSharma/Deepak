<div class="vendor_partner_name">
    <div class="vendor_partner" style="margin-bottom: 10px;">
        <h4><strong>Vendor Name : </strong> <a href="<?php echo base_url(); ?>employee/invoice/invoice_summary/<?php echo $invoiceid_data[0]['vendor_partner'];?>/<?php echo $invoiceid_data[0]['vendor_partner_id'];?>" target="_blank"><?php echo $vendor_name[0]['name']; ?></a></h4>
    </div> 
</div>
<hr>
<table class="table table-bordered  table-hover table-striped data"  >
    <thead>
        <tr >
            <th>S.No</th>
            <th>Invoice Id</th>
            <th>Main Invoice File</th>
            <th>Detailed Invoice File</th>
            <th>Type</th>
            <th>Bookings</th>
            <th>Invoice Period</th>
            <th>Service Charges</th>
            <th>Additional Service Charges</th>
            <th>Parts / Stands</th>
            <th>TDS Amount</th>
            <th>Upcountry Charges</th>
            <th>Courier Charges</th>
            <th>Penalty</th>
            <th>Amount to be Paid By 247Around</th>
            <th>Amount to be Paid By Partner</th>
            <th>Amount Paid</th> 
        </tr>
    </thead>
    <tbody>
        <?php $sn=1; foreach($invoiceid_data as $key=> $value){ ?>
        <tr>
            <td><?php echo $sn; ?></td>
            <td><?php echo $value['invoice_id']; ?></td>
            <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $value['invoice_file_main']; ?>" target="_blank"><?php echo $value['invoice_file_main']; ?></a></td>
            <td><a href="https://s3.amazonaws.com/bookings-collateral/invoices-excel/<?php echo $value['invoice_detailed_excel']; ?>" target="_blank"><?php echo $value['invoice_detailed_excel']; ?></a></td>
            <td><?php echo $value['type']; ?></td>
            <td><?php echo $value['num_bookings']; ?></td>
            <td><?php echo date("jS M, Y", strtotime($value['from_date'])). " <b>to</b> ". date("jS M, Y", strtotime($value['to_date'])); ?></td>
            <td><?php echo ($value['total_service_charge'] + $value['service_tax']); ?></td>
            <td><?php echo $value['total_additional_service_charge']; ?></td>
            <td><?php echo ($value['parts_cost'] + $value['vat']); ?></td>
            <td><?php echo $value['tds_amount']; ?></td>
            <td><?php if($value['type'] == "Cash" && $value['vendor_partner'] == "vendor") { echo "-".$value['upcountry_price'];} else { echo $value['upcountry_price']; } ?></td>
            <td><?php echo $value['courier_charges']; ?></td>
            <td><?php echo "-".$value['penalty_amount']; ?></td>
            <td><?php  if($value['amount_collected_paid'] < 0){ echo $value['amount_collected_paid'];} ?></td>
            <td><?php if($value['amount_collected_paid'] > 0){ echo $value['amount_collected_paid']; } ?></td>
            <td><?php echo $value['amount_paid'] ?></td>
            
        </tr>
        <?php } ?>
    </tbody>
</table>