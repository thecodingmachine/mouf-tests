'use strict';

app.controller('addCarController', ['$scope', 'carsService',
    function ($scope, carsService) {

        $scope.add = add;

        //Let's go
        init();

        function init(){
            carsService.getBrands(function (items) {
                $scope.brands = items.brands;
            })
        }

        function add (newCar, brand){
            carsService.postCar(newCar, brand, function () {
                window.alert("The car has been added : its model is " + newCar.name + ", its maxSpeed is " + newCar.maxSpeed + "KPH and its brand is " + brand.name + ".");
                window.location.replace('cars');
            });
        }

    }
]);