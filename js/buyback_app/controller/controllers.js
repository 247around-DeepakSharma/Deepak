
//order details file upload
uploadfile.controller('uploadOrderDetailsFile', ['$scope', 'fileUpload', function ($scope, fileUpload) {

        $scope.uploadFile = function () {
            var file = $scope.myFile;
            //var file_date = $scope.file_date.received_date;
            $scope.ShowSpinnerStatus = true;
            var file_date = $('#file_date').val();
            //var qc_svc = $('#qc_svc').val();
            if(file_date === ''){
                $scope.ShowSpinnerStatus = false;
                $scope.errorMsg = true;
                $scope.msg = "Please Select File Received Date To Continue";
            }else {
                $scope.errorMsg = false;
                //console.log(file_date);
                var uploadUrl = baseUrl + "/buyback/upload_buyback_process/process_upload_order";
                fileUpload.uploadFileToUrl($scope, file, uploadUrl,file_date);
            }
        };

    }]);

//price charges details file upload
uploadfile.controller('uploadPriceChargesFile', ['$scope', 'fileUpload', function ($scope, fileUpload) {

        $scope.uploadFile = function () {
            var file = $scope.myFile;
            var file_date = ''
            $scope.errorMsg = false;
            $scope.ShowSpinnerStatus = true;
            var uploadUrl = baseUrl + "/buyback/upload_buyback_process/proces_upload_bb_price_charges";
            fileUpload.uploadFileToUrl($scope, file, uploadUrl,file_date);
        };

    }]);

// set global variables for order details app
orderDetails.run(function($rootScope){
    $rootScope.isValidObject = function(value){
        return !value;
    };
    $rootScope.getDateFormat = function(timestamp) {
        return new Date(timestamp);
    }; 
});

//get buyback order data
orderDetails.controller('viewOrderDetails', function ($scope, $http) {

    var get_url = baseUrl + "/buyback/buyback_process/get_bb_order_details_data/" + partner_order_id;
    $http.get(get_url)
            .then(function (response) {
                $scope.order_date = response.data[0].order_date;
                $scope.delivery_date = response.data[0].delivery_date;
                $scope.city = response.data[0].city;
                $scope.partner_gc_id = response.data[0].partner_gc_id;
                $scope.partner_tracking_id = response.data[0].partner_tracking_id;
                $scope.internal_status = response.data[0].internal_status;
                $scope.current_status = response.data[0].current_status;
                $scope.partner_name = response.data[0].partner_name;
                $scope.cp_name = response.data[0].cp_name;
                $scope.acknowledge_date = response.data[0].acknowledge_date;
            });
            
    
});

//get buyback order history
orderDetails.controller('viewOrderHistory', function ($scope, $http) {

    var get_url = baseUrl + "/buyback/buyback_process/get_bb_order_history_details/" + partner_order_id;
    $http.get(get_url)
            .then(function (response) {
                $scope.orderHistoryDetails = response.data;
            });      
});

//get buyback unit details
orderDetails.controller('viewOrderAppLianceDetails', function ($scope, $http) {

    var get_url = baseUrl + "/buyback/buyback_process/get_bb_order_appliance_details/" + partner_order_id;
    $http.get(get_url)
            .then(function (response) {
                $scope.orderHistoryDetails = response.data;
            });
            
    $scope.get_invoice_data = function(invoice_id){
        var url = baseUrl + '/employee/accounting/search_invoice_id';
        var postData = $.param({
                invoice_id: invoice_id
            });
        var config = {
                headers : {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
                }
            };   
        
        $http.post(url,postData,config).success(function(response){
            $('#open_model').html(response);
            $('#invoiceDetailsModal').modal("show");
        });
    };        
});

addDealers.controller("addDealersController", function($scope, $http){
    $scope.tempData = {};
    var get_url = baseUrl + "/employee/dealers/getpartner_city_list";
    
    $http.get(get_url)
    .then(function (response) {
       
        $scope.partner_list = response.data.sources;
        $scope.city_list = response.data.city;
        $scope.state_list = response.data.state;
      /// $scope.tempData = {city : $scope.city_list[0].district};
//        $scope.$watch("sourceCityId", function(newValue, oldValue) {
//           if(newValue) $scope.fetchAsset();
//        });

        
    $scope.create_new_dealer = function (type) {
        var data = $.param({
            'data': $scope.tempData,
            'type': type
        });
        
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        var URL = baseUrl + "/employee/dealers/process_add_dealer";
        $http.post(URL, data, config).success(function (response) {
           
            if (response.code === 247) {
               
                notifyMe(response.msg);
                alert(response.msg);
                
                $scope.dealerForm.$setPristine();
                $scope.tempData = {};
                $scope.tempData.city = {};
                $scope.tempData.state = {};

            } else {
                notifyMe(response.msg);
                alert(response.msg);
            }
        });
    };

    // function to add user data
    $scope.create_dealer = function () {
        $scope.create_new_dealer('new_dealer');
    };
       
    });
});

