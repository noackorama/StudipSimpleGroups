<?php
require "application.php";

class ShowController extends ApplicationController
{

    function before_filter($action, $args)
    {
        parent::before_filter($action, $args);
        if (!$GLOBALS['perm']->have_perm('root')) {
            throw new AccessDeniedException('Kein Zugriff');
        }

    }


    function index_action()
    {
        $this->all_groups = $this->plugin->findAllGroups();
        ksort($this->all_groups);
    }

    function details_action($group)
    {
        $user_ids = $this->plugin->findUsersByGroupName($group);
        if ($user_ids) {
            $this->users = User::findMany($user_ids, 'ORDER BY nachname');
        }
        $course_ids = $this->plugin->findCoursesByGroupName($group);
        if ($course_ids) {
            $this->courses = Course::findMany($course_ids, 'ORDER BY Name');
        }
        $this->group = $group;
    }
}

