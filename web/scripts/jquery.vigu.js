(function($) {
	$.fn.vigu = function() {
		var scope = function($this) {
			var f = {
				/**
				 * The "constructor"
				 */
				init : function() {
					console.log('init vigu');
					f.setup();
				},

				/**
				 * Sample function - Sets the background color of $this.
				 * 
				 * @param {String}
				 *            color The color to set.
				 */
				setup : function(color) {
					console.log('setup vigu');
					Vigu.create();
				},

				events : {
				}
			};
			f.init();
		};

		return this.each(function() {
			scope($(this));
		});
	};
}(jQuery));