'use strict';

app.factory('carsService', ['$http', function ($http) {

    function getCars (callback) {
        var url = url_api + 'cars';

        $http({
            method: 'GET',
            url: url
        }).then(function (success){
            callback(success.data);
            console.log("success" + url);
        },function (error){
            console.log("erreur");
            callback(error);
        });
    }

    function getBrands (callback) {
        var url = url_api + 'brands';

        $http({
            method: 'GET',
            url: url
        }).then(function (success){
            callback(success.data);
            console.log("success : " + url);
        },function (error){
            console.log("erreur : " + error);
            callback(error);
        });
    }

    function postCar (newCar, brands, callback) {
        var url = url_api + 'cars';
        console.log("service postCars");

        console.log("newCar", newCar);
        console.log("brands", brands);
      /*  var req = {
            method: 'POST',
            url: url,
            headers: {
                'Content-Type': undefined
            },
            data: { description: newCar, brand: brands.id }
        }*/

        $http({
            method: 'POST',
            url: url,
            data: { car: newCar, brand: brands }
        }).then(function (success){
            callback(success.data);
            console.log("success : " + url);
        },function (error){
            console.log("erreur :", error);
            callback(error);
        });

    }

    function putCar (id, car, brand, callback) {
        var url = url_api + 'car/' + id;

        console.log("id", id);
        console.log("car", car);
        console.log("brand", brand);


        $http({
            method: 'PUT',
            url: url,
            data: { car: car, brand: brand }
        }).then(function (success){
            callback(success.data);
            console.log("success : " + url);
        },function (error){
            console.log("erreur :", error);
            callback(error);
        });
    }

    function deleteCar (id, callback) {
        var url = url_api + 'car/' + id;


        $http({
            method: 'DELETE',
            url: url
        }).then(function (success){
            callback(success.data);
            console.log("success : " + url);
        },function (error){
            console.log("erreur :", error);
            callback(error);
        });
    }

    function getCar (id, callback) {
        var url = url_api + 'car/' + id;

        $http({
            method: 'GET',
            url: url
        }).then(function (success){
            callback(success.data);
            console.log("success : " + url);
        },function (error){
            console.log("erreur :", error);
            callback(error);
        });

    }

    return {
        getCars: getCars,
        postCar: postCar,
        putCar: putCar,
        deleteCar: deleteCar,
        getBrands: getBrands,
        getCar: getCar
    }
}]);