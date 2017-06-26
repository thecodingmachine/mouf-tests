'use strict';

app.controller('updateCarController', ['$scope', 'carsService',
    function ($scope, carsService) {

        $scope.id = window.location.pathname.match(/\d+/)[0];

        $scope.update = update;

        //Let's go
        init();

        function init(){
            carsService.getCar($scope.id, function (item) {
                $scope.car = item.car;

                carsService.getBrands(function (items) {
                    $scope.brands = items.brands;
                    $scope.brandCar = items.brands[$scope.car.brand.id-1];
                })
            });

        }

        function update (car, brand){
            carsService.putCar($scope.id, car, brand, function(){
                window.alert("The car has been updated : its model is " + car.name + ", its maxSpeed is " + car.maxSpeed + "KPH and its brand is " + brand.name + ".");
                window.location.replace('../cars');
            });
        }

    }])

;