/**
 * This file is part of the Vigu PHP error aggregation system.
 * @link https://github.com/Ibmurai/vigu
 *
 * @copyright Copyright 2012 Jens Riisom Schultz, Johannes Skov Frandsen
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */
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
					.append($('<h1>').text(title));
			toolbar.appendTo(node);
			this.addSearch(toolbar);
			this.getErrorLevels(toolbar);
		},
		/**
		 * Render the toolbar
		 *
		 * @return undefined
		 */
		render : function () {
		},
		/**
		 * Get errorlevels
		 *
		 * @param {jQuery} node Node
		 *
		 * @return {undefined}
		 */
		getErrorLevels : function(node) {
			$.ajax({
				url : '/api/log/error_levels',
				dataType : 'json',
				success : function(data) {
					if (data['error'] == undefined) {
						Vigu.Toolbar.addErrorFilter(node, data['levels'])
					} else {
						Vigu.notify(data['error']);
					}
				},
				error : function() {
					Vigu.notify('Could not retrive error levels');
				}
			});
		},
		/**
		 * Add filter selects
		 *
		 * @param {jQuery} node   Node
		 * @param {array}  levels Levels
		 *
		 * @return {undefined}
		 */
		addErrorFilter : function(node, levels) {
			var label = $('<label class="error-levels">Error level:</label>');
			var select = $('<select>').attr('name', 'errorLevel').change(function() {
				Vigu.Grid.parameters.level = $('select[name="errorLevel"]').val();
				Vigu.Grid.reload();
			}).appendTo(label);
			$('<option>').attr('value', '').text('All').appendTo(select);
			for (level in levels) {
				var text = '' + levels[level].charAt(0).toUpperCase() + levels[level].toLowerCase().slice(1);
				text = text.replace(' ', '&nbsp;');
				$('<option>').attr('value', levels[level]).text(text).appendTo(select);
			}
			label.appendTo(node);
			select.selectmenu({format : Vigu.Grid.levelFormatter});
		},
		/**
		 * Add search field
		 *
		 * @param {jQuery} node Node
		 *
		 * @return {undefined}
		 */
		addSearch : function(node) {
			$('<div class="search">')
			.append($('<span>')
				.addClass('ui-icon ui-icon-circle-close')
				.attr('Title', 'Reset search')
				.click(function(){
					Vigu.Grid.parameters.path = '';
					$('input[name="search"]').val('');
					Vigu.Grid.reload();
					Vigu.Toolbar.updateSearchReset();
				})
				.hide())
			.append($('<input type="text">')
				.attr('name', 'search')
				.addClass('ui-corner-all')
				.keypress(function(event) {
					if (event.which == 13) {
						event.preventDefault();
						Vigu.Grid.parameters.path = $('input[name="search"]').val();
						Vigu.Grid.reload();
					}
				})
				.keyup(Vigu.Toolbar.updateSearchReset)
				.change(Vigu.Toolbar.updateSearchReset))
			.appendTo(node);
		},
		/**
		 * Show or hide the search field reset button depending on content.
		 *
		 * @return {undefined}
		 */
		updateSearchReset : function() {
			if ($('input[name="search"]').val() != '') {
				$('div[role=toolbar]>div>span').show();
			} else {
				$('div[role=toolbar]>div>span').hide();
			}
		}
	};
})(jQuery);
