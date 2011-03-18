<?php
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
abstract class baseActionHandler {

    static public function &getActionHandler($actionName) {
        $classFileName = SF_PLUGIN_DIR.'/actions/class-action-'.$actionName.'.php';
        if (file_exists($classFileName)) { include_once($classFileName); } else { return null; }
        return new $actionName;
    }

    abstract public function execute($atts, $content=null, $code="");

    // --- helper methods ---

    protected function curlPost2Url($url, $request, $post = '1', $timeout = 60, $headerExtra = '', $cookieFileLocation = '') {
        if (function_exists('curl_init')) {
            if ($timeout <= 0) { $timeout = 60; }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_POST, $post);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
            if ( ! empty($cookieFileLocation)) {
                curl_setopt($ch,CURLOPT_COOKIEJAR,$cookieFileLocation);
                curl_setopt($ch,CURLOPT_COOKIEFILE,$cookieFileLocation);
            }
            if ($headerExtra != '') { curl_setopt($ch, CURLOPT_HTTPHEADER, $headerExtra); }
            $response = curl_exec($ch);
            $status['errno'] = curl_errno($ch);
            $status['errdescr'] = curl_error($ch);
            $status['response'] = strpos($response, '</error_descr>') !== false ? sf_getPluginMessage($response) : $response;
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (($httpCode > 0) && ($httpCode != 200)) {
                $status['errno'] = $httpCode; $status['errdescr'] = 'http error';
                $errorResponse = strip_tags($status['response']);
                if ( ! empty($errorResponse)) {
                    $status['errdescr'] .= ' ('.$errorResponse.')';
                }
            }
            curl_close($ch);
        } else {
            $status['errno'] = 1;
            $status['errdescr'] = "CURL is not installed on the server";
        }
        return $status;
    }

}

?>