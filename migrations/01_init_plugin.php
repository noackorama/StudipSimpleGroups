<?php
class InitPlugin extends DBMigration
{
    function up()
    {
        DBManager::get()->exec("INSERT IGNORE INTO `datafields` (`datafield_id`, `name`, `object_type`, `object_class`, `edit_perms`, `view_perms`, `priority`, `mkdate`, `chdate`, `type`, `typeparam`, `is_required`, `description`) VALUES
(MD5('StudipSimpleGroupsUser'), 'Gruppen', 'user', '14', 'root', 'root', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'textline', '', 0, ''),
(MD5('StudipSimpleGroupsCourse'), 'Gruppen', 'sem', NULL, 'root', 'root', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 'textline', '', 0, '');
");
    }
}