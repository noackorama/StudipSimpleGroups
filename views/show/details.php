<?=\Studip\LinkButton::create('<< Zurück', $this->controller->url_for('show'))?>
<h1>Gruppe: <?=$group?></h1>
<? if (count($users)) :?>
    <h3>Nutzer</h3>
    <table class="zebra-hover">
    <thead>
        <tr>
        <th>Nutzername</th>
        <th>Vorname</th>
        <th>Nachname</th>
        <th>Status</th>
        <th>Gruppen</th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($users as $user) : ?>
        <tr>
        <td><a href="<?=UrlHelper::getLink('dispatch.php/admin/user/edit/' . $user->id)?>"><?=htmlready($user->username)?></a></td>
        <td><?=htmlready($user->vorname)?></td>
        <td><?=htmlready($user->nachname)?></td>
        <td><?=htmlready($user->perms)?></td>
        <td><?=htmlready($user->datafields->findBy('datafield_id', StudipSimpleGroups::$datafield_user)->val('content'))?></td>
        </tr>
    <? endforeach; ?>
    </tbody>
    </table>
<? endif; ?>
<? if (count($courses)) :?>
    <h3>Veranstaltungen</h3>
    <table class="zebra-hover">
    <thead>
        <tr>
        <th>Name</th>
        <th>Dozenten</th>
        <th>Typ</th>
        <th>Semester</th>
        <th>Gruppen</th>
        </tr>
    </thead>
    <tbody>
    <? foreach ($courses as $course) : ?>
        <tr>
        <td><a href="<?=UrlHelper::getLink('dispatch.php/course/basicdata/view/' . $course->id)?>"><?=htmlready($course->name)?></a></td>
        <td><?=htmlready(join('; ', array_slice($course->members->findBy('status','dozent')->pluck('nachname'),0,3)))?></td>
        <td><?=htmlready($GLOBALS['SEM_CLASS'][$GLOBALS['SEM_TYPE'][$course->status]["class"]]["name"] . ':' . $GLOBALS['SEM_TYPE'][$course->status]["name"])?></td>
        <td><?=htmlready($course->start_semester->name)?></td>
        <td><?=htmlready($course->datafields->findBy('datafield_id', StudipSimpleGroups::$datafield_course)->val('content'))?></td>
        </tr>
    <? endforeach; ?>
    </tbody>
    </table>
<? endif; ?>
