if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu menu operations
 */
Vigu.Toolbar = (function($) {
	return {
		/**
		 * Create the toolbar
		 * 
		 * @param {jQuery} Dom node
		 * @param {String} Title in the menu
		 * 
		 * @return undefined
		 */
		setup : function(node , title) {
			var toolbar = $('<div>').attr('role', 'toolbar')
					.addClass('ui-widget-header ui-corner-all')
					.append($('<h1>').text(title))
					.append(this.addFilterSelect('hosts'))
					.append(this.addFilterSelect('modules'))
					.append(this.addFilterSelect('errors'))
					.append(this.addSearch());
			toolbar.appendTo(node);
		},
		/**
		 * Render the toolbar
		 * 
		 * @return undefined
		 */
		render : function () {
			$('div[role="toolbar"] select').selectmenu();
		},
		/**
		 * Add filter select
		 * 
		 * @param {string} Id of the select to create
		 * 
		 * @return {object}
		 */
		addFilterSelect : function(id) {
			switch(id) {
			case 'hosts' :
				select = this.addHosts();
				break;
			case 'modules' :
				select = this.addModules();
				break;
			case 'errors' :
				select = this.addErrors();
				break;
			}
			return select;
		},
		/**
		 * Get modules
		 * 
		 * @return {object}
		 */
		addModules : function() {
			///api/modules/get
			var select = $('<select>').attr('name', 'module');
			select.append($('<option>').text('All modules'));
			select.append($('<option>').text('Module 1'));
			select.append($('<option>').text('Module 2'));
			return select;
		},
		/**
		 * Get server
		 * 
		 * @return {object}
		 */
		addHosts : function() {
			///api/sites/get
			var select = $('<select>').attr('name', 'host');
			select.append($('<option>').text('All hosts'));
			select.append($('<option>').text('Host 1'));
			select.append($('<option>').text('Host 2'));
			return select;
		},
		/**
		 * Get errors
		 * 
		 * @return {object}
		 */
		addErrors : function() {
			var select = $('<select>').attr('name', 'errorlevel');
			select.append($('<option>').text('All errors'));
			select.append($('<option>').text('Error'));
			select.append($('<option>').text('Warning'));
			select.append($('<option>').text('Notice'));
			select.append($('<option>').text('Deprecated'));
			return select;
		},
		/**
		 * Get errors
		 * 
		 * @return {object}
		 */
		addSearch : function() {
			return $('<input type="text">').addClass('ui-corner-all');
		}
	};
})(jQuery);
