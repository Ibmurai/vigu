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
		     * @param {jQuery} Dom node
			 * @param {Integer} Id of the document to render
			 * 
			 * @return {Object}
			 */
			render : function(node, id) {
				$.ajax({
					url : '/api/log/details',
					dataType : 'json',
					data : {
						id : id
					},
					success : function(data) {
						console.log(data);
						$("[role=document]").remove();
						var data = data['details'];
						var document = $('<div>').attr('role', 'document').addClass('ui-widget ui-widget-content ui-corner-all');
						Vigu.Document.headerSection(document, data);
						Vigu.Document.stacktraceSection(document, data.stacktrace);
						Vigu.Document.contextSection(document, data.context);
						document.appendTo(node);
					}
				});
			},
			/**
			 * Generate the header block
			 * 
			 * @param {jQuery} node Node
			 * @param {Object} data Document data
			 * 
			 * @return undefined
			 */
			headerSection : function(node, data) {
				var title = data.level + ': ' + data.message;
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text(title).attr('title', title)).appendTo(node);
				left = $('<div>').addClass('icons').appendTo(node);
				right = $('<div>').addClass('fields').appendTo(node);
				$('<div>').addClass(data.level.toLowerCase()).addClass('errorLevel').appendTo(left);
				$('<div>').addClass('count').text(data.count).appendTo(left);
				dl = $('<dl>');
				$('<dt>').text('First').appendTo(dl);
				$('<dd>').text(data.first).attr('title', data.first).appendTo(dl); 
				$('<dt>').text('Module').appendTo(dl);
				if (data.module) {
					$('<dd>').text(data.module).attr('title', data.module).appendTo(dl);
				} else {
					$('<dd>').text('<no module>').appendTo(dl);
				}
				$('<dt>').text('File').appendTo(dl);
				$('<dd>').text(data.file).attr('title', data.file).appendTo(dl);
				$('<dt>').text('Line').appendTo(dl);
				$('<dd>').text(data.line).appendTo(dl);
				dl.appendTo(right);
			},
			/**
			 * Generate the stacktrace block
			 * 
			 * @param {jQuery} node       Node
			 * @param {Object} stacktrace Stacktrace
			 * 
			 * @return undefined
			 */
			stacktraceSection : function(node, stacktrace) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text('Stacktrace')).appendTo(node);
				trace = $('<div>').addClass('stacktrace');
				for (line in stacktrace) {
					var path = stacktrace[line]['file'];
					if (path != undefined) {
						var lineNumber = stacktrace[line]['line'];
						$('<p>').text(path + ' : ' + lineNumber).appendTo(trace);
					}
				}
				node.append(trace);
			},
			/**
			 * Generate the context block
			 * 
			 * @param {jQuery} node    Node
			 * @param {Object} context Context
			 * 
			 * @return undefined
			 */
			contextSection : function(node, context) {
				$('<div>').addClass('ui-widget-header ui-corner-all ui-helper-clearfix messageTitle').append($('<span>').text('Context')).appendTo(node);
				node.append($('<div>').addClass('context').text(context));
			}
		};
})(jQuery);
