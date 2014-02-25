<?
/**
 * Copyright (c) 2003-2014, SearchFit, Inc. All Rights Reserved.
 * -------------------------------------------------------------
 * Permission to use, copy, modify, and distribute this
 * software for any purpose without permission is forbidden!
 * -------------------------------------------------------------
 */
class utilOptions {

    public function getSelectOptions(&$arr, $selected = "0", $arr_exceptions = 0, $urlencode = 0) {
        $rendered = ''; $attributes = array();
        foreach ($arr as $key=>$val) {
            if (isset($arr_exceptions[$key])) { continue; }
            if (is_array($val)) {
                if (is_array($val['attributes'])) { $attributes = $val['attributes']; }
                if (isset($val['key'])) {
                    $key = $val['key']; $val = $val['name'];
                } else {
                    $val = (is_numeric($key) ? '' : '['.$key.'] ').$val['name'];
                }
            }
            if (strpos($key, '[optgroup]') !== false) {
                $rendered .= '<optgroup label="'.$val.'"></optgroup>'."\n";
            } else {
                $key1 = $key;
                if ($urlencode) { $key1 = urlencode($key); }
                $rendered .= '<option value="'.$key1.'"';
                if ($key == $selected) { $rendered .= " selected"; }
                foreach ($attributes as $attrKey=>$attrVal) { $rendered .= ' '.$attrKey.'="'.$attrVal.'"'; }
                $rendered .= ">".$val."</option>\n";
            }
        }
        return $rendered;
    }

}

$utilOptions = new utilOptions();

?>