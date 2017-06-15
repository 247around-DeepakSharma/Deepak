//buyback file upload app controller/
app.controller("fileUploadController", function ($scope, $http) {
    $scope.uploadFile = function () {
        $scope.ShowSpinnerStatus = true;
        $scope.SuccessResponseState = false;
        $scope.ErrorResponseState = false;
        var form_data = new FormData();
        angular.forEach($scope.files, function (file) {
            form_data.append('file', file);
        });
        $http.post(upload_url, form_data,
                {
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined, 'Process-Data': false}
                }).success(function (response) {
            var obj = angular.fromJson(response);
            //console.log(obj);
            if (obj['code'] < 0) {
                //console.log(res);
                $scope.ShowSpinnerStatus = false;
                $scope.ErrorResponseMsg = obj['msg'];
                notifyMe(obj['msg']);
                $scope.ErrorResponseState = true;
            } else if (obj['code'] > 0) {
                $scope.ShowSpinnerStatus = false;
                $scope.SuccessResponseMsg = obj['msg'];
                $scope.SuccessResponseState = true;
                notifyMe(obj['msg']);
                
            }
        });
    }
});

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

    // At last, if the user already denied any notification, and you
    // want to be respectful there is no need to bother them any more.
}