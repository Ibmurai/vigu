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
				if (stacktrace.length != 0) {
					for (line in stacktrace) {
						var path = stacktrace[line]['file'];
						if (path != undefined) {
							var pathName = $('<span>').addClass('pathField').text('').append($('<span>').addClass('pathName').text(stacktrace[line]['file']));
							var className = $('<span>').addClass('classField').text(' in ').append($('<span>').addClass('className').text(stacktrace[line]['class']));
							var functionName = $('<span>').addClass('functionField').text('::').append($('<span>').addClass('functionName').text(stacktrace[line]['function'] + '()'));
							var lineNumber = $('<span>').addClass('lineField').text(' on line ').append($('<span>').addClass('lineNumber').text(stacktrace[line]['line']));
							$('<p>').addClass('trace').append(pathName).append(className).append(functionName).append(lineNumber).appendTo(trace);
						}
					}
				} else {
					$('<p>').text('No stacktrace available').appendTo(trace);
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
				contextSection = $('<div>').addClass('context');
				if (context.length != 0) {
					for (key in context) {
						var varName = $('<span>').addClass('varName').text(key + ' : ').append($('<span>').addClass('varValue').text(context[key]));
						$('<p>').append(varName).appendTo(contextSection);
					}
				} else {
					$('<p>').text('No context available').appendTo(contextSection);
				}
				node.append(contextSection);
			}
		};
})(jQuery);
