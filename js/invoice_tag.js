    /*  This js is only for fetching vertical, category and sub category data only for invoices */
    
    
    /*
     * @des - this function is used to get all verticals for invoices
     * @param {controller url} serviceUrl
     * @returns {html for vertical select}
     */
    function get_vertical(serviceUrl){ 
        var vertical_input = $('#vertical_input').val();
        $.ajax({
            method: "POST",
            url: serviceUrl+"employee/invoice/get_all_invoice_vertical",
            data:{'vertical_input':vertical_input},
            async: false,
            success: function (response) { 
                $("#vertical").html(response);
                get_category(serviceUrl);
               
            }
        });
    }
    
     /*
     * @des - this function is used to get all categorty for specific vertical
     * @param {controller url} serviceUrl
     * @returns {html for category select}
     */
    function get_category(serviceUrl){ 
        var vertical = $('#vertical').val();
        if(vertical === null){
            vertical = $('#vertical_input').val();
        }
        var category_input = $("#category_input").val();
        $.ajax({
            method: "POST",
            url: serviceUrl+"employee/invoice/get_invoice_category",
            data:{'vertical':vertical, 'category_input': category_input},
            async: false,
            success: function (response) {
                $("#category").html(response);
                if(!category_input){
                    $("#category").val('').trigger('change');
                    $("#sub_category").val('').trigger('change');
                }
                get_sub_category(serviceUrl);
            }
        });
    }
    
     /*
     * @des - this function is used to get all sub categorty for specific vertical and specific category
     * @param {controller url} serviceUrl
     * @returns {html for sub category select}
     */
    function get_sub_category(serviceUrl){
        var vertical = $('#vertical').val();
        var category = $("#category").val();
        var sub_category_input = $("#sub_category_input").val();
        if(vertical === null){
            vertical = $('#vertical_input').val();
        }

        if(category === null){
            category = $('#category_input').val();
        }
        $.ajax({
            method: "POST",
            url: serviceUrl+"employee/invoice/get_invoice_sub_category",
            data:{'vertical':vertical, 'category':category, 'sub_category_input': sub_category_input},
            async: false,
            success: function (response) {
                $("#sub_category").html(response);
                if(!sub_category_input){
                    $("#sub_category").val('').trigger('change');
                }
            }
        });
    }
    
    function get_accounting(select){
        $("#accounting").val($('option:selected', select).attr('data-id'));
        $('#select2-accounting-container').text($("#accounting").find(':selected').text());
    }