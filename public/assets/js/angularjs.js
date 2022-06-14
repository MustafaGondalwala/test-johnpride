var classApp = angular.module('classApp', []);

classApp.controller('classCtrl', function ($scope) {
    $scope.colSm = false;
  $scope.activeButton = function() {
    $scope.colSm = !$scope.colSm;
  }  
});