<table class="sorts_amendements">
  <tr><th>Proposés</th><th/><th>Signés</th></tr>
  <?php foreach ($amendements["proposes"] as $key => $val)
    if ($key == "Total" || $key == "adoptés" || $amendements["proposes"][$key] || $amendements["signes"][$key])
      echo "<tr".($key == "Total" ? ' class="total_sorts"' : "")."><td>".$amendements["proposes"][$key].'</td><td class="titre_sort">'.$key."</td><td>".$amendements["signes"][$key]."</td></tr>";
  ?>
</table>
