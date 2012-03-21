if (typeof Vigu === 'undefined') {
	var Vigu = {};
}
/**
 * Base object for all Vigu menu operations
 */
Vigu.Document = (function($) {
		return {
			/**
			 * Create the error document
			 * 
			 * @return {Object}
			 */
			render : function(id) {
				$("[role=document]").remove();
				console.log('new');
				//var data = Vigu.Document.getData();
				var data = Vigu.Document.dummyData();
				var document = $('<div>').attr('role', 'document').addClass('ui-widget ui-widget-content ui-corner-all');
				Vigu.Document.headerSection2(document, data.level, data.module, data.date, data.count, data.line, data.message, data.file);
				Vigu.Document.stacktraceSection(document, data.stacktrace);
				Vigu.Document.contextSection(document, data.context);
				return document;
			},
			/**
			 * Generate the header block
			 * 
			 * @return undefined
			 */
			headerSection2 : function(node, level, module, date, count, line, message, file) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text(level + ': ' + message)).appendTo(node);
				left = $('<div>').addClass('icons').appendTo(node);
				right = $('<div>').addClass('fields').appendTo(node);
				$('<div>').addClass(level).addClass('errorLevel').appendTo(left);
				$('<div>').addClass('count').text(count).appendTo(left);
				dl = $('<dl>');
				$('<dt>').text('Date').appendTo(dl);
				$('<dd>').text(date).appendTo(dl);
				$('<dt>').text('Module').appendTo(dl);
				$('<dd>').text(module).appendTo(dl);
				$('<dt>').text('File').appendTo(dl);
				$('<dd>').text(file).appendTo(dl);
				$('<dt>').text('Line').appendTo(dl);
				$('<dd>').text(line).appendTo(dl);
				dl.appendTo(right);
			},
			/**
			 * Generate the header block
			 * 
			 * @return undefined
			 */
			headerSection : function(node, level, module, date, count, line, message, file) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text(message)).appendTo(node);
				var header = $('<div>').attr('role', 'heading');
				var table = $('<table>').addClass('').appendTo(header);
				var thead = $('<thead>').appendTo(table);
				var tr1 = $('<tr>').appendTo(thead);
				$('<th>').attr('colspan', 2).append($('<div>').addClass(level).addClass('errorLevel')).appendTo(tr1);
				this.addHeaderField(tr1, 'Module', module);
				this.addHeaderField(tr1, 'Date', date);
				var tr2 = $('<tr>').appendTo(thead);
				$('<th>').attr('colspan', 2).append($('<div>').addClass('count').text(count)).appendTo(tr2);
				this.addHeaderField(tr2, 'Line', line);
				this.addHeaderField(tr2, 'File', file);
				header.appendTo(node);
			},
			
			/**
			 * Generate the stacktrace block
			 * 
			 * @return undefined
			 */
			stacktraceSection : function(node, stacktrace) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text('Stacktrace')).appendTo(node);
				var content = $('<div>').addClass('stacktrace').text(stacktrace);
				content.appendTo(node);
			},
			/**
			 * Generate the context block
			 * 
			 * @return undefined
			 */
			contextSection : function(node, context) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text('Context')).appendTo(node);
				var content = $('<div>').addClass('context').text(context);
				content.appendTo(node);
			},
			addHeaderField : function(node, key, value) {
				$('<th>').addClass('').text(key).appendTo(node);
				$('<td>').addClass('').text(value).appendTo(node);
			},
			/**
			 * Dummy data while we wait
			 * 
			 * @return {object}
			 */
			dummyData : function() {
				return {
					'level'      : 'Notice', 
					'module'     : 'xphoto', 
					'message'    : 'Unterminated string', 
					'date'       : '2012-03-21 07:20', 
					'line'       : 4, 
					'file'       : '/admin/class/XphotoAdminController/Folder.php', 
					'count'      : 123456,
					'context'    : 'context',
					'stacktrace' : 'Stacktrace'
					};
			}
		};
})(jQuery);
