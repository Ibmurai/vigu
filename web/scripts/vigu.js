if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu operations
 */
Vigu = (function($) {
		return {
			application : undefined,
			leftColumn : undefined,
			rightColumn : undefined,
			
			create : function() {
				this.application = $('<div>').attr('role', 'application');
				this.application.appendTo('body');
				this.application.append(Vigu.Toolbar.create('Vigu - You did this!'));
				this.leftColumn = $('<div>').attr('role', 'region');
				this.rightColumn = $('<div>').attr('role', 'region');
				this.application.append(this.leftColumn);
				this.application.append(this.rightColumn);
				this.rightColumn.append(Vigu.Document.create());
				jQuery('div[role="toolbar"] select').selectmenu();
				Vigu.Grid.setup(this.leftColumn);
				Vigu.Grid.render();
			}
		};
})(jQuery);
