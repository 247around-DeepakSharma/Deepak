<script src="<?php echo base_url(); ?>js/invoice_tag.js"></script>
<script src="<?php echo base_url();?>assest/DataTables/Buttons-1.5.1/js/dataTables.buttons.min.js"></script>
<!--<script src="<?php echo base_url();?>bower_components/buttons.dataTables/pdfmake.min.js"></script>
<script src="<?php echo base_url();?>bower_components/buttons.dataTables/vfs_fonts.js"></script>-->
<div id="page-wrapper" >
    <div class="container-fluid">
        <div class="search_invoice_id" style="border: 1px solid #e6e6e6; margin-top: 20px; margin-bottom: 20px;padding: 10px;">
            <h3><strong>Settled Invoice Details  For - <?php echo $invoice; ?></strong></h3>
            <hr>
            <div class="text-center" id="loader" style="display: none;" ><img src= '<?php echo base_url(); ?>images/loadring.gif' /></div>
            <hr>
            <section class="show_invoice_id_data">
                <table class="table table-bordered  table-hover table-striped data" id="datatable"  >
   <thead>
      <tr >
         <th>Incoming Invoice</th>
         <th>Outgoing Invoice</th>
         <th>Settled Quantity</th>
         <th>Part Name</th>
         <th>Part Number</th>   
      </tr>
   </thead>
<tbody>
    
    <?php foreach ($invoice_details as $key => $value) { ?>
                <tr>
                <td><?php echo $value->outgoing_invoice_id; ?></td>
                <td><?php echo $value->incoming_invoice_id; ?></td>
                <td><?php echo $value->settle_qty; ?></td>
                <td><?php echo $value->part_name; ?></td>
                <td><?php echo $value->part_number; ?></td>
            </tr>

    <?php }  ?>

</tbody>



                </table>
            </section>
        </div>
    </div>
</div>

 <!--Invoice Payment History Modal-->
 
<!-- end Invoice Payment History Modal -->

<script>
 
          $("#datatable").dataTable({
            dom: 'Bfrtip',
            "searching": false,
            pageLength: 50,
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export',
                    title: 'Sale_Purchase'
                }
            ]
        });
</script>
 