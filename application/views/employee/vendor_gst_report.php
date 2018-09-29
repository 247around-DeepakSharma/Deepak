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
                    <td><?php  echo $value->name; ?></td>
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
