angular.module("rb").directive('grouptable', function ($parse) {
    return {
        restrict: 'E', // It is an attribute
        scope: {
            gruppe: '='
        },
        templateUrl: "app/shared/grouptable/grouptableView.html",
        link: function($scope, element, attrs){
            $scope.getErg = function(gruppe,team,innerTeam){
                return "";
               /* var spiel;
                var foundSpiel = null;
                for(var i = 0; i < gruppe.ownSpiel.length; i++){
                    spiel = gruppe.ownSpiel[i];

                    if((typeof(spiel) !== "undefined" && typeof(team) !== "undefined" && typeof(innerTeam) !== "undefined") &&
                        (team.id == spiel.team1_id && innerTeam.id == spiel.team2_id
                        || team.id == spiel.team2_id && innerTeam.id == spiel.team1_id)){
                        foundSpiel = spiel;
                        break;
                    }
                }

                if(foundSpiel == null || foundSpiel.erg == null)return "";

                //Ergebnis umdrehen falls Team vertauscht
                if(team.id == spiel.team2_id){
                    var values = foundSpiel.erg.split(":");
                    return values[1]+":"+values[0];
                }

                return spiel.erg;*/
            }
        }
    };
});