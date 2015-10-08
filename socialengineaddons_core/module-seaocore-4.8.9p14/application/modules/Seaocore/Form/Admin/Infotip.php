<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Form_Admin_Infotip extends Engine_Form {

  public function init() {
    
    $coreModules = Engine_Api::_()->getDbtable('modules', 'core');

    $sitestoreproductEnabled = $coreModules->isModuleEnabled('sitestoreproduct');
    $siteusercoverphoto = $coreModules->isModuleEnabled('siteusercoverphoto');
    $sitecontentcoverphoto = $coreModules->isModuleEnabled('sitecontentcoverphoto');
		$pagememberEnabled = $coreModules->isModuleEnabled('sitepagemember');
		$pokeEnabled = $coreModules->isModuleEnabled('poke');
		$suggestionEnabled = $coreModules->isModuleEnabled('suggestion');
		
		$listEnabled = $coreModules->isModuleEnabled('list');
		$recipeEnabled = $coreModules->isModuleEnabled('recipe');
		$sitepageEnabled = $coreModules->isModuleEnabled('sitepage');
		$sitebusinessEnabled = $coreModules->isModuleEnabled('sitebusiness');
		$sitegroupEnabled = $coreModules->isModuleEnabled('sitegroup');
		$sitestoreEnabled = $coreModules->isModuleEnabled('sitestore');
		$sitereviewEnabled = $coreModules->isModuleEnabled('sitereview');
		$siteeventEnabled = $coreModules->isModuleEnabled('siteevent');
		
		$sitememberEnabled = $coreModules->isModuleEnabled('sitemember');
		$siteverifyEnabled = $coreModules->isModuleEnabled('siteverify');

		$actionLinkArray = array(
			"addfriend" => "Add Friend (For Users)",
			"message" => "Message (For Users)",
			"share" => "Share (For Content Items)"
		);

		if (!empty($suggestionEnabled)) {
			$actionLinkArray["suggestion"] = "Suggest to Friends (For Content Items and Friends)";
		}
		
		if (!empty($sitereviewEnabled)) {
			$actionLinkArray["review_wishlist"] = "Add to Wishlist (Multiple Listing Types - For Listings)";
		}
		
		if (!empty($sitestoreproductEnabled)) {
			$actionLinkArray["storeproductreview_wishlist"] = "Add to Wishlist (Stores - For Products)";
		}
		
		if (!empty($pokeEnabled)) {
			$actionLinkArray["poke"] = "Poke (For Users)";
		}
		
		if (!empty($pagememberEnabled)) {
			$actionLinkArray["joinpage"] = "Join Page (For Pages)";
			$actionLinkArray["requestpage"] = "Request Page (For Pages)";
		}
			
		//ADD ACTION LINKS FOR ADVANCED EVENT PLUGIN IF ENABLED
		if (!empty($siteeventEnabled)) {
			$actionLinkArray["joinevent"] = "Join Event (For Events)";
			//$actionLinkArray["requestevent"] = "Request Event (For Events)";
			$actionLinkArray["editevent"] = "Edit Event (For Events)";
			$actionLinkArray["inviteevent"] = "Invite Guests (For Events)";          
		}
		
		$desc_modules = Zend_Registry::get( 'Zend_Translate' )->_( "Choose the Action Links that you want to be available in the Info Tooltips for the various content types and users. Action Links will enable users to quickly perform a relevant action on the entity, thus increasing interactivity. (Format below: LINK_TEXT (For ENTITY_CONTENT_TYPE)) <br /> Note: The Action Link will come for content from 2 official SE Plugins: ‘Classifieds Plugin’ and ‘Events Plugin’ and 4 SocialEngineAddOns Plugins: ‘<a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a>’, ‘<a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-events' target='_blank'>Directory / Pages - Events Extension</a>’, ‘<a href='http://www.socialengineaddons.com/socialengine-listings-catalog-showcase-plugin' target='_blank'>Listings / Catalog Showcase Plugin</a>’ and ‘<a href='http://www.socialengineaddons.com/socialengine-recipes-plugin' target='_blank'>Recipes Plugin</a>’.");
		
		// VALUE FOR AJAX LAYOUT
		$this->addElement('MultiCheckbox', 'seaocore_action_link', array(
			'description' => $desc_modules,
			'label' => 'Action Links',
			'multiOptions' => $actionLinkArray,
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.action.link',
			array("poke" => "poke", "share" => "share", "message" => "message", "addfriend" => "addfriend",
			"suggestion" => "suggestion", "joinpage" => "joinpage", "requestpage" => "requestpage", "review_wishlist" => "review_wishlist", "joinevent" => "joinevent", "editevent" => "editevent", "inviteevent" => "inviteevent")),
		));
		$this->seaocore_action_link->addDecorator( 'Description' , array ( 'placement' => Zend_Form_Decorator_Abstract::PREPEND , 'escape' => false ) ) ;

    
    
		$informationArray = array(
			"category" => "Content Type and Category (For Content Items)",
			"like" => "Likes (For Content Items)",
			"friendcommon" => "Friends' Likes (For Content Items, Shows the number of friends who Like the content item and also nicely displays thumbnail photos of some friends.)",
			"mutualfriend" => "Mutual Friends (For Users, Shows the number of mutual friends with the respective user and also nicely displays thumbnail photos of some friends.)",
			"eventmember" => "Members Attending (For Events, Shows the number of members attending the event.)",
			"attendingeventfriend" => "Friends Attending (For Events, Shows the number of friends attending the event and also nicely displays thumbnail photos of some friends.)",
			"groupmember" => "Total Members (For Groups, Shows the number of members of the group.)",
			"joingroupfriend" => "Friends who are Members (For Groups, Shows the number of friends who are members of the group and also nicely displays thumbnail photos of some friends.)",
			"price" => "Price (For Content Items)",
			"location" => "Location (For Content Items)",
		);

		if (!empty($siteusercoverphoto)) {
			$informationArray["siteusercoverphoto_cover"] = "User Cover Photo";
		}
				
		if (!empty($sitecontentcoverphoto)) {
			$informationArray["sitecontentcoverphoto_cover"] = "Content Cover Photo (For Content Items)";
		}

		if (!empty($sitestoreproductEnabled)) {
			$informationArray["storeproductreview_count"] = "Reviews (Stores - For Products)";
			$informationArray["storeproductrating_count"] = "Ratings (Stores - For Products)";
			$informationArray["storeproductrecommend"] = "Recommended (Stores - For Product Reviews)";
			$informationArray["storeproductreview_helpful"] = "Helpful (Stores - For Product Reviews)";
		}

		if (!empty($sitereviewEnabled)) {
			$informationArray["review_count"] = "Reviews (Multiple Listing Types - For Listings)";
			$informationArray["rating_count"] = "Ratings (Multiple Listing Types - For Listings)";
			$informationArray["recommend"] = "Recommended (Multiple Listing Types - For Reviews)";
			$informationArray["review_helpful"] = "Helpful (Multiple Listing Types - For Reviews)";
			$informationArray["rwcreated_by"] = "Created By (Multiple Listing Types - For Wishlist)";
			$informationArray["rewishlist_item"] = "Entries (Multiple Listing Types - For Wishlist)";
			
		}

		if (!empty($sitepageEnabled) || !empty($sitebusinessEnabled) || !empty($sitegroupEnabled) || !empty($sitestoreEnabled)) {
			$informationArray["phone"] = "Phone (For Pages and Businesses)";
			$informationArray["email"] = "Email (For Pages and Businesses)";
			$informationArray["website"] = "Website (For Pages and Businesses)";
			
		}
		
	  if (!empty($sitepageEnabled) || !empty($sitebusinessEnabled) || !empty($sitegroupEnabled) || !empty($sitestoreEnabled) || !empty($listEnabled) || !empty($recipeEnabled) || !empty($sitereviewEnabled) || !empty($siteeventEnabled) || !empty($sitememberEnabled)) {
			$informationArray["featured"] = "Featured Label";
			$informationArray["sponsored"] = "Sponsored Label";
		}
		
	  if (!empty($siteverifyEnabled)) {
			$informationArray["verify_count"] = "Verifies";
			$informationArray["verify"] = "Verify Icon";
		}
		
		if (!empty($sitememberEnabled)) {
			$informationArray["profile_field"] = "Profile Field (For Users)";
		}
		
		// VALUE FOR AJAX LAYOUT
		$this->addElement('MultiCheckbox', 'seaocore_information_link', array(
			'description' => 'Choose the Information that you want to be available in the Info Tooltips for the various content types and users. The information will be useful meta-data for the entity, and will motivate users to explore the respective content / user. (Format below: INFORMATION (For ENTITY_CONTENT_TYPE, DESCRIPTION))',
			'label' => 'Information',
			'multiOptions' => $informationArray,
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.information.link', array( "category" => "category", "like" => "like" , "eventmember" => "eventmember",	"groupmember"	=> "groupmember", "mutualfriend" => "mutualfriend" , "friendcommon" => "friendcommon", "joingroupfriend" =>		"joingroupfriend", "attendingeventfriend" => "attendingeventfriend", "price" => "price", "review_count" => "review_count", "rating_count" => "rating_count", "recommend" => "recommend", "review_helpful" => "review_helpful", "rwcreated_by" => "rwcreated_by", "rewishlist_item" => "rewishlist_item", "location" => "location", "siteusercoverphoto_cover" => "User Cover Photo", "sitecontentcoverphoto_cover" => "Content Cover Photo (For Content Items)")),
		));
		
		if (!empty($sitememberEnabled)) {
			$this->addElement('Text', 'seaocore_customParams', array(
					'label' => "Custom Profile Fields",
					'Description' => 'How many profile fields you want to show for users in infotooltip?',
					'required' => true,
					'value' => 5,
			));
			
			$this->addElement('Radio', 'seaocore_customfieldtitle', array(
					'label' => 'Do you want to show title of custom field?',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'value' => 0,
			));
			
			$this->addElement('Radio', 'seaocore_customfieldheading', array(
					'label' => 'Do you want to show heading of custom field?',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'value' => 0,
			));
    }
    
		// Element: submit
		$this->addElement('Button', 'submit', array(
				'label' => 'Save Changes',
				'type' => 'submit',
				'ignore' => true
		));
  }
}