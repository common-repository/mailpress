if(!String.sprintf){String.sprintf=function(b){var a=Array.prototype.slice.call(arguments,1);return b.replace(/{(\d+)}/g,function(c,d){return typeof a[d]!="undefined"?a[d]:c})}};

var mp_fileupload = {

	nbfiles : -1,
	class_icon : false,
/*
	changed : function(id, file) {
		return true;
	},
*/
	parsexml : function(xml){
		xml = xml.replace(/\&gt;/g,'>');
		xml = xml.replace(/\&lt;/g,'<');
		xml = xml.replace(/><!--\[CDATA\[/g,'><![CDATA[');
		xml = xml.replace(/\]\]--></g,']]><');
		if( window.ActiveXObject && window.GetObject ) {
			var dom = new ActiveXObject( 'Microsoft.XMLDOM' );
			dom.loadXML( xml );
			return dom;
		}
		if( window.DOMParser )
			return new DOMParser().parseFromString( xml, 'text/xml' );
		throw new Error( 'No XML parser available' );
	},

	loaded : function(id, filename, xml, oldid) {
		jQuery('span.mp_fileupload_txt').html();
		jQuery('iframe#mp_fileupload_iframe_' + oldid).remove();
		xml = mp_fileupload.parsexml(xml);
		var upload = jQuery(xml).find('mp_fileupload').each(function() {
			var error = jQuery(this).find('error').text();
			var id    = jQuery(this).find('id').text();
			var url   = jQuery(this).find('url').text();
			var file  = jQuery(this).find('file').text();
			if (error)
				jQuery('#attachment-item-u-' + oldid).html(error);
			else
			{
				jQuery('#attachment-item-u-' + oldid).replaceWith(mp_fileupload.html(filename, id, url, true ));
				mp_fileupload.class_icon = false;
			}
		});
		jQuery('iframe#mp_fileupload_iframe_' + oldid).remove();
		mp_fileupload.add();
	var toto = 0;
	},

	html : function (name, id, url, ok) {

		var maybe = (ok) ? '<input type="checkbox" class="" disabled=disabled />' : '';

		if (name && !mp_fileupload.class_icon)
		{
			// ajax
			var f_data = { action: "mp_ajax", mp_action: "html_mail_icon", fname: name };

			jQuery.ajax({
				data: f_data,
				type: "POST",
				url: htmuploadL10n.iframeurl,
				async: false,
				success: function( r ) { mp_fileupload.class_icon = r; if ( !mp_fileupload.class_icon ) mp_fileupload.class_icon = 'mp_ext mp_ext_unknown'; }
			});
		}


		var html = '';

		html += String.sprintf( '<div id="attachment-item-u-{0}" class="attachment-item child-of-{1}">', id, draft_id );
		html += '<table><tr><td>';

		html += ( url ) ?  String.sprintf( '<input type="checkbox" name="Files[{0}]" value="{0}" class="mp_fileupload_cb" checked="checked" />', id )
				:  maybe;

		html += '</td><td>';

		html += ( url ) ? String.sprintf( '<div class="{0}"></div><div><a href="{2}" class="attachme" title="{1}">{1}</a></div>', mp_fileupload.class_icon, name, url )
				: String.sprintf( '<div class="{0}"></div><div>{1}</div>', 'mp_ext_uploading', name );

		html += '</td></tr></table></div>';
		html += String.sprintf( '<div id="mp_htmlupload_input_file_{0}" class="hidden"></div>', id );

		return html;
	},

	submitted : function(id, file) {
		jQuery('span.mp_fileupload_txt').html(htmuploadL10n.uploading);
		jQuery('#attachment-items').append(mp_fileupload.html(file, id, false, true));
	},

	iframe_loaded : function(id) {
		var i = document.getElementById('mp_fileupload_iframe_' + id);
		i.onload = null;
		var count = jQuery('input.mp_fileupload_cb').size();
		jQuery('span.mp_fileupload_txt').html((count == 0)  ? htmuploadL10n.attachfirst : htmuploadL10n.attachseveral);
	},

	add    : function() {
		mp_fileupload.nbfiles++;

		var i = document.createElement('iframe');
		i.setAttribute('class', 'mp_fileupload_iframe');
		i.setAttribute('id', 'mp_fileupload_iframe_' + mp_fileupload.nbfiles);
		i.setAttribute('name', 'mp_fileupload_iframe_' + mp_fileupload.nbfiles);
		i.setAttribute('style', 'height:24px;width:132px;');
		i.setAttribute('onload', 'mp_fileupload.iframe_loaded('+ mp_fileupload.nbfiles + ')');
		iframeurl = htmuploadL10n.iframeurl + '?action=mp_ajax&mp_action=upload_iframe_html&draft_id=' + draft_id + '&id=' + mp_fileupload.nbfiles;
		i.setAttribute('src', iframeurl);
		i.style.height = '24px';
		i.style.width = '132px';
		i.style.overflow = 'hidden';
		var d = document.getElementById('mp_fileupload_file_div');
		d.appendChild(i);
	},

	init : function () {
		if (draft_id != 0) 	mp_fileupload.add();
		else 				jQuery('#attachmentsdiv').hide();
	}
};