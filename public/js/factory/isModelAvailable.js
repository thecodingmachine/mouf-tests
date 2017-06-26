'use strict';

app.factory('isModelAvailable', function($q, $http) {
    return function(model) {
        var deferred = $q.defer();

        var url = url_api + model;

        $http({
            method: 'GET',
            url: url
        }).then(function (success){
            if (success.data.modelExist){
                deferred.reject();
            } else {
                deferred.resolve();
            }
        },function (error){
            console.log(error);
        });

        return deferred.promise;
    }
});