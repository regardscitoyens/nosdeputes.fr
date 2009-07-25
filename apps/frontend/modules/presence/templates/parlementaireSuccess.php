<? foreach($presences as $presence) : ?>
<? $p = $presence->toArray(true); ?>
<? $s = $p['Seance'];?>
<? $o = $s['Organisme'];?>
<div>
<div class='seance'>
<? echo $s['type']; ?> : <? echo $o['nom']; ?> (<a href="<? echo url_for('@interventions_seance?seance='.$s['id']); ?>"><? echo $s['date']; ?>, <? echo $s['moment']; ?></a>)
</div>
<div class="preuves">
<em>Preuves :</em>
<ul>
<? $pr = $p['Preuves'];?>
<? foreach($pr as $preuve) : ?>
    <li><? echo link_to($preuve['type'], $preuve['source']); ?></li>
<? endforeach; ?>
</ul>
<br/>
</div>
</div>
<? endforeach; ?>