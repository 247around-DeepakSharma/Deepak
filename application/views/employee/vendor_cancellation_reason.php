<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row" style="margin-top:40px; ">
            <div class="col-lg-12">
            <table class="table  table-striped table-bordered">
            <thead>
            	<th>Cancellation Reason </th>
            	<th>Total</th>
            </thead>
            <tbody>
            	<?php foreach ($reason as $value) { ?>
            		<tr>
            		<td><?php echo $value['cancellation_reason'];?></td>
            		<td><?php echo $value['count'];?></td>
            		</tr>
            	<?php } ?>
            </tbody>	
            </table>
            </div>
        </div>
    </div>
</div>