<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js"></script>
       
 <div class="row" style="background-color:lightgrey;" >
       
                <div class="col-md-12">
                    <div class="col-md-4" id="piechart" style="height:500px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);"></div>
                     <div class="col-md-4" id="services_piechart" style=" height:500px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);"></div>
                      <div class="col-md-4" id="main_invoice_piechart" style=" height:500px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);"></div>
                      
                          
                    
                </div>
            <div class="col-md-12" style="margin-top:15px;">
                <div class="col-md-4"  style=" height:500px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);">
                    <div id="duplicate_booking_id" style="height:100%"></div>
                   
                </div>
                <div class="col-md-4" id="bbbb" style="height:500px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);">
                    <div id="installation_not_added" style="height:100%">
                        
                    </div>
                </div>
                <div class="col-md-4" id=aaaa style="height:500px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);">
                   <div style="height:100%">
                        
                    </div>
                </div>
            </div>
            
    </div>
        
       
        <script type="text/javascript">
            // Load the Visualization API and the line package.
            google.charts.load('current', {'packages':['corechart']});
            // Set a callback to run when the Google Visualization API is loaded.
            google.charts.setOnLoadCallback(drawChart);
              
            function drawChart() {
                 document.getElementById("duplicate_booking_id").innerHTML = '';
                document.getElementById("duplicate_booking_id").style.backgroundColor = "transparent";
                document.getElementById("installation_not_added").innerHTML = '';
                document.getElementById("installation_not_added").style.backgroundColor = "transparent";
                 $.ajax({
                   type: 'POST',
                   url: '<?php echo base_url()."employee/invoiceDashboard/get_count_unit_details" ?>',
                   success: function (data1) {
                     var data = new google.visualization.DataTable();
                     // Add legends with data type
                     data.addColumn('string', 'Source');
                     data.addColumn('number', 'Total Line Item');
                     data.addColumn('number', 'Partner Id');
                     
                    //Parse data into Json
                    var jsonData = $.parseJSON(data1);
                    var line_item = 0;
                    for (var i = 0; i < jsonData.length; i++) {
                       line_item += parseInt(jsonData[i].total_unit);
                      data.addRow([jsonData[i].source, parseInt(jsonData[i].total_unit), parseInt(jsonData[i].partner_id)]);
                    }
            
                    var options = {
                     legend: '',
                     pieSliceText: 'label',
                     title: 'Total Completed Booking Unit - ' + line_item,
                     sliceVisibilityThreshold: .000000025
                     
                   };
            
                   var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                   function selectHandler() {
                   var selectedItem = chart.getSelection()[0];
                   if (selectedItem) {
                     var partner_id = data.getValue(selectedItem.row, 2);
                     var source = data.getValue(selectedItem.row, 0);
                      services_pie_chart(partner_id, source);
                      main_invoice(partner_id, source);
                      check_duplicate_entry(partner_id, source);
                   }
                 }
            
            google.visualization.events.addListener(chart, 'select', selectHandler);    
            chart.draw(data, options);
                  }
               });
             }
             
             function services_pie_chart(partner_id, source){
             
             $.ajax({
                   type: 'POST',
                   url: '<?php echo base_url()."employee/invoiceDashboard/get_count_services/" ?>' + partner_id,
                   success: function (data1) {
                     var data = new google.visualization.DataTable();
                     
                     // Add legends with data type
                     data.addColumn('string', 'Appliance');
                     data.addColumn('number', 'Total Line Item');
                     data.addColumn('number', 'Service Id');
            
                    //Parse data into Json
                    var jsonData = $.parseJSON(data1);
                   
                    var line_item = 0;
                    for (var i = 0; i < jsonData.length; i++) {
                       line_item += parseInt(jsonData[i].total_unit);
                      data.addRow([jsonData[i].services, parseInt(jsonData[i].total_unit), parseInt(jsonData[i].service_id)]);
                    }
            
                    var options = {
                     legend: '',
                     pieSliceText: 'label',
                     title: source + " Completed Appliances - " +line_item,
                     sliceVisibilityThreshold: .000000025,
                     is3D: true
                   };
                   
                   
                   var chart = new google.visualization.PieChart(document.getElementById('services_piechart'));
                   chart.draw(data, options);
            
             }
             });
             }
             
             function main_invoice(partner_id, source){
               $.ajax({
                   type: 'POST',
                   url: '<?php echo base_url()."employee/invoiceDashboard/get_main_invoice/" ?>' + partner_id,
                   success: function (data1) {
                    
                     var data2 = new google.visualization.DataTable();
                     data2.addColumn('string', 'Description');
                     data2.addColumn('number', 'Total Line Item');
                     data2.addColumn('number', 'Partner Id');
                     
                     //Parse data into Json
                    var jsonData = $.parseJSON(data1);
                     
                    var line_item2 = 0;
                    for (var i = 0; i < jsonData.length; i++) {
                       line_item2 += parseInt(jsonData[i].qty);
                      data2.addRow([jsonData[i].description, parseInt(jsonData[i].qty),  parseInt(partner_id)]);
                    }
                    
                     var options = {
                     legend: '',
                     pieSliceText: 'label',
                     title: source + " Main Invoice - " +line_item2,
                     sliceVisibilityThreshold: .000000025,
                     is3D: true
                   };
                   
                   var chart = new google.visualization.PieChart(document.getElementById('main_invoice_piechart'));
                   chart.draw(data2, options);
               }
                   
                  
               });
             
             }
             
            function check_duplicate_entry(partner_id, source){
             
              $.ajax({
                   type: 'POST',
                   url: '<?php echo base_url()."employee/invoiceDashboard/check_duplicate_completed_booking/" ?>' + partner_id,
                   success: function (data1) {
                       var jsonData = $.parseJSON(data1);
                       console.log(jsonData.length );
                       if(jsonData.length > 0){
                        $("#check_duplicate").css("display", "inline");
                        document.getElementById("duplicate_booking_id").style.backgroundColor = "#fff";
                        
                        var tr_html = "";
                        tr_html += '<h4 class="text-center" style="padding-top:25px;">'+ source+' Duplicate Booking ID<h4>';
                        tr_html += '<div class="table-responsive" ><table class="table table-bordered table-hover table-striped">';
                        tr_html += '<thead><tr><th>Booking ID</th><th>Price tags</th></tr>';
                        
                       for (var i = 0; i < jsonData.length; i++) {
                       
                          tr_html += '<tr><td>'+jsonData[i].booking_id+'</td><td>'+jsonData[i].price_tags+'</td></tr>';
                       }
                                tr_html += '<tbody></table></div>';
                       
                          document.getElementById("duplicate_booking_id").innerHTML = tr_html;
                       } else {
                          document.getElementById("duplicate_booking_id").innerHTML = '';
                          document.getElementById("duplicate_booking_id").style.backgroundColor = "transparent";
                       }
                   }
               });
             
             
             }
             
             function installation_not_added(){
             $.ajax({
                   type: 'POST',
                   url: '<?php echo base_url()."employee/invoiceDashboard/installation_not_added/" ?>' + partner_id,
                   success: function (data1) {
                       var jsonData = $.parseJSON(data1);
                       console.log(jsonData.length );
                       if(jsonData.length > 0){
                        $("#installation_not_added").css("display", "inline");
                        document.getElementById("installation_not_added").style.backgroundColor = "#fff";
                        
                        var tr_html = "";
                        tr_html += '<h4 class="text-center" style="padding-top:25px;">Installation Not Added<h4>';
                        tr_html += '<div class="table-responsive" ><table class="table table-bordered table-hover table-striped">';
                        tr_html += '<thead><tr><th>Booking ID</th>/tr>';
                        
                       for (var i = 0; i < jsonData.length; i++) {
                       
                          tr_html += '<tr><td>'+jsonData[i].booking_id+'</td></tr>';
                       }
                                tr_html += '<tbody></table></div>';
                       
                          document.getElementById("installation_not_added").innerHTML = tr_html;
                       } else {
                          document.getElementById("installation_not_added").innerHTML = '';
                          document.getElementById("installation_not_added").style.backgroundColor = "transparent";
                       }
                   }
               });
             
             }
        </script>
    </body>
</html>