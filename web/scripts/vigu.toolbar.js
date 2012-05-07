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
		},
		/**
		 * Render the toolbar
		 * 
		 * @return undefined
		 */
		render : function () {
		},
		/**
		 * Get search field
		 * 
		 * @param {jQuery} node Node
		 * 
		 * @return {undefiend}
		 */
		addSearch : function(node) {
			$('<label>')
				.append($('<span>')
						.addClass('ui-icon ui-icon-circle-close')
						.attr('Title', 'Reset search')
						.click(function(){
							Vigu.Grid.parameters.path = '';
							$('input[name="search"]').val('');
							Vigu.Grid.reload();
							$('div[role=toolbar]>label>span').hide();
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
					.click(function(){
						 this.select();
					})
					.focus(function(){
						 this.select();
					})
					.keydown(function() {
						if ($('input[name="search"]').val() != '') {
							$('div[role=toolbar]>label>span').show();
						} else {
							$('div[role=toolbar]>label>span').hide();
						}
					}))
				.appendTo(node);
			$('<button>')
				.text('Reload')
				.click(function(){
					Vigu.Grid.reload();
				})
				.appendTo(node)

				.button();
			$('<button>')
				.text('Auto Reload')
				.click(function() {
					if (!Vigu.Grid.autorefresh) {
						$(this).find('span').addClass('reloadOn');
						$('[role=toolbar] button:nth-child(4)').button("disable");
						$('[role=toolbar] button:nth-child(3)').button("disable");
					} else {
						$(this).find('span').removeClass('reloadOn');
						$('[role=toolbar] button:nth-child(4)').button("enable");
						$('[role=toolbar] button:nth-child(3)').button("enable");
					}
					Vigu.Grid.autoRefresh();
				}) 
				.appendTo(node).button();
		}
	};
})(jQuery);
