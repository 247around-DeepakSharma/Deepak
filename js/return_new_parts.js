
var returnItemArray = [];

function addnewpart(inventory_id, prestock){
    
    if(Number(prestock) > 0){
       $("#sellItem").removeClass("action-ban");
        if(returnItemArray.length > 0){
                var found = false;
                var foundIndex = 0;
                for(i =0; i< returnItemArray.length; i++){
                    if(returnItemArray[i]['inventory_id'] === inventory_id){

                        foundIndex = i;
                        found = true;
                        break;
                    }
                }
                
                if(found === true){
                    var addQty = 1;//$("#qty_" +inventory_id).val();
                    var qty = Number(returnItemArray[foundIndex]['quantity']) + Number(addQty);
                    if(Number(prestock) >=  qty){
                        addInArray(inventory_id, qty, foundIndex);
                    } else {
                        alert("You can not return inventory qty more than stock");
                    }
                    
                } else {
                    addInArray(inventory_id, 1, returnItemArray.length);
                    
                }
                
        } else {

            addInArray(inventory_id, 1, 0);
        }
        
    } 
}

function remove_inventory(inventory_id, index, request_type){

    var qty = Number(returnItemArray[index]['quantity']);
    var addqty = qty -1;
    if(addqty > 0){
        addInArray(inventory_id, addqty, index);
    } else {
        returnItemArray.splice(index, 1);
        if(request_type == '1'){
            $("#return_new_parts_data").find('tbody').html("");
            $("#qty_" +inventory_id).val(0);
        }else{
        $("#sell_mwh_parts_data").find('tbody').html("");
        $("#qty_" +inventory_id).val(0);
        }
    }
    crate_table(request_type);
    if(returnItemArray.length < 1){
        returnItemArray = [];
    }
    $("#sellItem").html("Return new Parts ("+ getSellItemQty() +")");
    $("#settle_item").html("Consumed Parts On OOW Booking ("+ getSellItemQty() +")");
    $("#soldItem").html("Consumed Parts Without Booking ("+ getSellItemQty() +")");
}

function addInArray(inventory_id, qty, index){
    var is_micro = Number($("#wh_id").find(':selected').attr('data-warehose'));
    var is_micro_wh = 2;
    if(is_micro === 2){
        is_micro_wh = 1;
    }
    returnItemArray[index] = [];
        returnItemArray[index] = {
            'inventory_id': inventory_id,
            'quantity':qty,
            'booking_partner_id':$('#partner_id').val(), //((is_micro === 2) ? $("#wh_id").val() : $('#partner_id').val()),
            'services':$("#services_"+inventory_id).text(),
            'service_id':$("#serviceid_"+inventory_id).text(),
            'type':$("#type_"+inventory_id).text(),
            'part_name':$("#part_name_"+inventory_id).text(),
            'part_number':$("#part_number_"+inventory_id).text(),
            'basic_price':$("#basic_"+inventory_id).text(),
            'gst_rate':$("#gst_rate_"+inventory_id).text(),
            'total_amount':$("#total_amount_"+inventory_id).text(),
            'sub_total_amount': Number(qty) * Number($("#total_amount_"+inventory_id).text()),
            'warehouse_id': $("#wh_id").val(),
            'is_micro_wh': is_micro_wh,
            'shipping_quantity':qty,
        };
        
    $("#sellItem").html("Return New Parts ("+ getSellItemQty() +")");
    $("#settle_item").html("Consumed Parts On OOW Booking ("+ getSellItemQty() +")");
    $("#soldItem").html("Consumed Parts Without Booking ("+ getSellItemQty() +")");
    $("#qty_" +inventory_id).val(qty);
}

function getSellItemQty(){
    var item =0;
    for(i =0; i< returnItemArray.length; i++){
        item = item + returnItemArray[i]['quantity'];
    }
    return item;
}