advanced_search.controller("advancedSearchController", function ($scope, $http) {
    var get_url = baseUrl + "/buyback/buyback_process/get_advanced_search_optionlist";
    
        $http.get(get_url)
        .then(function (response) {
          
                $scope.service_list = response.data.service;
                $scope.city_list = response.data.city;
                $scope.internal_status_list = response.data.internal_status;
                $scope.cp_list = response.data.cp_list;
                shop_list_details = response.data.shop_list;
                $scope.current_status_list = response.data.current_status;
       });
    
});
//add shop address
addShopAddressDetails.controller("userController", function ($scope, $http) {
    
    var get_url = baseUrl + "/buyback/collection_partner/get_active_cp_sf";
    $http.get(get_url)
            .then(function (response) {
                //console.log(response);
                $scope.cp_list = response.data;
            });
    
    $scope.tempData = {};

    // function to insert or update user data to the database
    $scope.saveAddress = function (type) {
        var data = $.param({
            'data': $scope.tempData,
            'type': type
        });
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        var URL = baseUrl + "/buyback/collection_partner/process_add_cp_shop_address";
        $http.post(URL, data, config).success(function (response) {
            //console.log(response);
            if (response.status === 'OK') {
                $scope.userForm.$setPristine();
                $scope.tempData = {};
                $('.formData').slideUp();
                $scope.messageSuccess(response.msg);

            } else {
                //console.log('ssss');
                $scope.messageError(response.msg);
            }
        });
    };

    // function to add user data
    $scope.saveShopAddress = function () {
        $scope.saveAddress('add');
    };

    // function to display success message
    $scope.messageSuccess = function (msg) {
        //console.log(msg);
        $('.alert-success > p').html(msg);
        $('.alert-success').show();
        $('.alert-success').delay(5000).slideUp(function () {
            $('.alert-success > p').html('');
        });
    };

    // function to display error message
    $scope.messageError = function (msg) {
        $('.alert-danger > p').html(msg);
        $('.alert-danger').show();
        $('.alert-danger').delay(5000).slideUp(function () {
            $('.alert-danger > p').html('');
        });
    };


    $scope.getCity = function () {
        var data = $.param({
            pincode: $scope.tempData.shop_address_pincode,
            city: '',
            region:''
        });
        
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        var URL = baseUrl+"/buyback/collection_partner/get_city_for_cp";

        $http.post(URL, data, config).success(function (response) {
            
            if(response === "Not Exist"){
                 alert("Please check Pincode. It is not exist in the System.");
                 var s_html = '<option selected value="">Select City</option>';
                 var r_html = '<option selected value="">Select Region</option>';
                 $('#shop_address_region').html(r_html);  
                 $("shop_address_city").html( s_html );
            } else {
              
                $('#shop_address_city').html(response.city);
                $('#shop_address_region').html(response.region);  
            }
        });
    };
});

viewBBOrderList.controller('assignCP', function ($scope, $http) {

    var post_url = baseUrl + "/buyback/buyback_process/assigned_bb_unassigned_data";
    $scope.showDialogueBox = function() {
        swal({
                title: "Do You Want To Continue?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                closeOnConfirm: true
            },
            function(){
                $scope.showLoader = true;
                
                $http.post(post_url).success(function (response) {
                    console.log(response);
                    if(response.status === 247){
                       
                        alert("Assigned CP Successfully");
                        $scope.showLoader = false;

                    } else if(response.status === -247){
                        message = response.error;
                        $scope.notFoundCity = message;
                        $scope.showLoader = false;
                        $('#myModal').modal("show");
                    } 
                    
                });
            });
    };
    $scope.closeModel = function(){
        location.reload();
    };
});

buyback_dashboard.controller('buyback_dashboardController', function ($scope, $http) {

    var get_url = baseUrl + "/employee/dashboard/get_buyback_balanced_amount";
    $http.get(get_url)
        .then(function (response) {
             //console.log(response.data);
            $("#table_data").html(response.data);
       
               
    });
});


