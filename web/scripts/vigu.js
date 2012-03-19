if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu operations
 */
Vigu = (function() {
		return {
			create : function() {
				var base = jQuery('<div>').attr('role', 'application');
				base.appendTo('body');
				jQuery('div[role="application"]').append(Vigu.Toolbar.create('Vigu - You did this!'));
				jQuery('div[role="application"]').append(Vigu.list());
				jQuery('div[role="application"]').append(Vigu.Document.create());
				jQuery('div[role="toolbar"] select').selectmenu();
			},
			dummyMessages : function() {
				return ['This is a message 1', 'This is a message 2', 'This is a message 3', 'This is a message 4', 'This is a message 5'];
			},
			list : function() {
				var i, list, messages;
				var list = jQuery('<table>').attr('role','navigation');
				messages = Vigu.dummyMessages();
				for (i = 0; i < messages.length; i++) {
					
					list.append(Vigu.Entry.create('Fatal', messages[i], Math.floor(Math.random() * 6) + 1));
				}
				return list;
			}
		};
})();
