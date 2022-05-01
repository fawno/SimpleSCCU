<?php
  /**
   * SimpleIPTC: IPTC convenience class (https://github.com/fawno/SimpleIPTC)
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

  use SimpleSCCU\SimpleSCCUException;

	class SimpleSCCU {
		public static function unpack (string $bin) : array {
			$fields = [];

			$fp = fopen('php://memory','rb+');
			fwrite ($fp, $bin);
			rewind($fp);

			$header = unpack('a4app/Nlength/nver/x6/Ncount', fread($fp, 20));
			if ($header['app'] != 'SCCU') {
        throw new SimpleSCCUException('Invalid app mark', SimpleSCCUException::ERROR_FORMAT_UNKNOWN);
			}

			for ($i = $header['count']; $i; $i--) {
				$fheader = unpack('a2start/Nlength/nmark/x4', fread($fp, 12));

				$string = fread($fp, $fheader['length'] - 12);

				$key = current(unpack('Z*', $string));
				$field = unpack(sprintf('Z%ukey/x/Z*value', strlen($key)), $string);

				$fields[$key] = $field['value'];
			}

			fclose($fp);

			return $fields;
		}

    public static function pack (array $fields) : string {
      $sccu = pack('nx6N', 1, count($fields));

      foreach ($fields as $key => $value) {
        $mark = (strlen($key) < 10) ? 0x0C : 0x16;

        $end = strlen($key . $value) % 2;
        $lenght = strlen($key . $value) + $end + 14;

        $sccu .= '[[' . pack('Nnx4Z*Z*x' . $end, $lenght, $mark, $key, $value);
      }

      $sccu = 'SCCU' . pack('N*', strlen($sccu) + 8) . $sccu;

      return $sccu;
    }
	}
