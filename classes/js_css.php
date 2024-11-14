<?php

function my_first_plugin_enqueue_scripts() {
  wp_enqueue_style('my-first-plugin-style', plugin_dir_url(__FILE__) . 'css/style.css');
  wp_enqueue_script('my-first-plugin-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'my_first_plugin_enqueue_scripts');
