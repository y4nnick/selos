angular.module('rb').factory('bewerb' , bewerbFactory);

function bewerbFactory($resource,$rootScope){
    var resource = $resource($rootScope.url + "api/bewerb/:id",{id: "@id"},
        {
            'update': {
                method:'PUT',
                transformRequest: function(data, headersGetter){

                    data.ownGruppe.forEach(function(gruppe){
                        gruppe.ownTeam.forEach(function(team) {
                            delete team.display;
                        });
                    });

                    return angular.toJson(data);
                }
            },
            'auslosen': {method:'POST', url: $rootScope.url + "api/bewerb/:id/auslosen"}
        }
    );

    return resource;
};