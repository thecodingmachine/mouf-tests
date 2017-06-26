'use strict';

var app = angular.module('testMouf', ['ngMessages', 'ngRoute', 'ui.bootstrap']);

app.config(['$interpolateProvider', function($interpolateProvider){
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
}]);

app.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {

    $locationProvider.html5Mode({
        enabled: false,
        requireBase: false
    });
    $routeProvider
        .when('/cars', {
            controller: 'listCarsController'
        })
        .when('/car', {
            controller: 'addCarController'
        })
        .when('/car/:id', {
            controller: 'updateCarController'
        });
}]);

var url_api = 'http://localhost/theCodingMachine/mouf-tests/api/';