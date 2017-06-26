'use strict';

app.controller('listCarsController', ['$scope', 'carsService',
    function ($scope, carsService) {

        $scope.reverse = true;
        $scope.currentPage = 1;
        $scope.numPerPage = 4;
        $scope.maxSize = 5;


        $scope.sortBy = sortBy;
        $scope.editCar = editCar;
        $scope.removeCar = removeCar;

        // Let's go
        init();

        function init(){
            carsService.getCars(function (items) {
                $scope.allCars = items.cars;

                $scope.$watch('currentPage + numPerPage', function() {
                    var begin = (($scope.currentPage - 1) * $scope.numPerPage);
                    var end = begin + $scope.numPerPage;
                    $scope.cars = $scope.allCars.slice(begin, end);
                });
            });
        }

        function sortBy(propertyName) {
            $scope.reverse = ($scope.propertyName === propertyName) ? !$scope.reverse : false;
            $scope.propertyName = propertyName;
        }

        function editCar(id){
            window.location.replace('car/' + id);
        }

        function removeCar(car) {
            carsService.deleteCar(car, function () {
                window.alert("The car has been removed.");
                return init();
            })
        }

    }
]);