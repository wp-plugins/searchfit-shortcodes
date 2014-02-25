<?php
/**
 * @package SearchFit
 */
/*
Plugin Name: SearchFit
Plugin URI: http://www.searchfit.com
Description: SearchFit is the ultimate online shopping cart solution. Driven by visionary thinking, the ecommerce platform combines two key components for online business success. SearchFit builds user-friendly and search engine friendly websites. The result is more targeted traffic driven to a website that converts visitors into customers. See also: <a href="http://www.searchfit.com/why-choose-searchfit-shopping-cart-software.htm" target="_blank">SearchFit</a>.
Version: 1.1.2
Author: SearchFit
Author URI: http://www.searchfit.com
*/

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

define('SF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SF_PLUGIN_DIR', dirname(__FILE__));
include_once(SF_PLUGIN_DIR.'/searchfit-constants.php');

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'This is WordPress '.SF_PLUGIN_NAME.' v'.SF_PLUGIN_VERSION.' plugin. It cannot be called directly.';
	exit;
}

// Runs when plugin is activated
register_activation_hook(__FILE__, 'sf_install');
// Runs on plugin deactivation
register_deactivation_hook(__FILE__, 'sf_remove');

// Include the plugin's libraries
include_once(SF_PLUGIN_DIR.'/actions/class-action-baseHandler.php');

// --- WordPress main execution thread ---

// For admin, register the settings content
if (is_admin()) {
	include_once(SF_PLUGIN_DIR.'/searchfit-admin.php');
}

// --- WordPress system methods ---

// This version only supports WP 2.5+
function searchfit() {
    if (function_exists('add_shortcode')) {
        add_shortcode(SF_SHORTCODE, 'sf_shortcode');
    } else {
        return;
    }
}
add_action('init', 'searchfit');

// This is the method that is executed for each shortcode
function sf_shortcode($atts, $content=null, $code="") {
    // Validate plugin settings
    if ( ! sf_isValidUrl(get_option('sf_webservice_public_url'))) { return sf_getPluginMessage('Missing the Public Service Endpoint setting.'); }
    if ( ! sf_isValidUrl(get_option('sf_webservice_website_url'))) { return sf_getPluginMessage('Missing the WebSite Service Endpoint setting.'); }

    // Execute the plugin handler by the provider action
    if (empty($atts['action'])) { return sf_getPluginMessage('Missing action attribute.'); }
    $action_handler = &baseActionHandler::getActionHandler($atts['action']);
    if (empty($action_handler)) { return sf_getPluginMessage('Invalid action: '.$atts['action']); }

    return $action_handler->execute($atts, $content, $code);
}

// Creates new database fields
function sf_install() {
    add_option("sf_webservice_public_url", '', '', 'yes');
    add_option("sf_webservice_website_url", '', '', 'yes');
}

// Deletes the database fields
function sf_remove() {
    delete_option('sf_webservice_public_url');
    delete_option('sf_webservice_website_url');
}

// --- Helper methods ---

function sf_isValidUrl($url) {
    return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

function sf_getPluginMessage($msg) {
    return SF_PLUGIN_NAME.' PlugIn: '.$msg;
}

?>