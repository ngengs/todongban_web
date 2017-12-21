<?php
/**
 * Copyright (c) 2017 Rizky Kharisma (@ngengs)
 *
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author rizky Kharisma <ngeng.ngengs@gmail.com>
 *
 * Date: 8/4/2017
 * Time: 12:03 AM
 *
 * Created by PhpStorm.
 */

if (!function_exists('asset_url')) {
    function asset_url(string $path = null)
    {
        echo base_url('assets/' . (!empty($path) ? $path : ''));
    }
}

if (!function_exists('css_url')) {
    function css_url(string $path = null)
    {
        asset_url('css/' . (!empty($path) ? $path . '.css' : ''));
    }
}

if (!function_exists('js_url')) {
    function js_url(string $path = null)
    {
        asset_url('js/' . (!empty($path) ? $path . '.js' : ''));
    }
}

if (!function_exists('plugin_url')) {
    function plugin_url(string $path = null)
    {
        asset_url('plugins/' . (!empty($path) ? $path : ''));
    }
}

if (!function_exists('css_plugin_url')) {
    function css_plugin_url(string $path = null)
    {
        plugin_url(!empty($path) ? $path . '.css' : '');
    }
}

if (!function_exists('js_plugin_url')) {
    function js_plugin_url(string $path = null)
    {
        plugin_url(!empty($path) ? $path . '.js' : '');
    }
}

if (!function_exists('avatar_user_url_return')) {
    /**
     * @param \User_data $user
     *
     * @return string
     */
    function avatar_user_url_return($user)
    {
        return base_url('uploads/' . $user->USERNAME . '/' . $user->AVATAR);
    }
}
if (!function_exists('avatar_user_url')) {
    /**
     * @param \User_data $user
     */
    function avatar_user_url($user)
    {
        echo avatar_user_url_return($user);
    }
}
if (!function_exists('identity_user_url_return')) {

    /**
     * @param \User_data $user
     *
     * @return string
     */
    function identity_user_url_return($user)
    {
        return base_url('uploads/' . $user->USERNAME . '/' . $user->IDENTITY_PICTURE);
    }
}
if (!function_exists('identity_user_url')) {

    /**
     * @param \User_data $user
     */
    function identity_user_url($user)
    {
        echo identity_user_url_return($user);
    }
}