//desktop notification msg
function notifyMe(msg) {
    // Let's check if the browser supports notifications
    if (!("Notification" in window)) {
        alert("This browser does not support desktop notification");
    }

    // Let's check if the user is okay to get some notification
    else if (Notification.permission === "granted") {
        // If it's okay let's create a notification
        var options = {
            body: msg,
            icon: baseUrl + "/images/logo.png",
            dir: "ltr"
        };
        var notification = new Notification('', options);
    }

    // Otherwise, we need to ask the user for permission
    // Note, Chrome does not implement the permission static property
    // So we have to check for NOT 'denied' instead of 'default'
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {
            // Whatever the user answers, we make sure we store the information
            if (!('permission' in Notification)) {
                Notification.permission = permission;
            }

            // If the user is okay, let's create a notification
            if (permission === "granted") {
                var options = {
                    body: "This is the body of the notification",
                    icon: "icon.jpg",
                    dir: "ltr"
                };
                var notification = new Notification("Hi there", options);
            }
        });
    }
}


    function reAssign(){
        var URL = baseUrl + "/buyback/collection_partner/process_assign_order";
        var fd = new FormData(document.getElementById("reAssignForm"));
        fd.append("label", "WEBUPLOAD");
        $.ajax({
            url: URL,
            type: "POST",
            data: fd,
            processData: false,  // tell jQuery not to process the data
            contentType: false,   // tell jQuery not to set contentType
            beforeSend: function(){
                    $('body').loadingModal({
                    position: 'auto',
                    text: 'Loading Please Wait...',
                    color: '#fff',
                    opacity: '0.7',
                    backgroundColor: 'rgb(0,0,0)',
                    animation: 'wave'
                  });

                }
        }).done(function( response ) {
          var data1 = jQuery.parseJSON(response);
          if(data1.status === 247){
              $('body').loadingModal('destroy');
              $(".assign_cp_id option:selected").prop("selected", false);
              alert("Assigned CP Successfully");
              window.location.reload();
          } else if(data1.status === -247){
               message = data1.error;
               console.log(message);
               var table_td = "";
               for(i=0;i< message.length; i++){
                   table_td += '<tr><td>'+(i+1)+'</td><td>'+message[i]['order_id']+'</td><td>'+message[i]['msg']+'</td></tr>';
               }
               $('body').loadingModal('destroy');
               $("#error_td").html(table_td);
               $('#myModal').modal("show");
               
               
          } else {
              alert("There is problem in Assign Vendor. Please Contact to 247Around Dev Team");
          }

        });
    }
    
    
//tag untag buyback orders
taggingUntaggingBbOrders.controller("tagUntagController", function ($scope, $http) {
    
    $scope.IsInvoiceDivToShow = false;
    $scope.tempData = {};
    $scope.buttonText = "Submit";
    $scope.showInvoiceIdDiv = function(selectedOption){
        if(selectedOption === 'claim_debit_note_raised'){
            $scope.IsInvoiceDivToShow = true;
        }else{
            $scope.IsInvoiceDivToShow = false;
        }
    };
    
    // function to insert or update user data to the database
    $scope.processTagUntagOrderId = function (type) {
        var data = $.param({
            'data': $scope.tempData,
            'type': type
        });
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        var URL = baseUrl + "/buyback/buyback_process/process_tagging_untagging_bb_orders";
        $http.post(URL, data, config).success(function (response) {
             //console.log(response);
            if (response.status === 'OK') {
                $scope.form.$setPristine();
                $scope.tempData = {};
                $scope.buttonText = "Submit";
                $scope.messageSuccess(response.msg);

            } else {
                //console.log('ssss');
                $scope.buttonText = "Submit";
                $scope.messageError(response.msg);
            }
        });
    };

    // function to process tagging untagging
    $scope.tagUntagOrderId = function () {
        $scope.buttonText = "Processing...";
        $scope.processTagUntagOrderId('add');
    };

    // function to display success message
    $scope.messageSuccess = function (msg) {
        //console.log(msg);
        $('.alert-success > p').html(msg);
        $('.alert-success').show();
        $('.alert-success').delay(5000).slideUp(function () {
            $('.alert-success > p').html('');
        });
    };

    // function to display error message
    $scope.messageError = function (msg) {
        $('.alert-danger > p').html(msg);
        $('.alert-danger').show();
        $('.alert-danger').delay(5000).slideUp(function () {
            $('.alert-danger > p').html('');
        });
    };
});

