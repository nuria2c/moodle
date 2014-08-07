YUI.add('moodle-theme_cleanudem-navdropdownbehavior', function(Y) {

/**
 * Class navdropdownbehavior for Clean UdeM theme.
 * Init this class by calling M.theme_cleanudem.init_nav_dropdown_behavior
 */
var navdropdownbehavior = function() {
    navdropdownbehavior.superclass.constructor.apply(this, arguments);
};
navdropdownbehavior.prototype = {
    initializer : function() {
        // Init only for desktop devices.
        if (Y.one('header.navbar-fixed-top').hasClass('default-device')) {
            this.matchMediaPolyfill();
            var mql = window.matchMedia('all and (max-width: 979px)');
            this.handleMatchMedia(mql); // Execute on load.
            if (mql.addListener) {
                // Execute each time media query will be reached.
                mql.addListener(this.handleMatchMedia);
            } else {
                // For ie9.
                Y.on('windowresize',this.handleMatchMedia , window, this, mql);
            }
        }
    },
    handleMatchMedia : function(mediaQuery) {
        // Need to redeclare this for ie9.
        mediaQuery = window.matchMedia('all and (max-width: 979px)');
        var dropdowntoggle = Y.all('.default-device .nav li.dropdown > .dropdown-toggle');
        if (mediaQuery.matches) {
            dropdowntoggle.setAttribute('data-toggle', 'dropdown');
        } else {
            dropdowntoggle.setAttribute('data-toggle', '');
        }
    },
    matchMediaPolyfill : function() {
        window.matchMedia || (window.matchMedia = function() {
            "use strict";
            // For browsers that support matchMedium api such as IE 9 and webkit.
            var styleMedia = (window.styleMedia || window.media);
            return function(media) {
                return {
                    matches: styleMedia.matchMedium(media || 'all'),
                    media: media || 'all'
                };
            };
        }());
    }
};
Y.extend(navdropdownbehavior, Y.Base, navdropdownbehavior.prototype, {
    NAME : 'Clean UdeM theme navigation dropdown behavior',
    ATTRS : {}
});

M.theme_cleanudem = M.theme_cleanudem || {};
M.theme_cleanudem.init_nav_dropdown_behavior = function(config) {
    return new navdropdownbehavior(config);
};

}, '@VERSION@', {requires:['base','node', 'event-resize']});
