angular.module('rb').controller('eingabeController' , eingabeController)
    .filter('highlight', function($sce) {
        return function(text, phrase) {
            if (phrase) text = text.replace(new RegExp('('+phrase+')', 'gi'),
                '<span class="highlighted">$1</span>')
            return $sce.trustAsHtml(text)
        }
    }).filter('myDateFilter', function() {
        return function(input) {
            return new Date(input);
        };
    }
);

function eingabeController($scope,team,bewerb,$rootScope) {

    $scope.teams = [];
    $scope.currentTeamSave = null;
    $scope.currentTeam = null;
    $scope.editmode = false;

    //
    // Store function
    //

    $scope.store = function(){

        $scope.currentTeam.$update().then(
            //success
            function( value ){
                swal({
                    title: "Erfolgreich!",
                    text: "Team wurde erfolgreich gespeichert",
                    type: "success",
                    confirmButtonText: "Ok"
                });
                $scope.currentTeam = null;
                $scope.editmode = false;
                $scope.storeUpdatedTeam(value);
               // $scope.loadTeams();
            },
            //error
            function( error ){
                swal({
                    title: "Fehler",
                    text: "Fehler während dem Speichern, " + error.message,
                    type: "error"
                });
            }
        );
    }

    var BreakException= {};
    $scope.storeUpdatedTeam = function(team){

        $scope.setBezahlung(team);

        var index = 0;
        var foundIndex = -1;
        try{
            $scope.teams.forEach(function(t){
                if(t.id == team.id){
                    foundIndex = index;
                    throw BreakException;
                }
                index++;
            });
        }catch (e){
            if (e!==BreakException) throw e;
        }

        if(foundIndex != -1){
            $scope.teams[foundIndex] = team;
        }
    }

    //
    // Notiz
    //
    $scope.showNotiz = function(team){
        swal(
            {
                title: "Notiz",
                text: "für das Team '" + team.display["Teamname"]+"'",
                input: "textarea",
                showCancelButton: true,
                closeOnConfirm: true,
                animation: "slide-from-top",
                inputPlaceholder: "Schreib etwas...",
                inputValue: team.notiz,
                inputClass: "normalfontsize"
            }
        ).then(function(result){

            if (result !== false) {
                team.notiz = result;

                team.$update().then(
                    //success
                    function( value ){
                        swal({
                            title: "Erfolgreich!",
                            text: "Notiz wurde erfolgreich gespeichert",
                            type: "success",
                            confirmButtonText: "Ok"
                        });
                        $scope.storeUpdatedTeam(value);
                    },
                    //error
                    function( error ){
                        swal({
                            title: "Fehler",
                            text: "Fehler während dem Speichern, " + error.message,
                            type: "error"
                        });
                    }
                );
            }

        });
    }


    //
    // Search
    //
    $scope.searchTerm = "";
    $scope.searchTermSmall = "";
    $scope.filter = {
        nurBezahlte: false,
        nurNichtBezahlte: false,
        anwesend:false,
        nichtanwesend:false,
        bewerb: null
    }

    $scope.$watch('searchTerm', function() {
        $scope.searchTermSmall = $scope.searchTerm.toLowerCase();
    });

    $scope.search = function(inputTeam){

        var filter = true;

        //Teamname erst ab 2 Zeichen
        if($scope.searchTermSmall != null && $scope.searchTermSmall.length >= 2){
            filter = filter && (
                (inputTeam.onlineid.indexOf($scope.searchTermSmall) !== -1)
                ||  (angular.lowercase(inputTeam.spieler).indexOf($scope.searchTermSmall) !== -1)
                ||  (angular.lowercase(inputTeam.display['Teamname']).indexOf($scope.searchTermSmall) !== -1) );
        }

        //Bezahlt
        if($scope.filter.nurBezahlte){
            filter = filter && inputTeam.genugBezahlt;
        }else if($scope.filter.nurNichtBezahlte){
            filter = filter && !inputTeam.genugBezahlt;
        }

        //Anwesenheit
        if($scope.filter.anwesend){
            filter = filter && (inputTeam.anwesend == 1);
        }else if($scope.filter.nichtanwesend){
            filter = filter && (inputTeam.anwesend != 1);
        }

        //Bewerb
        if($scope.filter.bewerb != null){
            filter = filter && (inputTeam.bewerb_id == $scope.filter.bewerb.id);
        }

        return filter;
    };



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
    $scope.loadTeams = function(){
        $scope.query = team.query(function(data){
            $scope.teams = data;
            $scope.teams.forEach(function(team){
               $scope.setBezahlung(team);
            });

        },function(error){
            $scope.teams = [];
        })
    }

    $scope.setBezahlung = function(team){
        team.genugBezahlt = $scope.genugBezahlt(team);
    }

    $scope.loadBewerbe();
    $scope.loadTeams();



    //
    // Bearbeiten Stuff
    //

    $scope.isInt = function(value) {
        return !isNaN(value) &&
            parseInt(Number(value)) == value &&
            !isNaN(parseInt(value, 10));
    }

    $scope.onDoubleClick = function(inputTeam){
        var loadedTeam = team.get({id:inputTeam.id});
        $scope.currentTeam = loadedTeam;
        $scope.currentTeamSave = angular.copy(loadedTeam);

        $scope.changend = false;
    }

    $scope.changend = false;
    $scope.changedCurrentTeam = function(){
        if($scope.currentTeam == null || $scope.currentTeamSave == null){
            return false;
        }
        return $scope.changend;
    }

    $scope.selectedCurrentTeam = function(){
        return $scope.currentTeam != {};
    }

    $scope.resetCurrentTeam = function(){

        if(!$scope.changedCurrentTeam()){
            $scope.performReset();
        }else{
            swal({
                title: "Nicht gespeichert",
                text: "Willst du die Änderungen wirklich verwerfen?",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "Zurück",
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ja, verwerfen!",
                closeOnConfirm: true
            }).then(function(result){
                if(result){

                    $scope.performReset();
                    $scope.$apply();
                }
            });
        }
    }

    $scope.performReset = function(){
        $scope.currentTeam = $scope.currentTeamSave;
        $scope.currentTeam = null;
        $scope.editmode = false;
        $scope.changend = false;
    }

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

    $scope.isZweiterBewerb = function(spieler){
        var zweiter = false;
        spieler.ownSpielerinfo.forEach(function(info){
            if(info.titel == "Zweiter Bewerb" && info.wert != ""){
                zweiter = true;
            }
        });
        return zweiter;
    }

    $scope.allesBezahlen = function(team){


        if(team.bezahlt_vorort.length > 0){
            team.bezahlt_vorort = parseInt(team.bezahlt_vorort,10) +  parseInt($scope.getZuzahlen(team),10);
        }else{
            team.bezahlt_vorort = parseInt($scope.getZuzahlen(team),10);
        }

        $scope.changend = true;
        team.anwesend = true;
        team.abgemeldet = false;
    }

    //
    // Bezahlungen vor Ort
    //

    $scope.bezahltvorort = 0;
    $scope.updateBezahlungen = function(){
        team.zahlungenVorOrt(function(response){
            $scope.bezahltvorort = response.bezahlt;
        },function(response){
            console.log(response);
        });
    }
    $scope.updateBezahlungen();
}