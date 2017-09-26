booking_advanced_search.controller("bookingAdvancedSearchController", function ($scope, $http) {
    var get_url = baseUrl + "/employee/booking/get_booking_advanced_search_optionlist";
    
        $http.get(get_url)
        .then(function (response) {
            $scope.partner_list = response.data.partners;
            $scope.service_centers_list = response.data.service_centers;
            $scope.services_list = response.data.services;
            $scope.internal_status_list = response.data.internal_status;
            $scope.current_status_list = response.data.current_status;
            $scope.product_or_service_list = response.data.product_or_service;
            $scope.city_list = response.data.cities;
            $scope.rating_list = response.data.ratings;
            $scope.service_list = response.data.service;
            $scope.brand_list = response.data.brands;
            $scope.capacity_list = response.data.capacity;
            $scope.category_list = response.data.category;
            $scope.request_type_list = response.data.request_type;
            $scope.is_upcountry_list = response.data.is_upcountry;
            $scope.paid_by_list = response.data.paid_by;
       });
    
});