orderDetails.controller('viewCpOrderDetails', function ($scope, $http) {

    var get_url = baseUrl + "/service_center/buyback/get_bb_order_details_data/" + partner_order_id;
    $http.get(get_url)
            .then(function (response) {
                $scope.order_date = response.data[0].order_date;
                $scope.delivery_date = response.data[0].delivery_date;
                $scope.city = response.data[0].city;
                $scope.partner_gc_id = response.data[0].partner_gc_id;
                $scope.partner_tracking_id = response.data[0].partner_tracking_id;
                $scope.internal_status = response.data[0].internal_status;
                $scope.current_status = response.data[0].current_status;
                $scope.partner_name = response.data[0].partner_name;
                $scope.cp_name = response.data[0].cp_name;
                $scope.acknowledge_date = response.data[0].acknowledge_date;
            });          
});

orderDetails.controller('viewCpOrderHistory', function ($scope, $http) {

    var get_url = baseUrl + "/service_center/buyback/get_bb_order_history_details/" + partner_order_id;
    $http.get(get_url)
            .then(function (response) {
                $scope.orderHistoryDetails = response.data;
            });       
});

orderDetails.controller('viewCpOrderAppLianceDetails', function ($scope, $http) {

    var get_url = baseUrl + "/service_center/buyback/get_bb_order_appliance_details/" + partner_order_id;
    $http.get(get_url)
            .then(function (response) {
                $scope.orderHistoryDetails = response.data;
            });    
});

buyback_dashboard.controller('bb_dashboard_summary', function ($scope, $http) {

    var get_url = baseUrl + "/buyback/buyback_process/get_buyback_dashboard_summary";
    $http.get(get_url)
        .then(function (response) {
             //console.log(response.data);
            $("#title_count").html(response.data);    
    });
});

rm_dashboard.controller('rm_dashboardController', function ($scope, $http) { 
    //Escalation Start
    $scope.loadAllRMView = function(escalation_url){
         $http.get(escalation_url).then(function (response) {
              $("#loader_gif_escalation").css("display", "none");
              $scope.escalationAllRMData = response.data;
         });
     }
     //Call loadAllRMView Function with dates
    $scope.daterangeloadFullRMView = function(){
         var dateRange = $('#daterange_id').val().split(" - ");
         $("#s_date").val(dateRange[0]);
         $("#e_date").val(dateRange[1]);
         $scope.loadAllRMView(baseUrl + "/employee/dashboard/get_escalation_by_all_rm/"+dateRange[0]+"/"+dateRange[1]);
    }
//Escalation End
//Pending Booking Start
// var pending_booking_url = baseUrl + "/employee/dashboard/pending_booking_by_rm/"+rm_id;
//    $http.get(pending_booking_url).then(function (response) {
//            $scope.pendingBookingData = response.data;
//     });
//Pending Booking End
});
//Missing Pincode Full View
    bracket_allocation.controller('bracketAllocationController', function ($scope, $http) {
    var bracket_url = baseUrl + "/employee/partner/get_bracket_allocation_form_data";
     $http.get(bracket_url).then(function (response) {
             $scope.partner_list = response.data.partner;
             $scope.brand_list = response.data.brand;
     });
     });
//get buyback balance
buyback_dashboard.controller('bb_balance', function ($scope, $http) {
    
    $scope.showLoader = true;
    $scope.showBuybackBalance = false;
    var get_url = baseUrl + "/buyback/buyback_process/get_bb_svc_balance";
    $http.get(get_url)
        .then(function (response) {
            var data = angular.fromJson(response.data);
            if( data === 'no data found'){
                $scope.tv_balance = '0';
                $scope.la_balance = '0';
                $scope.mobile_balance = '0';
                $scope.total_balance = '0';
                $scope.showLoader = false;
                $scope.showBuybackBalance = true;
            }else{
                $scope.tv_balance = data.tv_balance;
                $scope.la_balance = data.la_balance;
                $scope.mobile_balance = data.mobile_balance;
                $scope.total_balance = data.total_balance;
                $scope.showLoader = false;
                $scope.showBuybackBalance = true;
            }
            
    });
});
//This Function is used to create escalation view of RM(All SF related to a RM)
rm_escalation.controller('rm_escalationController', function ($scope, $http) {
     $scope.loadView = function(escalation_url){
     $http.get(escalation_url).then(function (response) {
         $("#loader_gif_escalation_n").css("display", "none");
         $scope.totalItems = 5;
          $scope.escalationData = response.data;
          $("#sf_json_data").val(JSON.stringify(response.data));
     });
 }
 //Call loadView Function With Date Range
  $scope.daterangeloadView = function(){
     var dateRange = $('#daterange_id').val().split(" - ");
     var rm_id = $('#rm_id_holder').html();
     $("#s_date").val(dateRange[0]);
     $("#e_date").val(dateRange[1]);
     $scope.loadView(baseUrl + "/employee/dashboard/get_sf_escalation_by_rm/"+rm_id+"/"+dateRange[0]+"/"+dateRange[1]);
}
  $scope.full_view_escalation = function(){
      $scope.totalItems = $scope.escalationData.length;
      var current_button_text = $('#full_view_escalation').text();
      if(current_button_text === 'Show All SF'){
        $scope.totalItems = $scope.escalationData.length;
        $('#full_view_escalation').text("Show top 5 SF");
      }
      else{
          $scope.totalItems = 5;
          $('#full_view_escalation').text("Show All SF");
      }
}
});

