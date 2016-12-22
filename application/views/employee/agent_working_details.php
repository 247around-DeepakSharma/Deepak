<div id="page-wrapper" >
	<div class="container-fluid" >
		<div class="panel panel-info" style="margin-top:20px;">
			<div class="panel-heading">
				<h2>Agent Daily Reports</h2>
			</div>
			<div class="panel-body">
				<table class="table table-striped table-bordered" id="sum_table">
			        <tr class="titlerow">
			            <th>Agent Name</th>
			            <th>Query Insert</th>
						<th>Query To Query</th>
						<th>Query To Cancel</th>
						<th>Query To Booking</th>
						<th>Booking Insert</th> 
						<th>Booking Cancel</th>
						<th>Booking Completed</th>
						<th>Booking Rescheduled</th>
						<th>Escalation</th>
						<th>Outgoing Call Placed</th>
						<th>Incoming Call Received</th>
			        </tr>

			        <tbody>
				        <?php foreach($data as $key=>$value) { ?>
							<tr>
								<td><?php echo $value['employee_id']; ?></td>
								<td id="input_count"><?php echo $value['new_query_to_followup']; ?></td>
								<td id="input_count"><?php echo $value['followup_to_followup']; ?></td>
								<td id="input_count"><?php echo $value['followup_to_cancel']; ?></td>
								<td id="input_count"><?php echo $value['followup_to_pending']; ?></td>
								<td id="input_count"><?php echo $value['booking_insert']; ?></td>
								<td id="input_count"><?php echo $value['pending_to_cancel']; ?></td>
								<td id="input_count"><?php echo $value['pending_to_completed']; ?></td>
								<td id="input_count"><?php echo $value['pending_to_rescheduled']; ?></td>
								<td id="input_count"><?php echo $value['pending_to_escalation']; ?></td>
								<td id="input_count"><?php echo $value['calls_placed']; ?></td>
								<td id="input_count"><?php echo $value['calls_recevied']; ?></td>
							</tr>					
						<?php } ?>
						<tr class="totalColumn info">
						    <td><b>Total<b></td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						    <td class="totalCol">-</td>
						</tr>
			        
			        </tbody>

			    </table>

			    <script type="text/javascript">
			    	$("#sum_table tr:last td:not(:first)").text(function(i){
					    var t = 0;
					    $(this).parent().prevAll().find("td:nth-child("+(i+2)+")").each(function(){
					        //console.log(t);
					        t += parseInt( $(this).text(), 10 ) || 0;
					    });
					    return t;
					});
			    </script>


				