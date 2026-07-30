<?php
/**
 * Plugin Name: Subjects Untuk SLiMS
 * Plugin URI: -
 * Description: Plugins subject untuk SLIMS
 * Version: 1.0.0
 * Author: -
 * Author URI: -
 */
use SLiMS\Plugins;

$plugin = Plugins::getInstance();

Plugins::getInstance()->registerAutoload(__DIR__);

$path =  __DIR__ . '/pages/opac/index.php';

Plugins::menu('opac', 'Subject', $path);