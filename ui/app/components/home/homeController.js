angular.module('rb').controller('homeController' , homeController);

function homeController($scope, team, $rootScope, $http,$interval) {

    $scope.gesamt = null;
    $scope.bewerbe = null;

    $scope.reload = function(){

        team.anwesenheit(function(response){
            $scope.gesamt = response.gesamt;
            $scope.bewerbe = response.bewerbe;
        },function(response){
            console.log(response);
        });

        /*$http({
            method: 'GET',
            url: $rootScope.url+'/TeamService/getAnwesenheiten'
        }).then(function successCallback(response) {
            $scope.gesamt = response.data.gesamt;
            $scope.bewerbe = response.data.bewerbe;
        }, function errorCallback(response) {
        });*/
    };

    $interval($scope.reload,5000);
    $scope.reload();
}