if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu entry operations
 */
Vigu.Entry = (function() {
		return {
			create : function(severity, message, count) {
				return Vigu.Entry.createTr(severity, message, count);
			},
			createTr : function(severity, message, count) {
				return jQuery('<tr>').append(jQuery('<td>').text(severity)).append(jQuery('<td>').text(message)).append(jQuery('<td>').text(count));
			}
		};
})();
