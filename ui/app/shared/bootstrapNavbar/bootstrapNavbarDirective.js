angular.module("rb")
    .directive("bootstrapNavbar", bootstrapNavbar);

function bootstrapNavbar($location,$state) {
    return {
            restrict: "E",
            replace: true,
            transclude: true,
            templateUrl: "app/shared/bootstrapNavbar/bootstrapNavbarView.html",
            link: function(scope){
                scope.isActive = function (viewLocation) {
                    return viewLocation === $location.path();
                };
            }
        }};