YUI.add('moodle-theme_cleanudem-fullscreenmode', function(Y) {

/**
 * Global CSS selectors.
 */
var CSS = {
        COLLAPSED: 'cleanudem-collapsed'
    },
    SELECTORS = {
        FULLSCREEN_BUTTON: '.fullscreen-toggle-btn'
    },
    /**
     * Clean UdeM fullscreen mode class.
     * initializes this class by calling M.theme_cleanudem.init
     */
    fullscreenmode = function() {
        fullscreenmode.superclass.constructor.apply(this, arguments);
    };

fullscreenmode.prototype = {

    /*
     * Store the Anim object shared between methods, to prevent simultaneous animations.
     */
    fadeanim: null,

    /**
     * Constructor for this class
     * @param {object} config
     */
    initializer: function(config) {
        var PROPERTIES = {
            DELAY: 7500
        };
        // Attach events to the link to change fullscreenmode state so we can do it with
        // JavaScript without refreshing the page.
		if ((btn = Y.one(SELECTORS.FULLSCREEN_BUTTON)) && config.disableurl) {
            btn.on('click', this.setFullscreenModeState, this, config.disableurl);
            // If the fullscreen mode is activated, display a dialog div indicating the state.
            if (Y.one(document.body).hasClass('cleanudem-collapsed')) {
                // If the upload dialog is also present, delay the displaying of this dialog.
                if (window.location.search.indexOf('notifyeditingon=1') > -1) {
                    Y.later(PROPERTIES.DELAY, this, function(){this.show_fullscreen_div(config.disableurl);}, null, false);
                } else {
                    this.show_fullscreen_div(config.disableurl);
                }
            }
		}
    },

    /**
     * Sets the state being used for the Clean UdeM theme.
     * @param {Y.Event} e The event that fired.
     * @param {String} url The URL in the HTML message displayed when activated.
     */
    setFullscreenModeState: function(e, url) {
        // Prevent the event from refreshing the page.
        e.preventDefault();
		var body = Y.one(document.body);
        // Switch over the CSS classes on the body.
		body.toggleClass(CSS.COLLAPSED);
		var state = body.hasClass(CSS.COLLAPSED);
		var btn = Y.one(SELECTORS.FULLSCREEN_BUTTON);
		if (state){
			btn.setAttribute('title', M.str.theme_cleanudem.disablefullscreenmode);
            this.show_fullscreen_div(url);
		} else {
			btn.setAttribute('title', M.str.theme_cleanudem.enablefullscreenmode);
            this.hide_fullscreen_div();
		}

		// Dispatch the resize event for resize the page ressource container.
		// Ugly hack because of a IE8 YUI bug.
		if (Y.UA.ie === 0 || Y.UA.ie > 8) {
			Y.one('window').simulate('resize');
		}

        // Store the users selection (Uses AJAX to save to the database).
        M.util.set_user_preference('theme_cleanudem_fullscreenmode_state', state);

    },

    /**
     * Show div element to tell the user that they are currently in fullscreen mode.
     * @param {String} url The URL in the HTML message displayed.
     */
    show_fullscreen_div: function(url) {
        var CSS = {
                FULLSCREEN_STATUS: 'fullscreen-status',
                FULLSCREEN_CLASSES: 'alert alert-info'
            },
            SELECTORS = {
                FULLSCREEN_STATUS_ID: '#' + CSS.FULLSCREEN_STATUS
            },
            PROPERTIES = {
                DISPLAY_TIME: 5000,
                ANIM_DURATION: 0.5
            },

            // Get the page element, the parent node of the status box.
            coursecontents = document.getElementById('page');

        if (!coursecontents) {
            return;
        }

        // Create the div element of the status box or retrieve it.
        if (Y.one(SELECTORS.FULLSCREEN_STATUS_ID)) {
            var statusbox = Y.one(SELECTORS.FULLSCREEN_STATUS_ID);
        } else {
            var statusbox = document.createElement('div');
            statusbox.id = CSS.FULLSCREEN_STATUS;
            coursecontents.insertBefore(statusbox, coursecontents.firstChild);

            statusbox = Y.one(statusbox);
            statusbox.setStyle('top', '0px');
            statusbox.setStyle('opacity', '0');

            // Set the message inside the status box.
            statusbox.append(M.str.theme_cleanudem.fullscreenactivated);
            var disablelink = statusbox.appendChild('<a></a>');
            disablelink.setAttribute('href', url);
            disablelink.setHTML(M.str.theme_cleanudem.disablefullscreenmode);
            disablelink.on('click', this.setFullscreenModeState, this, url);
        }

        // Stop and remove previous animation if existing.
        if (this.fadeanim) {
            this.fadeanim.stop();
            this.fadeanim.detachAll();
            this.fadeanim.destroy();
        }

        // Animate the status box.
        this.fadeanim = new Y.Anim({
            node: SELECTORS.FULLSCREEN_STATUS_ID,
            to: {
                opacity: 1.0,
                top: '60px'
            },
            duration: PROPERTIES.ANIM_DURATION
        });

        this.fadeanim.once('end', function() {
            Y.later(PROPERTIES.DISPLAY_TIME, this, function(){this.hide_fullscreen_div();}, null, false);
        }, this);
        this.fadeanim.run();
    },

    /**
     * Hide the div element who tell the user that they are currently in fullscreen mode.
     */
    hide_fullscreen_div: function() {
        var CSS = {
                FULLSCREEN_STATUS: 'fullscreen-status'
            },
            SELECTORS = {
                FULLSCREEN_STATUS_ID: '#' + CSS.FULLSCREEN_STATUS
            },
            PROPERTIES = {
                ANIM_DURATION: 0.5
            };

        // Stop and remove previous animation if existing.
        if (this.fadeanim) {
            this.fadeanim.stop();
            this.fadeanim.detachAll();
            this.fadeanim.destroy();
        }

        // Hide the status box if existing.
        if (Y.one(SELECTORS.FULLSCREEN_STATUS_ID)) {
            var statusbox = Y.one(SELECTORS.FULLSCREEN_STATUS_ID);

            // Animate the status box.
            this.fadeanim = new Y.Anim({
                node: SELECTORS.FULLSCREEN_STATUS_ID,
                to: {
                    opacity: 0.0,
                    top: '0px'
                },
                duration: PROPERTIES.ANIM_DURATION
            });
            this.fadeanim.once('end', function() {
                this.detachAll();
                this.destroy();
            });
            this.fadeanim.run();
        }
    }
};

// Make the fullscreen mode a fully fledged YUI module.
Y.extend(fullscreenmode, Y.Base, fullscreenmode.prototype, {
    NAME: 'Clean UdeM theme fullscreen mode',
    ATTRS: {
        state: {
            value: 0
        }
    }
});

// Our Clean UdeM theme namespace.
M.theme_cleanudem = M.theme_cleanudem || {};
// Initialization function for the fullscreen mode.
M.theme_cleanudem.initFullscreenMode = function(cfg) {
    return new fullscreenmode(cfg);
};

}, '@VERSION@', {requires:['base', 'node', 'anim', 'node-event-simulate']});
