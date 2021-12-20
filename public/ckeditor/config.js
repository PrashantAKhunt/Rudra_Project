/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = "fr";
	// config.uiColor = "#AADC6E";
config.allowedContent = true;
	config.filebrowserBrowseUrl = CKEDITOR.basePath + "filemanager/browser/default/browser.html?Connector=" + CKEDITOR.basePath +"filemanager/connectors/php/connector.php";
  config.filebrowserImageBrowseUrl = CKEDITOR.basePath + "filemanager/browser/default/browser.html?Type=Image&Connector=" + CKEDITOR.basePath +"filemanager/connectors/php/connector.php";
	config.filebrowserFlashBrowseUrl = CKEDITOR.basePath + "filemanager/browser/default/browser.html?Type=Flash&Connector=" + CKEDITOR.basePath +"filemanager/connectors/php/connector.php";
	config.filebrowserUploadUrl = CKEDITOR.basePath + "filemanager/connectors/php/upload.php?Type=File";
	config.filebrowserImageUploadUrl = CKEDITOR.basePath + "filemanager/connectors/php/upload.php?Type=Image";
	config.filebrowserFlashUploadUrl = CKEDITOR.basePath + "filemanager/connectors/php/upload.php?Type=Flash";
	
	config.toolbar = 'Forum';
	config.toolbar_Forum =
	[
		{ name: 'insert', items : [ 'Image','Smiley' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'document', items : [ 'Preview' ] }
	];
	
	config.toolbar = 'Pages';
	config.toolbar_Pages =
	[
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'document', items : [ 'Source','Preview' ] },
		'/',
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] }
	];
	
	config.toolbar = 'Main';
 	config.enterMode = CKEDITOR.ENTER_BR;
	config.toolbar_Main =
	[
		{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 
					'HiddenField' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
		'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
		'/',
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
	];
	
	config.toolbar = 'News';
	config.toolbar_News =
	[
		{ name: 'document', items : [ 'Source','Preview','Print','Templates' ] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
	'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] }
	];	
};