// This Function is usedto call admin view of escalation
admin_dashboard.controller('admin_escalationController', function ($scope, $http) {
       //Escalation Start
    $scope.loadAllRMView = function(escalation_url){
         $http.get(escalation_url).then(function (response) {
             $("#loader_gif_escalation").css("display", "none");
              $scope.escalationAllRMData = response.data;
         });
     }
     //Call loadAllRMView Function with dates
    $scope.daterangeloadFullRMView = function(){
         var dateRange = $('#daterange_id').val().split(" - ");
         $("#s_date").val(dateRange[0]);
         $("#e_date").val(dateRange[1]);
         $scope.loadAllRMView(baseUrl + "/employee/dashboard/get_escalation_by_all_rm/"+dateRange[0]+"/"+dateRange[1]);
    }
//Escalation End
});
 //This Function is used to call admin view of Pending Booking Count
admin_dashboard.controller('pendngBooking_Controller', function ($scope, $http) {
    $scope.loadPendingBookingView = function(pending_url){
        $("#loader_gif_pending").css("display", "block");
            $scope.pendingBookingByRM = "";
            $http.get(pending_url).then(function (response) {
            $("#loader_gif_pending").css("display", "none");
            $scope.pendingBookingByRM = response.data;
         });
     }
     $scope.callloadPendingBookingView = function(){
        $scope.loadPendingBookingView(baseUrl + "/employee/dashboard/pending_booking_count_by_rm/");
     }
     
     $scope.ShowBookingActorView = function(){
         var actor = $('#actor').val();
         if(actor === 'all'){
              $scope.loadPendingBookingView(baseUrl + "/employee/dashboard/pending_booking_count_by_rm/");
         }
         else{
             $scope.loadPendingBookingView(baseUrl + "/employee/dashboard/pending_booking_count_by_rm/"+actor);
        }
    }
});


rm_Bookings.controller('rm_PendingBookingControllerInstallation', function ($scope, $http) {
    var rm_id = $("#rm_id_holder").val();
     var url = baseUrl + "/employee/dashboard/pending_booking_by_rm_view/"+rm_id;
    $http.get(url).then(function (response) {
        $scope.totalBookings = 5;
        $scope.totalBookingsRepair = 5;
        $("#loader_gif_pending").css("display", "none");
        $scope.pendingBookingByRMFullView = response.data;
     });
    $scope.full_view_bookings_installation = function(){
      var current_button_text = $('#full_view_installation').text();
      if(current_button_text === 'Show All Vendors Installation'){
        $scope.totalBookings = $scope.pendingBookingByRMFullView.length;
        $('#full_view_installation').text("Show top 5 Installation");
      }
      else{
          $scope.totalBookings = 5;
          $('#full_view_installation').text("Show All Vendors Installation");
      }
}
$scope.full_view_bookings_repair = function(){
      var current_button_text = $('#full_view_repair').text();
      if(current_button_text === 'Show All Vendors Repair'){
        $scope.totalBookingsRepair = $scope.pendingBookingByRMFullView.length;
        $('#full_view_repair').text("Show top 5 Repair");
      }
      else{
          $scope.totalBookingsRepair = 5;
          $('#full_view_repair').text("Show All Vendors Repair");
      }
}
$scope.createBookingIDView = function(bookingIDList,remarksList,statusList){
    $("#booking_id_holder").html("<p style='font-size: 17px;text-align:center;'>No Booking Found</p>");
    if(bookingIDList !== ''){
            bookingArray = bookingIDList.split(",");
            remarksArray = remarksList.split(",");
            statusArray = statusList.split(",");
            var bookingString = '<table class="table  table-striped table-bordered">';
            bookingString +="<th>S.N</th>";
            bookingString +="<th>Booking ID</th>";
            bookingString +="<th>Remarks</th>";
            bookingString +="<th>Internal Status</th>";
            for(var i=0;i<bookingArray.length;i++){
                bookingString +="<tr>";
                bookingString +="<td>"+(i+1)+"</td>";
                bookingString +="<td style='width: 26%;'><a style='font-size: 13px;line-height: 24px;padding: 10px 0px;' target='_blank' href='"+baseUrl+"/employee/booking/viewdetails/"+bookingArray[i]+"'>"+bookingArray[i]+"</a></td>";
                bookingString +="<td>"+remarksArray[i]+"</td>";
                bookingString +="<td>"+statusArray[i]+"</td>";
                bookingString +="</tr>";
            }
            bookingString += '</table>';
            console.log(bookingString);
            $("#booking_id_holder").html(bookingString);
        }
        }
});

