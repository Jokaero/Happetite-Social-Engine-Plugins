<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locationcontents.php 6590 2014-06-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Model_DbTable_Locationcontents extends Engine_Db_Table {

    protected $_name = 'seaocore_locationcontents';
    protected $_rowClass = 'Seaocore_Model_Locationcontent';

    public function getLocations($params = array()) {

        $select = $this->select();

        if (isset($params['columns'])) {
            $select->from($this->info('name'), $params['columns']);
        }

        if (isset($params['status'])) {
            $select->where('status = ?', $params['status']);
        }
        
        $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificorder', 'locationcontent_id');
        
        $select->order("$order ASC");

        return $this->fetchAll($select);
    }

    public function getSpecificLocationRow($location) {

        $select = $this->select()
                ->from($this->info('name'))
                ->where('location = ?', $location);

        return $this->fetchRow($select);
    }

    public function getSpecificLocationColumn($params = array()) {

        $columnName = isset($params['columnName']) ? $params['columnName'] : 'locationcontent_id';
        
        $select = $this->select()
                ->from($this->info('name'), $columnName);

        if (!empty($params['location'])) {
            $select->where('location = ?', $params['location']);
        }
        
        if (!empty($params['status'])) {
            $select->where('status = ?', $params['status']);
        }        
        
        return $select->query()->fetchColumn();
    }

}