function crate_table(request_type){
    if(returnItemArray.length > 0){
        var tr = "";
        var tr1 = "";
        subtotal =0;
        totalQty = 0;
        $("#return_new_parts_data").find('tbody').html('');
        $("#sell_mwh_parts_data").find('tbody').html('');
        for(i =0; i< returnItemArray.length; i++){
            tr += '<tr>';
            tr += '<td>'+(i+1)+'</td>';
            tr += '<td>'+returnItemArray[i]['services']+'</td>';
            tr += '<td>'+returnItemArray[i]['type']+'</td>';
            tr += '<td>'+returnItemArray[i]['part_name']+'</td>';
            tr += '<td>'+returnItemArray[i]['part_number']+'</td>';
            if(request_type!=1){
                tr += '<td> <i class="fa fa-rupee" ></i> '+returnItemArray[i]['basic_price']+'</td>';
                tr += '<td>'+returnItemArray[i]['gst_rate']+'</td>';
            }
            tr += '<td>'+returnItemArray[i]['quantity']+'</td>';
            if(request_type!=1){
                var stotal = Number(returnItemArray[i]['quantity'] * returnItemArray[i]['total_amount']);
                tr += '<td> <i class="fa fa-rupee" ></i> '+stotal+'</td>';
            }
            
            tr += '<td><i class="fa fa-close" onclick="remove_inventory('+returnItemArray[i]['inventory_id']+', '+i+', '+request_type+')" style="font-size:48px;color:red; cursor:pointer"></i></td>';
            tr += '</tr>';

            totalQty += Number(returnItemArray[i]['quantity']);

            subtotal = subtotal + Number(stotal);

        }
        
        if(request_type == '1'){
           $("#return_new_parts_data").find('tbody').append(tr);
         }else{
          $("#sell_mwh_parts_data").find('tbody').append(tr); 
         }
         
         tr1 = '<tr>';
         tr1 += '<td></td>';
         tr1 += '<td></td>';
         tr1 += '<td></td>';
         tr1 += '<td></td>';
         if(request_type!=1){
            tr1 += '<td></td>';
            tr1 += '<td></td>';
        }
         tr1 += '<td><b>Total</b></td>';
         tr1 += '<td><b>'+totalQty+'</b></td>';
         if(request_type!=1){
             tr1 += '<td><b><i class="fa fa-rupee" ></i> '+subtotal.toFixed(2)+'</b></td>';
        }
         tr1 += '<td></td>';
         tr1 += '</tr>';
         if(request_type == '1'){
           $("#return_new_parts_data").find('tbody').append(tr1);  
         }else{
          $("#sell_mwh_parts_data").find('tbody').append(tr1); 
         }
        
    } else {
        $("#return_new_parts_data").remove();
        var HTMLBODy = '';
        HTMLBODy += '<table id="return_new_parts_data" class="table table-bordered table-responsive">';
        HTMLBODy += '<thead>';
        HTMLBODy += '<tr>';
        HTMLBODy += '<th>S.No</th>';
        HTMLBODy += '<th>Appliance</th>';
        HTMLBODy += '<th>Type</th>';
        HTMLBODy += '<th>Name</th>';
        HTMLBODy += '<th>Number</th>';
        HTMLBODy += '<th>Quantity</th>';
        HTMLBODy += '<th>Remove</th>';
        HTMLBODy += '</tr>';
        HTMLBODy += '</thead>';
        HTMLBODy += '<tbody></tbody>';
        HTMLBODy += '</table>';
        $("#return_new_parts_id").html(HTMLBODy);

        $("#sell_mwh_parts_data").remove();
        
        var HTMLTableBody = '';
        HTMLTableBody += '<table id="sell_mwh_parts_data" class="table table-bordered table-responsive">';
        HTMLTableBody += '<thead>';
        HTMLTableBody += '<tr>';
        HTMLTableBody += '<th>S.No</th>';
        HTMLTableBody += '<th>Appliance</th>';
        HTMLTableBody += '<th>Type</th>';
        HTMLTableBody += '<th>Name</th>';
        HTMLTableBody += '<th>Number</th>';
        HTMLTableBody += '<th>Basic Price</th>';
        HTMLTableBody += '<th>GST Rate</th>';
        HTMLTableBody += '<th>Quantity</th>';
        HTMLTableBody += '<th>Total Price</th>';
        HTMLTableBody += '<th>Remove</th>';
        HTMLTableBody += '</tr>';
        HTMLTableBody += '</thead>';
        HTMLTableBody += '<tbody></tbody>';
        HTMLTableBody += '</table>';

        $("#sell_mwh_parts_id").html(HTMLTableBody);
        
        $("#sellItem").html("Return New Parts (0)");
        $("#settle_item").html("Consumed Parts On OOW Booking (0)");
        $("#soldItem").html("Consumed Parts Without Booking (0)");
        alert("Please add new parts to return");
        
    }
    
}
function return_new_parts(){
    var formData = new FormData(document.getElementById("courier_model_form"));
    
    formData.append('inventory_data',JSON.stringify(returnItemArray));  
    formData.append("label", "WEBUPLOAD");
    formData.append("partner_id", $('#partner_id').val());
    formData.append("wh_type", $("#wh_id").find(':selected').attr('data-warehose'));
    formData.append("warehouse_id", $("#wh_id").val());
    formData.append("warehouse_name", $("#wh_id").find(':selected').text().split("-")[0]);
    formData.append("from_gst_number", $("#from_gst_number").val());
    formData.append("to_gst_number", $("#to_gst_number").val());
    formData.append("receiver_id", $("#to_wh_id").val());
   // console.log(JSON.stringify(returnItemArray));
    $.ajax({
        method:'POST',
        url: baseUrl + '/employee/user_invoice/generate_invoice_for_return_new_inventory',
        data:formData,
        contentType: false,
        processData: false,
        beforeSend: function(){
            $('#submit_courier_form').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true);
            $("#courier_model_form")[0].reset();
            $('body').loadingModal({
                position: 'auto',
                text: 'Loading Please Wait...',
                color: '#fff',
                opacity: '0.7',
                backgroundColor: 'rgb(0,0,0)',
                animation: 'wave'
            });

        },
        success:function(response){
            console.log(response);
            var data = jQuery.parseJSON(response);
            if(data.status){
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
            $('body').loadingModal('destroy');
        },
        complete: function() {
            $('#submit_courier_form').html("Submit").attr('disabled',false);
        }
    });
    
}



