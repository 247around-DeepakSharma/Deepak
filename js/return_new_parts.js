
var returnItemArray = [];

function addnewpart(inventory_id, prestock){
    
    if(Number(prestock) > 0){
       
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

function remove_inventory(inventory_id, index){
   

    var qty = Number(returnItemArray[index]['quantity']);
    var addqty = qty -1;
    if(addqty > 0){
        addInArray(inventory_id, addqty, index);
    } else {
        
        returnItemArray.splice(index, 1);
        $("#return_new_parts_data").find('tbody').append("");
        $("#qty_" +inventory_id).val(0);
    }
    crate_table();
    if(returnItemArray.length < 1){
        returnItemArray = [];
    }
}

function addInArray(inventory_id, qty, index){
    
    returnItemArray[index] = [];
        returnItemArray[index] = {
            'inventory_id': inventory_id,
            'quantity':qty,
            'booking_partner_id':$('#partner_id').val(),
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
            'is_micro': $("#wh_id").find(':selected').attr('data-id'),
            'shipping_quantity':qty,
        };
        
    $("#sellItem").val("Return new Parts ("+ getSellItemQty() +")");
    $("#qty_" +inventory_id).val(qty);
}

function getSellItemQty(){
    var item =0;
    for(i =0; i< returnItemArray.length; i++){
        item = item + returnItemArray[i]['quantity'];
    }
    return item;
}

function crate_table(){
    if(returnItemArray.length > 0){
        var tr = "";
        var tr1 = "";
        subtotal =0;
        totalQty = 0;
        $("#return_new_parts_data").find('tbody').html('');
        for(i =0; i< returnItemArray.length; i++){
            tr += '<tr>';
            tr += '<td>'+(i+1)+'</td>';
            tr += '<td>'+returnItemArray[i]['services']+'</td>';
            tr += '<td>'+returnItemArray[i]['type']+'</td>';
            tr += '<td>'+returnItemArray[i]['part_name']+'</td>';
            tr += '<td>'+returnItemArray[i]['part_number']+'</td>';
            tr += '<td> <i class="fa fa-rupee" ></i> '+returnItemArray[i]['basic_price']+'</td>';
            tr += '<td>'+returnItemArray[i]['gst_rate']+'</td>';
            tr += '<td>'+returnItemArray[i]['quantity']+'</td>';
            var stotal = Number(returnItemArray[i]['quantity'] * returnItemArray[i]['total_amount']);
            tr += '<td> <i class="fa fa-rupee" ></i> '+stotal+'</td>';
            
            tr += '<td><i class="fa fa-close" onclick="remove_inventory('+returnItemArray[i]['inventory_id']+', '+i+')" style="font-size:48px;color:red; cursor:pointer"></i></td>';
            tr += '</tr>';

            totalQty += Number(returnItemArray[i]['quantity']);

            subtotal = subtotal + Number(stotal);

        }
        $("#return_new_parts_data").find('tbody').append(tr);
         tr1 = '<tr>';
         tr1 += '<td></td>';
         tr1 += '<td></td>';
         tr1 += '<td></td>';
         tr1 += '<td></td>';
         tr1 += '<td></td>';
         tr1 += '<td></td>';
         tr1 += '<td><b>Total</b></td>';
         tr1 += '<td><b>'+totalQty+'</b></td>';
         tr1 += '<td><b><i class="fa fa-rupee" ></i> '+subtotal.toFixed(2)+'</b></td>';
         tr1 += '<td></td>';
         tr1 += '</tr>';
        $("#return_new_parts_data").find('tbody').append(tr1);
    } else
    {
        $("#return_new_parts_data").remove();
        $("#sellItem").val("Return new Parts (0)");
        alert("Please add new parts to return");
        
    }
    
}

function open_selected_parts_to_return(){
    $('#radio_partner').prop('checked',true).change();
    if(returnItemArray.length > 0){
        $("#return_new_parts_data").show();
        crate_table();
        $('#myModal').modal('toggle');
    } else
    {
        $("#return_new_parts_data").remove();
        $("#sellItem").val("Return new Parts (0)");
        alert("Please add new parts to return");
        
    }
}

function check_awb_exist(){
    var awb = $("#awb").val();
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

                    $("#shippped_date").val(data.message[0].shipment_date);
                    $("#courier_name").val(data.message[0].courier_name);
                    $("#courier_price").val("0");
                    $("#courier_price").css("display","none");
                    if(data.message[0].courier_file){

                        $("#exist_courier_image").val(data.message[0].courier_file);
                        $("#shippped_courier").css("display","none");
                    }

                } else {

                    $('body').loadingModal('destroy');
                    $("#shippped_courier_pic").css("display","block");
                    $("#courier_price").css("display","block");
                    $("#same_awb").css("display","none");
                    $("#exist_courier_image").val("");
                }

            }
        });
    }

}

function return_new_parts(){
   // $('#submit_courier_form').html("<i class = 'fa fa-spinner fa-spin'></i> Processing...").attr('disabled',true); 
    var formData = new FormData(document.getElementById("courier_model_form"));
    
    formData.append('inventory_data',JSON.stringify(returnItemArray));  
    formData.append("label", "WEBUPLOAD");
    formData.append("partner_id", $('#partner_id').val());
    formData.append("wh_type", $("#wh_id").find(':selected').attr('data-warehose'));
    formData.append("warehouse_id", $("#wh_id").val());
    formData.append("warehouse_name", $("#wh_id").find(':selected').text().split("-")[0]);
    formData.append("from_gst_number", $("#from_gst_number").val());
    formData.append("receiver_id", $("#to_wh_id").val());
   // console.log(JSON.stringify(returnItemArray));

    $.ajax({
        method:'POST',
        url: baseUrl + '/employee/user_invoice/generate_invoice_for_return_new_inventory',
        data:formData,
        contentType: false,
        processData: false,
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
        }
    });
    
}

//function print_courier_address(){
//    var wh_type = $("#wh_id").find(':selected').attr('data-warehose');
//    var warehouse_id = $("#wh_id").val();
//    var partner_id = $('#partner_id').val();
//    window.open(baseUrl + '/employee/service_centers/print_new_part_return_address/'+ warehouse_id+"/"+partner_id+"/"+ wh_type,'_blank');
//}