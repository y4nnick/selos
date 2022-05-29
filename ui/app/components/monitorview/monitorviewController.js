angular.module('rb').controller('monitorviewController' , monitorviewController);

function monitorviewController($scope, monitor,monitoransicht,$rootScope) {

    console.log("in monitorviewController");


    monitor.getByNumber({number:23},function(response){


        if(response.success == false){

            console.log(response);
            console.log("not found: " + response.result);

            $scope.new = new monitor();
            $scope.new.number = 23;
            $scope.new.name = "test";
            $scope.new.$save(function(data){
                $scope.current = data;
            });
        }else{
            console.log("foudn");
            console.log(response);
            $scope.current = response;

        }

    });



    $rootScope.monitorview = true;

   /* $scope.monitore = null;
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
    };*/
}