function get_partner_gst_number(){
    $.ajax({
        type: 'POST',
        url: baseUrl + '/employee/inventory/get_partner_gst_number',
        data:{partner_id:$('#partner_id').val()},
        success: function (response) {
            $("#to_gst_number,#mwh_to_gst_number").html(response);
        }
    });
}

function open_selected_parts_to_return(request_type){
    $('#sellItem').attr('disabled',true);
    $('#radio_partner').prop('checked',true).change();
    if(request_type == '1'){
           $("#return_new_parts_data").find('tbody').html("");  
         }else{
          $("#sell_mwh_parts_data").find('tbody').html(""); 
         }
    if(request_type == '1'){
        get_partner_gst_number();
        if(returnItemArray.length > 0){
            $("#return_new_parts_data").show();
            crate_table(request_type);
            $('#myModal').modal('toggle');
        } else {
            $("#return_new_parts_data").remove();
            $("#sellItem").html("Return New Parts (0)");
            alert("Please add new parts to return");
        } 
    }else{
        if (returnItemArray.length > 0) {
            returnItemArray[0]['invoice_type'] = request_type;
            if(request_type == 2){
                $("#mwh_action_title").html("Micro Warehouse Consumed Parts on Out Of Warranty booking");
            } else {
                $("#mwh_action_title").html("Micro Warehouse Consumed Parts Without Booking");
            }
            $("#sell_mwh_parts_data").show();
            crate_table(request_type);
            $('#mwh_used_spare_modal').modal('toggle');
        } else {
            $("#sell_mwh_parts_data").remove();
            $("#settle_item").html("Consumed Parts On OOW Booking (0)");
            $("#soldItem").html("Consumed Parts Without Booking (0)");
            alert("Please add settle parts by warehouse");

        }
    }
  
    
}

function mwh_consumed_ow() {
    var wh_type = $("#wh_id").find(':selected').attr('data-warehose');
    if (Number(wh_type) === 2) {
        var formData = new FormData(document.getElementById("consumed_ow_form"));
        formData.append('invoice_type', returnItemArray[0]['invoice_type']);
        formData.append('inventory_data', JSON.stringify(returnItemArray));
        formData.append("partner_id", $('#partner_id').val());
        formData.append("wh_type", $("#wh_id").find(':selected').attr('data-warehose'));
        formData.append("warehouse_id", $("#wh_id").val());
        formData.append("warehouse_name", $("#wh_id").find(':selected').text().split("-")[0]);
        // console.log(JSON.stringify(returnItemArray));
        if (confirm('Are you sure you want to submit ?')) {
            $.ajax({
                method: 'POST',
                url: baseUrl + '/employee/user_invoice/process_consumed_non_return_mwh_msl',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#submit_mwh_consumed_form').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled', true);
                    $("#consumed_ow_form")[0].reset();
                    $('body').loadingModal({
                        position: 'auto',
                        text: 'Loading Please Wait...',
                        color: '#fff',
                        opacity: '0.7',
                        backgroundColor: 'rgb(0,0,0)',
                        animation: 'wave'
                    });

                },
                success: function (response) {
                    console.log(response);
                    var data = jQuery.parseJSON(response);
                    if (data.status) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                    $('body').loadingModal('destroy');
                },
                complete: function () {
                    $('#submit_mwh_consumed_form').html("Submit").attr('disabled', false);
                }
            });
        }
    } else {
       alert('It allows only for Micro Warehouse');
    }
}

