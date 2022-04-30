<?php
  /**
   * SimpleSCCU: SCCU convenience class (https://github.com/fawno/SimpleSCCU)
   * Copyright (c) Fernando Herrero (https://github.com/alpha)
   *
   * Licensed under The MIT License
   * For full copyright and license information, please see the LICENSE
   * Redistributions of files must retain the above copyright notice.
   *
   * @copyright     Fernando Herrero (https://github.com/alpha)
   * @link          https://github.com/fawno/SimpleSCCU SimpleSCCU
   * @since         0.0.1
   * @license       https://opensource.org/licenses/mit-license.php MIT License
   */
  declare(strict_types=1);

  namespace SimpleSCCU;

  use \Throwable;
  use \Exception;

  /**
   * Provides custom exception class.
   *
   * @package SimpleSCCU
   */
  class SimpleSCCUException extends Exception {
    public const ERROR_INDETERMINATE   = 0;
    public const ERROR_FORMAT_UNKNOWN  = 1;

    protected int $severity;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     *
     * @param string $message
     * [optional] The Exception message to throw.
     *
     * @param int $code
     * [optional] The Exception code.
     *
     * @param int $severity
     * [optional] The Exception severity.
     * Note:
     * While the severity can be any int value, it is intended that the error constants be used.
     *
     * @param string|null $filename
     * [optional] The filename where the exception is thrown.
     *
     * @param int|null $line
     * [optional] The line number where the exception is thrown.
     *
     * @param Throwable|null $previous
     * [optional] The previous throwable used for the exception chaining.
     *
     * @return void
     */
    public function __construct (string $message = '', int $code = self::ERROR_INDETERMINATE, int $severity = E_ERROR, string $filename = null, int $line = null , Throwable $previous = null) {
      $this->severity = $severity;

      if (!is_null($filename)) {
        $this->filename = $filename;
      }

      if (!is_null($line)) {
        $this->line = $line;
      }

      parent::__construct($message, $code, $previous);
    }

    /**
     * Gets the exception severity
     *
     * @return int
     * Returns the severity of the exception.
     */
    public function getSeverity () : int {
      return $this->severity;
    }
  }
