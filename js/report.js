 var getUrl = window.location;
 var baseUrl = getUrl .protocol + "//" + getUrl.host ;
 var vendor_performanceUrl = baseUrl + '/employee/vendor/vendor_performance/';
 var getPricingDetailsUrl = baseUrl + '/employee/service_centre_charges/get_pricing_details';
 var EditPricingDetailsUrl = baseUrl + '/employee/service_centre_charges/editPriceTable';
 var UserCountUrl = baseUrl + '/employee/user/getusercount';

	function getVendorPerformance(){

		var postData = {};
		postData['vendor_id'] = $('#vendor').val();
		postData['city'] = $('#city').val();
		postData['service_id'] = $('#service').val();
		postData['period'] = $('#period').val();
		//postData['date_range'] = $('input[name="datefilter"]').val();
       // postData['source'] = $('#source').val();

		if(postData['period'] != null || postData['date_range'] != ""){
            $('#loader_gif').attr('src', baseUrl +"/images/loader.gif");

			sendAjaxRequest(postData, vendor_performanceUrl).done(function(data) {
				$('#performance').html(data);

			    table_pagination();
            });
		}
	}


	function get_pricing_details(){

		var postData = {};
		postData['source'] = $('#source').val();
		postData['city'] = $('#city').val();
		postData['service_id'] = $('#service').val();
		postData['category'] = $('#category').val();
		postData['capacity'] = $('#capacity').val();
		postData['appliances'] = $('#appliances').val();
        $('#loader_gif').attr('src', baseUrl +"/images/loader.gif");
		
		sendAjaxRequest(postData, getPricingDetailsUrl).done(function(data) {
			    $('#mytable').html(data);

				$('.pager').remove();
				$('table.paginated').each(function() {
                    $('#loader_gif').attr('src',"");

						var currentPage = 0;
						var numPerPage = 40;
						var $table = $(this);
						$table.bind('repaginate', function() {
							$table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
						});
						$table.trigger('repaginate');
						var numRows = $table.find('tbody tr').length;
						var numPages = Math.ceil(numRows / numPerPage);

						var $pager = $('<div class="pager"></div>');
						for (var page = 0; page < numPages; page++) {
							$('<span class="page-number"></span>').text(page + 1).bind('click', {
								newPage: page
							}, function(event) {
								currentPage = event.data['newPage'];
								$table.trigger('repaginate');
								$(this).addClass('active').siblings().removeClass('active');
							}).appendTo($pager).addClass('clickable');
						}
						$pager.insertBefore($table).find('span.page-number:first').addClass('active');
				});
       
        });

	}

	function editPriceTable(div, id){
      var postData = {};
      postData['id'] = id;
      postData['check_box'] = $('#checkbox_input'+div).val();
      postData['active'] = $('#active_input'+div).val();
      postData['vendor_svc_charge'] = $('#vendor_svc_charge_input'+div).val();
      postData['vendor_tax'] = $('#vendor_tax_input'+ div).val();
      postData['around_tax'] = $('#around_tax_input'+div).val();
      postData['around_svc_charge'] = $('#around_svc_charge_input'+div).val();
      postData['customer_total'] = $('#customer_total_input'+div).val();
      postData['partner_payment'] = $('#partner_payment_input'+div).val();
      postData['customer_charges'] = $('#customer_charges_input'+div).val();
      $('#loader_gif').attr('src', baseUrl +"/images/loader.gif");

      sendAjaxRequest(postData, EditPricingDetailsUrl).done(function(data) {
          if(data == "success"){

          	get_pricing_details();
          }
      });
	}

	function displayPricetableInput(div){
		if ( $('#checkbox_p'+div).hasClass('displaytrue') ){

			$('#active_p'+div).removeClass('displaytrue');
            $('#active_p' + div).addClass('displayfalse');

             $('#checkbox_p'+div).removeClass('displaytrue');
             $('#checkbox_p'+div).addClass('displayfalse');

             $('#vendor_svc_charge_p'+div).removeClass('displaytrue');
             $('#vendor_svc_charge_p'+div).addClass('displayfalse');

             $('#vendor_tax_p'+div).removeClass('displaytrue');
             $('#vendor_tax_p'+div).addClass('displayfalse');

             $('#around_svc_charge_p'+div).removeClass('displaytrue');
             $('#around_svc_charge_p'+div).addClass('displayfalse');

             $('#around_tax_p'+div).removeClass('displaytrue');
             $('#around_tax_p'+div).addClass('displayfalse');

             $('#customer_total_p'+div).removeClass('displaytrue');
             $('#customer_total_p'+div).addClass('displayfalse');

             $('#partner_payment_p'+div).removeClass('displaytrue');
             $('#partner_payment_p'+div).addClass('displayfalse');

             $('#customer_charges_p'+div).removeClass('displaytrue');
             $('#customer_charges_p'+div).addClass('displayfalse');

             
             $('#active_input'+div).removeClass('displayfalse');
             $('#active_input' + div).addClass('displaytrue');

             $('#checkbox_input'+div).removeClass('displayfalse');
             $('#checkbox_input'+div).addClass('displaytrue');

             $('#vendor_svc_charge_input'+div).removeClass('displayfalse');
             $('#vendor_svc_charge_input'+div).addClass('displaytrue');

             $('#vendor_tax_input'+div).removeClass('displayfalse');
             $('#vendor_tax_input').addClass('displaytrue');

             $('#around_svc_charge_input'+div).removeClass('displayfalse');
             $('#around_svc_charge_input').addClass('displaytrue');

             $('#around_tax_input'+div).removeClass('displayfalse');
             $('#around_tax_input').addClass('displaytrue');

             $('#customer_total_input'+div).removeClass('displayfalse');
             $('#customer_total_input'+div).addClass('displaytrue');

             $('#partner_payment_input'+div).removeClass('displayfalse');
             $('#partner_payment_input').addClass('displaytrue');

             $('#customer_charges_input'+div).removeClass('displayfalse');
             $('#customer_charges_input'+div).addClass('displaytrue');

             $('#edit'+div).removeClass('displaytrue');
             $('#edit'+div).addClass('displayfalse');

            
             $('#submit'+div).addClass('displaytrue');

             $('#submit'+div).removeClass('displayfalse');
             $('#cancel'+div).addClass('displaytrue');

             $('#cancel'+div).removeClass('displayfalse');






		} else {

			$('#active_p'+div).removeClass('displayfalse');
            $('#active_p' + div).addClass('displaytrue'); 

			 $('#checkbox_p'+div).removeClass('displayfalse');
             $('#checkbox_p'+div).addClass('displaytrue');

             $('#vendor_svc_charge_p'+div).removeClass('displayfalse');
             $('#vendor_svc_charge_p'+div).addClass('displaytrue');

             $('#vendor_tax_p'+div).removeClass('displayfalse');
             $('#vendor_tax_p'+div).addClass('displaytrue');

             $('#around_svc_charge_p'+div).removeClass('displayfalse');
             $('#around_svc_charge_p'+div).addClass('displaytrue');

             $('#around_tax_p'+div).removeClass('displayfalse');
             $('#around_tax_p'+div).addClass('displaytrue');

             $('#customer_total_p'+div).removeClass('displayfalse');
             $('#customer_total_p'+div).addClass('displaytrue');

             $('#partner_payment_p'+div).removeClass('displayfalse');
             $('#partner_payment_p'+div).addClass('displaytrue');

             $('#customer_charges_p'+div).removeClass('displayfalse');
             $('#customer_charges_p'+div).addClass('displaytrue');


              $('#active_input'+div).removeClass('displaytrue');
             $('#active_input' + div).addClass('displayfalse');

             $('#checkbox_input'+div).removeClass('displaytrue');
             $('#checkbox_input'+div).addClass('displayfalse');

              $('#vendor_svc_charge_input'+div).removeClass('displaytrue');
             $('#vendor_svc_charge_input'+div).addClass('displayfalse');

             $('#vendor_tax_input'+div).removeClass('displaytrue');
             $('#vendor_tax_input'+div).addClass('displayfalse');

             $('#around_svc_charge_input'+div).removeClass('displaytrue');
             $('#around_svc_charge_input'+div).addClass('displayfalse');

             $('#around_tax_input'+div).removeClass('displaytrue');
             $('#around_tax_input'+div).addClass('displayfalse');

             $('#customer_total_input'+div).removeClass('displaytrue');
             $('#customer_total_input'+div).addClass('displayfalse');

             $('#partner_payment_input'+div).removeClass('displaytrue');
             $('#partner_payment_input'+div).addClass('displayfalse');

             $('#customer_charges_input'+div).removeClass('displaytrue');
             $('#customer_charges_input'+div).addClass('displayfalse');

             $('#edit'+div).removeClass('displayfalse');
             $('#edit'+div).addClass('displaytrue');
            
             $('#submit'+div).removeClass('displaytrue');
             $('#submit'+div).addClass('displayfalse');
             $('#cancel'+div).removeClass('displaytrue');
             $('#cancel'+div).addClass('displayfalse');




		}
	}

	function sendAjaxRequest(postData, url) {
        return $.ajax({
         data: postData,
         url: url,
         type: 'post'
        });
    }

    function getusercount(){

        var postData = {};
        $('#loader_gif').attr('src', baseUrl +"/images/loader.gif");
        postData['city'] = $('#city').val();
        postData['type'] = $('#mon_user').val();
        postData['source'] = $('#source').val();
        //postData['date_range'] = $('input[name="datefilter"]').val();
    
        sendAjaxRequest(postData, UserCountUrl).done(function(data) {

                $('#performance').html(data);
                
                $('#total_user').html("Total User:  " + $('#total_booking_user').val());
                $('#completed_booking_user').html("Completed booking:  " + $('#total_booking_completed_booking_user').val());
                $('#cancelled_booking_user').html("Cancelled booking:  " + $('#total_booking_cancelled').val());
                $('#loader_gif').attr('src',"");
                table_pagination();
                   
                
        });
        
    }


    function table_pagination(){
        $('.pager').remove();

        $('table.paginated').each(function() {
            $('#loader_gif').attr('src', "");
            var currentPage = 0;
            var numPerPage = 40;
            var $table = $(this);
            $table.bind('repaginate', function() {
                $table.find('tbody tr').hide().slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).show();
            });
            $table.trigger('repaginate');
            var numRows = $table.find('tbody tr').length;
            var numPages = Math.ceil(numRows / numPerPage);

            var $pager = $('<div class="pager"></div>');
            for (var page = 0; page < numPages; page++) {
                $('<span class="page-number"></span>').text(page + 1).bind('click', {
                    newPage: page
                }, function(event) {
                    currentPage = event.data['newPage'];
                    $table.trigger('repaginate');
                    $(this).addClass('active').siblings().removeClass('active');
                }).appendTo($pager).addClass('clickable');
            }
            $pager.insertBefore($table).find('span.page-number:first').addClass('active');
        });
    }
