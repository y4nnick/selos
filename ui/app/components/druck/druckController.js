angular.module('rb').controller('druckController' , druckController);

function druckController($scope, team,bewerb,gruppe) {

    //
    // Prints options
    //

    $scope.infoOptions = [
        "Ein Satz auf 15",
        "Ein Satz auf 21",
        "Zwei gespielte Sätze auf 15",
        "Zwei gespielte Sätze auf 21",
        "Zwei gewonnen Sätze auf 15, 3. Satz auf 15",
        "Zwei gewonnen Sätze auf 21, 3. Satz auf 15",
    ]

    $scope.print = {
        raster: true,
        rasterBackground: true,
        rasterblatt: {
            options: ['A3', 'A4'],
            selected: 'A3'
        },
        rastergroesse: {
            options: ['4', '5', '6'],
            selected: '5'
        },
        spiele: true,
        spieleBackground: true,
        spieleInfo: "",
        bewerb: "",
        gruppenname: ""
    }


    //
    // Load Basic data
    //
    $scope.bewerbe = [];
    $scope.teams = [];
    $scope.loadData = function(){
        team.query(function(data){
            $scope.teams = data;
        })

        bewerb.query(function(data){
            $scope.bewerbe = data;
        })
    }
    $scope.loadData();


    //
    // Selected Teams
    //
    $scope.selectedTeams = [];
    $scope.removeTeam = function(team){
        var index = $scope.selectedTeams.indexOf(team);

        if (index > -1) {
            $scope.selectedTeams.splice(index, 1);
        }
    };

    $scope.selectTeam = function(teamName){

        if(teamName.length === 0){
            return;
        }

        var team = $scope.getTeamFromName(teamName);

        if(team == null){
            var team = {display:{Teamname:teamName},spieler:"-"};
            console.log("Team not found: " + teamName);
        }

        $scope.selectedTeams.push(team);

        $scope.team = "";
    };


    //
    // Print function
    //

    $scope.drucken = function(opt,teams){//printRaster,printSpiele,rasterBack,spieleBack,format,groesse,info,gruppenname, bewerb,teams){

        var teamnames = [];

        teams.forEach(function(team){
            teamnames.push({name:team.display.Teamname,spieler:team.spieler});
        });

        gruppe.druckenCustom({
            gruppenname:        opt.gruppenname,
            bewerb:             opt.bewerb,
            teams:              teamnames,
            raster:             opt.raster,
            spiele:             opt.spiele,
            backgroundRaster:   opt.rasterBackground,
            backgroundSpiele:   opt.spieleBackground,
            format:             opt.rasterblatt.selected,
            groesse:            opt.rastergroesse.selected,
            info:               opt.spieleInfo
        },function(result){
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


    //
    // Helper
    //

    var BreakException= {};
    $scope.getTeamFromName = function(name){
        var found = null;

        try{
            $scope.teams.forEach(function(team){
                if(team.display.Teamname+"" === name+""){
                    found = team;
                    throw BreakException;
                }
            });
        }catch (e){
            if (e!==BreakException) throw e;
        }

        return found;
    }
}