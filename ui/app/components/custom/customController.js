angular.module('rb').controller('customController' , customController)
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


function customController($scope,team,bewerb,$rootScope) {

    $scope.showTeams = function(){
        return [$scope.team1,$scope.team2]
    };

    $scope.team1 = null;
    $scope.team2 = null;

    $scope.teams = [];
    $scope.searchTerm = "";
    $scope.searchTerm2 = "";

    $scope.searchTermSmall = "";
    $scope.searchTermSmall2 = "";

    $scope.$watch('searchTerm', function () {
        $scope.searchTermSmall = $scope.searchTerm.toLowerCase();
    });

    $scope.$watch('searchTerm2', function () {
        $scope.searchTermSmall2 = $scope.searchTerm2.toLowerCase();
    });

    $scope.selectedCurrentTeam = function () {
        return $scope.currentTeam != {};
    }

    $scope.isInt = function (value) {
        return !isNaN(value) &&
            parseInt(Number(value)) == value && !isNaN(parseInt(value, 10));
    }

    $scope.query = null;
    $scope.loadTeams = function () {
        $scope.query = team.query(function (data) {
            $scope.totalItems = data.length;
            $scope.teams = data;
        }, function (error) {
            console.log("error while loading teams");
            console.log(error);
            $scope.teams = [];
        })
    }

    $scope.onDoubleClick = function (side,inputTeam) {
        var loadedTeam = team.get({id: inputTeam.id});

        if(side == 1){
            $scope.team1 = loadedTeam;
            $scope.searchTerm = "";
        }else{
            $scope.team2 = loadedTeam;
            $scope.searchTerm2 = "";
        }

    }

    $scope.search = function (inputTeam) {
        return (
        (inputTeam.onlineid.indexOf($scope.searchTermSmall) !== -1)
        || (angular.lowercase(inputTeam.spieler).indexOf($scope.searchTermSmall) !== -1)
        || (angular.lowercase(inputTeam.display['Teamname']).indexOf($scope.searchTermSmall) !== -1) )
    };

    $scope.search2 = function (inputTeam) {
        return (
        (inputTeam.onlineid.indexOf($scope.searchTermSmall2) !== -1)
        || (angular.lowercase(inputTeam.spieler).indexOf($scope.searchTermSmall2) !== -1)
        || (angular.lowercase(inputTeam.display['Teamname']).indexOf($scope.searchTermSmall2) !== -1) )
    };

    $scope.loadBewerbe = function () {
        bewerb.query(function (data) {
            $rootScope.bewerbe = [];
            data.forEach(function (entry) {
                $rootScope.bewerbe[entry.id] = entry;
            });
        }, function (error) {
            console.log("error while loading bewerbe: " + error);
        });
    }

    $scope.loadBewerbe();
    $scope.loadTeams();

    $scope.resetCurrentTeam = function () {
        $scope.performReset();
    }

    $scope.performReset = function () {
        $scope.currentTeam = $scope.currentTeamSave;
        $scope.currentTeam = null;
    }

    $scope.getBewerbName = function (id) {
        if ($rootScope.bewerbe !== undefined && $rootScope.bewerbe[id] !== undefined) {
            return $rootScope.bewerbe[id].name.charAt(0);
        }
    }

    $scope.getBewerbNameLong = function (id) {
        if ($rootScope.bewerbe !== undefined && $rootScope.bewerbe[id] !== undefined) {
            return $rootScope.bewerbe[id].name;
        }
    }

    $scope.isZweiterBewerb = function (spieler) {
        var zweiter = false;
        spieler.ownSpielerinfo.forEach(function (info) {
            if (info.titel == "Zweiter Bewerb" && info.wert != "") {
                zweiter = true;
            }
        });
        return zweiter;
    }

    $scope.getRelevantTeamInfo = function(team){

        if( team === 'undefined' ||  team == null || team.ownTeaminfo === 'undefined' || team.ownTeaminfo == null) {
            return;
        }

        var infos = [];
        team.ownTeaminfo.forEach(function(info){
            if(info.titel != "E-Mail" && info.titel != "Handynummer" &&info.titel != "Teilnahmebedingungen") {
                if(info.wert == ""){
                    info.wert = "-";
                }
                infos.push(info);
            }
        });
        return infos;
    }

}