<?php

/**
 * captcha actions.
 *
 * @package    cpc
 * @subpackage captcha
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class captchaActions extends sfActions
{
  
  /* CAPTCHA image */
  
  public function executeGetcaptcha()
  {
    header("Content-type: image/png");
    echo self::captcha();
    return sfView::NONE;
  }
  
  private function nombre($n)
  {
    return str_pad(mt_rand(0,pow(10,$n)-1),$n,'0',STR_PAD_BOTH);
  }
  
  private function image($mot)
  {
    $size = 30;
    $marge = 10;
  
  /* Polices disponibles : GorriSans.ttf, CHOPS___.TTF, VeraSeBd.ttf, VeraSe.ttf, LiberationSerif-Regular.ttf,
  LiberationSerif-Italic.ttf, LiberationSerif-BoldItalic.ttf, LiberationSerif-Italic.ttf */
    $font = 'zencaptcha/fonts/LiberationSerif-Regular.ttf';
  
    $box = imagettfbbox($size, 0, $font, $mot);
    $largeur = $box[2] - $box[0];
    $hauteur = $box[1] - $box[7];
    $largeur_lettre = round($largeur/strlen($mot));
    
    $img = imagecreate($largeur+$marge, $hauteur+$marge);
    $blanc = imagecolorallocate($img, 255, 255, 255); 
    $noir = imagecolorallocate($img, 0, 0, 0);
  
  // Couleur des hachures
    $hachures = imagecolorallocate($img, 167, 166, 170);
  
    // Le fond hachuré
  for($x = 6; $x < $largeur+$marge; $x+=6)
  {
    imageline($img, $x,0,$x,$hauteur+$marge,$hachures);
  }
  for($y = 6; $y < $largeur+$marge; $y+=6)
  {
    imageline($img, 0,$y, $largeur+$marge, $y, $hachures);
  }
  
  // Liste des couleurs aléatoires
    $couleur = array(
        imagecolorallocate($img, 159, 135, 62),
        imagecolorallocate($img, 0, 0, 0),
        imagecolorallocate($img, 143, 142, 139),
        imagecolorallocate($img, 111, 43, 68),
        imagecolorallocate($img, 159, 101, 101));

    for($i = 0; $i < strlen($mot);++$i)
    {
    $l = $mot[$i];
    // Angle des lettres
      $angle = mt_rand(-15,15);
      imagettftext($img,mt_rand($size-7,$size),$angle,($i*$largeur_lettre)+$marge, $hauteur+mt_rand(0,$marge/2),$couleur[array_rand($couleur)], $font, $l);    
    }
    
  // Lignes avec angle random
    imageline($img, 0,mt_rand(2,$hauteur), $largeur+$marge, mt_rand(2,$hauteur), $noir);
    imageline($img, 0,mt_rand(2,$hauteur), $largeur+$marge, mt_rand(2,$hauteur), $noir);
  /* 
  // Matrice du flou gaussien
    $matrix_blur = array(
    array(1,2,1),
    array(2,4,2),
    array(1,2,1));
  
  // Flou gaussien
    imageconvolution($img, $matrix_blur,16,0);
    imageconvolution($img, $matrix_blur,16,0);
     */
    imagepng($img);
    imagedestroy($img);
  }

  private function captcha()
  {
    $mot = self::nombre(5);
    $this->getUser()->setAttribute('codesecu', $mot);
    self::image($mot);
  }
  
  /* CAPTCHA sonore */
  
  public function executeGetcaptchasonore(sfWebRequest $request)
  {
    header('Content-type: audio/x-wav');
    header('Content-Disposition: attachment; filename="codesecu.wav"');
    echo self::toWav($this->getUser()->getAttribute('codesecu'));
    return sfView::NONE;
  }
  
  // Read functions
  
  function isWav($file)
  {
    if(is_file($file) && is_readable($file))
    {
      $res = fopen($file, 'rb');
      $data = fread($res, 16);
      $h = unpack('H8riff/Vfile_size/H8wave/H8fmt',$data);
      
      if($h['riff'] === '52494646' &&
         $h['file_size'] === filesize($file) - 8 &&
         $h['wave'] === '57415645' &&
         $h['fmt'] === '666d7420')
      {
        fseek($res, 36); // position au bloc data
        
        $data = fread($res,8);
        fclose($res);
        
        $h = unpack('H8data/Vdata_size',$data);
        
        if($h['data'] === '64617461' && $h['data_size'] === filesize($file) - 44)
          return true;
        else
          return false;
      }
      else {
        fclose($res);
        return false;
      }
    }
    else {
        return false;
    }
  }
    
  function getHeader($file)
  {
    if(self::isWav($file))
    {
      $res = fopen($file,'rb');
      $data = fread($res, 44);
      fclose($res);
      
      // Riff chunk descriptor
      $entete_unpack = 'H8FileTypeBlocID/VFileSize/H8FileFormatID';
      // Sub Chunck fmt
      $entete_unpack .='/H8FormatBlocID/VBlocSize/vAudioFormat/vNbrCanaux/VFrequence/VBytePerSec/vBytePerBloc';
      $entete_unpack .='/vBitsPerSample';
      // Sub Chunck data
      $entete_unpack .='/H8DataBlocID/VDataSize';
      return unpack($entete_unpack,$data);
    }
    else {
      return false;
    }
  }
  
  function getData($file)
  {
    if(self::isWav($file))
    {
      $res = fopen($file, 'rb');
      fseek($res, 44);
      return fread($res, filesize($file) - 44);
    }
    else {
      return false;
    }
  }
  
  // Write functions
  
  function listWav($mot)
  {
    $l = strlen($mot);
    $list = array();
    for($i = 0; $i < $l; ++$i)
    {
      $list[] = 'zencaptcha/sons/'.$mot[$i].'.wav';
    }
    return $list;
  }
  
  function toWav($mot)
  {
    $list = self::listWav($mot);
    $datas = '';
    $nbFiles = count($list);
    
    if($nbFiles > 0)
    {
      if($nbFiles === 1)
      return file_get_contents(current($list));
      
      $infos = self::getHeader(current($list));
      for($i = 1; $i < $nbFiles; ++$i)
      {
        $h = self::getHeader($list[$i]);
        if($h['AudioFormat'] !== $infos['AudioFormat'])
          die('AudioFormat in '.$list[$i].' different');
        if($h['NbrCanaux'] !== $infos['NbrCanaux'])
          die('NbrCanaux in '.$list[$i].' different');
        if($h['Frequence'] !== $infos['Frequence'])
          die('Frequence in '.$list[$i].' different');
        if($h['BytePerSec'] !== $infos['BytePerSec'])
          die('BytePerSec in '.$list[$i].' different');
        if($h['BytePerBloc'] !== $infos['BytePerBloc'])
          die('BytePerBloc in '.$list[$i].' different');
        if($h['BitsPerSample'] !== $infos['BitsPerSample'])
          die('BitsPerSample in '.$list[$i].' different');
        
        foreach($list as $file)
        {
          $datas .= self::getData($file);
        }
        
        $datasize = strlen($datas);
        $filesize = 36 + $datasize; 
        $entete_pack = 'H8VH8H8VvvVVvvH8V';
        
        $file = pack($entete_pack, 
            $infos['FileTypeBlocID'], $filesize,$infos['FileFormatID'],
            $infos['FormatBlocID'], $infos['BlocSize'], $infos['AudioFormat'],
            $infos['NbrCanaux'], $infos['Frequence'], $infos['BytePerSec'],
            $infos['BytePerBloc'],$infos['BitsPerSample'],$infos['DataBlocID'],
            $datasize) . $datas;
        
        return $file;
      }
    }
    else {
      return false;
    }
  }
}