//This Function is used to call RM view of Pending Booking Count
rm_dashboard.controller('pendngBooking_Controller', function ($scope, $http) { 
    var url = baseUrl + "/employee/dashboard/pending_booking_count_by_rm";
    $scope.pendingBookingByRMCall = function(){ 
        $http.get(url).then(function (response) {
                $("#loader_gif_pending").css("display", "none");
                $scope.pendingBookingByRM = response.data;
        });
    }
});

//price quote file upload
uploadfile.controller('uploadPriceQuoteFile', ['$scope', 'fileUpload', function ($scope, fileUpload) {

        $scope.uploadFile = function () {
            var file = $scope.myFile;
            var file_date = ''
            $scope.ShowSpinnerStatus = true;
            var uploadUrl = baseUrl + "/buyback/upload_buyback_process/proces_upload_bb_price_quote";
            fileUpload.uploadFileToUrl($scope, file, uploadUrl,file_date);
        };

    }]);



//This Function is used to //get brackets snapshot
admin_dashboard.controller('bracketsSnapshot_Controller', function ($scope, $http) { 
    var url = baseUrl + "/employee/inventory/get_inventory_snapshot";
    $http.get(url).then(function (response) {
            if(response.data.length === 0){
                $scope.brackets_div = false;
                $scope.brackets_div_err_msg = true;
                $scope.brackets_div_err_msg_text = "No Data Found";
            }else{
                $scope.brackets_div = true;
                 $scope.brackets_div_err_msg = false;
                $scope.bracketsSnapshot = response.data;
                $scope.quantity = '5';
            }
            
            $("#brackets_loader").css("display", "none");
     });
});

//This Function is used to //get brackets snapshot
//inventory_dashboard.controller('bracketsSnapshot_Controller', function ($scope, $http) {
//    var url = baseUrl + "/employee/inventory/get_inventory_snapshot";
//    $http.get(url).then(function (response) {
//            if(response.data.length === 0){
//                $scope.brackets_div = false;
//                $scope.brackets_div_err_msg = true;
//                $scope.brackets_div_err_msg_text = "No Data Found";
//            }else{
//                $scope.brackets_div = true;
//                 $scope.brackets_div_err_msg = false;
//                $scope.bracketsSnapshot = response.data;
//                $scope.quantity = '5';
//            }
//            
//            $("#brackets_loader").css("display", "none");
//     });
//});

//This Function is used to //get brackets snapshot
rm_dashboard.controller('bracketsSnapshot_Controller', function ($scope, $http) {
    var url = baseUrl + "/employee/inventory/get_inventory_snapshot";
    $scope.bracketsSnapshotCall = function(){
    $http.get(url).then(function (response) {
            if(response.data.length === 0){
                $scope.brackets_div = false;
                $scope.brackets_div_err_msg = true;
                $scope.brackets_div_err_msg_text = "No Data Found";
            }else{
                $scope.brackets_div = true;
                 $scope.brackets_div_err_msg = false;
                $scope.bracketsSnapshot = response.data;
                $scope.quantity = '5';
            }
            
            $("#brackets_loader").css("display", "none");
     });
    };
});

 //This Function is used to call Completed Booking Days Reports
