<?php

declare(strict_types=1);

namespace XLSXWriter;

class BuffererWriter {

  /**
   * @var resource|false|null
   */
  protected $fileDescriptor = null;

  protected string $buffer = '';
  protected bool $checkUtf8 = false;

  /**
   * BuffererWriter constructor.
   * @param string $filename
   * @param string $fdFopenFlags
   * @param bool $checkUtf8
   */
  public function __construct(string $filename, string $fdFopenFlags = 'w', bool $checkUtf8 = false) {
    $this->checkUtf8 = $checkUtf8;
    $this->fileDescriptor = fopen($filename, $fdFopenFlags);
    if ($this->fileDescriptor === false) {
      $this->log("Unable to open $filename for writing.");
    }
  }

  /**
   * Write a string to the buffer
   * @param string $stringValue
   */
  public function write($stringValue): void {
    $this->buffer .= $stringValue;
    if (isset($this->buffer[8191])) {
      $this->purge();
    }
  }

  /**
   * Purge the buffer to the file
   */
  protected function purge(): void {
    if (!$this->fileDescriptor) {
      return;
    }
    if ($this->checkUtf8 && !$this->isValidUTF8($this->buffer)) {
      $this->log("Error, invalid UTF8 encoding detected.");
      $this->checkUtf8 = false;
    }
    fwrite($this->fileDescriptor, $this->buffer);
    $this->buffer = '';
  }

  /**
   * Close the file
   */
  public function close(): void {
    $this->purge();

    if (!$this->fileDescriptor) {
      return;
    }

    fclose($this->fileDescriptor);
    $this->fileDescriptor = null;
  }

  /**
   * Destructor
   */
  public function __destruct() {
    $this->close();
  }

  /**
   * Get the current position in the file
   * @return int|false
   */
  public function ftell() {
    if (!$this->fileDescriptor) {
      return -1;
    }
    $this->purge();
    return ftell($this->fileDescriptor);
  }

  /**
   * Seek to a position in the file
   * @param int $pos
   * @return int|false
   */
  public function fseek(int $pos) {
    if ($this->fileDescriptor) {
      $this->purge();
      return fseek($this->fileDescriptor, $pos);
    }
    return -1;
  }

  /**
   * Check if the string is valid UTF8
   * @param string $string
   * @return bool
   */
  protected static function isValidUTF8($string) {
    if (function_exists('mb_check_encoding')) {
      return mb_check_encoding($string, 'UTF-8') ? true : false;
    }
    return preg_match("//u", $string) ? true : false;
  }

  /**
   * Log a message
   * @param string|array<string> $message
   */
  public function log($message): void {
    error_log(date("Y-m-d H:i:s:") . rtrim(is_array($message) ? json_encode($message) : $message) . "\n");
  }
}
