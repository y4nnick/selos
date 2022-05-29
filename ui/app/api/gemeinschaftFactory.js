angular.module('rb').factory('gemeinschaft' , gemeinschaftFactory);

function gemeinschaftFactory($resource,$rootScope){
    var resource = $resource($rootScope.url + "api/gemeinschaft/:id",{id: "@id"},
        {
            'update': { method:'PUT' }
        }
    );

    return resource;
};