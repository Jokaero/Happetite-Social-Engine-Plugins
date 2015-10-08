<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteusercoverphoto
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Main.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

if (Engine_Api::_()->hasModuleBootstrap('advalbum')) {

	class Siteusercoverphoto_Model_DbTable_Albums extends Advalbum_Model_DbTable_Albums
	{

		protected $_name = 'album_albums';
		protected $_serializedColumns = array('cover_params');

		public function getSpecialAlbumCover(User_Model_User $user, $type)
		{
			if( !in_array($type, array('cover')) ) {
				throw new Album_Model_Exception('Unknown special album type');
			}

			$tableAlbum = Engine_Api::_()->getDbtable('albums', 'advalbum');
			$select = $tableAlbum->select()
					->where('owner_type = ?', $user->getType())
					->where('owner_id = ?', $user->getIdentity())
					->where('type = ?', $type)
					->order('album_id ASC')
					->limit(1);
			
			$album = $tableAlbum->fetchRow($select);
			if( null === $album ) {
				$translate = Zend_Registry::get('Zend_Translate');
				$album = $tableAlbum->createRow();
				$album->owner_type = 'user';
				$album->owner_id = $user->getIdentity();
				$album->title = $translate->_(ucfirst($type) . ' Photos');
				$album->type = $type;
				$album->search = 1;
				$album->save();
				$auth = Engine_Api::_()->authorization()->context;
				$auth->setAllowed($album, 'everyone', 'view',    true);
				$auth->setAllowed($album, 'everyone', 'comment', true);
			}
			return $album;
		}

		public function getAdvAlbumSelect($options = array())
		{
			$select = $this->select();
			if( !empty($options['owner']) && 
					$options['owner'] instanceof Core_Model_Item_Abstract ) {
				$select
					->where('owner_type = ?', $options['owner']->getType())
					->where('owner_id = ?', $options['owner']->getIdentity())
					->order('modified_date DESC')
					;
			}

			if( !empty($options['search']) && is_numeric($options['search']) ) {
				$select->where('search = ?', $options['search']);
			}

			return $select;
		}

		public function getAdvAlbumPaginator($options = array())
		{
			return Zend_Paginator::factory($this->getAdvAlbumSelect($options));
		}

		public function getAdvPhotoPaginator($params =  array()) {
			return Zend_Paginator::factory($this->getAdvPhotoSelect($params));
		}

		public function getAdvPhotoSelect($params =  array())
		{
			$select = Engine_Api::_()->getDbtable('photos', 'advalbum')->select();
			
			if( !empty($params['album']) && $params['album'] instanceof Album_Model_Album ) {
				$select->where('album_id = ?', $params['album']->getIdentity());
			} else if( !empty($params['album_id']) && is_numeric($params['album_id']) ) {
				$select->where('album_id = ?', $params['album_id']);
			}
			
			if( !isset($params['order']) ) {
				$select->order('order ASC');
			} else if( is_string($params['order']) ) {
				$select->order($params['order']);
			}
			
			return $select;
		}
	}

} else {

	class Siteusercoverphoto_Model_DbTable_Albums extends Album_Model_DbTable_Albums
	{

		protected $_name = 'album_albums';
		protected $_serializedColumns = array('cover_params');
		public function getSpecialAlbumCover(User_Model_User $user, $type)
		{
			if( !in_array($type, array('cover')) ) {
				throw new Album_Model_Exception('Unknown special album type');
			}

			$tableAlbum = Engine_Api::_()->getDbtable('albums', 'album');
			$select = $tableAlbum->select()
					->where('owner_type = ?', $user->getType())
					->where('owner_id = ?', $user->getIdentity())
					->where('type = ?', $type)
					->order('album_id ASC')
					->limit(1);
			
			$album = $tableAlbum->fetchRow($select);
			if( null === $album ) {
				$translate = Zend_Registry::get('Zend_Translate');
				$album = $tableAlbum->createRow();
				$album->owner_type = 'user';
				$album->owner_id = $user->getIdentity();
				$album->title = $translate->_(ucfirst($type) . ' Photos');
				$album->type = $type;
				$album->search = 1;
				$album->save();
				$auth = Engine_Api::_()->authorization()->context;
				$auth->setAllowed($album, 'everyone', 'view',    true);
				$auth->setAllowed($album, 'everyone', 'comment', true);
			}
			return $album;
		}
	}

}