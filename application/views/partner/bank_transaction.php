<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Bank Transaction</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table class="table table-bordered table-hover table-striped data" id="bank_transaction_table" >
                        <thead>
                            <tr>
                                <th>No #</th>
                                <th>Transaction Date</th>
                                <th>Amt Received</th>
                                <th>Invoices</th>
                                <th>Description</th>
                            </tr>
                        </thead>
<!--                        <tbody>
                            <?php
                            //$count = 1;
                            ?>
                            <?php //foreach ($bank_statement as $value) { ?>
                                <tr>
                                    <td><?php //echo $count; $count++;?></td>
                                    <td><?php //echo date("d-M-Y", strtotime($value['transaction_date'])); ?></td>
                                    <td><?php //echo round($value['credit_amount'], 0);?></td>
                                    <td><?php //echo $value['invoice_id']; ?></td>
                                    <td><?php //echo $value['description']; ?></td>
                            <?php //} ?>
                            </tr>
                        </tbody>-->
                    </table>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#bank_transaction_table').DataTable({
         "processing": true, //Feature control the processing indicator.
         "serverSide": true, //Feature control DataTables' server-side processing mode.
         "order": [[ 1, "desc" ]], //Initial no order.
         "pageLength": 10,
          dom: 'lBfrtip',
         "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50, 100,"All"]],
          buttons: [
                {
                    extend: 'excel',
                    text: '<span class="fa fa-file-excel-o"></span> Excel Export',
                    pageSize: 'LEGAL',
                    title: 'Invoice',
                    exportOptions: {
                       columns: [1,2,3,4],
                        modifier : {
                             // DataTables core
                             order : 'index',  // 'current', 'applied', 'index',  'original'
                             page : 'All',      // 'all',     'current'
                             search : 'none'     // 'none',    'applied', 'removed'
                         }
                    }
                    
                }
            ],
         // Load data for the table's content from an Ajax source
            ajax: {
                url: "<?php echo base_url();?>employee/accounting/get_payment_summary_searched_data",
                type: "POST",
                data: function(d){

                         d.request_type = "partner_bank_transaction";
                         d.vendor_partner = 'partner';
                         d.vendor_partner_id = '<?php echo $this->session->userdata('partner_id'); ?>';
                    }

            },

            //Set column definition initialisation properties.
            columnDefs: [
                {
                    targets: [0,1,2,3,4], //first column / numbering column
                    orderable: false //set not orderable
                }
            ],
            fnInitComplete: function (oSettings, response) {
              //$('#datatable tr span.satteled_row').each(function(){ $(this).closest('tr').css("background-color", "#90EE90")  });
            }

        });
    });
 </script>