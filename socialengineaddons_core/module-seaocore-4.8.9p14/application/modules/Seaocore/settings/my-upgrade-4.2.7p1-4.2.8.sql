/* This query was removed for changes in 4.2.8 */

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`)
VALUES ('share', 'activity', '{item:$subject} shared {item:$object}''s {var:$type}.\r\n{body:$body}', 1, 5, 1, 1, 0, 1);

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`, `default`)
VALUES ('shared', 'activity', '{item:$subject} has shared your {item:$object:$label}.', 0, '', 1);

