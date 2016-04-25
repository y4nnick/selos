angular.module('rb').controller('teamsController' , teamsController)
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


function teamsController($scope,team,$scope,team,bewerb,$rootScope) {

    $scope.teams = [];
    $scope.searchTerm = "";

    $scope.currentTeamSave = null;
    $scope.currentTeam = null;

    $scope.searchTermSmall = "";

    $scope.$watch('searchTerm', function() {
        $scope.searchTermSmall = $scope.searchTerm.toLowerCase();
    });

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
                $scope.loadTeams();
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
        },function(error){
            console.log("error while loading teams: " + error);
            $scope.teams = [];
        })
    }

    $scope.onDoubleClick = function(inputTeam){
        var loadedTeam = team.get({id:inputTeam.id});
        $scope.currentTeam = loadedTeam;
        $scope.currentTeamSave = angular.copy(loadedTeam);
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

    $scope.resetCurrentTeam = function(){
        $scope.performReset();
    }

    $scope.performReset = function(){
        $scope.currentTeam = $scope.currentTeamSave;
        $scope.currentTeam = null;
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
}