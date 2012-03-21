if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu menu operations
 */
Vigu.Document = (function($) {
		return {
			create : function(severity, message, count) {
				var data = Vigu.Document.dummyData();
				var document = $('<div>').attr('role', 'document').addClass('ui-widget ui-widget-content ui-corner-all');
				
				Vigu.Document.header(document, data.server, data.level, data.module, data.date, data.count, data.line, data.message);
				//$('<hr>').appendTo(document);
				Vigu.Document.stacktrace(document, data.stacktrace);
				Vigu.Document.context(document, data.context);
				return document;
			},
			display : function(id) {
				$("[role=document]").text(id);
			},
			header : function(node, server, level, module, date, count, line, message) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text(message)).appendTo(node);
				var header = $('<div>').attr('role', 'heading');
				var table = $('<table>').addClass('').appendTo(header);
				var thead = $('<thead>').appendTo(table);
				var tr1 = $('<tr>').appendTo(thead);
				this.addSpecialHeaderField(tr1, level);
				this.addHeaderField(tr1, 'Host', server);
				this.addHeaderField(tr1, 'Module', module);
				this.addHeaderField(tr1, 'Date', date);
				var tr2 = $('<tr>').appendTo(thead);
				this.addHeaderField(tr2, 'Line', line);
				this.addHeaderField(tr2, 'Count', count);
				header.appendTo(node);
			},
			stacktrace : function(node, stacktrace) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text('Stacktrace')).appendTo(node);
				var content = $('<div>').addClass('stacktrace').text(stacktrace);
				content.appendTo(node);
			},
			context : function(node, context) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text('Context')).appendTo(node);
				var content = $('<div>').addClass('context').text(context);
				content.appendTo(node);
			},
			addHeaderField : function(node, key, value) {
				$('<th>').addClass('').text(key).appendTo(node);
				$('<td>').addClass('').text(value).appendTo(node);
			},
			addSpecialHeaderField : function(node, level) {
				$('<th>').attr('colspan', 2).append($('<div>').addClass(level)).appendTo(node);
			},
			dummyData : function() {
				return {
					'server'     : 'fyens.dk', 
					'level'      : 'Warning', 
					'module'     : 'xphoto', 
					'message'    : 'Unterminated string', 
					'date'       : '2012-03-21 07:20', 
					'line'       : 4, 
					'count'      : 78,
					'context'    : 'context',
					'stacktrace' : 'Stacktrace'
					};
			}
		};
})(jQuery);
