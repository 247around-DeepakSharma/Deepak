
//order details file upload
uploadfile.controller('uploadOrderDetailsFile', ['$scope', 'fileUpload', function ($scope, fileUpload) {

        $scope.uploadFile = function () {
            var file = $scope.myFile;
            $scope.ShowSpinnerStatus = true;
            var uploadUrl = baseUrl + "/buyback/upload_buyback_process/process_upload_order";
            fileUpload.uploadFileToUrl($scope, file, uploadUrl);
        };

    }]);

//price charges details file upload
uploadfile.controller('uploadPriceChargesFile', ['$scope', 'fileUpload', function ($scope, fileUpload) {

        $scope.uploadFile = function () {
            var file = $scope.myFile;
            $scope.ShowSpinnerStatus = true;
            var uploadUrl = baseUrl + "/buyback/upload_buyback_process/proces_upload_bb_price_charges";
            fileUpload.uploadFileToUrl($scope, file, uploadUrl);
        };

    }]);


uploadfile.controller('uploadFileHistory', function ($scope, $http) {
    var get_url = baseUrl + "/buyback/upload_buyback_process/upload_file_history/BB-Price-List";
    $http.get(get_url)
            .then(function (response) {
                $scope.uploadFileHistory = response.data;
            });
});

uploadfile.controller('getOrderFileHistory', function ($scope, $http) {
    var get_url = baseUrl + "/buyback/upload_buyback_process/upload_file_history/BB-Order-List";
    $http.get(get_url)
            .then(function (response) {
                $scope.getOrderFileHistory = response.data;
            });
});

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
            });
});

orderDetails.controller('viewOrderHistory', function ($scope, $http) {

    var get_url = baseUrl + "/buyback/buyback_process/get_bb_order_history_details/" + partner_order_id;
    $http.get(get_url)
            .then(function (response) {
                $scope.orderHistoryDetails = response.data;
            });
    $scope.getDateFormat = function(timestamp) {
    return new Date(timestamp);
  }        
});

orderDetails.controller('viewOrderAppLianceDetails', function ($scope, $http) {

    var get_url = baseUrl + "/buyback/buyback_process/get_bb_order_appliance_details/" + partner_order_id;
    $http.get(get_url)
            .then(function (response) {
                $scope.orderHistoryDetails = response.data;
            });
});

addDealers.controller("addDealersController", function($scope, $http){
    $scope.tempData = {};
    var get_url = baseUrl + "/employee/dealers/getpartner_city_list";
    
    $http.get(get_url)
    .then(function (response) {
       
        $scope.partner_list = response.data.sources;
        $scope.city_list = response.data.city;
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
                        $('#invoiceDetailsModal').modal("show");
                    } 
                   
                   
                    
                    
                });
            });
    };
    $scope.closeModel = function(){
        location.reload();
    };
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
          
          //location.reload();

        });
    }