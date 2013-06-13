<?php

class StudipSimpleGroups extends StudIPPlugin implements SystemPlugin {

    public static $config = array();
    public static $datafield_user;
    public static $datafield_course;

    function __construct()
    {
        parent::__construct();
        $this->restoreConfig();
        $this->me = strtolower(__CLASS__);
        self::$datafield_user = MD5('StudipSimpleGroupsUser');
        self::$datafield_course = MD5('StudipSimpleGroupsCourse');
        // set up tab navigation
        if ($GLOBALS['perm']->have_perm('root')) {
            $navigation = new Navigation($this->getDisplaytitle());
            $navigation->setURL(PluginEngine::getURL("$this->me/show"));
            Navigation::addItem('/start/'.$this->me, $navigation);
        }

        NotificationCenter::addObserver($this, 'modifyGroupEntries', 'DatafieldDidUpdate');
    }

    /**
    * gets called on every store of a datafields value
    *
    * @param string $event name of event
    * @param DataFieldEntry $datafield datafield object
    * @param array $data assoc array with keys 'old_value' and 'changed'
    */
    function modifyGroupEntries($event, $datafield, $data)
    {
        $object_id = $datafield->getRangeId();
        if ($object_id
            && is_string($object_id)
            && in_array($datafield->getId(), array(self::$datafield_user, self::$datafield_course))
            && $data['old_value'] != $datafield->getValue()) {
            $values = array_unique(array_map('trim', preg_split('/[\s,]+/', strtolower($datafield->getValue()), -1, PREG_SPLIT_NO_EMPTY)));
            $st = DBManager::get()->prepare("UPDATE datafields_entries SET content = ? "
            . "WHERE datafield_id = ? AND range_id = ? AND sec_range_id = ?");
            $st->execute(array(join(',', $values), $datafield->getID(), (string)$datafield->getRangeID() , (string)$datafield->getSecondRangeID()));
            $type = get_object_type($object_id, array('sem','user'));
            if ($type === 'sem') $this->addCourseToGroupUsers($object_id, $values);
            if ($type === 'user') $this->addUserToGroupCourses($object_id, $values);
        }
    }

    function findCoursesByGroupName($group)
    {
        return $this->findByGroupName(self::$datafield_course, $group);
    }
    function findUsersByGroupName($group)
    {
        return $this->findByGroupName(self::$datafield_user, $group);
    }

    function findByGroupName($datafield_id, $group)
    {
        $st = DBManager::get()->prepare("SELECT range_id FROM  datafields_entries "
            . "WHERE datafield_id = ? AND
            (content LIKE ?
            OR content LIKE ?
            OR content LIKE ?
            OR content LIKE ?)");
        $st->execute(array($datafield_id, $group.',%', '%,'.$group,$group,'%,'.$group.',%'));
        return $st->fetchAll(PDO::FETCH_COLUMN);
    }

    function addUserToGroupCourses($user_id, $groups)
    {
        foreach ($groups as $group) {
            foreach($this->findCoursesByGroupName($group) as $course_id) {
                try {
                    $course = new Seminar($course_id);
                    $course->addMember($user_id, 'autor');
                } catch (Exception $e) {}
            }
        }
    }

    function addCourseToGroupUsers($course_id, $groups)
    {
        try {
            $course = new Seminar($course_id);
        } catch (Exception $e) { }
        if ($course) {
            foreach ($groups as $group) {
                foreach($this->findUsersByGroupName($group) as $user_id) {
                    $course->addMember($user_id, 'autor');
                }
            }
        }
    }

    function findAllGroups()
    {
        $groups = array();
        $st = DBManager::get()->prepare("SELECT content FROM  datafields_entries "
            . "WHERE datafield_id = ?");
        $st->execute(array(self::$datafield_user));
        while ($content = $st->fetch(PDO::FETCH_COLUMN)) {
            foreach (array_unique(array_map('trim', preg_split('/[\s,]+/', strtolower($content), -1, PREG_SPLIT_NO_EMPTY))) as $group) {
                $groups[$group]['user']++;
            }

        }
        $st->execute(array(self::$datafield_course));
        while ($content = $st->fetch(PDO::FETCH_COLUMN)) {
            foreach (array_unique(array_map('trim', preg_split('/[\s,]+/', strtolower($content), -1, PREG_SPLIT_NO_EMPTY))) as $group) {
                $groups[$group]['sem']++;
            }

        }
        return $groups;
    }

    function restoreConfig()
    {
        $config = DBManager::get()
        ->query("SELECT comment FROM config WHERE field = 'CONFIG_" . $this->getPluginName() . "' AND is_default=1")
        ->fetchColumn();
        self::$config = unserialize($config);
        return self::$config != false;
    }

    function storeConfig()
    {
        $config = serialize(self::$config);
        $field = "CONFIG_" . $this->getPluginName();
        $st = DBManager::get()
        ->prepare("REPLACE INTO config (config_id, field, value, is_default, type, range, chdate, comment)
            VALUES (?,?,'do not edit',1,'string','global',UNIX_TIMESTAMP(),?)");
        return $st->execute(array(md5($field), $field, $config));
    }

    function getDisplayTitle()
    {
        return _("Gruppenverwaltung");
    }

    /**
    * This method dispatches and displays all actions. It uses the template
    * method design pattern, so you may want to implement the methods #route
    * and/or #display to adapt to your needs.
    *
    * @param  string  the part of the dispatch path, that were not consumed yet
    *
    * @return void
    */
    function perform($unconsumed_path)
    {
        if(!$unconsumed_path){
            header("Location: " . PluginEngine::getUrl($this), 302);
            return false;
        }
        $trails_root = $this->getPluginPath();
        $dispatcher = new Trails_Dispatcher($trails_root, null, 'show');
        $dispatcher->current_plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }
}
