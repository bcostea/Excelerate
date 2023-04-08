<?php

namespace XLSXWriter;

class BuffererWriter {
  protected $fileDescriptor = null;
  protected $buffer = '';
  protected $check_utf8 = false;

  public function __construct($filename, $fd_fopen_flags = 'w', $check_utf8 = false) {
    $this->check_utf8 = $check_utf8;
    $this->fileDescriptor = fopen($filename, $fd_fopen_flags);
    if ($this->fileDescriptor === false) {
      $this->log("Unable to open $filename for writing.");
    }
  }

  public function write($string) {
    $this->buffer .= $string;
    if (isset($this->buffer[8191])) {
      $this->purge();
    }
  }

  protected function purge() {
    if ($this->fileDescriptor) {
      if ($this->check_utf8 && !$this->isValidUTF8($this->buffer)) {
        $this->log("Error, invalid UTF8 encoding detected.");
        $this->check_utf8 = false;
      }
      fwrite($this->fileDescriptor, $this->buffer);
      $this->buffer = '';
    }
  }

  public function close() {
    $this->purge();
    if ($this->fileDescriptor) {
      fclose($this->fileDescriptor);
      $this->fileDescriptor = null;
    }
  }

  public function __destruct() {
    $this->close();
  }

  public function ftell() {
    if ($this->fileDescriptor) {
      $this->purge();
      return ftell($this->fileDescriptor);
    }
    return -1;
  }

  public function fseek($pos) {
    if ($this->fileDescriptor) {
      $this->purge();
      return fseek($this->fileDescriptor, $pos);
    }
    return -1;
  }

  protected static function isValidUTF8($string) {
    if (function_exists('mb_check_encoding')) {
      return mb_check_encoding($string, 'UTF-8') ? true : false;
    }
    return preg_match("//u", $string) ? true : false;
  }

  public function log($string) {
    error_log(date("Y-m-d H:i:s:") . rtrim(is_array($string) ? json_encode($string) : $string) . "\n");
  }
}
