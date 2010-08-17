<?php

/**
 * CSV I/O
 *
 * Use with find('all') and saveAll() to quickly create fixtures
 */
class CsvIoComponent extends SlComponent {

    public function convert($from, $to, $data, $options = array()) {
        $function1 = Inflector::variable("{$from}_to_array");
        $function2 = Inflector::variable("array_to_{$to}");
        if (method_exists($this, $function1) && method_exists($this, $function2)) {
            return $this->$function2($this->$function1($data, $options), $options);
        }
    }

    protected function arrayToArray($data) {
        return $data;
    }

    public function arrayToCsvFile($array, $options) {
        $options += array(
            'filename' => CONFIGS . 'schema/fixtures.csv',
            'output' => empty($options['filename']),
        );

        $text = $this->arrayToCsv($array, $options);
        
        if ($options['output']) {
            $filename = basename($options['filename']);
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            echo $text;
        } else {
            file_put_contents($options['filename'], $text);
        }
    }

    public function xlsFileToArray($filename, $options = array()) {
        $options += array(
        );

        App::import('vendor', 'php-excel-reader/excel_reader2');

        $xls = new Spreadsheet_Excel_Reader($filename);

        foreach ($xls->sheets[0]['cells'] as $row) {
            if (empty($array)) {
                $array[0] = $row;
                continue;
            }
            
            $line = array();
            $empty = true;
            foreach ($array[0] as $col => $field) {
                if (isset($row[$col])) {
                    $line[$col] = $row[$col];
                    $empty = false;
                }
            }
            if (!$empty) {
                $array[] = $line;
            }
        }
        return $array;
    }

    public function cakeArrayToArray($data, $options = array()) {
        $options += array(
            'fields' => array(), // empty to include all
            'models' => array(), // empty for all
        );
        $options['fields'] = Set::normalize($options['fields'], false);
        $options['models'] = Set::normalize($options['models'], false);
        $options += array(
            'defaultModel' => count($options['models']) == 1 ? $options['models'][0] : null,
        );

        $firstRow = true;
        $array = array();
        foreach ($data as $item) {
            
            foreach ($item as $model => $fields) {
                if ($options['models'] && !in_array($model, $options['models'])) {
                    continue;
                }
                foreach ($fields as $field => $value) {
                    if ($options['fields'] && !in_array($field, $options['fields']) && !in_array("$model.$field", $options['fields'])) {
                        continue;
                    }
                    if ($firstRow) {
                        if ($model == $options['defaultModel']) {
                            $array[0][] = $field;
                        } else {
                            $array[0][] = "$model.$field";
                        }
                    }
                    $row[] = $value;
                }
                $array[] = $row;
            }

            $firstRow = false;
        }
        return $array;
    }

    public function arrayToCakeArray($array, $options = array()) {
        $options += array(
            'defaultModel' => null,
        );
        $columns = array_shift($array);

        $data = array();
        foreach ($array as $i => $row) {
            foreach ($row as $j => $cell) {
                if (strpos($columns[$j], '.')) {
                    list($model, $field) = explode($columns[$j]);
                } else {
                    $model = $options['dafaultModel'];
                    $field = $columns[$j];
                }
                if ($model) {
                    $data[$i][$model][$field] = $cell;
                } else {
                    $data[$i][$field] = $cell;
                }
            }
        }
        return $data;
    }

    public function arrayToCsv($array, $options = array()) {
        $csv = array();
        foreach ($array as $item) {
            if (is_array(($item))) {
                $csv[] = $this->_arrayToCsvLine($items, $options);
            }
        }
        return implode('', $csv);
    }

    protected function _arrayToCsvLine($items, $options = array()) {
        $options += array(
            'CSV_SEPARATOR' => ';',
            'CSV_ENCLOSURE' => '"',
            'CSV_LINEBREAK' => "\n",
        );
        extract($options);

        $string = '';
        $o = array();

        foreach ($items as $item) {
            if (stripos($item, $CSV_ENCLOSURE) !== false) {
                $item = str_replace($CSV_ENCLOSURE, $CSV_ENCLOSURE . $CSV_ENCLOSURE, $item);
            }

            if ((stripos($item, $CSV_SEPARATOR) !== false)
             || (stripos($item, $CSV_ENCLOSURE) !== false)
             || (stripos($item, $CSV_LINEBREAK !== false))) {
                $item = $CSV_ENCLOSURE . $item . $CSV_ENCLOSURE;
            }

            $o[] = $item;
        }

        $string = implode($CSV_SEPARATOR, $o) . $CSV_LINEBREAK;

        return $string;
    }

    public function csvToArray($csv, $options = array()) {
        $array = array();
        while ($csv) {
            $array[] = $this->_csvLineToArray($csv, $options);
        }
        return $array;
    }

    protected function _csvLineToArray(&$string, $options = array()) {
        $options += array(
            'CSV_SEPARATOR' => ';',
            'CSV_ENCLOSURE' => '"',
            'CSV_LINEBREAK' => "\n",
        );
        extract($options);

        $o = array();

        $cnt = strlen($string);
        $esc = false;
        $escesc = false;
        $num = 0;
        $i = 0;
        while ($i < $cnt) {
            $s = $string[$i];

            if ($s == $CSV_LINEBREAK) {
              if ($esc) {
                $o[$num] .= $s;
              } else {
                $i++;
                break;
              }
            } elseif ($s == $CSV_SEPARATOR) {
              if ($esc) {
                $o[$num] .= $s;
              } else {
                $num++;
                $esc = false;
                $escesc = false;
              }
            } elseif ($s == $CSV_ENCLOSURE) {
              if ($escesc) {
                $o[$num] .= $CSV_ENCLOSURE;
                $escesc = false;
              }

              if ($esc) {
                $esc = false;
                $escesc = true;
              } else {
                $esc = true;
                $escesc = false;
              }
            } else {
              if ($escesc) {
                $o[$num] .= $CSV_ENCLOSURE;
                $escesc = false;
              }

              $o[$num] .= $s;
            }

            $i++;
        }

        $string = substr($string, $i);

        return $o;
    }
}