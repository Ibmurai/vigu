if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu menu operations
 */
Vigu.Toolbar = (function() {
	return {
		/**
		 * Crate the toolbar
		 * 
		 * @return {object}
		 */
		create : function(severity, message, count) {
			var toolbar = jQuery('<div>').attr('role', 'toolbar')
					.addClass('ui-widget-header ui-corner-all')
					.append(Vigu.Toolbar.addFilterSelect('servers', Vigu.Toolbar.getServers()))
					.append(Vigu.Toolbar.addFilterSelect('modules', Vigu.Toolbar.getModules()))
					.append(Vigu.Toolbar.addFilterSelect('errors', Vigu.Toolbar.getGetErrors()));
			return toolbar;
		},
		/**
		 * Add filter select
		 * 
		 * @return {object}
		 */
		addFilterSelect : function(id, options) {
			var select = jQuery('<select>').attr('id', id);
			return select.append(options);
		},
		/**
		 * Get modules
		 * 
		 * @return {object}
		 */
		getModules : function() {
			return jQuery('<option>').text('Module 1');
		},
		/**
		 * Get server
		 * 
		 * @return {object}
		 */
		getServers : function() {
			return jQuery('<option>').text('Server 1');
		},
		/**
		 * Get errors
		 * 
		 * @return {object}
		 */
		getGetErrors : function() {
			return jQuery('<option>').text('Error 1');
		}
	};
})();
