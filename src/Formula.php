<?php

namespace Excelerate;

use InvalidArgumentException;

/**
 * A cell value that XLSXWriter writes as a formula.
 * A plain string is always written as text, also when it starts with "=".
 *
 * Simple formula: new Formula('=D2*0.05')
 * Array formula:  new Formula('{=SUM(A1:A2)}')
 *
 * @license MIT License
 **/
final class Formula {

  private string $expression;

  public function __construct(string $expression) {
    $isSimple = str_starts_with($expression, '=') && strlen($expression) > 1;
    $isArray = str_starts_with($expression, '{=') && str_ends_with($expression, '}') && strlen($expression) > 3;
    if (!$isSimple && !$isArray) {
      throw new InvalidArgumentException('A formula must have the form "=..." or "{=...}".');
    }
    $this->expression = $expression;
  }

  public function getExpression(): string {
    return $this->expression;
  }

  public function isArray(): bool {
    return $this->expression[0] === '{';
  }

  /**
   * The formula text as SpreadsheetML stores it: "=..." for a simple formula,
   * and the text between "{=" and "}" for an array formula.
   */
  public function getBody(): string {
    return $this->isArray() ? substr($this->expression, 2, -1) : $this->expression;
  }
}
