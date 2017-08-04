<?php
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
    function asset_url($path = null)
    {
        echo base_url('assets/' . (!empty($path) ? $path : ''));
    }
}

if (!function_exists('css_url')) {
    function css_url($path = null)
    {
        asset_url('css/' . (!empty($path) ? $path . '.css' : ''));
    }
}

if (!function_exists('js_url')) {
    function js_url($path = null)
    {
        asset_url('js/' . (!empty($path) ? $path . '.js' : ''));
    }
}

if (!function_exists('plugin_url')) {
    function plugin_url($path = null)
    {
        asset_url('plugins/' . (!empty($path) ? $path : ''));
    }
}

if (!function_exists('css_plugin_url')) {
    function css_plugin_url($path = null)
    {
        plugin_url(!empty($path) ? $path . '.css' : '');
    }
}

if (!function_exists('js_plugin_url')) {
    function js_plugin_url($path = null)
    {
        plugin_url(!empty($path) ? $path . '.js' : '');
    }
}

if (!function_exists('avatar_user_url')) {
    function avatar_user_url($user = null)
    {
        echo base_url('uploads/'.$user->USERNAME.'/'.$user->AVATAR);
    }
}
if (!function_exists('identity_user_url')) {
    function identity_user_url($user = null)
    {
        echo base_url('uploads/'.$user->USERNAME.'/'.$user->IDENTITY_PICTURE);
    }
}

