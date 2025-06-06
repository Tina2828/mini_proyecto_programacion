<?php

class Log {
  static public function info(mixed $str){
    $output = new LogOutput();
    $str = $output->convertToString($str);
    $output->output("INFO: {$str}");
  }

  static public function warning(mixed $str){
    $output = new LogOutput();
    $str = $output->convertToString($str);
    $output->output("\033[33mWARNING:\033[0m {$str}");
  }

  static public function debug(mixed $str){
    $output = new logoutput();
    $str = $output->converttostring($str);
    $output->output("\033[34mDEBUG:\033[0m {$str}");
  }

  static public function error(mixed $str){
    $output = new logoutput();
    $str = $output->converttostring($str);
    $output->error("{$str}");
  }
}

class LogOutput {
  public function output(string $str){
    $date_str = date('Y-m-d H:i:s.z');
    $str = "{$date_str} - {$str}". PHP_EOL;
    file_put_contents('php://stdout',$str,FILE_APPEND);
  }

  public function error(string $str){
    $date_str = date('Y-m-d H:i:s.z');
    $str = "{$date_str} - ERROR: {$str}". PHP_EOL;
    file_put_contents('php://stderr',$str,FILE_APPEND);
  }

  public function convertToString(mixed $str): string {
    if (is_array($str) || is_object($str)) {
      return print_r($str, true);
    }
    return (string)$str;
  }
}
