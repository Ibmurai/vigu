if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu entry operations
 */
Vigu.Grid = (function($) {
	return {
		parameters : {
			/**
			 * Module to limit search by
			 * @type String
			 */
			module : 'fsArticle		',
			/**
			 * Site to limit search by
			 * @type String
			 */
			site : '',
			/**
			 * Error level to limit search by
			 * @type String
			 */
			level : '',
			/**
			 * File path to limit search by
			 * @type String
			 */
			path : '',
			/**
			 * Error message to limit search by
			 * @type String
			 */
			search : ''
		},
		setup : function(node) {
			jQuery('<table>').attr('role','grid').attr('id', 'grid').appendTo(node);
			jQuery('<div>').attr('id', 'pager').appendTo(node);
		},
		/**
		 * Setup the grid
		 * 
		 * @return undefined
		 */
		render : function() {
			var gridHeight = $(window).height() - 125;
			$("[role='grid']").jqGrid(
					{
						url : '/api/log/grid' + Vigu.Grid.queryString(),
						datatype : "json",
						colNames : [ 'Level', 'Message', 'Count'],
						colModel : [ {name : 'level', index : 'level', width : 55 }, 
						             {name : 'message', index : 'message', width : 90 }, 
						             {name : 'count', index : 'count', width : 100 }
						           ],
						loadtext: 'Loading...',
						rowNum : 100,
						rowList : [ 100, 200, 300 ],
						pager : '#pager',
						sortname : 'level',
						viewrecords : true,
						sortorder : "desc",
						autowidth: true,
						viewrecords: true, 
						gridview: true,
						hidegrid: false,
						height: gridHeight,
						caption : "Errors"
					});
			
			$(window).bind('resize', function() {
				$("#grid").setGridWidth(($("[role='application']").width() - 2) / 2, true);
			}).trigger('resize');

		},
		/**
		 * Construct the query string
		 * 
		 * @return String
		 */
		queryString : function() {
			params = [];
			$.each(Vigu.Grid.parameters, function(key, value) {
				if (value) {
					params.push(key + '=' + value);
				}
			});
			
			return (params.length > 0 ? '?' : '') + params.join('&');
		}
	};
})(jQuery);
