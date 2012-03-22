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
					.append(this.addSearch());
			toolbar.appendTo(node);
			this.addFilterSelect(toolbar, 'hosts');
			//this.addFilterSelect(toolbar, 'modules');
			this.addFilterSelect(toolbar, 'levels');
		},
		/**
		 * Render the toolbar
		 * 
		 * @return undefined
		 */
		render : function () {
			//$('div[role="toolbar"] select').selectmenu();
		},
		/**
		 * Add filter select
		 * 
		 * @param {jQuery} Node
		 * @param {string} Id of the select to create
		 * 
		 * @return {object}
		 */
		addFilterSelect : function(node, id) {
			switch(id) {
			case 'hosts' :
				select = this.addHosts(node);
				break;
			case 'modules' :
				select = this.addModules(node);
				break;
			case 'levels' :
				select = this.addlevels(node);
				break;
			}
			return select;
		},
		/**
		 * Get modules
		 * 
		 * @param {jQuery} node Node
		 * 
		 * @return {object}
		 */
		addModules : function(node) {
			var select = $('<select>').attr('name', 'module');
			select.append($('<option>').text('All modules'));
			select.append($('<option>').text('Module 1'));
			select.append($('<option>').text('Module 2'));
			select.appendTo(node);
			select.selectmenu();
		},
		/**
		 * Get server
		 * 
		 * @param {jQuery} node Node
		 * 
		 * @return {undefiend}
		 */
		addHosts : function(node) {
			$.ajax({
				url : '/api/log/gethosts',
				dataType : 'json',
				success : function(data) {
					var select = $('<select>').attr('name', 'host');
					select.append($('<option>').attr('value', 'all').text('Any host'));
					for (host in data['hosts']) {
							if (data['hosts'][host] == '') {
								select.append($('<option>').attr('value', data['hosts'][host]).text('No host'));
							} else {
								select.append($('<option>').attr('value', data['hosts'][host]).text(data['hosts'][host]));
							}
					}
					select.appendTo(node);
					select.selectmenu();
				}
			});
		},
		/**
		 * Get error levels
		 * 
		 * @param {jQuery} node Node
		 * 
		 * @return {undefiend}
		 */
		addlevels : function(node) {
			$.ajax({
				url : '/api/log/getlevels',
				dataType : 'json',
				success : function(data) {
					var select = $('<select>').attr('name', 'errorlevel');
					select.append($('<option>').attr('value', 'all').text('Any error'));
					for (level in data['levels']) {
						select.append($('<option>').attr('value', data['levels'][level]).text(data['levels'][level]));
					}
					select.appendTo(node);
					select.selectmenu();
				}
			});
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
