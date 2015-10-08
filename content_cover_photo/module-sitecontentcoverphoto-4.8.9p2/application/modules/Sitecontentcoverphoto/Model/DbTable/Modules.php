<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecontentcoverphoto
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Settings.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecontentcoverphoto_Model_DbTable_Modules extends Engine_Db_Table {

    protected $_name = 'sitecontentcoverphoto_modules';
    protected $_rowClass = 'Sitecontentcoverphoto_Model_Module';

    /**
     * Get the settings according module type.
     *
     * @param string $modType
     */
    public function checkEnableModule($params = array()) {
        return $this->select()->from($this->info('name'), array('enabled'))->where('resource_type =?', $params['resource_type'])->where('enabled =?', 1)->query()->fetchColumn();
    }

    public function getModules() {

        $modules = array();
        $tableName = $this->info('name');

        $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
        $coreTableName = $coreTable->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($tableName, array('resource_type'))
                ->join($coreTableName, "$coreTableName . name = $tableName . module", array('enabled', 'title', 'name'))
                ->where($tableName . '.enabled = ?', 1)
                ->where($coreTableName . '.enabled = ?', 1);
        $row = $select->query()->fetchAll();

        if (!empty($row)) {
            $modules[0] = '';
            foreach ($row as $modName) {
                if ($modName['name'] == 'sitereview') {
                    $explodedResourceType = explode('_', $modName['resource_type']);
                    if (!empty($explodedResourceType[2])) {
                        $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
                        if (!empty($listingtypesTitle)) {
                            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')) {
                                $modName['title'] = 'Reviews & Ratings - Multiple Listing Types ( ' . $listingtypesTitle . ' )';
                            } else {
                                $modName['title'] = 'Reviews & Ratings';
                            }
                        }
                    }
                }
                $modules[$modName['resource_type']] = $modName['title'];
            }
        }

        return $modules;
    }

    public function getMobileModules() {

        if (!Engine_Api::_()->hasModuleBootstrap('sitemobile'))
            return false;

        $modules = array();
        $tableName = $this->info('name');

        $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
        $coreTableName = $coreTable->info('name');
        $sitemobileTable = Engine_Api::_()->getDbtable('modules', 'sitemobile');
        $sitemobileTableName = $sitemobileTable->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($tableName, array('resource_type'))
                ->join($coreTableName, "$coreTableName . name = $tableName . module", array('enabled', 'title', 'name'))
                ->join($sitemobileTableName, "$sitemobileTableName . name = $tableName . module", null)
                ->where($tableName . '.enabled = ?', 1)
                ->where($sitemobileTableName . '.integrated = ?', 1)
                ->where($coreTableName . '.enabled = ?', 1);
        $row = $select->query()->fetchAll();

        if (!empty($row)) {
            $modules[0] = '';
            foreach ($row as $modName) {
                if ($modName['name'] == 'sitereview') {
                    $explodedResourceType = explode('_', $modName['resource_type']);
                    if (!empty($explodedResourceType[2])) {
                        $listingtypesTitle = Engine_Api::_()->getDbtable('listingtypes', 'sitereview')->getListingRow($explodedResourceType[2])->title_plural;
                        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereviewlistingtype')) {
                            $modName['title'] = 'Reviews & Ratings - Multiple Listing Types ( ' . $listingtypesTitle . ' )';
                        } else {
                            $modName['title'] = 'Reviews & Ratings';
                        }
                    }
                }
                $modules[$modName['resource_type']] = $modName['title'];
            }
        }

        return $modules;
    }

    /**
     * Get the settings according module type.
     *
     * @param string $modType
     */
    public function getModuleName($params = array()) {

        if ($params['resource_type'] === 0) {
            return false;
        }

        return $this->select()->from($this->info('name'), array('module'))->where('resource_type =?', $params['resource_type'])->where('enabled =?', 1)->query()->fetchColumn();
    }

}