admin_dashboard.controller('completedBooking_Controller', function ($scope, $http) { 
    $scope.loadCompletedBookingView = function(pending_url){
            $scope.completedBookingByRM = "";
            $http.get(pending_url).then(function (response) { 
            $("#loader_gif_completed_rm").css("display", "none");
            $scope.completedBookingByRM = response.data;
         });
    }
     $scope.ShowRMCompletedBookingBYDateRange = function(){
                $("#loader_gif_completed_rm").css("display", "block");
                dateRange = $("#completed_daterange_id").val();
                dateArray = dateRange.split(" - ");
                startDate = dateArray[0];
                endDate = dateArray[1];
                status = $("#completed_status").val();
                service_id = $("#service_id").val();
                partner_id = $("#partner_id").val();
                request_type = getMultipleSelectedValues("request_type");
                free_paid = $("#free_paid").val();
                upcountry = $("#upcountry").val();
                if(!status){
                    status = "not_set";
                }
                if(!service_id){
                    service_id = "not_set";
                }
                if(!request_type){
                    request_type = "not_set";
                }
                if(!free_paid){
                    free_paid = "not_set";
                }
                 if(!upcountry){
                    upcountry = "not_set";
                }
                if(!partner_id){
                    partner_id = "not_set";
                }
              $scope.loadCompletedBookingView(baseUrl + "/employee/dashboard/get_booking_tat_report/"+startDate+"/"+endDate+"/"+status+"/"+service_id+"/"+request_type+"/"+free_paid+"/"+upcountry+"/RM/0/"+partner_id);
    }
});

//This Function is used to call Completed Booking Days Reports
admin_dashboard.controller('completedBooking_ControllerAM', function ($scope, $http) { 
    $scope.loadCompletedBookingViewAM = function(pending_url){
            $("#loader_gif_completed_am").css("display", "block");
            $scope.completedBookingByAM = "";
            $http.get(pending_url).then(function (response) {
            $("#loader_gif_completed_am").css("display", "none");
            $scope.completedBookingByAM = response.data;
         });
     }
     $scope.ShowAMCompletedBookingBYDateRange = function(){ 
                dateRange = $("#completed_daterange_id_am").val();
                dateArray = dateRange.split(" - ");
                startDate = dateArray[0];
                endDate = dateArray[1];
                status = $("#completed_status_am").val();
                service_id = $("#service_id_am").val();
                partner_id = $("#partner_id_am").val();
                request_type = getMultipleSelectedValues("request_type_am");
                free_paid = $("#free_paid_am").val();
                upcountry = $("#upcountry_am").val();
                if(!status){
                    status = "not_set";
                }
                if(!service_id){
                    service_id = "not_set";
                }
                if(!request_type){
                    request_type = "not_set";
                }
                if(!free_paid){
                    free_paid = "not_set";
                }
                 if(!upcountry){
                    upcountry = "not_set";
                }
                 if(!partner_id){
                    partner_id = "not_set";
                }
              $scope.loadCompletedBookingViewAM(baseUrl + "/employee/dashboard/get_booking_tat_report/"+startDate+"/"+endDate+"/"+status+"/"+service_id+"/"+request_type+"/"+free_paid+"/"+upcountry+"/AM/0/"+partner_id);
    }
});

//This Function is used to call Pending Booking Days Reports
admin_dashboard.controller('pendingBooking_ControllerRM', function ($scope, $http) { 
    $scope.loadPendingBookingViewRM = function(pending_url){
            $("#loader_gif_pending_rm").css("display", "block");
            $scope.pendingBookingByRM = "";
            $http.get(pending_url).then(function (response) {
            $("#loader_gif_pending_rm").css("display", "none");
            $scope.pendingBookingByRM = response.data;
         });
     }
     $scope.ShowRMPendingBookingBYDateRange = function(){ 
                dateRange = $("#pending_daterange_id_rm").val();
                dateArray = dateRange.split(" - ");
                startDate = dateArray[0];
                endDate = dateArray[1];
                service_id = $("#service_id_rm_pending").val();
                partner_id = $("#partner_id_rm_pending").val();
                request_type = getMultipleSelectedValues("request_type_rm_pending");
                free_paid = $("#free_paid_rm_pending").val();
                upcountry = getMultipleSelectedValues("upcountry_rm_pending");
                status = getMultipleSelectedValues("pending_dependency");
                if(!status){
                  status = "not_set";
                }
                if(!service_id){
                    service_id = "not_set";
                }
                if(!request_type){
                    request_type = "not_set";
                }
                if(!free_paid){
                    free_paid = "not_set";
                }
                 if(!upcountry){
                    upcountry = "not_set";
                }
                 if(!partner_id){
                    partner_id = "not_set";
                }
              $scope.loadPendingBookingViewRM(baseUrl + "/employee/dashboard/get_booking_tat_report/"+startDate+"/"+endDate+"/"+status+"/"+service_id+"/"+request_type+"/"+free_paid+"/"+upcountry+"/RM"+"/Pending/"+partner_id);
    }
});

