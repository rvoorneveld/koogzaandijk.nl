<?php

class KZ_Api_Services_GetTeams extends KZ_Api_Services
{
	
	public 	$service 			= 'GetTeams';
    public 	$version 			= '1.0';
    public	$code				= 'ef0baad8aa6d23b60';
    public 	$location			= 'http://www.knkv.nl/kcp';

    public function run()
    {
    	// Set Load From Cache
    	$booLoadCached			= false;
    	
    	// Set Configuration array
        $arrConfig	= array(
                'file'          => 'xml',
                'f'             => 'get_data',
                't'             => 'teams',
                't_id'          => '',
                'p'             => 0,
                'full'          => 1
        );
        
        $strLoadedXml		= $this->_loadXml($booLoadCached, $arrConfig);

//	    mail('rick@mediaconcepts.nl','KZ/Hiltex - XML - Teams', var_export($strLoadedXml,true));
        
        // Load XML string and parse
        $objXml 			= simplexml_load_string($strLoadedXml);
    	
    	if(isset($objXml) && is_object($objXml)) {
	    	
    		// Set Models
    		$objModelTeams		= new KZ_Models_Teams();
    		
    		// Set Defaults
    		$intInsertedTeams	= 0;
    		$intUpdatedTeams	= 0;
    		
    		// Empty Current Standings
    		$objModelTeams->_truncate('teams');
    		
	    	// Loop trough Data
	    	foreach($objXml->children() as $arrAttributes) {
	    		
	    		// Set Variables
	    		$strCategory		= (string)$arrAttributes->category;
	    		$strSport			= (string)$arrAttributes->sport;
	    		$strName			= (string)$arrAttributes->name;
	    		$strNameBasic		= (string)$arrAttributes->name_basic;
	    		$strSeasonSerie		= (string)$arrAttributes->season_serie;
	    		$intID				= (int)$arrAttributes->id;
	    		$strUrl				= (string)$arrAttributes->url;

    			$arrTeamsData	= array(
    				'category'		=> $strCategory,
    				'sport'			=> $strSport,
    				'name'			=> $strName,
    				'name_basic'	=> $strNameBasic,
    				'season_serie'	=> $strSeasonSerie,
    				'id'			=> $intID,
    				'url'			=> $strUrl
    			);

    			// Insert New Team
   				$intInsertID 		= $objModelTeams->addTeam($arrTeamsData);

   				// Add One to Inserted Teams
	    		$intInsertedTeams++;
	    		
	    	}

	    	// Set Response Data
    		$arrData = array(
    			'response' => array(
    				'type' 		=> 'success',
    				'service'	=> 'GetTeams',
    				'teams'  	=> array(
    					'inserted'	=> $intInsertedTeams
    				)
    		));
    		
    		// Return Xml Data
    		return $arrData;
    		
    	} else {
    		// Create Error
    		return $this->createError('1002','invalid xml format');
    	}
    	
    	// Return Screen Feedback on Development and Preview
    	if(APPLICATION_ENV != 'production') {
    		Zend_Debug::dump($objXml);
    		exit;
    	}
    	
    	// Create Error
    	return $this->createError('1001','no xml returned from webservice');
    	    	
    }
    
}