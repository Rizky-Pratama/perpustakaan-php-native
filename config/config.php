<?php
// Mendefinisikan konstanta-konstanta path
define('ROOT_PATH', dirname(__DIR__) . '/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// URL base untuk aset dan link
define('BASE_URL', '/perpustakaan_pwl/'); // Sesuaikan dengan nama folder Anda di web server
define('ASSETS_URL', BASE_URL . 'assets/');
define('CSS_URL', ASSETS_URL . 'css/');
define('JS_URL', ASSETS_URL . 'js/');
