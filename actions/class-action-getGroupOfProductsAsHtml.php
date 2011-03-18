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
class getGroupOfProductsAsHtml extends baseActionHandler {

    // @Override
    public function execute($atts, $content=null, $code="") {
        // validate shortcode attributes
        if (empty($atts['group_id'])) { return SF_PLUGIN_NAME.': Missing the group_id attribute.'; }

        // construct the containerType web service parameter
        include_once(SF_PLUGIN_DIR.'/enums/includeExternalArtifactsEnum.php');
        $includeExternalArtifacts = get_option('sf_include_external_artifacts');
        if ($includeExternalArtifacts == $includeExternalArtifactsEnum['none']['key']) {
            $containerType = 5; // CONTAINERTYPE_PRODUCTS_EXTERNAL_NOCSSJS
        } else if ($includeExternalArtifacts == $includeExternalArtifactsEnum['noCSS']['key']) {
            $containerType = 6; // CONTAINERTYPE_PRODUCTS_EXTERNAL_NOCSS
        } else if ($includeExternalArtifacts == $includeExternalArtifactsEnum['noJS']['key']) {
            $containerType = 7; // CONTAINERTYPE_PRODUCTS_EXTERNAL_NOJS
        } else {
            $containerType = 4; // CONTAINERTYPE_PRODUCTS_EXTERNAL
        }

        // get shortcode attributes
        $groupId = (int)$atts['group_id'];
        $maxItems = (int)$atts['max_items'];
        $maxDaysInPast = (int)$atts['max_days_in_past'];
        $manufacturerId = (int)$atts['manufacturer_id'];
        $categoryTypeId = (int)$atts['category_type_id'];
        $categoryId = (int)$atts['category_id'];

        // prepare the full service endpoint
        $webServiceUrl = get_option('sf_webservice_website_url');
        $url = $webServiceUrl.'&action='.$atts['action'].'&consumerCode=WordPress&containerType='.$containerType.'&productGroupId='.$groupId;
        $url .= ! empty($maxDaysInPast) ? '&maxDaysInPast='.$maxDaysInPast : '';
        $url .= ! empty($maxItems) ? '&maxItems='.$maxItems : '';
        $url .= ! empty($manufacturerId) ? '&manufacturerId='.$manufacturerId : '';
        $url .= ! empty($categoryTypeId) ? '&categoryTypeId='.$categoryTypeId : '';
        $url .= ! empty($categoryId) ? '&categoryId='.$categoryId : '';

        // get the content from the remote server
        $contentRequest = $this->curlPost2Url($url, $request, 0);
        return ! empty($contentRequest['errno']) ? sf_getPluginMessage('Error number '.$contentRequest['errno'].': "'.$contentRequest['errdescr'].'"') : $contentRequest['response'];
    }

}

?>