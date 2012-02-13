YUI.add('moodle-backup-proceedbutton', function(Y) {

// Namespace for the backup
M.core_backup = M.core_backup || {};
/**
 * Adds confirmation dialogues to the proceed buttons on the import page.
 * 
 * @param {object} config
 */
M.core_backup.watch_proceed_buttons = function(config) {
    Y.all('.proceedbutton').each(function(){
		// Prevent the any previously added event (submit) from firing.
		if (this._confirmationListener) {
			 this._confirmationListener.detach();
		}
        this._confirmationListener = this.on('click', function(e){
			
			// Prevent the default event (submit) from firing
            e.preventDefault();
            // Create the confirm box
            var confirm = new M.core.confirm(config);
            // If the user clicks yes
            confirm.on('complete-yes', function(e){
                // Detach the listener for the confirm box so it doesn't fire again.
                this._confirmationListener.detach();
				
				// Create the loading infobox
            	var objBody = Y.one(document.body);
           		var infobox = Y.Node.create('<div class="infobox" >'
					+ '<h2>'+config.inprogress+'</h2>'
                	+ '</div>');
				
				// Create the loading image
				var image = Y.Node.create('<img>');
				image.set('src', M.cfg.wwwroot +'/pix/i/progressbar.gif');
				image.on('load', function(e, obj) {
					// Simulate the click on image load (image need to be loaded)
					obj.simulate('click');
					// Disable the submit button
					obj.set('disabled', true);
				},this, this);
				
				// Clone the overlay for re-use it after destroy of the object
				var container = Y.Node.create('<div class="moodle-dialogue-base" />');
				var overlay = Y.one('.moodle-dialogue-lightbox');
				overlay = overlay.cloneNode(true);
				
				// Display the overlay contents
				infobox.append(image);
				container.append(infobox);
				container.append(overlay);
				objBody.append(container);
				
				// Center the infobox
				infobox.setStyle("position", 'fixed');
				infobox.setStyle("top", '50%');
				infobox.setStyle("left", '50%');
				infobox.setStyle("marginLeft", '-' + (parseInt(infobox.getComputedStyle('width').slice(0,-2))/2) + 'px');
				infobox.setStyle("marginTop", '-' + (parseInt(infobox.getComputedStyle('height').slice(0,-2))/2) + 'px');
				infobox.setStyle("zIndex", '10000');
				
				// Reshow the overlay
				overlay.setStyle('height','100%');
				overlay.setStyle("position", 'fixed');
				overlay.removeClass('hidden');

            }, this);
            // Show the confirm box
            confirm.show();
        }, this);
		
    });
}

}, '@VERSION@', {'requires':['base','node','node-event-simulate','overlay','moodle-enrol-notification']});