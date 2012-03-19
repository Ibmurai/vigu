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
		create : function(title) {
			var toolbar = jQuery('<div>').attr('role', 'toolbar')
					.addClass('ui-widget-header ui-corner-all')
					.append(jQuery('<h1>').text(title))
					.append(Vigu.Toolbar.addFilterSelect('sites'))
					.append(Vigu.Toolbar.addFilterSelect('modules'))
					.append(Vigu.Toolbar.addFilterSelect('errors'))
					.append(Vigu.Toolbar.addSearch());
			return toolbar;
		},
		/**
		 * Add filter select
		 * 
		 * @return {object}
		 */
		addFilterSelect : function(id, options) {
			var select = jQuery('<select>').attr('id', id);
			switch(id) {
			case 'sites' :
				select = Vigu.Toolbar.addSites(select);
				break;
			case 'modules' :
				select = Vigu.Toolbar.addModules(select);
				break;
			case 'errors' :
				select = Vigu.Toolbar.addErrors(select);
				break;
			}
			return select;
		},
		/**
		 * Get modules
		 * 
		 * @return {object}
		 */
		addModules : function(select) {
			///api/modules/get
			select.append(jQuery('<option>').text('All modules'));
			select.append(jQuery('<option>').text('Module 1'));
			select.append(jQuery('<option>').text('Module 2'));
			return select;
		},
		/**
		 * Get server
		 * 
		 * @return {object}
		 */
		addSites : function(select) {
			///api/sites/get
			select.append(jQuery('<option>').text('All sites'));
			select.append(jQuery('<option>').text('Site 1'));
			select.append(jQuery('<option>').text('Site 2'));
			return select;
		},
		/**
		 * Get errors
		 * 
		 * @return {object}
		 */
		addErrors : function(select) {
			select.append(jQuery('<option>').text('All errors'));
			select.append(jQuery('<option>').text('Fatal'));
			select.append(jQuery('<option>').text('Error'));
			select.append(jQuery('<option>').text('Warning'));
			select.append(jQuery('<option>').text('Notice'));
			select.append(jQuery('<option>').text('Deprecated'));
			return select;
		},
		/**
		 * Get errors
		 * 
		 * @return {object}
		 */
		addSearch : function() {
			return jQuery('<input type="text">').addClass('ui-corner-all');
		}
	};
})();
