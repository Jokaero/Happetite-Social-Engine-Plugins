<?php
 if ($this->isFacebook) :
  //SET THE OPEN GRAPH META TAGS 
  $local_language = Engine_Api::_()->getApi('settings', 'core')->getSetting('fblanguage.id', 'en_US');
  
  if (!empty($this->page_id)) {
  	$fbmeta_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/invite-code/' . $this->invitecode . '/' . $this->page_id . '/' . $this->resource_type;
     $facebookapi = new Seaocore_Api_Facebook_Facebookinvite();
     
     $invite_mess = $facebookapi->getInviteMessage($this->page_id, '', $this->resource_type);
     $sitepage_title = Engine_Api::_()->getItem($this->resource_type, $this->page_id)->getTitle();
     $fbmeta_title = $this->translate('Join') . ' ' . $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title'))) . ' ' . $this->translate('and check out') . ' ' . $sitepage_title;
   }else {
  	 $fbmeta_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/invite-code/' . $this->invitecode;
			$invite_mess_default = Zend_Registry::get('Zend_Translate')->_('Come and join me on this community. There is a lot to do here!');
			$invite_mess = Engine_Api::_()->getApi('settings', 'core')->getSetting('fbfriend.siteinvite');
   
    if (empty($invite_mess))
      $invite_mess = substr(Engine_Api::_()->getApi('settings', 'core')->invite_message, 0, 240);
      
		if (empty($invite_mess))
		   $invite_mess = $invite_mess_default;
			
			$fbmeta_title = $this->translate('Join') . ' ' . $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.home.title', Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')));
  	}	

  
 
    
 $fb_appid = Engine_Api::_()->getApi('settings', 'core')->core_facebook_appid;?>
 
 <meta property="og:locale" content="<?php echo $local_language;?>" />
<meta property="og:title" content="<?php echo $fbmeta_title;?>" />

<meta property="og:url" content="<?php echo $fbmeta_url;?>" />
<meta property="og:image" content="<?php echo $this->picture;?>" />
<meta property="og:site_name" content="<?php echo $fbmeta_title;?>" />
<meta property="og:description" content="<?php echo $invite_mess;?>" />
<meta property="fb:app_id" content="<?php echo $fb_appid;?>" />

 <?php endif;

?>
