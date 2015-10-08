<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Socialenhineaddonshelper.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_Controller_Action_Helper_Seaocorehelper extends Zend_Controller_Action_Helper_Abstract {

    function preDispatch() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $location_info = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.info.location', 0);
        if (Engine_Api::_()->hasModuleBootstrap('sitemember') && ($module == 'user' && $controller == 'edit' && $action == 'profile')) {
            $user = Engine_Api::_()->core()->getSubject();

            // General form w/o profile type
            $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
            $topLevelId = 0;
            $topLevelValue = '';
            if (isset($aliasedFields['profile_type'])) {
                $aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);
                $topLevelId = $aliasedFields['profile_type']->field_id;
                $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
                if (!$topLevelId) {
                    $topLevelId = '';
                }

                if (!$topLevelValue) {
                    $topLevelValue = '';
                }
            }
            if ($topLevelId && $topLevelValue) {
                $field_option_child = '';
                $field_option_child_city = '';
                $field_option_child_heading = '';
                $mapstable = Engine_Api::_()->fields()->getTable('user', 'maps');
                $metatable = Engine_Api::_()->fields()->getTable('user', 'meta');
                $mapstableName = $mapstable->info('name');
                $metatableName = $metatable->info('name');
                $select = $mapstable->select()
                        ->setIntegrityCheck(false)
                        ->from($mapstableName, array('*'))
                        ->join($metatableName, $mapstableName . '.child_id = ' . $metatableName . '.field_id', array())
                        ->where($metatableName . '.type = ?', 'location')
                        ->where($mapstableName . '.field_id = ?', $topLevelId)
                        ->where($mapstableName . '.option_id = ?', $topLevelValue);
                $row = $mapstable->fetchRow($select);
                if ($row) {
                    $field_option_child = $row->field_id . '_' . $row->option_id . '_' . $row->child_id;
                }
                $field_option_child_heading = '';
                //HEADING
                $select = $mapstable->select()
                        ->setIntegrityCheck(false)
                        ->from($mapstableName, array('*'))
                        ->join($metatableName, $mapstableName . '.child_id = ' . $metatableName . '.field_id', array())
                        ->where($metatableName . '.type = ?', 'heading')
                        ->where($metatableName . '.label like (?)', '%Location%')
                        ->where($mapstableName . '.field_id = ?', $topLevelId)
                        ->where($mapstableName . '.option_id = ?', $topLevelValue);
                $row = $mapstable->fetchRow($select);
                if ($row && Engine_Api::_()->hasModuleBootstrap('sitemember')) {
                    $field_option_child_heading = $row->field_id . '_' . $row->option_id . '_' . $row->child_id;
                }

                $select = $mapstable->select()
                        ->setIntegrityCheck(false)
                        ->from($mapstableName, array('*'))
                        ->join($metatableName, $mapstableName . '.child_id = ' . $metatableName . '.field_id', array())
                        ->where($metatableName . '.type = ?', 'city')
                        ->where($mapstableName . '.field_id = ?', $topLevelId)
                        ->where($mapstableName . '.option_id = ?', $topLevelValue);
                $row = $mapstable->fetchRow($select);
                if ($row) {
                    $field_option_child_city = $row->field_id . '_' . $row->option_id . '_' . $row->child_id;
                }

                if ($field_option_child || $field_option_child_city || $field_option_child_heading):

                    //GET API KEY
                    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                    $view->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
                    $view->headScript()->appendFile($view->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/core.js");
                    $city = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities');
                    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                        $script = <<<EOF
                        var field_option_child = "$field_option_child";
                        var field_option_child_city = "$field_option_child_city";
                        var field_option_child_heading = "$field_option_child_heading";
                        var city = "$city";
                        window.addEvent('domready', function() {
                            if(field_option_child_city) {                  
                                locationAutoSuggest(city, field_option_child, field_option_child_city);
                                if(field_option_child) {
                                   field_option_child_wrapper = field_option_child + '-wrapper';
                            if($location_info)
                                   document.getElementById(field_option_child_wrapper).style.display = 'none';
                                }
                                if(field_option_child_heading) {
                                    field_option_child_heading = field_option_child_heading + '-wrapper';
                            if($location_info)
                                    document.getElementById(field_option_child_heading).style.display = 'none';
                                }
                           } else {
                                new google.maps.places.Autocomplete(document.getElementById(field_option_child));
                                field_option_child_wrapper = field_option_child + '-wrapper';
                            if($location_info)
                                document.getElementById(field_option_child_wrapper).style.display = 'none';
                                if(field_option_child_heading) {
                                   field_option_child_heading = field_option_child_heading + '-wrapper';
                            if($location_info)
                                   document.getElementById(field_option_child_heading).style.display = 'none';
                                }
                           }
                        });
EOF;
                        $view->headScript()
                                ->appendScript($script);
                    } else {
                        $script = <<<EOF
                        var field_option_child = "$field_option_child";
                        var field_option_child_city = "$field_option_child_city";
                        var field_option_child_heading = "$field_option_child_heading";
                        var city = "$city";
                         sm4.core.runonce.add(function() {
                            if(field_option_child_city) {                  
                                locationAutoSuggest(city, field_option_child, field_option_child_city);
                                if(field_option_child) {
                                   field_option_child_wrapper = field_option_child + '-wrapper';
                                   $('#field_option_child_wrapper').css("display", "none");
                                }
                                if(field_option_child_heading) {
                                    field_option_child_heading = field_option_child_heading + '-wrapper';   
                                
                             $('#field_option_child_heading').css("display", "none");
                                
                                }
                           } else {
                                new google.maps.places.Autocomplete(document.getElementById(field_option_child));
                                field_option_child_wrapper = field_option_child + '-wrapper';
                              $('#field_option_child_wrapper').css("display", "none");
                                if(field_option_child_heading) {
                                   field_option_child_heading = field_option_child_heading + '-wrapper';
                                    $('#field_option_child_heading').css("display", "none");
                                }
                           }
                        });
EOF;
                        $view->headScriptSM()
                                ->appendScript($script);
                    }
                endif;
            }
        }
    }

    function postDispatch() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        if (Engine_Api::_()->hasModuleBootstrap('sitemember') && ($module == 'user' && $controller == 'signup' && $action == 'index')) {
            if ($this->getRequest()->isPost() && (isset($view->form))) {
                $field_option_child = '';
                $field_option_child_city = '';
                if (array_key_exists('profile_type', $_SESSION['User_Plugin_Signup_Account']['data'])) {
                    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');

                    if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
                        $profile_field_id = $topStructure[0]->getChild()->field_id;
                    }

                    $profile_type_id = $_SESSION['User_Plugin_Signup_Account']['data']['profile_type'];
                    if (!$profile_type_id) {
                        $profileTypeField = $topStructure[0]->getChild();
                        $options = $profileTypeField->getOptions();
                        if (count($options) == 1) {
                            $profile_type_id = $options[0]->option_id;
                        }
                    }

                    if ($profile_field_id && $profile_type_id) {
                        $mapstable = Engine_Api::_()->fields()->getTable('user', 'maps');
                        $metatable = Engine_Api::_()->fields()->getTable('user', 'meta');
                        $mapstableName = $mapstable->info('name');
                        $metatableName = $metatable->info('name');
                        $select = $mapstable->select()
                                ->setIntegrityCheck(false)
                                ->from($mapstableName, array('*'))
                                ->join($metatableName, $mapstableName . '.child_id = ' . $metatableName . '.field_id', array())
                                ->where($metatableName . '.type = ?', 'location')
                                ->where($mapstableName . '.field_id = ?', $profile_field_id)
                                ->where($mapstableName . '.option_id = ?', $profile_type_id);
                        $row = $mapstable->fetchRow($select);
                        if ($row) {
                            $field_option_child = $row->field_id . '_' . $row->option_id . '_' . $row->child_id;
                        }

                        $select = $mapstable->select()
                                ->setIntegrityCheck(false)
                                ->from($mapstableName, array('*'))
                                ->join($metatableName, $mapstableName . '.child_id = ' . $metatableName . '.field_id', array())
                                ->where($metatableName . '.type = ?', 'city')
                                ->where($mapstableName . '.field_id = ?', $profile_field_id)
                                ->where($mapstableName . '.option_id = ?', $profile_type_id);
                        $row = $mapstable->fetchRow($select);
                        if ($row) {
                            $field_option_child_city = $row->field_id . '_' . $row->option_id . '_' . $row->child_id;
                        }

                        if ($field_option_child || $field_option_child_city):

                            //GET API KEY
                            $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                            $view->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
                            $view->headScript()->appendFile($view->layout()->staticBaseUrl . "application/modules/Seaocore/externals/scripts/core.js");
                            $city = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities');
                            if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
                                $script = <<<EOF
                                                var field_option_child = "$field_option_child";
                                                var field_option_child_city = "$field_option_child_city";
                                                var city = "$city";
                                                window.addEvent('domready', function() {
                                if(field_option_child_city) {                  
                                locationAutoSuggest(city, field_option_child, field_option_child_city);
                                
                               } else {
                                window.addEvent('domready', function() {
																				  new google.maps.places.Autocomplete(document.getElementById(field_option_child));
																			  });
                                }
                                                });
EOF;

                                $view->headScript()
                                        ->appendScript($script);
                            } else {
                                $script = <<<EOF
                            var field_option_child = "$field_option_child";
                            var field_option_child_city = "$field_option_child_city";
                            var city = "$city";
                             sm4.core.runonce.add(function() {
                                if(field_option_child_city) {                  
                                locationAutoSuggest(city, field_option_child, field_option_child_city);
                               } else {
                               sm4.core.runonce.add(function() {
                                 new google.maps.places.Autocomplete(document.getElementById(field_option_child));
 });
                                }
                            });
EOF;
                                $view->headScriptSM()
                                        ->appendScript($script);
                            }
                        endif;
                    }
                }
            }
        }
    }

}
