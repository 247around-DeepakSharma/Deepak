<div id="page-wrapper" >
	<div class="container-fluid" >
		<div class="panel panel-info" style="margin-top:20px;">
			<div class="panel-heading">
				<h2>Agent Daily Reports</h2>
			</div>
			<div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php $last_url_parm = $this->uri->segment(3);  ?>
                                    
                                <select  onchange="get_data()" class="form-control"  id="period" name="period" >
                                    
                                    <option  disabled>Select Period</option>
                                    <option  value = "date" <?php if($last_url_parm == 'date'){?> selected <?php }?> >Current Date</option>
                                    <option value="month" <?php if($last_url_parm == 'month'){?> selected <?php }?>>Current Month</option>
                                 </select>
                                       
                                </div>
                            </div>
                            
                            
                            <div id="chart_container" style="height: 100%;margin-top:20px;width: 100%"></div>

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
                            
                            <script type="text/javascript">
                                $('#period').select2();
                                
                                function get_data()
                                {
                                    var period = $("#period option:selected").val();
                                    var href = '<?php echo base_url() ?>BookingSummary/agent_working_details/'+period;
                                    window.location.href = href;
                                }
                             </script>
                              <script src="http://code.highcharts.com/highcharts.js"></script>
                              <script src="http://code.highcharts.com/modules/exporting.js"></script>

                             
                             <script>
                                 
                                 
                                 var agent_name = [];
                                 var query_insert = [];
                                 var query_update = [];
                                 var query_cancel = [];
                                 var query_booking = [];
                                 var booking_insert = [];
                                 var booking_cancel = [];
                                 var booking_completed = [];
                                 var booking_rescheduled = [];
                                 var escalation = [];
                                 var calls_placed = [];
                                 var calls_received = [];
                                     
                                <?php foreach ($data as $value) { ?>
                                     agent_name.push("<?php echo $value['employee_id'];?>");
                                     query_insert.push(parseInt("<?php echo $value['new_query_to_followup'];?>"));
                                     query_update.push(parseInt("<?php echo $value['followup_to_followup'];?>"));
                                     query_cancel.push(parseInt("<?php echo $value['followup_to_cancel'];?>"));
                                     query_booking.push(parseInt("<?php echo $value['followup_to_pending'];?>"));
                                     booking_insert.push(parseInt("<?php echo $value['booking_insert'];?>"));
                                     booking_cancel.push(parseInt("<?php echo $value['pending_to_cancel'];?>"));
                                     booking_completed.push(parseInt("<?php echo $value['pending_to_completed'];?>"));
                                     booking_rescheduled.push(parseInt("<?php echo $value['pending_to_rescheduled'];?>"));
                                     escalation.push(parseInt("<?php echo $value['pending_to_escalation'];?>"));
                                     calls_placed.push(parseInt("<?php echo $value['calls_placed'];?>"));
                                     calls_received.push(parseInt("<?php echo $value['calls_recevied'];?>"));
                                     
                                     
                                <?php } ?>
                                    
