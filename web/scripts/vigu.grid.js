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
			module : '',
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
		/**
		 * Setup the tags for the grid
		 */
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
			var gridHeight = $(window).height() - 130;
			$("[role='grid']").jqGrid(
					{
						url : '/api/log/grid' + Vigu.Grid.queryString(),
						datatype : "json",
						colNames : [ 'Level', 'Message', 'Date', 'Count'],
						colModel : [ 
						             {name : 'level', index : 'level', width : 80, align: 'center', fixed : true }, 
						             {name : 'message', index : 'message' }, 
						             {name : 'timestamp', index : 'timestamp', width : 140 , fixed : true }, 
						             {name : 'count', index : 'count', width : 50, align: 'center', fixed : true }
						           ],
						loadtext: 'Loading...',
						rowNum : 100,
						rowList : [ 100, 200, 300 ],
						pager : '#pager',
						sortname : 'timestamp',
						viewrecords : true,
						sortorder : "desc",
						autowidth: true,
						viewrecords: true, 
						gridview: true,
						hidegrid: false,
						height: gridHeight,
						caption : "Errors",
					    onSelectRow: function(id) {
						   Vigu.rightColumn.append(Vigu.Document.render(id));
						},
						gridComplete: function() {
							var firstIdOnPage = $("[role='grid']").getDataIDs()[0];
							Vigu.rightColumn.append(Vigu.Document.render(firstIdOnPage));
						},
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
