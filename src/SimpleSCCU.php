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

			$header = unpack('a4app/Nlength/N2/Ncount/a', fread($fp, 21));
			if ($header['app'] != 'SCCU') {
        throw new SimpleSCCUException('Invalid app mark', SimpleSCCUException::ERROR_FORMAT_UNKNOWN);
			}

			do {
				fseek($fp, -1, SEEK_CUR);
				$fhead = unpack('a2mark/Nlength/na/Nb', fread($fp, 12));

				$key = '';
				while ("\x00" != $c = fread($fp, 1)) {
					$key .= $c;
				}

				$value = '';
				while ("\x00" != $c = fread($fp, 1)) {
					$value .= $c;
				}

				$fields[$key] = $value;

				while ("\x00" == fread($fp, 1));
			} while (!feof($fp));
			fclose($fp);

			return $fields;
		}

    public static function pack (array $fields) : string {
      $sccu = pack('N*', 0x10000, 0, count($fields));

      foreach ($fields as $key => $value) {
        $mark = (strlen($key) < 10) ? "\x00\x0C" : "\x00\x16";
        $mark .= "\x00\x00\x00\x00";

        $lenght = strlen($key . $value) + 13;
        $end = 2 - ($lenght % 2);
        $lenght += $end;

        $sccu .= '[[' . pack('N*', $lenght) . $mark;
        $sccu .= $key . "\x00";
        $sccu .= $value . str_repeat("\x00", $end);
      }

      $sccu = 'SCCU' . pack('N*', strlen($sccu) + 8) . $sccu;

      return $sccu;
    }
	}
