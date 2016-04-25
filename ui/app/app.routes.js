angular.module('rb')
    .config(function($stateProvider,$urlRouterProvider,$resourceProvider) {

        $stateProvider
            .state('home',      {url: '/home',      templateUrl: 'app/components/home/homeView.html',          controller:'homeController'})
            .state('eingabe',   {url: '/eingabe',   templateUrl: 'app/components/eingabe/eingabeView.html',    controller:'eingabeController'})
            .state('teams',     {url: '/teams',     templateUrl: 'app/components/teams/teamView.html',         controller:'teamsController'})
            .state('gruppen',   {url: '/gruppen',   templateUrl: 'app/components/gruppen/gruppenView.html',    controller:'gruppenController'})
            .state('monitore',  {url: '/monitore',  templateUrl: 'app/components/monitore/monitorView.html',   controller:'monitorController'})
            .state('monitorview',  {url: '/monitorview',  templateUrl: 'app/components/monitorview/monitorviewView.html',   controller:'monitorviewController'})
            .state('custom',    {url: '/custom',    templateUrl: 'app/components/custom/customView.html',      controller:'customController'})


        $urlRouterProvider.otherwise("/home");
        $resourceProvider.defaults.stripTrailingSlashes = false;

    })
    .run(function($rootScope) {
        $rootScope.url = "../backend/";
        $rootScope.monitorview = false;
        $rootScope.genugBezahlt = function(team){
            if(team == null || team.betrag_bezahlt == null)return false;
            return team.nenngeld_gesamt <= team.betrag_bezahlt;
        }
    })
    .directive('stringToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                ngModel.$parsers.push(function(value) {
                    return '' + value;
                });
                ngModel.$formatters.push(function(value) {
                    return parseFloat(value, 10);
                });
            }
        };
    });;
