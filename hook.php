<?php

/**
 * -------------------------------------------------------------------------
 * GLPIwithBookstack plugin for GLPI
 * Copyright (C) 2024 by the GLPIwithBookstack Development Team.
 * -------------------------------------------------------------------------
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * --------------------------------------------------------------------------
 */

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_glpiwithbookstack_install()
{
    $config = new Config();
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['bookstack_url' => '']);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['bookstack_token_id' => '']);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['bookstack_token_secret' => '']);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['search_in_tags_only' => false]);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['search_type_pages_only' => true]);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['search_category_name_only' => false]);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['search_category_completename_but_only_visible' => true]);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['curl_timeout' => 1]);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['display_max_search_results' => 10]);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['display_text_tab_name' => 'Knowledge base']);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['display_text_title' => 'Title']);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['display_text_content_preview' => 'Content preview']);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['display_text_search_on_bookstack' => 'Search [search_term] on Bookstack']);
    $config->setConfigurationValues('plugin:Glpiwithbookstack', ['display_text_max_results_reached' => '[result_count] of [max_results] results are displayed. Click here to view all: [url]']);

    //ProfileRight::addProfileRights(['glpiwithbookstack:read']);

    return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_glpiwithbookstack_uninstall()
{
    $config = new Config();
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['bookstack_url']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['bookstack_token_id']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['bookstack_token_secret']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['search_in_tags_only']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['search_category_name_only']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['search_type_pages_only']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['search_category_completename_but_only_visible']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['curl_timeout']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['display_max_search_results']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['display_text_tab_name']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['display_text_title']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['display_text_content_preview']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['display_text_search_on_bookstack']);
    $config->deleteConfigurationValues('plugin:Glpiwithbookstack', ['display_text_max_results_reached']);

    //ProfileRight::deleteProfileRights(['glpiwithbookstack:read']);
    return true;
}
/*
 * TODO: no integrated search yet, so need to create a page for it
 * Add new menu for Bookstack integrated search on top level
*/
function plugin_myplugin_redefine_menus($menu) {
    if (empty($menu)) {
        return $menu;
    }
    /*
     * Create custom menu for the new Bookstack knowledge base.
     * It will be placed on the top menu so it can be reached directly.
     * A new search form will be display to show the API search
    */
    if (array_key_exists('knowledgebase', $menu) === false && $_SESSION['glpiactiveprofile']['interface'] == 'central') {
        $menu['knowledgebase'] = [
        //'default'   => '/plugins/myplugin/front/model.php',
        'default'   => '/front/knowbaseitem.php',
        'title'     => __('Knowledge base', 'knowledgebase'),
        'icon'      => 'ti ti-lifebuoy',
        'content'   => [true]
    ];
    }
    return $menu;
}
