if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu operations
 */
Vigu = (function($) {
		return {
			application : undefined,
			
			create : function() {
				this.application = jQuery('<div>').attr('role', 'application');
				this.application.appendTo('body');
				this.application.append(Vigu.Toolbar.create('Vigu - You did this!'));
				this.application.append(Vigu.Document.create());
				jQuery('div[role="toolbar"] select').selectmenu();
				Vigu.Grid.setup(this.application);
				Vigu.Grid.render();
			}
		};
})(jQuery);
