if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu entry operations
 */
Vigu.Grid = (function($) {
	return {
		/**
		 * Paramters used in query string
		 * 
		 * @type {String}
		 */
		parameters : {
			/**
			 * Module to limit search by
			 * @type {String}
			 */
			module : '',
			/**
			 * Site to limit search by
			 * @type {String}
			 */
			site : '',
			/**
			 * Error level to limit search by
			 * @type {String}
			 */
			level : '',
			/**
			 * File path to limit search by
			 * @type {String}
			 */
			path : '',
			/**
			 * Error message to limit search by
			 * @type {String}
			 */
			search : ''
		},
		/**
		 * Setup the tags for the grid
		 * 
		 * @param {jQuery} Dom node
		 * 
		 * @return undefined
		 */
		setup : function(node) {
			$('<table>').attr('role','grid').attr('id', 'grid').appendTo(node);
			$('<div>').attr('id', 'pager').appendTo(node);
		},
		/**
		 * Render the grid
		 * 
		 * @return undefined
		 */
		render : function() {
			var gridHeight = $(window).height() - 130;
			$("[role='grid']").jqGrid(
					{
						url : '/api/log/grid' + Vigu.Grid.queryString(),
						datatype : "json",
						colNames : [ 'Level', 'Message', 'Ago', 'Count'],
						colModel : [ 
						             {name : 'level', index : 'level', width : 80, align: 'center', fixed : true }, 
						             {name : 'message', index : 'message' }, 
						             {name : 'last', index : 'last', width : 140 , align: 'center', fixed : true.constructor, formatter : Vigu.Grid.agoFormatter }, 
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
						   Vigu.Document.render(Vigu.rightColumn, id);
						},
						gridComplete: function() {
							var firstIdOnPage = $("[role='grid']").getDataIDs()[0];
							Vigu.Document.render(Vigu.rightColumn, firstIdOnPage);
						},
					});
			
			$(window).bind('resize', function() {
				$("#grid").setGridWidth(($("[role='application']").width() - 2) / 2, true);
			}).trigger('resize');

		},
		/**
		 * Formats the date
		 * 
		 * @param {String} cellvalue The value to be formatted
		 * @param {Object} options   Containing the row id adn column id
		 * @param {Object} rowObject Is a row data represented in the format determined from datatype option
		 * 
		 * @return {String}
		 * @see http://www.trirand.com/jqgridwiki/doku.php?id=wiki:custom_formatter
		 */
		agoFormatter : function(cellvalue, options, rowObject) {
			var date = new Date((cellvalue || "").replace(/-/g,"/").replace(/[TZ]/g," ")),
			diff = (((new Date()).getTime() - date.getTime()) / 1000),
			day_diff = Math.floor(diff / 86400);
					
			if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
				return;
					
			return day_diff == 0 && (
					diff < 60 && "just now" ||
					diff < 120 && "1 minute ago" ||
					diff < 3600 && Math.floor( diff / 60 ) + " minutes ago" ||
					diff < 7200 && "1 hour ago" ||
					diff < 86400 && Math.floor( diff / 3600 ) + " hours ago") ||
				day_diff == 1 && "Yesterday" ||
				day_diff < 7 && day_diff + " days ago" ||
				day_diff < 31 && Math.ceil( day_diff / 7 ) + " weeks ago";
		},
		/**
		 * Construct the query string
		 * 
		 * @return {String}
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
