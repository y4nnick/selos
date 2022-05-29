angular.module('rb').factory('gruppe' , bewerbFactory);

function bewerbFactory($resource,$rootScope){
    var resource = $resource($rootScope.url + "api/gruppe/:id",{id: "@id"},
        {
            'update': { method:'PUT' },
            'drucken': {method:'POST', url: $rootScope.url + "api/gruppe/:id/drucken"},
            'druckenCustom': {method:'POST', url: $rootScope.url + "api/gruppe/drucken"}
        }
    );

    return resource;
};