//                               /**
// * Create the data table
// */
//Highcharts.drawTable = function() {
//    
//    // user options
//    var tableTop = 390,
//        colWidth = 100,
//        tableLeft = 20,
//        rowHeight = 20,
//        cellPadding = 2.5,
//        valueDecimals = 0,
//        valueSuffix = ' ';
//        
//    // internal variables
//    var chart = this,
//        series = chart.series,
//        renderer = chart.renderer,
//        cellLeft = tableLeft;
//
//    // draw category labels
//    $.each(chart.xAxis[0].categories, function(i, name) {
//        renderer.text(
//            name, 
//            cellLeft + cellPadding, 
//            tableTop + (i + 2) * rowHeight - cellPadding
//        )
//        .css({
//            fontWeight: 'bold'
//        })       
//        .add();
//    });
//
//    $.each(series, function(i, serie) {
//        cellLeft += colWidth;
//        
//        // Apply the cell text
//        renderer.text(
//                serie.name,
//                cellLeft - cellPadding + colWidth, 
//                tableTop + rowHeight - cellPadding
//            )
//            .attr({
//                align: 'right'
//            })
//            .css({
//                fontWeight: 'bold'
//            })
//            .add();
//        
//        $.each(serie.data, function(row, point) {
//            
//            // Apply the cell text
//            renderer.text(
//                    Highcharts.numberFormat(point.y, valueDecimals) + valueSuffix, 
//                    cellLeft + colWidth - cellPadding, 
//                    tableTop + (row + 2) * rowHeight - cellPadding
//                )
//                .attr({
//                    align: 'right'
//                })
//                .add();
//            
//            // horizontal lines
//            if (row == 0) {
//                Highcharts.tableLine( // top
//                    renderer,
//                    tableLeft, 
//                    tableTop + cellPadding,
//                    cellLeft + colWidth, 
//                    tableTop + cellPadding
//                );
//                Highcharts.tableLine( // bottom
//                    renderer,
//                    tableLeft, 
//                    tableTop + (serie.data.length + 1) * rowHeight + cellPadding,
//                    cellLeft + colWidth, 
//                    tableTop + (serie.data.length + 1) * rowHeight + cellPadding
//                );
//            }
//            // horizontal line
//            Highcharts.tableLine(
//                renderer,
//                tableLeft, 
//                tableTop + row * rowHeight + rowHeight + cellPadding,
//                cellLeft + colWidth, 
//                tableTop + row * rowHeight + rowHeight + cellPadding
//            );
//                
//        });
//        
//        // vertical lines        
//        if (i == 0) { // left table border  
//            Highcharts.tableLine(
//                renderer,
//                tableLeft, 
//                tableTop + cellPadding,
//                tableLeft, 
//                tableTop + (serie.data.length + 1) * rowHeight + cellPadding
//            );
//        }
//        
//        Highcharts.tableLine(
//            renderer,
//            cellLeft, 
//            tableTop + cellPadding,
//            cellLeft, 
//            tableTop + (serie.data.length + 1) * rowHeight + cellPadding
//        );
//            
//        if (i == series.length - 1) { // right table border    
// 
//            Highcharts.tableLine(
//                renderer,
//                cellLeft + colWidth, 
//                tableTop + cellPadding,
//                cellLeft + colWidth, 
//                tableTop + (serie.data.length + 1) * rowHeight + cellPadding
//            );
//        }
//        
//    });
//    
//        
//};
//
///**
// * Draw a single line in the table
// */
//Highcharts.tableLine = function (renderer, x1, y1, x2, y2) {
//    renderer.path(['M', x1, y1, 'L', x2, y2])
//        .attr({
//            'stroke': 'silver',
//            'stroke-width': 1
//        })
//        .add();
//}     
                                    
                               window.chart = new Highcharts.Chart({

                                        chart: {
                                            renderTo: 'chart_container',
                                            events: {
                                                load: Highcharts.drawTable
                                            },
                                        },
                                        title: {
                                            text: 'Agent Daily Report',
                                            x: -20 //center
                                        },
                                        
                                        xAxis: {
                                            categories: agent_name
                                        },
                                        yAxis: {
                                            title: {
                                                text: 'Count'
                                            },
                                            plotLines: [{
                                                value: 0,
                                                width: 1,
                                                color: '#808080'
                                            }]
                                        },
                                        
                                        legend: {
                                            layout: 'vertical',
                                            align: 'right',
                                            verticalAlign: 'middle',
                                            borderWidth: 0
                                        },
                                        series: [{
                                            name: 'Query Insert',
                                            data: query_insert
                                        }, {
                                            name: 'Query Update',
                                            data: query_update
                                        }, {
                                            name: 'Query Cancel',
                                            data: query_cancel
                                        }, {
                                            name: 'Query Booking',
                                            data: query_booking
                                        }, {
                                            name: 'Booking Insert',
                                            data: booking_insert
                                        }, {
                                            name: 'Booking Cancel',
                                            data: booking_cancel
                                        }, {
                                            name: 'Booking Completed',
                                            data: booking_completed
                                        }, {
                                            name: 'Booking Rescheduled',
                                            data: booking_rescheduled
                                        }, {
                                            name: 'Escalation',
                                            data: escalation
                                        }, {
                                            name: 'Outgoing Calls',
                                            data: calls_placed
                                        }, {
                                            name: 'Incomming Calls',
                                            data: calls_received
                                        }]
                                    });
                                
    

                             </script>
                            


				