angular.module('rb').factory('team' , factory);

function factory($resource,$rootScope){

    var resource = $resource($rootScope.url + "api/team/:id",{id: "@id"},
        {
            update: {
                method: 'PUT',
                transformRequest: function(data, headersGetter){
                    delete data.display;
                    return angular.toJson(data);
                }
            },
            anwesenheit: {method: 'GET', url: $rootScope.url + "api/team/anwesenheit"},
            zahlungenVorOrt: {method: 'GET', url: $rootScope.url + "api/team/zahlungenVorOrt"}
        }
    );

    return resource;
};
