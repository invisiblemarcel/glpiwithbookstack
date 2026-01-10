<?php
if (!defined('GLPI_ROOT')) { define('GLPI_ROOT', realpath(__DIR__ . '/../..')); }
/**
 * -------------------------------------------------------------------------
 * Example plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Example.
 *
 * Example is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Example is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Example. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2006-2022 by Example plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/glpiwithbookstack
 * -------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

use Config as GlpiConfig;

// Non menu entry case
//header("Location:../../central.php");

// Entry menu case
include (__DIR__ . "/../../../../www/glpi/inc/includes.php");

Session::checkRight("config", UPDATE);

// To be available when plugin in not activated
Plugin::load('Glpiwithbookstack');

Html::header("GLPI with Bookstack plugin setup", $_SERVER['PHP_SELF'], "config", "plugins");

if (!Session::haveRight("config", UPDATE)) {
    return false;
}

$my_config = GlpiConfig::getConfigurationValues('plugin:Glpiwithbookstack');

echo "<form name='form' action=\"".Toolbox::getItemTypeFormURL('Config')."\" method='post'>";
echo "<div class='center' id='tabsbody'>";
echo "<table style='max-width: 800px;' class='tab_cadre_fixe'>";
echo "<tr><th colspan='2'>" . __('GLPI with Bookstack plugin setup') . "</th></tr>";
// text field for bookstack_url
echo "<td >" . __('Bookstack URL:') . "</td>";
echo "<td>";
echo "<input size='32' name='bookstack_url' placeholder='https://example.com' value='".($my_config['bookstack_url'])."'>";
echo "<tr><td colspan='2'>Just the base URL (the /api will be added in code) without a slash (/) at the end, ex: https://my-bookstack.my-domain.com</td></tr>";
echo "</td></tr>";
// text field for bookstack_token_id
echo "<td >" . __('Bookstack token id:') . "</td>";
echo "<td>";
echo "<input size='32' name='bookstack_token_id' value='".($my_config['bookstack_token_id'])."'>";
echo "</td></tr>";
// text field for bookstack_token_secret
echo "<td >" . __('Bookstack token secret:') . "</td>";
echo "<td>";
echo "<input size='32' name='bookstack_token_secret' value='".($my_config['bookstack_token_secret'])."'>";
echo "</td></tr>";
// yes or no dropdown for setting search_in_tags_only
echo "<td >" . __('Search in tags only:') . "</td>";
echo "<td>";
Dropdown::showYesNo("search_in_tags_only", $my_config['search_in_tags_only']);
echo "</td></tr>";
// yes or no dropdown for setting search_type_pages_only
echo "<td >" . __('Search type pages only:') . "</td>";
echo "<td>";
Dropdown::showYesNo("search_type_pages_only", $my_config['search_type_pages_only']);
echo "</td></tr>";
// text field for display_max_search_results
echo "<td >" . __('Display max search results:') . "</td>";
echo "<td>";
echo "<input size='2' name='display_max_search_results' value='".($my_config['display_max_search_results'])."'>";
echo "</td></tr>";
// yes or no dropdown for setting search_category_name_only
echo "<td >" . __('Seach category name only:') . "</td>";
echo "<td>";
Dropdown::showYesNo("search_category_name_only", $my_config['search_category_name_only']);
echo "</td></tr>";
// yes or no dropdown for setting search_category_completename_but_only_visible
echo "<td >" . __('Search category completename but only visible:') . "</td>";
echo "<td>";
Dropdown::showYesNo("search_category_completename_but_only_visible", $my_config['search_category_completename_but_only_visible']);
echo "</td></tr>";
echo "<tr><td colspan='2'>If 'Seach category name only' is true then this value is disabled.</td></tr>";
// text field for curl_timeout
echo "<td >" . __('Curl timeout (in seconds):') . "</td>";
echo "<td>";
echo "<input size='2' name='curl_timeout' value='".($my_config['curl_timeout'])."'>";
echo "</td></tr>";
// yes or no dropdown for curl_ssl_verifypeer
echo "<td >" . __('Curl SSL verify:') . "</td>";
echo "<td>";
Dropdown::showYesNo("curl_ssl_verifypeer", $my_config['curl_ssl_verifypeer']);
echo "</td></tr>";
/*
 * Optional: change the display texts for the labels
*/
echo "<tr><th colspan='2'>" . __('Optional settings, change labels') . "</th></tr>";
// text field for display_text_tab_name
echo "<td >" . __('Display text tab name:') . "</td>";
echo "<td>";
echo "<input size='32' name='display_text_tab_name' value='".($my_config['display_text_tab_name'])."' placeholder='Knowledge base'>";
echo "</td></tr>";
// text field for display_text_title
echo "<td >" . __('Display text title:') . "</td>";
echo "<td>";
echo "<input size='32' name='display_text_title' value='".($my_config['display_text_title'])."' placeholder='Title'>";
echo "</td></tr>";
// text field for display_text_content_preview
echo "<td >" . __('Display text content preview:') . "</td>";
echo "<td>";
echo "<input size='32' name='display_text_content_preview' value='".($my_config['display_text_content_preview'])."' placeholder='Content preview'>";
echo "</td></tr>";
// text field for display_text_search_on_bookstack
echo "<td >" . __('Display text search on bookstack:') . "</td>";
echo "<td>";
echo "<input size='32' name='display_text_search_on_bookstack' value='".($my_config['display_text_search_on_bookstack'])."' placeholder='Search [search_term] on Bookstack'>";
echo "</td></tr>";
echo "<tr><td></td><td>Possible placeholders: [search_term]</td></tr>";
// text field for display_text_max_results_reached
echo "<td >" . __('Display text max results reached:') . "</td>";
echo "<td>";
echo "<input size='32' name='display_text_max_results_reached' value='".($my_config['display_text_max_results_reached'])."' placeholder='[result_count] of [max_results] results are displayed. Click here to view all: [url]'>";
echo "</td></tr>";
echo "<tr><td></td><td>Possible placeholders: [result_count], [max_results], [url]</td></tr>";
/*
 * Hidden field for plugin config context
 * Save button
*/
echo "<input type='hidden' name='config_context' value='plugin:Glpiwithbookstack'>";
echo "<tr class='tab_bg_2'>";
echo "<td colspan='2' class='center'>";
echo "<input type='submit' name='update' class='submit' value=\""._sx('button', 'Save')."\">";
echo "</td></tr>";

echo "</table></div>";
Html::footer();
Html::closeForm();
