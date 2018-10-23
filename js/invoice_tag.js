    function get_vertical(serviceUrl){
        var vertical_input = $('#vertical_input').val();
        $.ajax({
            method: "POST",
            url: serviceUrl+"employee/invoice/get_all_invoice_vertical",
            data:{'vertical_input':vertical_input},
            success: function (response) { 
                $("#vertical").html(response);
                 get_category(serviceUrl);
            }
        });
    }
    
    function get_category(serviceUrl){ 
        var vertical = $('#vertical').val();
        var category_input = $("#category_input").val();
        $.ajax({
            method: "POST",
            url: serviceUrl+"employee/invoice/get_invoice_category",
            data:{'vertical':vertical, 'category_input': category_input},
            success: function (response) {
                $("#category").html(response);
                get_sub_category(serviceUrl);
            }
        });
    }
    
    function get_sub_category(serviceUrl){
        var vertical = $('#vertical').val();
        var category = $("#category").val();
        var sub_category_input = $("#sub_category_input").val();
        $.ajax({
            method: "POST",
            url: serviceUrl+"employee/invoice/get_invoice_sub_category",
            data:{'vertical':vertical, 'category':category, 'sub_category_input': sub_category_input},
            success: function (response) {
                $("#sub_category").html(response);
            }
        });
    }
    
    function get_accounting(select){
        $("#accounting").val($('option:selected', select).attr('data-id'));
    }