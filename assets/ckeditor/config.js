/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config
	
	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi','JustifyLeft'] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		'/',
		{ name: 'editing',     groups: [ 'find', 'selection'] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'tools' },
		
		'/',
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'document',	   groups: [ 'mode', 'document'] },
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript,Print,Flash,Language,NewPage,SelectAll';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';

	// Config allowed content css
	config.allowedContent = true;
	config.resize_dir = 'both';
	config.extraAllowedContent = '*(*);*{*}';

	// Config browse upload file
	config.filebrowserBrowseUrl      = base_url+'assets/kcfinder/browse.php?type=files&login=1';
	config.filebrowserImageBrowseUrl = base_url+'assets/kcfinder/browse.php?type=images&login=1';
	config.filebrowserFlashBrowseUrl = base_url+'assets/kcfinder/browse.php?type=flash&login=1';
	config.filebrowserUploadUrl      = base_url+'assets/kcfinder/upload.php?type=files&login=1';
	config.filebrowserImageUploadUrl = base_url+'assets/kcfinder/upload.php?type=images&login=1';
	config.filebrowserFlashUploadUrl = base_url+'assets/kcfinder/upload.php?type=flash&login=1';

	// config.filebrowserBrowseUrl = CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'kcfinder' ) + 'browse.php?type=files' );
	// config.filebrowserImageBrowseUrl = CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'kcfinder' ) + 'browse.php?type=images' );
	// config.filebrowserFlashBrowseUrl = CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'kcfinder' ) + 'browse.php?type=flash' );
	// config.filebrowserUploadUrl = CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'kcfinder' ) + 'upload.php?type=files' );
	// config.filebrowserImageUploadUrl = CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'kcfinder' ) + 'upload.php?type=images' );
	// config.filebrowserFlashUploadUrl = CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'kcfinder' ) + 'upload.php?type=flash' );
};



