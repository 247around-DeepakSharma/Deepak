<div id="page-wrapper" >
    <div class="container col-md-12" >
         <br/> <br/>
        <div class="panel panel-info" >
           
            <div class="panel-heading" >FOC GST Credit/Debit Note Summary </div>
            <div class="panel-body">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>FY</th>
                            <th>Total DN</th>
                            <th>DN Amount</th>
                            <th>Total CN</th>
                            <th>CN Amount</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php  
                foreach ($summary as $key => $value){ 
            ?>
                <tr>
                    <td><?php echo ++$key; ?></td>
                    <td><?php echo $value['financial_year'];?></td>
                    <td><?php echo $value['DN']; ?></td>
                    <td><?php echo $value['DN_AMOUNT']; ?></td>
                    <td><?php echo $value['CN'];?></td>
                    <td><?php echo $value['CN_AMOUNT'] ?></td>
                </tr>
           <?php } ?>
                </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>
