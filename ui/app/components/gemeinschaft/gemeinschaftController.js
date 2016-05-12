angular.module('rb').controller('gemeinschaftController' , gemeinschaftController)
    .filter('myDateFilter', function() {
        return function(input) {
            return new Date(input);
        };
    }
);

function gemeinschaftController($scope,team,bewerb,gemeinschaft,$rootScope) {

    $scope.gemeinschaft = {};
    $scope.changed = false;

    $scope.bereitsBezahlt = function(){
        var betrag = 0;
        if($scope.gemeinschaft.ownTeam !== undefined){
            $scope.gemeinschaft.ownTeam.forEach(function(team){
                betrag += $scope.getBezahltGesamt(team);
            });
        }
        return betrag;
    };

    $scope.zuZahlen = function(){
        var betrag = 0;
        if($scope.gemeinschaft.ownTeam !== undefined){
            $scope.gemeinschaft.ownTeam.forEach(function(team){
                if(team.anwesend == 1){
                    betrag += $scope.getZuzahlen(team);
                }
            });
        }

        return betrag;
    };

    $scope.setAlleAnwesend = function(){
        $scope.gemeinschaft.ownTeam.forEach(function(team){

            if(team.anwesend == false){
                team.anwesend = true;
                console.log("in");
                $scope.onClickAnwesend(team);
            }

        });

        $scope.changed = true;
    }

    $scope.alleBezahlen = function(){

        swal({
            text: "Willst du wirklich alle Bezahlungen für alle anwesenden Teams eintragen? Betrag: " + $scope.zuZahlen()+"€",
            type: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ja'
        }).then(function(isConfirm) {

            countFinished = 0;
            countError = 0;
            toFinish = 0;

            if (isConfirm) {

                $scope.gemeinschaft.ownTeam.forEach(function(t){

                    if(t.anwesend == true){
                        team.get({id:t.id},function(loadedT){

                            var zuZahlen = $scope.getZuzahlen(loadedT);

                            if(zuZahlen > 0){

                                loadedT.bezahlt_vorort = zuZahlen;
                                toFinish++;
                                loadedT.$update().then(
                                    //success
                                    function( value ){
                                        countFinished++;

                                        if((countFinished + countError) == toFinish){
                                            swal("Erfolgreich gespeichert");
                                            $scope.loadGemeinschaft();
                                        }
                                    },
                                    //error
                                    function( error ){
                                        countError++;
                                        console.log(error);

                                        if((countFinished + countError) == toFinish){
                                            swal("Erfolgreich gespeichert");
                                            $scope.loadGemeinschaft();
                                        }
                                    }
                                );
                            }

                        });
                    }
                });
            }
        })




    }


    var countFinished = 0;
    var countError = 0;
    var toFinish = 0;
    $scope.storeAll = function(){

        countFinished = 0;
        countError = 0;
        toFinish = 0;

        $scope.gemeinschaft.ownTeam.forEach(function(t){

            if(t.changed == true){


                team.get({id:t.id},function(loadedT){

                    if(loadedT.anwesend != t.anwesend || loadedT.abgemeldet != t.abgemeldet){
                        loadedT.anwesend = t.anwesend;
                        loadedT.abgemeldet = t.abgemeldet;

                        toFinish++;
                        loadedT.$update().then(
                            //success
                            function( value ){
                                countFinished++;

                                if((countFinished + countError) == toFinish){
                                    swal("Erfolgreich gespeichert");
                                    $scope.loadGemeinschaft();
                                }
                            },
                            //error
                            function( error ){
                                countError++;
                                console.log(error);

                                if((countFinished + countError) == toFinish){
                                    swal("Erfolgreich gespeichert");
                                    $scope.loadGemeinschaft();

                                }
                            }
                        );
                    }
                });
            }

        });
    }

    $scope.onClickAnwesend = function(team){
        team.changed = true;

        if(team.anwesend && team.abgemeldet){
            team.abgemeldet = false;
        }
    }

    //
    // Load basic data
    //

    $scope.loadBewerbe = function(){
        bewerb.query(function(data){
            $rootScope.bewerbe = [];
            $rootScope.bewerbeIndex = []

            data.forEach(function(entry){
                $rootScope.bewerbe.push(entry);
                $rootScope.bewerbeIndex[entry.id] = entry;
            });

        },function(error){
            console.log("error while loading bewerbe: " + error);
        });
    }

    $scope.query = null;
    $scope.loadGemeinschaft = function(){
        $scope.query = gemeinschaft.query(function(data){

            data.forEach(function(g){
                if(g.onlineid==3){

                    gemeinschaft.get({id:g.id},function(loadedG){


                        $scope.gemeinschaft = loadedG;

                        console.log($scope.gemeinschaft);

                        $scope.gemeinschaft.ownTeam.forEach(function(team){
                            $scope.setBezahlung(team);
                        });

                    });

                }
            })

        },function(error){
            console.log(error);
            $scope.gemeinschaft = {};
        })
    }

    $scope.setBezahlung = function(team){
        team.genugBezahlt = $scope.genugBezahlt(team);
    }

    $scope.loadBewerbe();
    $scope.loadGemeinschaft();

    //
    // Helper
    //
    $scope.getBewerbName = function(id){
        if($rootScope.bewerbeIndex !== undefined && $rootScope.bewerbeIndex[id] !== undefined){
            return $rootScope.bewerbeIndex[id].name;
        }
    }

    $scope.getBezahltGesamt = function(team){
        if(team == null) return 0;

        var sum = 0;
        if(!isNaN(team.bezahlt_betrag))sum += parseInt(team.bezahlt_betrag,10);
        if(!isNaN(team.bezahlt_vorort))sum += parseInt(team.bezahlt_vorort,10);

        return sum;
    }

    $scope.getZuzahlen = function(team){
        if(team == null) return 0;

        return team.nenngeld_gesamt - $scope.getBezahltGesamt(team);
    }

    $scope.genugBezahlt = function(team){
        if(team==null)return false;
        return $scope.getBezahltGesamt(team) >= team.nenngeld_gesamt;
    }
}