<?php
/**
 * Plugin Name: WPA Posts
 * Plugin URI: https://wpassist.me/plugins/wpa-posts/
 * Description: Display post lists or post grids using intelligent filters
 * Author: Metin Saylan
 * Author URI: https://wpassist.me
 *
 * Version: 20221024
 * Text Domain: wpa-posts
 *
 * License:     GPLv2 or later
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Invalid request.' );
}

// utility functions
require_once __DIR__ . '/wpa-posts-functions.php';

// register required taxonomies
require_once __DIR__ . '/wpa-posts-taxonomies.php';

// register widgets
require_once __DIR__ . '/wpa-posts-widget.php';

add_action( 'wp_enqueue_scripts', 'wpa_posts_assets' );
function wpa_posts_assets() {
  wp_register_style( 'wpa-posts', plugins_url( '/wpa-posts.css' , __FILE__ ) );
  wp_enqueue_style( 'wpa-posts' );
}