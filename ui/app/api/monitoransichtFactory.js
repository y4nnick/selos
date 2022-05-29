angular.module('rb').factory('monitoransicht' , factory);

function factory($resource,$rootScope){

    var resource = $resource($rootScope.url + "api/monitoransicht/:id",{id: "@id"},
        {
            update: {
                method: 'PUT',
            }
        }
    );

    return resource;
};
