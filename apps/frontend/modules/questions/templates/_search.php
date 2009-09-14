<?php use_helper('Text') ?>
  <div class="question" id="<?php echo $question->id; ?>">
  <h2><?php echo link_to($question['titre'], '@question?id='.$question->id); ?></h2>
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
 }else{
  $p_inter = truncate_text($inter, 400);
 }
echo $p_inter;
?>
<div><a href="<?php echo url_for('@question?id='.$question->id); ?>">Lire la suite de la question</a></div>
    </div>
  </div>
