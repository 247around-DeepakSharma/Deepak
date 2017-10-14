
/*
 * this code section has written for advance search
 */
var ad_table;
    $(document).ready(function () {
        
        $('input[name="booking_date"]').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'YYYY/MM/DD',
                 cancelLabel: 'Clear'
            }
        });
        
        $('input[name="close_date"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY/MM/DD',
                     cancelLabel: 'Clear'
                }
        });
        
        $('input[name="booking_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));    
            ad_table.ajax.reload( function ( json ) {
                   //create_dropdown();
            });
        });
        
        $('input[name="booking_date"]').on('cancel.daterangepicker', function (ev, picker) {
            var value = $('input[name="booking_date"]').val();
            if(value !== ''){
                $(this).val('');
                ad_table.ajax.reload( function ( json ) {
                  //create_dropdown();
                });
            }
        });
        
        $('input[name="close_date"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
            ad_table.ajax.reload( function ( json ) {
               //create_dropdown();
            });
        });

        $('input[name="close_date"]').on('cancel.daterangepicker', function(ev, picker) {
            var value1 = $('input[name="close_date"]').val();
            if(value1 !== ''){
                $(this).val('');
                ad_table.ajax.reload( function ( json ) {
                   //create_dropdown();
                });
            }
        });
        
        
        ad_table = $('#datatable1').DataTable({
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
            "pageLength": 50,
            "deferLoading": 0,
            // Load data for the table's content from an Ajax source
            "ajax": {
                //"url": "http://localhost/247around/employee/booking/get_advance_search_result_view",
                "url": baseUrl+"/employee/booking/get_advance_search_result_view",
                "type": "POST",
                "data": function(d){
                    d.booking_date = $('input[name="booking_date"]').val();
                    d.close_date = $('input[name="close_date"]').val();
                    d.partner =  $("#partner option:selected").val();
                    d.city =     $("#city option:selected").val();
                    d.sf =       $("#sf option:selected").val();
                    d.current_status =  $("#current_status option:selected").val();
                    d.internal_status =  $("#internal_status option:selected").val();
                    d.product_or_service =  $("#product_or_service option:selected").val();
                    d.upcountry =  $("#upcountry option:selected").val();
                    d.rating =  $("#rating option:selected").val();
                    d.service =  $("#service option:selected").val();
                    d.categories =  $("#categories option:selected").val();
                    d.capacity =  $("#capacity option:selected").val();
                    d.brand =  $("#brand option:selected").val();
                    d.paid_by =  $("#paid_by option:selected").val();
                    d.request_type =  $("#request_type option:selected").val();
                 }
            },
            "drawCallback": function( settings ) {
               $('input[type="search"]').attr("name", "search_value");
               //create_dropdown();
            },
            //Set column definition initialisation properties.
            "columnDefs": [
                {
                    "targets": [0, 1, 8, 9,10], //first column / numbering column
                    "orderable": false //set not orderable
                }
            ],
            "fnInitComplete": function (oSettings, response) {
               //$("#count_total_order").text(response.recordsTotal);

            }
        });
        
    });
    /*
     * End of advance search
     */
    
     function submitPincodeForm(data){
         console.log(JSON.stringify(data));
        document.getElementById("pincode").value=data.pincode;
        document.getElementById("city").value=data.city;
        document.getElementById("state").value=data.state;
        document.getElementById("service").value=JSON.stringify(data.service);
        document.getElementById("pincodeForm").submit();
    }
    
    function missingPincodeDetailedView(data){
        var table = document.getElementById("mssingPincodeTable");
        var rowCount = table.rows.length;
        for (var x = rowCount-1; x >0; x--) {
                     table.deleteRow(x);
        }
        var count = data.service.length;
        for(var i=0;i<count;i++){
            var row = table.insertRow(i+1);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            cell1.innerHTML = data.pincode;
            cell2.innerHTML = data.city;
            cell3.innerHTML = data.state;
            cell4.innerHTML = data.service[i].service_name;
           cell5.innerHTML = data.service[i].pincodeCount;
        }
        
        
    }
    
     