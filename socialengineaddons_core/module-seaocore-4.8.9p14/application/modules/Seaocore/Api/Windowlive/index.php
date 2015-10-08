<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

	//  Example of how to use the library  -- contents put in $ret_array
	include "contacts_fn.php";
	$ret_array = get_people_array();
	
	//to see a array dump...
	print_r($ret_array);
	
?>   
