<table>
<tr><th></th>
<?php 
$last = end($tops); $i = 0; foreach(array_keys($last[0]->getTop()) as $key) { $i++ ;?>
  <th><a href="?sort=<?php echo $i; ?>"><?php echo preg_replace('/_/', ' ', $key); ?></a></th>
<?php } ?></tr>
<?php foreach($tops as $t) { ?>
<tr><td>
<img src="<?php echo url_for('@photo_parlementaire?slug='.$t[0]->slug);?>/30" width='23' height='30'/><br/>
<? echo link_to($t[0]->nom, '@parlementaire?slug='.$t[0]->slug); ?></td>
<?php for($i = 1 ; $i < count($t) ; $i++) { ?>
     <td<?php echo $t[$i]['style']; ?>><?php 
     if (preg_match('/\./', $t[$i]['value'])) {
       printf('%02d', $t[$i]['value']);
     }else{
       echo $t[$i]['value']; 
     }
?></td>
<?php } ?>
<?php } ?>
</table>