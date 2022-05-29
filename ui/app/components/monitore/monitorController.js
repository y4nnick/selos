angular.module('rb').controller('monitorController' , monitorController);

function monitorController($scope, monitor,monitoransicht,$interval) {

    //console.log("setted   {{$root.monitorview}}");

    $scope.monitore = null;
    $scope.monitoransichten = null;

    $scope.newansicht = false;
    $scope.ansicht = new monitoransicht();

    $scope.reload = function(){
        monitor.query(function(response){
            $scope.monitore = response;
        },function(response){
            console.log(response);
        });

        monitoransicht.query(function(response){
            $scope.monitoransichten = response;
        },function(response){
            console.log(response);
        });
    };

    //$interval($scope.reload,5000);
    $scope.reload();

    //
    // Monitoransicht
    //
    $scope.saveNewAnsicht = function(ansicht){
      ansicht.$save(function (response) {
          $scope.reload();
          $scope.newansicht = false;
      });
    };
}