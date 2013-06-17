<?php
class InitPlugin extends DBMigration
{
    function up()
    {
        DBManager::get()->exec("INSERT IGNORE INTO `datafields` (`datafield_id`, `name`, `object_type`, `object_class`, `edit_perms`, `view_perms`, `priority`, `mkdate`, `chdate`, `type`, `typeparam`, `is_required`, `description`) VALUES
(MD5('StudipSimpleGroupsUser'), 'Gruppen', 'user', '14', 'root', 'root', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'textline', '', 0, ''),
(MD5('StudipSimpleGroupsCourse'), 'Gruppen', 'sem', NULL, 'root', 'root', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'textline', '', 0, '');
");
        DBManager::get()->exec("INSERT IGNORE INTO `config` (`config_id`, `parent_id`, `field`, `value`, `is_default`, `type`, `range`, `section`, `position`, `mkdate`, `chdate`, `description`, `comment`, `message_template`) VALUES
            (MD5('SIMPLE_GROUPS_USER_DEFAULT'), '', 'SIMPLE_GROUPS_USER_DEFAULT', '', '1', 'string', 'global', 'plugins', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'Standard Gruppe für neue Nutzer', '', '')");
    }
}