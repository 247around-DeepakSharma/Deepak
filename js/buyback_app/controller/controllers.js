//order details file upload
uploadfile.controller('uploadOrderDetailsFile', ['$scope', 'fileUpload', function($scope, fileUpload){
    
    $scope.uploadFile = function(){
        var file = $scope.myFile;
        $scope.ShowSpinnerStatus = true;
        var uploadUrl = baseUrl + "/buyback/upload_buyback_process/process_upload_order";
        fileUpload.uploadFileToUrl($scope,file, uploadUrl);
    };
    
}]);

//price charges details file upload
uploadfile.controller('uploadPriceChargesFile', ['$scope', 'fileUpload', function($scope, fileUpload){
    
    $scope.uploadFile = function(){
        var file = $scope.myFile;
        $scope.ShowSpinnerStatus = true;
        var uploadUrl = baseUrl + "/buyback/upload_buyback_process/upload_file";
        fileUpload.uploadFileToUrl($scope,file, uploadUrl);
    };
    
}]);

//shop address details file upload
uploadfile.controller('uploadShopAddressFile', ['$scope', 'fileUpload', function($scope, fileUpload){
    
    $scope.uploadFile = function(){
        var file = $scope.myFile;
        $scope.ShowSpinnerStatus = true;
        var uploadUrl = baseUrl + "/buyback/upload_buyback_process/upload_file";
        fileUpload.uploadFileToUrl($scope,file, uploadUrl);
    };
    
}]);


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
            icon: baseUrl+"/images/logo.png",
            dir: "ltr"
        };
        var notification = new Notification('',options);
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