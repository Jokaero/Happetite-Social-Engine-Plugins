<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminGeoLocationsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_AdminGeoLocationsController extends Core_Controller_Action_Admin {

    public function importDataAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $basePath = APPLICATION_PATH . "/temporary/GeoLiteCity";
        $fBlocks = $basePath . '/GeoLiteCity-Blocks.csv';
        $this->view->error = null;
        if (!file_exists($fBlocks)) {
            $this->view->error = $error = "The file is not here.<br />" . $fBlocks;
            return;
        }

        $fLocation = $basePath . '/GeoLiteCity-Location.csv';
        $this->view->error = null;
        if (!file_exists($fLocation)) {
            $this->view->error = $error = "The file is not here.<br />" . $fLocation;
            return;
        }

        if ($this->getRequest()->isPost()) {

            ini_set("memory_limit", "1024M");
            set_time_limit(0);

            $i = 0;

            $handle = @fopen($fBlocks, "r");
            if ($handle) {
                $insert = "INSERT IGNORE INTO `engine4_seaocore_geolitecity_blocks` ( `ip_start`, `ip_end`, `location_id`) VALUES";
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $values = array();
                $turncateTableSql = "TRUNCATE TABLE `engine4_seaocore_geolitecity_blocks`";

                $db->query($turncateTableSql);
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $values[] = $buffer;
                    $i++;
                    if ($i == 20000) {
                        $str = "(" . join("),(", $values) . ")";
                        $sql = $insert . $str;
                        $db->query($sql);
                        $values = array();
                        $i = 0;
                    }
                }
                if (!feof($handle)) {
                    echo "Error: unexpected fgets() fail\n";
                }
                fclose($handle);
            }


            $i = 0;

            $handle = @fopen($fLocation, "r");
            if ($handle) {
                $insert = "INSERT IGNORE INTO `engine4_seaocore_geolitecity_location` (  `locId` ,`country` ,`region` ,`city` ,`postalCode` ,`latitude` ,`longitude` ,`metroCode` ,`areaCode`) VALUES ";
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $values = array();
                $turncateTableSql = "TRUNCATE TABLE `engine4_seaocore_geolitecity_location`";

                $db->query($turncateTableSql);
                fgets($handle, 4096);
                fgets($handle, 4096);
                while (($buffer = fgets($handle, 4096)) !== false) {
                    $buffer = explode("\n", $buffer);
                    $buffer = explode(",", $buffer[0]);

                    if (empty($buffer[7])) {
                        $buffer[7] = 0;
                    }
                    if (empty($buffer[8])) {
                        $buffer[8] = 0;
                    }
                    $buffer = join(",", $buffer);
                    $values[] = $buffer;
                    $i++;
                    if ($i == 50) {
                        $str = "(" . join("),(", $values) . ")";
                        $sql = $insert . $str;

                        $db->query($sql);
                        $values = array();
                        $i = 0;
                    }
                }
                if (!feof($handle)) {
                    echo "Error: unexpected fgets() fail\n";
                }
                fclose($handle);
            }


            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Suucessfully import data'))
            ));
        }
        $this->renderScript('admin-geo-locations/import-data.tpl');
    }

    public function manageAction() {

        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seaocore_admin_main', array(), 'seaocore_admin_maps');

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Seaocore_Form_Admin_Geolocation_Filter();

        $locationContentTable = Engine_Api::_()->getDbTable('locationcontents', 'seaocore');
        //$locationContentTableName = $locationContentTable->info('name');
        
        $this->view->locationContentId = 0;
        if(Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0)) {
            $locationDefault = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefault');
            $this->view->locationContentId = $locationContentTable->getSpecificLocationColumn(array('location' => $locationDefault, 'columnName' => 'locationcontent_id', 'status' => 1));
        }

        $select = $locationContentTable->select();

        //PROCESS FROM 
        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array(
            'order' => 'locationcontent_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->formValues = array_filter($values);
        $this->view->assign($values);

        $select->order((!empty($values['order']) ? $values['order'] : 'locationcontent_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        $this->view->locationContents = $locationContentTable->fetchAll($select);
        
        $this->view->locationsCount = count($this->view->locationContents);
        
        //CLEAR THE MENUS CACHE
        Engine_Api::_()->seaocore()->clearMenuCache();
    }

    public function addAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GENERATE FORM
        $form = $this->view->form = new Seaocore_Form_Admin_Geolocation_Add();
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        $this->view->options = array();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            if (!empty($values) && (empty($values['latitude']) || empty($values['longitude']))) {

                $getLocationsValues = $this->checkValidLocation($values);
                if (is_array($getLocationsValues)) {
                    $values = array_merge($values, $getLocationsValues);
                } elseif (!is_array($getLocationsValues) && $getLocationsValues == 2) {
                    $error = $this->view->translate('Oops! Something went wrong. Please try again later.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);

                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                } elseif (!is_array($getLocationsValues) && $getLocationsValues == 1) {
                    $error = $this->view->translate('Please enter the valid location!');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);

                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $location = Engine_Api::_()->getDbtable('locationcontents', 'seaocore');

                $row = $location->createRow();
                $row->setFromArray($values);
                $row->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }

        $this->renderScript('admin-geo-locations/add.tpl');
    }

    public function editAction() {

        //LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        $location = Engine_Api::_()->getItem('seaocore_locationcontent', $this->_getParam('locationcontent_id', 0));

        //GENERATE FORM
        $form = $this->view->form = new Seaocore_Form_Admin_Geolocation_Edit(array('item' => $location));
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
        $form->populate($location->toArray());

        $this->view->options = array();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();

            if (!empty($values) && (empty($values['latitude']) || empty($values['longitude']))) {

                $getLocationsValues = $this->checkValidLocation($values);
                if (is_array($getLocationsValues)) {
                    $values = array_merge($values, $getLocationsValues);
                } elseif (!is_array($getLocationsValues) && $getLocationsValues == 2) {
                    $error = $this->view->translate('Oops! Something went wrong. Please try again later.');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);

                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                } elseif (!is_array($getLocationsValues) && $getLocationsValues == 1) {
                    $error = $this->view->translate('Please enter the valid location!');
                    $error = Zend_Registry::get('Zend_Translate')->_($error);

                    $form->getDecorator('errors')->setOption('escape', false);
                    $form->addError($error);
                    return;
                }
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                $location->setFromArray($values);
                $location->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }

        $this->renderScript('admin-geo-locations/edit.tpl');
    }

    public function checkValidLocation($values) {

        $location = $values['location'];
        $latitude = (isset($values['latitude']) && !empty($values['latitude'])) ? $values['latitude'] : 0;
        $longitude = (isset($values['longitude']) && !empty($values['longitude'])) ? $values['longitude'] : 0;
        $locationResults = array();
        if (!empty($location) && $location !== "World" && $location !== "world" && (empty($latitude) || empty($longitude))) {
            $locationResults = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getLatLong(array('location' => $location, 'module' => 'SocialEngineAddOns Core'));

            $latitude = $locationResults['latitude'];
            $longitude = $locationResults['longitude'];
        }

        if (!empty($locationResults['over_query_limit'])) {
            return 2;
        } elseif (empty($latitude) || empty($longitude)) {
            return 1;
        } else {
            return array('latitude' => $latitude, 'longitude' => $longitude);
        }
    }

    //ACTION FOR MAKING THE LOCATION APPROVE/DIS-APPROVE
    public function statusAction() {

        $locationcontent_id = $this->_getParam('locationcontent_id');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $locationContent = Engine_Api::_()->getItem('seaocore_locationcontent', $locationcontent_id);
            $locationContent->status = !$locationContent->status;

            $locationContent->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/seaocore/geo-locations/manage');
    }

    //ACTION FOR DELETE THE LOCATION
    public function deleteAction() {

        $this->_helper->layout->setLayout('admin-simple');
        $this->view->locationcontent_id = $locationcontent_id = $this->_getParam('locationcontent_id');

        if ($this->getRequest()->isPost()) {
            Engine_Api::_()->getItem('seaocore_locationcontent', $locationcontent_id)->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('Deleted Succesfully.')
            ));
        }
        $this->renderScript('admin-geo-locations/delete.tpl');
    }

    //ACTION FOR MULTI-DELETE LOCATIONS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    Engine_Api::_()->getItem('seaocore_locationcontent', (int) $value)->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }

}