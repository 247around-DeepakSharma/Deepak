       <div id="page-wrapper">
           <div class="col-md-12 page-header"><h2>Bank Transaction</h2></div> 
           <div role="tabpanel" class="tab-pane" id="bank_transaction_tab">
            <div class="col-md-12">
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
                    <tbody>
                        <?php
                            $count = 1;
                           
                            ?>
                        <?php foreach ($bank_statement as $value) { ?>
                        <tr>
                            <td><?php
                                echo $count;
                                $count++;
                                ?></td>
                            <td><?php echo date("jS M, Y", strtotime($value['transaction_date'])); ?></td>
                            <td><?php
                                echo round($value['credit_amount'], 0);
                               
                                ?></td>
                            <td><?php echo $value['invoice_id']; ?></td>

                            <td><?php echo $value['description']; ?></td>
                            
                            
                           
                            <?php } ?>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>
       </div>
<script>
$(document).ready(function () {
        $('#bank_transaction_table').DataTable({
          
    
        });
    });
 </script>