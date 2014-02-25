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
sf_admin_warnings();

function sf_admin_init() {
    global $wp_version;

    // all admin functions are disabled in old versions
    if ( ! function_exists('is_multisite') && version_compare($wp_version, '3.0', '<' )) {
        function sf_version_warning() {
            echo "<div id='sf-warning' class='updated fade'><p><strong>".sprintf(__('%s v%s requires WordPress 3.0 or higher.'), SF_PLUGIN_NAME, SF_PLUGIN_VERSION) ."</strong> ".sprintf(__('Please <a href="%s">upgrade WordPress</a> to a current version.'), 'http://codex.wordpress.org/Upgrading_WordPress', 'http://wordpress.org/extend/plugins/searchfit/download/'). "</p></div>";
        }
        add_action('admin_notices', 'sf_version_warning'); 
        return; 
    }

    wp_register_style('searchfit.css', SF_PLUGIN_URL.'/styles/searchfit.css');
	wp_enqueue_style('searchfit.css');
}
add_action('admin_init', 'sf_admin_init');

function sf_config_page() {
    if (function_exists('add_submenu_page')) {
        add_submenu_page('plugins.php', SF_PLUGIN_NAME.' '.__('Settings'), SF_PLUGIN_NAME.' '.__('Settings'), 'manage_options', 'sf-config-page', 'sf_show_settings');
    }
}
add_action('admin_menu', 'sf_config_page');

function sf_settings_link($links, $file) {
    if ($file == plugin_basename(SF_PLUGIN_DIR.'/searchfit.php')) {
        $links[] = '<a href="plugins.php?page=sf-config-page">'.__('Settings').'</a>';
    }
    return $links;
}
add_filter("plugin_action_links", 'sf_settings_link', 11, 2);

function sf_admin_warnings() {
    if ( ! sf_isValidUrl(get_option('sf_webservice_website_url'))) {
        function sf_warning() {
			echo "<div id='sf-warning' class='updated fade'><p><strong>".sprintf(__('%s is almost ready.'), SF_PLUGIN_NAME)."</strong> ".sprintf(__('You must <a href="%1$s">configure</a> it to work.'), "plugins.php?page=sf-config-page")."</p></div>";
		}
		add_action('admin_notices', 'sf_warning');
		return;
    }
}

function sf_show_settings() {
    include_once(SF_PLUGIN_DIR.'/enums/includeExternalArtifactsEnum.php');
    include_once(SF_PLUGIN_DIR.'/utils/class-util-options.php'); ?>

    <div class="sf_settingsContainer">
        <div class="sf_config_header"><h2><?php echo SF_PLUGIN_NAME; ?> Plugin v<?php echo SF_PLUGIN_VERSION; ?></h2></div>
        <div class="sf_config_body">
            <?php if (function_exists('add_shortcode')) { ?>
                <form method="post" action="options.php">
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="page_options" value="sf_webservice_public_url, sf_webservice_website_url, sf_include_external_artifacts" />
                    <?php wp_nonce_field('update-options'); ?>
                    <table width="100%" border="0" cellpadding="0" cellspacing="15">
                    <tr><td><b>Public Service Endpoint</b> <span class="sf_help">(Available at <?php echo SF_PLUGIN_NAME; ?> Settings/ConfigurePerUser/WebServices)</span><br /><input type="text" name="sf_webservice_public_url" id="sf_webservice_public_url" value="<?php echo get_option('sf_webservice_public_url'); ?>" size="100" /></td></tr>
                    <tr><td><b>WebSite Service Endpoint</b> <span class="sf_help">(Available at <?php echo SF_PLUGIN_NAME; ?> Settings/ConfigurePerUser/WebServices)</span><br /><input type="text" name="sf_webservice_website_url" id="sf_webservice_website_url" value="<?php echo get_option('sf_webservice_website_url'); ?>" size="100" /></td></tr>
                    <tr><td><b>Include External Artifacts</b> <span class="sf_help">(Ability to exclude <?php echo SF_PLUGIN_NAME; ?> artifacts from the visualized products content)</span><br /><select name="sf_include_external_artifacts" id="sf_include_external_artifacts"><?php echo $utilOptions->getSelectOptions($includeExternalArtifactsEnum, get_option('sf_include_external_artifacts')); ?></select></td></tr>
                    <tr><td><input type="submit" value="<?php _e('Save Changes') ?>" /></td></tr>
                    </table>
                </form>
            <?php } else { ?>
                <div class="sf_warn"><h3>Warning! This plugin requires WordPress 3.0+</h3></div>
            <?php } ?>
        </div>
        <div class="sf_config_footer">
            <h3>Available Shortcodes:</h3>
            <div class="sf_shortcodes_container">
                <div class="sf_shortcode_title">[searchfit action=<b>getGroupOfProductsAsHtml</b>]</div>
                <div class="sf_shortcode_content">
                    <p>The <b>getGroupOfProductsAsHtml</b> action retrieves the products of the target product group and visualizes them as HTML.</p>
                    <p>Required Attributes:</p>
                    <ol>
                        <li><i>group_id</i> - The target product group id.</li>
                    </ol>
                    <p>Optional Attributes:</p>
                    <ol>
                        <li><i>max_items</i> - The maximum number of visualized products.</li>
                        <li><i>max_days_in_past</i> - The maximum number of days in the past that intelligent product groups will use to retrieve products.</li>
                        <li><i>manufacturer_id</i> - The manufacturer id of the products to be retrieved.</li>
                        <li><i>category_type_id</i> - The category type id of the products to be retrieved.</li>
                        <li><i>category_id</i> - The category id of the products to be retrieved.</li>
                    </ol>
                    <p>Examples:</p>
                    <ul>
                        <li><code>[searchfit action=getGroupOfProductsAsHtml group_id=123]</code></li>
                        <li><code>[searchfit action=getGroupOfProductsAsHtml group_id=123 max_items=10]</code></li>
                        <li><code>[searchfit action=getGroupOfProductsAsHtml group_id=123 manufacturer_id=100]</code></li>
                        <li><code>[searchfit action=getGroupOfProductsAsHtml group_id=123 category_type_id=200]</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php } ?>