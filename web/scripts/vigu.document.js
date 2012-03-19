if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu menu operations
 */
Vigu.Document = (function() {
		return {
			create : function(severity, message, count) {
				return jQuery('<div>').attr('role', 'document').text('Display');
			},
		};
})();
