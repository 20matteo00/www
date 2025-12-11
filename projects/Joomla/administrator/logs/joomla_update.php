#
#<?php die('Forbidden.'); ?>
#Date: 2025-12-09 08:47:07 UTC
#Software: Joomla! 5.0.0 Stable [ Kuboresha ] 17-October-2023 16:00 GMT

#Fields: datetime	priority clientip	category	message
2025-12-09T08:47:07+00:00	INFO 127.0.0.1	update	Test logging
2025-12-09T08:47:07+00:00	INFO 127.0.0.1	update	Update started by user matteo (480). Old version is 5.0.0.
2025-12-09T08:47:07+00:00	INFO 127.0.0.1	update	Downloading update file from https://update.joomla.org/releases/5.4.1/Joomla_5.4.1-Stable-Update_Package.zip.
2025-12-09T08:47:10+00:00	INFO 127.0.0.1	update	File Joomla_5.4.1-Stable-Update_Package.zip downloaded.
2025-12-09T08:47:10+00:00	INFO 127.0.0.1	update	Starting installation of new version.
2025-12-09T08:47:43+00:00	INFO	127.0.0.1	update	Finalising installation.
2025-12-09T08:47:43+00:00	INFO	127.0.0.1	update	Start of SQL updates.
2025-12-09T08:47:43+00:00	INFO	127.0.0.1	update	The current database version (schema) is 5.0.0-2023-09-11.
2025-12-09T08:47:43+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2023-11-28. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-01-04. Query text: ALTER TABLE `#__fields_values` MODIFY `value` MEDIUMTEXT;.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-02-10. Query text: ALTER TABLE `#__guidedtour_steps` ADD COLUMN `params` text NULL ;.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-02-24. Query text: CREATE TABLE IF NOT EXISTS `#__tuf_metadata` (   `id` int NOT NULL AUTO_INCREMEN.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-02-24. Query text: INSERT INTO `#__tuf_metadata` (`update_site_id`, `root`) SELECT ue.`update_site_.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-02-24. Query text: UPDATE `#__update_sites`    SET `type` = 'tuf', `location` = 'https://update.joo.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-02-25. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-03-08. Query text: INSERT IGNORE INTO `#__guidedtour_steps` (`tour_id`, `title`, `published`, `desc.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-03-08. Query text: ALTER TABLE `#__guidedtours` ADD COLUMN `autostart` int NOT NULL DEFAULT 0 ;.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-03-08. Query text: INSERT IGNORE INTO `#__guidedtours` (`title`, `uid`, `description`, `ordering`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-03-08. Query text: UPDATE `#__guidedtour_steps` SET `tour_id` = LAST_INSERT_ID() WHERE `tour_id`=0;.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.0-2024-03-28. Query text: INSERT INTO `#__postinstall_messages` (`extension_id`, `title_key`, `description.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.1.1-2024-04-18. Query text: UPDATE `#__template_styles` SET `params` = JSON_SET(`params`, '$.colorScheme', '.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.0-2024-07-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.0-2024-07-19. Query text: INSERT INTO `#__action_logs_extensions` (`extension`) VALUES ('com_guidedtours').
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.0-2024-07-19. Query text: INSERT INTO `#__action_log_config` (`type_title`, `type_alias`, `id_holder`, `ti.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.0-2024-08-22. Query text: INSERT INTO `#__guidedtours` (`title`, `description`, `extensions`, `url`, `publ.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.0-2024-08-22. Query text: INSERT INTO `#__guidedtour_steps` (`title`, `description`, `position`, `target`,.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.0-2024-09-17. Query text: UPDATE `#__guidedtour_steps` SET `ordering` = `ordering` + 1 WHERE `title` IN ('.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.0-2024-09-17. Query text: INSERT INTO `#__guidedtour_steps` (`title`, `description`, `position`, `target`,.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.2-2024-09-24. Query text: UPDATE `#__assets` SET `title` = 'PUBLISH' WHERE `name` LIKE 'com_content.transi.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.2-2024-09-24. Query text: UPDATE `#__assets` SET `title` = 'UNPUBLISH' WHERE `name` LIKE 'com_content.tran.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.2-2024-09-24. Query text: UPDATE `#__assets` SET `title` = 'TRASH' WHERE `name` LIKE 'com_content.transiti.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.2-2024-09-24. Query text: UPDATE `#__assets` SET `title` = 'ARCHIVE' WHERE `name` LIKE 'com_content.transi.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.2-2024-09-24. Query text: UPDATE `#__assets` SET `title` = 'FEATURE' WHERE `name` LIKE 'com_content.transi.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.2-2024-09-24. Query text: UPDATE `#__assets` SET `title` = 'UNFEATURE' WHERE `name` LIKE 'com_content.tran.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.2-2024-09-24. Query text: UPDATE `#__assets` SET `title` = 'PUBLISH_AND_FEATURE' WHERE `name` LIKE 'com_co.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.2.3-2025-01-09. Query text: UPDATE `#__mail_templates` SET `params` = '{"tags":["messages","message","date",.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.0-2024-10-13. Query text: ALTER TABLE `#__fields` MODIFY `fieldparams` MEDIUMTEXT NOT NULL;.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.0-2024-10-26. Query text: CREATE TABLE IF NOT EXISTS `#__scheduler_logs` (   `id` int unsigned NOT NULL AU.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.0-2024-12-09. Query text: INSERT INTO `#__action_log_config` (`type_title`, `type_alias`, `id_holder`, `ti.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.0-2025-02-09. Query text: ALTER TABLE `#__action_logs_users` DROP COLUMN `exclude_self` ;.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.0-2025-02-22. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.0-2025-03-14. Query text: UPDATE `#__guidedtours` SET `autostart` = 0 WHERE `uid` = 'joomla-whatsnew-5-2';.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.0-2025-03-14. Query text: INSERT INTO `#__guidedtours` (`title`, `description`, `extensions`, `url`, `publ.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.0-2025-03-14. Query text: INSERT INTO `#__guidedtour_steps` (`title`, `description`, `position`, `target`,.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.4-2025-09-19. Query text: UPDATE `#__content_types` SET `field_mappings` = JSON_SET(`field_mappings`, '$.c.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.3.4-2025-09-19. Query text: UPDATE `#__content_types` SET `field_mappings` = JSON_SET(`field_mappings`, '$.c.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-04-23. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-05-10. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-05-10. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-05-10. Query text: INSERT IGNORE INTO `#__mail_templates` (`template_id`, `extension`, `language`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-05-10. Query text: INSERT INTO `#__postinstall_messages` (`extension_id`, `title_key`, `description.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-05-10. Query text: UPDATE `#__guidedtours` SET `autostart` = 0 WHERE `uid` = 'joomla-whatsnew-5-3';.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-05-10. Query text: INSERT INTO `#__guidedtours` (`title`, `description`, `extensions`, `url`, `publ.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-05-10. Query text: INSERT INTO `#__guidedtour_steps` (`title`, `description`, `position`, `target`,.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: UPDATE `#__extensions` SET `locked` = 1  WHERE `type` = 'plugin' AND `folder` = .
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: UPDATE `#__extensions` SET `locked` = 1  WHERE `type` = 'plugin' AND `element` =.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Ran query from file 5.4.0-2025-08-02. Query text: UPDATE `#__extensions` SET `locked` = 1  WHERE `type` = 'plugin' AND `element` =.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	End of SQL updates.
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Uninstalling extensions
2025-12-09T08:47:44+00:00	INFO	127.0.0.1	update	Deleting removed files and folders.
2025-12-09T08:47:45+00:00	INFO	127.0.0.1	update	Cleaning up after installation.
2025-12-09T08:47:45+00:00	INFO	127.0.0.1	update	Update to version 5.4.1 is complete.
