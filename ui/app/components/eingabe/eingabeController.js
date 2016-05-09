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
    $scope.searchTerm = "";

    $scope.currentTeamSave = null;
    $scope.currentTeam = null;

    $scope.editmode = false;

    $scope.searchTermSmall = "";

    $scope.$watch('searchTerm', function() {
        $scope.searchTermSmall = $scope.searchTerm.toLowerCase();
    });

    $scope.store = function(){

        $scope.currentTeam.$update().then(
            //success
            function( value ){
                console.log(value);
                swal({
                    title: "Erfolgreich!",
                    text: "Team wurde erfolgreich gespeichert",
                    type: "success",
                    confirmButtonText: "Ok"
                });
                $scope.currentTeam = null;
                $scope.editmode = false;
                console.log(value);
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
            console.log("found and updated team");
            console.log( $scope.teams[foundIndex]);
        }
    }

    $scope.selectedCurrentTeam = function(){
        return $scope.currentTeam != {};
    }

    $scope.isInt = function(value) {
        return !isNaN(value) &&
            parseInt(Number(value)) == value &&
            !isNaN(parseInt(value, 10));
    }

    $scope.query = null;
    $scope.loadTeams = function(){
        $scope.query = team.query(function(data){
            $scope.totalItems = data.length;
            $scope.teams = data;
            console.log($scope.teams);
        },function(error){
            console.log("error while loading teams: " + error);
            $scope.teams = [];
        })
    }

    $scope.onDoubleClick = function(inputTeam){
        var loadedTeam = team.get({id:inputTeam.id});
        $scope.currentTeam = loadedTeam;
        $scope.currentTeamSave = angular.copy(loadedTeam);
        $scope.changend = false;
    }

    $scope.search = function(inputTeam){
        return (
                (inputTeam.onlineid.indexOf($scope.searchTermSmall) !== -1)
            ||  (angular.lowercase(inputTeam.spieler).indexOf($scope.searchTermSmall) !== -1)
            ||  (angular.lowercase(inputTeam.display['Teamname']).indexOf($scope.searchTermSmall) !== -1) )
    };

    $scope.loadBewerbe = function(){
        bewerb.query(function(data){
            $rootScope.bewerbe = [];
            data.forEach(function(entry){
                $rootScope.bewerbe[entry.id] =  entry;
            });
        },function(error){
            console.log("error while loading bewerbe: " + error);
        });
    }

    $scope.loadBewerbe();
    $scope.loadTeams();

    $scope.changend = false;
    $scope.changedCurrentTeam = function(){
        if($scope.currentTeam == null || $scope.currentTeamSave == null){
            return false;
        }
        return $scope.changend;
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
                closeOnConfirm: true },
                function(){
                    $scope.performReset();
                    $scope.$apply();
                }
            );
        }
    }

    $scope.performReset = function(){
        $scope.currentTeam = $scope.currentTeamSave;
        $scope.currentTeam = null;
        $scope.editmode = false;
        $scope.changend = false;
    }

    $scope.getBewerbName = function(id){
        if($rootScope.bewerbe !== undefined && $rootScope.bewerbe[id] !== undefined){
            return $rootScope.bewerbe[id].name;
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