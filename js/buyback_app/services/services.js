uploadfile.service('fileUpload', ['$http', function ($http) {
    this.uploadFileToUrl = function($scope,file, uploadUrl){
        var fd = new FormData();
        fd.append('file', file);
        $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(response){
            obj = angular.fromJson(response);
            if(obj['code'] >0 ){
                $scope.ShowSpinnerStatus = false;
                $scope.successMsg = true;
                $scope.msg = obj['msg'];
                notifyMe(obj['msg']);
            }else{
                $scope.ShowSpinnerStatus = false;
                $scope.errorMsg = true;
                $scope.msg = obj['msg'];
                notifyMe(obj['msg']);
            }
            
        })
        .error(function(response){
            alert(response);
        });
    }
}]);