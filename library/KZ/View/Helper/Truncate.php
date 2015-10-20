<?php

class KZ_View_Helper_Truncate extends Zend_View_Helper_Abstract
{

	public static function truncate($input, $numwords, $padding = "", $strLink = false)
	{ 
		$arrTotalWords		= count(explode(' ', $input));
		$intNumberOfWords 	= $numwords;
		
		$output = strtok($input, " \n"); 
		while(--$numwords > 0) 
			$output .= " " . strtok(" \n"); 
		
			if($output != $input) 
				$output .= $padding; 
		
		
		$output = KZ_View_Helper_Truncate::restoreTags($output);
		if($strLink !== false) {
			$output .= '<br /><a href="'.$strLink.'">Lees&nbsp;meer&nbsp;&raquo;</a>';
		}
		return $output;
	}
	
	public static function restoreTags($input)
	{
		$opened = array(); // loop through opened and closed tags in order 
	
		if(preg_match_all("/<(\/?[a-z]+)>?/i", $input, $matches)) {
			foreach($matches[1] as $tag) { 
				if(preg_match("/^[a-z]+$/i", $tag, $regs)) { // a tag has been opened 
					if(strtolower($regs[0]) != 'br') $opened[] = $regs[0]; 
				} elseif(preg_match("/^\/([a-z]+)$/i", $tag, $regs)) { // a tag has been closed 
					unset($opened[array_pop(array_keys($opened, $regs[1]))]);
				} 
			} 
		} // close tags that are still open 
		
		if($opened) { 
			$tagstoclose = array_reverse($opened); 
			foreach($tagstoclose as $tag) 
				$input .= "</$tag>"; 
		} 
		return $input; 
	}
	
}
