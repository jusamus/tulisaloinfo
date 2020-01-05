<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://docs.kohanaphp.com/features/localization#time
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/Helsinki');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://docs.kohanaphp.com/features/autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array('index_file' => ''));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'a1'   => MODPATH.'a1',
	'database'   => MODPATH.'database',   // Database access
	'image'      => MODPATH.'image',      // Image manipulation
	// 'kodoc'      => MODPATH.'kodoc',      // Kohana documentation
	'orm'        => MODPATH.'orm',        // Object Relationship Mapping (not complete)
	// 'pagination' => MODPATH.'pagination', // Paging of results
	// 'paypal'     => MODPATH.'paypal',     // PayPal integration (not complete)
	// 'todoist'    => MODPATH.'todoist',    // Todoist integration
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	));

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('hangman', '<controller>(/<action>(/<guess>))', array('controller' => 'hangman')) 
	->defaults(array(
		'controller' => 'hangman',
		'action' => 'index'
	)); 

Route::set('proverb', '<controller>(/<action>)', array('controller' => 'proverb')) 
	->defaults(array(
		'controller' => 'proverb',
		'action' => 'generate'
	)); 

Route::set('news', '<controller>(/<action>)', array('controller' => 'news')) 
	->defaults(array(
		'controller' => 'news',
		'action' => 'pages'
	)); 

Route::set('logout', '(page/)logout') 
	->defaults(array(
		'controller' => 'login',
		'action' => 'logout'
	)); 

Route::set('login', '(page/)<controller>(/<action>)', array('controller' => 'login')) 
	->defaults(array(
		'controller' => 'login',
		'action' => 'show'
	)); 

Route::set('page', 'page(/<page>)', array('page' => '.+')) 
	->defaults(array(
		'controller' => 'page',
		'action' => 'load',
		'page' => 'welcome'
	)); 
	
Route::set('default', '(<page>)', array('page' => '.+')) 
	->defaults(array(
		'controller' => 'website'
	)); 

/**
 * Execute the main request using PATH_INFO. If no URI source is specified,
 * the URI will be automatically detected.
 */
echo Request::instance()
	->execute()
	->send_headers()
	->response;
