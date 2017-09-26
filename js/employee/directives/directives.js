booking_advanced_search.directive("uiSelect2", function() {
    var linker = function(scope, element, attr) {
        element.select2({
            allowClear: true
          });
       

        scope.$watch(attr.ngModel, function(newValue, oldValue) {
            //console.log("uiSelect", attr.ngModel, newValue, oldValue);
            
            // Give the new options time to render
            setTimeout(function() {
                if(newValue) element.trigger("change");
            });
            
        });
    };

    return {
        link: linker
    };
});

