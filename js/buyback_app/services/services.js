uploadfile.service('fileUpload', ['$http', function ($http) {
    this.uploadFileToUrl = function($scope,file, uploadUrl,file_date){
        var fd = new FormData();
        fd.append('file', file);
        if(file_date !== ''){
            fd.append('file_received_date',file_date);
        }
//        if(qc_svc !== ''){
//            fd.append('qc_svc',qc_svc);
//        }
        
        $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(response){
            console.log(response);
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