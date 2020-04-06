<?php
  class ID {

    public static function generate ($maxLength = 5) {

      $output = '';
      $charMap = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charMapLength = strlen($charMap);

      for ($i = 0; $i < $maxLength; $i++) {
          $output .= $charMap[rand(0, $charMapLength - 1)];
      }

      return $output;

    }

  }
?>