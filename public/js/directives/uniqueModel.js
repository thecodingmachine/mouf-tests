'use strict';

app.directive('uniqueModel', function(isModelAvailable) {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            ngModel.$asyncValidators.unique = isModelAvailable;
        }
    };
});