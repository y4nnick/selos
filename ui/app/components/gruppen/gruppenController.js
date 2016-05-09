angular.module('rb').controller('gruppenController' , gruppenController);

function gruppenController($scope,$element, dragularService, bewerb,gruppe,$timeout) {

    $scope.bewerbe = bewerb.query();
    $scope.selBewerb = null;
    $scope.activeTabIndex = 0;
    $scope.active = [];

    $scope.res = null;
    $scope.storeSelectedBewerb = function(){
        if($scope.res == null){
            $scope.res = $scope.selBewerb.$update(function(data){
               $scope.loadSelectedBewerb($scope.selBewerb.id);
            });
        }
    }

    $scope.initGrouptables = function(){
        $scope.res = null;

        $scope.selBewerb.ChangedTeams = false;

        if($scope.selBewerb.ownGruppe !== undefined){
            $scope.selBewerb.ownGruppe.forEach(function(gruppeIn){
                $timeout(function(){
                    $scope.$apply(function(){
                        var container = document.querySelector('#container_'+$scope.selBewerb.id+'_'+gruppeIn.id);

                        $scope.$on('dragulardragend', myFn('dragend'));

                        function myFn(eventName) {
                            return function() {
                                $scope.selBewerb.ChangedTeams = true;
                            };
                        }

                        dragularService([container], {
                            containersModel: [gruppeIn.ownTeam],
                            scope: $scope
                        });
                    });
                },0);
            });
        }
    }

    $scope.loadSelectedBewerb = function(id){
        bewerb.get({id:id},function(bewerbLoaded){
            $scope.selBewerb = bewerbLoaded;
            $scope.initGrouptables();
        });
    }

    $scope.selected = function(bewerbIn,index){
        $scope.activeTabIndex = index;
        $scope.loadSelectedBewerb(bewerbIn.id);
    }

    //
    // Drucken
    //
    $scope.druckAll = function(print){
        $scope.selBewerb.ownGruppe.forEach(function(gruppeIn){
            $scope.drucken(
                gruppeIn,
                print.raster,
                print.spiele,
                print.rasterBackground,
                print.spieleBackground,
                print.rasterblatt.selected,
                print.rastergroesse.selected,
                print.spieleInfo
            )
        });
        swal({
            title: "Erfolg",
            text: "Druckauftrag gesendet",
            type: "success"
        });
    }

    $scope.drucken = function(gr,printRaster,printSpiele,rasterBack,spieleBack,format,groesse,info){

        gruppe.drucken({
            id:gr.id,
            raster:printRaster,
            spiele:printSpiele,
            backgroundRaster:rasterBack,
            backgroundSpiele:spieleBack,
            format:format,
            groesse:groesse,
            info:info
        },function(gruppeLoaded){
            swal({
                title: "Erfolg",
                text: "Erfolgreich gedruckt",
                type: "success"
            });
        },function(error){
            console.log(error);
            swal({
                title: "Fehler",
                text: error.data.message,
                type: "error"
            });
        });
    }

    $scope.getTeamsAnzahl = function(){
        if($scope.selBewerb == null) return 0;
        return $scope.selBewerb.teams;
    }

    $scope.print = {
        raster:false,
        rasterBackground:false,
        rasterblatt: {
            options: ['A3','A4'],
            selected: 'A3'
        },
        rastergroesse: {
            options: ['4','5','6'],
            selected: '5'
        },
        spiele:false,
        spieleBackground:false,
        spieleInfo: "Ein Satz auf 21"
    }

    $scope.selectedBlatt = function(blatt){
        if(blatt == "A3"){
            $scope.print.rastergroesse.options = ['5','6'];

            if($scope.print.rastergroesse.selected == 4){
                $scope.print.rastergroesse.selected = '5';
            }
        }else{
            $scope.print.rastergroesse.options = ['4'];
            $scope.print.rastergroesse.selected = '4';
        }
    }

    $scope.selectedBlatt("A3");

    //
    // Auslosen
    //
    $scope.auslosen = function(bewerbInput,aufteilung){
        var gruppen = aufteilung.split(',');
        var gruppenInt = [];
        var sum = 0;

        bewerbInput.erneutauslosen = false;

        gruppen.forEach(function(elem){
            gruppenInt.push(parseInt(elem));
            sum += parseInt(elem);
        });

        //Check if summe stimmt
        if(sum != $scope.selBewerb.ownTeam.length){
            swal({
                title: "Fehler",
                text: "Anzahl der Teams in diesem Bewerb stimmen nicht mit der Anzahl der Teams in den Gruppen überein",
                type: "error"
            });
            return
        }

        bewerbInput.$auslosen({aufteilung:aufteilung},function(response){
            swal({
                title: "Erfolg",
                text: "Auslosung wurde erfolgreich durchgeführt",
                type: "success"
            });

            //Reload bewerbe
            $scope.bewerbe = bewerb.query();

            //set selected Bewerb to current one
            for(var i = 0; i < $scope.active.length; i++){
                $scope.active[i] = $scope.activeTabIndex == i;
            }

        },function(error){
            swal({
                title: "Fehler während dem Auslosen",
                text: error.data.message,
                type: "error"
            });
        });

    };
}