function check_awb_exist(){
    var awb = $("#awb").val();
    var mwh_awb = $("#mwh_awb").val();
    if(mwh_awb != ''){
      awb = mwh_awb;
    }
    
    if(awb){
            $.ajax({
            type: 'POST',
            beforeSend: function(){

            $('body').loadingModal({
                position: 'auto',
                text: 'Loading Please Wait...',
                color: '#fff',
                opacity: '0.7',
                backgroundColor: 'rgb(0,0,0)',
                animation: 'wave'
            });

                },
            url: baseUrl + '/employee/service_centers/check_wh_shipped_defective_awb_exist',
            data:{awb:awb},
            success: function (response) {
                console.log(response);
                var data = jQuery.parseJSON(response);
                if(data.code === 247){
                    alert("This AWB already used same price will be added");
                    $("#same_awb").css("display","block");
                    $('body').loadingModal('destroy');
                    if(mwh_awb != ''){
                        $("#mwh_shipped_date").val(data.message[0].shipment_date);
                        $('#mwh_courier_name option[value="' + data.message[0].courier_name + '"]').attr("selected", "selected");
                        if(data.message[0].courier_file){
                            $("#mwh_exist_courier_image").val(data.message[0].courier_file);
                        }
                        $('#mwh_shipped_spare_parts_boxes_count option[value="' + data.message[0]['box_count'] + '"]').attr("selected", "selected");
                        if (data.message[0]['box_count'] === 0) {
                            $('#mwh_shipped_spare_parts_small_boxes_count').val("");
                        } else {
                            $('#mwh_shipped_spare_parts_boxes_count').val(data.message[0]['box_count']).trigger('change');
                        }                            
                        var wt = Number(data.message[0]['billable_weight']);
                        if(wt > 0){
                        var wieght = data.message[0]['billable_weight'].split(".");
                            $("#mwh_shipped_spare_parts_weight_in_kg").val(wieght[0]).attr('readonly', "readonly");
                            $("#mwh_shipped_spare_parts_weight_in_gram").val(wieght[1]).attr('readonly', "readonly");
                        }
                    }else{
                        $("#shippped_date").val(data.message[0].shipment_date); 
                        $("#courier_name").val(data.message[0].courier_name);
                        $("#courier_price").val("0");
                        $("#courier_price").css("display","none");
                        if(data.message[0].courier_file){
                            $("#exist_courier_image").val(data.message[0].courier_file);
                            $("#shippped_courier").css("display","none");
                        }
                        $('#shipped_spare_parts_boxes_count option[value="' + data.message[0]['box_count'] + '"]').attr("selected", "selected");
                        if (data.message[0]['box_count'] === 0) {
                            $('#shipped_spare_parts_boxes_count').val("");
                        } else {
                            $('#shipped_spare_parts_boxes_count').val(data.message[0]['box_count']).trigger('change');
                        }                            
                        var wt = Number(data.message[0]['billable_weight']);
                        if(wt > 0){
                        var wieght = data.message[0]['billable_weight'].split(".");
                            $("#shipped_spare_parts_weight_in_kg").val(wieght[0]).attr('readonly', "readonly");
                            $("#shipped_spare_parts_weight_in_gram").val(wieght[1]).attr('readonly', "readonly");
                        }

                    }                   
                } else {

                    $('body').loadingModal('destroy');
                    $("#shippped_courier_pic").css("display","block");
                    $("#courier_price").css("display","block");
                    $("#same_awb").css("display","none");
                    $("#exist_courier_image").val("");
                    $("#shipped_spare_parts_weight_in_kg").removeAttr("readonly");
                    $("#shipped_spare_parts_weight_in_gram").removeAttr("readonly");
                    $("#mwh_shipped_spare_parts_weight_in_kg").removeAttr("readonly");
                    $("#mwh_shipped_spare_parts_weight_in_gram").removeAttr("readonly");
                }

            }
        });
    }
}