//This Function is used to call Pending Booking Days Reports for AM
admin_dashboard.controller('pendingBooking_ControllerAM', function ($scope, $http) { 
    $scope.loadPendingBookingViewAM = function(pending_url){
            $("#loader_gif_pending_AM").css("display", "block");
            $scope.pendingBookingByAM = "";
            $http.get(pending_url).then(function (response) {
            $("#loader_gif_pending_AM").css("display", "none");
            $scope.pendingBookingByAM = response.data;
         });
     }
     $scope.ShowAMPendingBookingBYDateRange = function(){ 
                dateRange = $("#pending_daterange_id_am").val();
                dateArray = dateRange.split(" - ");
                startDate = dateArray[0];
                endDate = dateArray[1];
                service_id = $("#service_id_am_pending").val();
                partner_id = $("#partner_id_am_pending").val();
                request_type = getMultipleSelectedValues("request_type_am_pending");
                free_paid = $("#free_paid_am_pending").val();
                upcountry = getMultipleSelectedValues("upcountry_am_pending");
                status = getMultipleSelectedValues("pending_dependency_am");
                if(!status){
                  status = "not_set";
                }
                if(!service_id){
                    service_id = "not_set";
                }
                if(!request_type){
                    request_type = "not_set";
                }
                if(!free_paid){
                    free_paid = "not_set";
                }
                 if(!upcountry){
                    upcountry = "not_set";
                }
                 if(!partner_id){
                    partner_id = "not_set";
                }
              $scope.loadPendingBookingViewAM(baseUrl + "/employee/dashboard/get_booking_tat_report/"+startDate+"/"+endDate+"/"+status+"/"+service_id+"/"+request_type+"/"+free_paid+"/"+upcountry+"/AM"+"/Pending/"+partner_id);
    }
});

//Get Review Page Details 
buyback_dashboard.controller('review_page_summary', function ($scope, $http) {
    $scope.showLoaderReview = true;
    $scope.showReviewDetails = false;
    var get_url = baseUrl + "/buyback/buyback_process/get_orders_without_invoices_and_without_reimbursement";
    $http.get(get_url)
        .then(function (response) {
            var data = angular.fromJson(response.data);
            $scope.showLoaderReview = false;
            $scope.showReviewDetails = true;
            $scope.faulty_bookings = data;
    });
});

buyback_dashboard.controller('claimed_price_order_summary', function ($scope, $http) {
    $scope.showLoaderClaimed = true;
    $scope.showClaimedDetails = false;
    var get_url = baseUrl + "/buyback/buyback_process/get_orders_with_cp_invoice_and_without_reimbursement";
    $http.get(get_url)
        .then(function (response) {
            var data = angular.fromJson(response.data);
            $scope.showLoaderClaimed = false;
            $scope.showClaimedDetails = true;
            $scope.faulty_bookings = data;
    });
});

buyback_dashboard.controller('review_details', function ($scope, $http) {
    $scope.showLoaderReviewOrders = true;
    $scope.showReviewOrderDetails = false;
    var get_url = baseUrl + "/buyback/buyback_process/get_review_page_orders";
    $http.get(get_url)
        .then(function (response) {
            var data = angular.fromJson(response.data);
            $scope.showLoaderReviewOrders = false;
            $scope.showReviewOrderDetails = true;
            $scope.faulty_bookings = data;
    });
});

    //reimbursment file upload
    uploadfile.controller('uploadReimbursmentFile', ['$scope', 'fileUpload', function ($scope, fileUpload) {
        $scope.uploadFile = function () { 
            var file = $scope.myFile;
            var file_date = ''
            $scope.ShowSpinnerStatus = true;
            var uploadUrl = baseUrl + "/buyback/upload_buyback_process/process_reimbursement_file";
            fileUpload.uploadFileToUrl($scope, file, uploadUrl,file_date);
        };
    }]);
