/**
* BouncingOrange TinyMCE Extension
*
* @category   BouncingOrange
* @package    BouncingOrange_TinyMce
* @author     Pierre ROUSSET (p.rousset@gmail.com)
* @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
* @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
*/

(function() {
	tinymce.create('tinymce.plugins.Magento', {
	
		getInfo : function() {
			return {
				longname :  'Magento Plugin',
				author :    'Pierre ROUSSET for BouncingOrange',
				authorurl : 'http://www.bouncingorange.com',
				infourl :   'http://www.magentocommerce.com/extension/1426/bouncingorange-tinymce-wysiwyg-',
				version :   '0.2'
			};
		},
		
		init : function(ed, url) {
			var t = this;
			
			// Before set the content
			ed.onBeforeSetContent.add(function(ed, o) {
				// Fix quote before Set the content
				if (ed.getParam('magento_fix_quote', true)) o.content = t._fixQuote(o.content);
			});

			// Rewrite the fonction convertURL to be able to keep this fonctionnality but also do the fix quote
			var tinyConvertURL = ed.convertURL.bind(ed);
			ed.convertURL = function(u, n, e) {
				if (ed.getParam('magento_fix_quote', true)) u = t._fixQuote(u);				
				return tinyConvertURL(u, n, e);
			};
			
			// Before save the content
			ed.onSaveContent.add(function(ed, o) {
				if (ed.getParam('magento_fix_nl2br', true))	
					o.content = o.content.replace(/\n/g, '');
			});
		},

		// Private methods
		_fixQuote : function(c) {
			var p1 = new RegExp('{{(.*?)}}', 'g');
			var p2 = new RegExp('"', 'g');
			
			return c.replace(p1, function(s){ return s.replace(p2, "'"); });
		}
	});

	// Register plugin
	tinymce.PluginManager.add('magento', tinymce.plugins.Magento);
})();
