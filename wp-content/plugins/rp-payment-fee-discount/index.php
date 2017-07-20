<?php
/*
Plugin Name: WooCommerce Fee Or Discounts Per Payment Method
Plugin URI: http://www.magerips.com/
Description: WooCommerce Fee Or Discounts Per Payment Method plugin allows you to add discounts or Fee to order total depending on selected payment method. Just set a amount (fixed or %) to the payment methods you want to give discount or add fee.
Author: Magerips
Version: 1.0
Author URI: http://www.magerips.com/
*/

global $rppfd_plugin_url,$rppfd_plugin_dir;

$rppfd_plugin_dir = dirname(__FILE__) . "/";
$rppfd_plugin_url = plugins_url()."/" . basename($rppfd_plugin_dir) . "/";
include_once $rppfd_plugin_dir.'lib/main.php';