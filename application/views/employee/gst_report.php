<div id="page-wrapper" >
    <div class="container col-md-12" >
        <div class="panel panel-info" >
            <div class="panel-heading" >Vendor GST Detail</div>
            <div class="panel-body">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Amount till last FY</th>
                            <th>Total Amound</th>
                        </tr>
                    </thead>
                    <tbody>
         <?php   foreach ($data as $value){ ?>
                <tr>
                    <td><a href="<?php echo base_url(); ?>employee/invoice/invoice_summary/vendor/<?php echo $value->vendor_partner_id; ?>"><?php  echo $value->name; ?></a></td>
                   <td><?php echo $value->fy_amount; ?></td>
                    <td><?php echo $value->total_amount; ?></td>
                </tr>
           <?php } ?>
               </tbody>
                 </table>
            </div>
            </div>
        </div>
    </div>
</div>
