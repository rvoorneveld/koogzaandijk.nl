<?php

class KZ_Controller_Editor {
	
	/* Function for creating the TinyMCE Editor
	 * Based on the AuthLevel of a user a different Icon set will be shown.
	 * 
	 * bedankt voor het maken van die class, scheelt een hoop tijd! Paar kleine aanpassingen gedaan en nu is ie static
	 * 
	 * @param	int $intAuthLevel
	 * @param	str	$textClassName
	 * @param	boo $booShowRightContext
	 * 
	 * @return	str $strEditor
	 */
	public static function setEditor($strClass) {
		
		$strEditor = 'tinyMCE.init({
						    mode 				: "specific_textareas",
						    editor_selector		: "'.$strClass.'",
						    theme				: "advanced",';
		
		$strEditor	.= 'theme_advanced_buttons1 : "bold,italic,underline,|,cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,link,image,insertimage,insertfile,|,undo,redo,|,forecolor,backcolor,|,charmap,|,code,",
					   	theme_advanced_buttons2 : "tablecontrols",';

		
		$strEditor	.= 'theme_advanced_buttons3 : "",
						theme_advanced_toolbar_location 	: "top",
						theme_advanced_toolbar_align 		: "left",
						theme_advanced_statusbar_location 	: "bottom",
						theme_advanced_resizing 			: false,

						paste_text_sticky 					: true,
						paste_text_linebreaktype			: "br",
						force_br_newlines 					: true,
   						force_p_newlines 					: false,
						forced_root_block					: false,
						
						table_styles: "Standaard=default;Wit=default default__no-background",
						
						relative_urls 						: false,
						remove_script_host 					: true,
						document_base_url 					: "/",
						theme_advanced_text_colors 			: "000000,00529b,febd11,ffecc4,d9d9cd,f5f6f2,ffffff",
						';
		
		$strEditor	.= 'plugins 			: "lists,pagebreak,searchreplace,paste,directionality,visualchars,nonbreaking,xhtmlxtras,advimage,imagemanager,filemanager,table",
							setup : function(ed) {
								ed.onInit.add(function(ed) {
									ed.pasteAsPlainText = true;
							    });
							}';

		$strEditor	.= '});';
		
		return $strEditor;		
	}
}
