<?php
  function randStrGen($len){
    $result = '';
    $chars = '01234567890abcdefghijklmnopqrstuvwxyz01234567890';
    $charArray = str_split($chars);
    for($i = 0; $i < $len; $i++){
      $randIndex = array_rand($charArray);
      $result .= '' . $charArray[$randIndex];
    }
    return $result;
  }
?>
