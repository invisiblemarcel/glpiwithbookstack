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

define('PLUGIN_GLPIWITHBOOKSTACK_VERSION', '0.1.0');

// Minimal GLPI version, inclusive
define("PLUGIN_GLPIWITHBOOKSTACK_MIN_GLPI_VERSION", "10.0.0");

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_glpiwithbookstack()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['glpiwithbookstack'] = true;

    Plugin::registerClass('PluginGlpiwithbookstackIntegrate', array('addtabon' => array('Ticket')));
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_glpiwithbookstack()
{
    return [
        'name'           => 'GLPI with Bookstack',
        'version'        => PLUGIN_GLPIWITHBOOKSTACK_VERSION,
        'author'         => '<a href="https://github.com/invisiblemarcel">invisiblemarcel\'</a>',
        'license'        => '',
        'homepage'       => 'https://github.com/invisiblemarcel',
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_GLPIWITHBOOKSTACK_MIN_GLPI_VERSION,
            ]
        ]
    ];
}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_glpiwithbookstack_check_prerequisites()
{
    return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_glpiwithbookstack_check_config($verbose = false)
{
    if (true) { // Your configuration check
        return true;
    }

    if ($verbose) {
        echo __('Installed / not configured', 'glpiwithbookstack');
    }
    return false;
}
