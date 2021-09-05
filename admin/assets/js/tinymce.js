$( document ).ready(function() {
	Tinymce();
});

function Tinymce() {
	tinymce.init({
		mode : "exact",
		selector:'textarea',
		language_url : './assets/js/tinymce/langs/fr_FR.js',
		language: 'fr_FR',
		statusbar: false,
		plugins : "preview,link,autolink,table",
		toolbar:'undo redo | styleselect | bold italic | fontsizeselect | link | bullist numlist outdent indent | table | alignleft aligncenter alignright alignjustify',
		convert_fonts_to_spans : false,
		remove_trailing_brs: false,
		protect: [
			/\<\/?(if|endif)\>/g,
			/\<xsl\:[^>]+\>/g,
			/<\?php.*?\?>/g
		]
	});
}