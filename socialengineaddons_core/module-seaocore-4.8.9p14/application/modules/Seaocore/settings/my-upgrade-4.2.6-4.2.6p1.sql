INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES
( 'seaocore_admin_map', 'seaocore', 'Locations & Maps', NULL, '{"route":"admin_default","module":"seaocore","controller":"settings","action":"map"}', 'seaocore_admin_main', NULL, '1', '0', '7');

INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('seaocore.tooltip.bgcolor', '#FFFFFF');