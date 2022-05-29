angular.module('rb').factory('monitor' , factory);

function factory($resource,$rootScope){

    var resource = $resource($rootScope.url + "api/monitor/:id",{id: "@id"},
        {
            update: {
                method: 'PUT',
            },
            getByNumber: {
                method: 'GET',
                url: $rootScope.url + "api/monitor/getByNumber"
            }
        }
    );

    return resource;
};
