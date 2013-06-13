<h1>Vorhandene Gruppen</h1>
<table class="zebra-hover">
<thead>
    <tr>
    <th>Gruppenname</th>
    <th>Anzahl Nutzer</th>
    <th>Anzahl Veranstaltungen</th>
    </tr>
</thead>
<tbody>
<? foreach ($all_groups as $group => $count) : ?>
    <tr>
    <td><a href="<?=$this->controller->link_for('show/details/' . $group)?>"><?=htmlready($group)?></a></td>
    <td><?=(int)$count['user']?></td>
    <td><?=(int)$count['sem']?></td>
    </tr>
<? endforeach ?>
</tbody>
</table>

