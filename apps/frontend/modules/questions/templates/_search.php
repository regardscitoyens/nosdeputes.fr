<?php use_helper('Text') ?>
  <div class="question" id="<?php echo $question->id; ?>">
    <div class="info">
    <strong>  
    <?php 
    echo $question['titre']; ?>
    </strong>
 <?php echo '<span class="source"><a href="'.$question->getSource().'">source</a>'; ?>
    </div>
    <div class="texte_question"><?php 
$inter = preg_replace('/<\/?p>|\&[^\;]+\;/i', ' ', $question->getQuestion().' '.$question->getReponse().' Thèmes : '.$question->getThemes());
$p_inter = '';
if (isset($highlight)) {
  foreach ($highlight as $h) {
    $p_inter .= excerpt_text($inter, $h, 400/count($highlight));
  }
  foreach ($highlight as $h) {
    if (!preg_match('/'.$h.'/', 'strong class="highlight"/'))
      $p_inter = highlight_text($p_inter, $h);
  }
}
echo $p_inter;
?>
    </div>
  </div>
