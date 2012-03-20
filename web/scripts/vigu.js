if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu operations
 */
Vigu = (function($) {
		return {
			create : function() {
				var base = jQuery('<div>').attr('role', 'application');
				base.appendTo('body');
				jQuery('div[role="application"]').append(Vigu.Toolbar.create('Vigu - You did this!'));
				jQuery('div[role="application"]').append(Vigu.list());
				jQuery('div[role="application"]').append(Vigu.pager());
				jQuery('div[role="application"]').append(Vigu.Document.create());
				jQuery('div[role="toolbar"] select').selectmenu();
				Vigu.Grid.setup();
			},
			list : function() {
				var i, list, messages;
				var list = jQuery('<table>').attr('role','list').attr('id', 'grid');
				return list;
			},
			pager : function() {
				return jQuery('<div>').attr('id', 'pager');
			}
		};
})(jQuery);
