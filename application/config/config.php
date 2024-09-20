<?php

defined('BASEPATH') OR exit('No direct script access allowed');


$config['base_url'] = APP_URL;


$config['index_page'] = 'index.php';


$config['uri_protocol'] = 'REQUEST_URI';


$config['url_suffix'] = '';


$config['language'] = 'english';


$config['charset'] = 'UTF-8';


$config['enable_hooks'] = FALSE;


$config['subclass_prefix'] = 'MY_';


$config['composer_autoload'] = FALSE;


$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';


$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';


$config['log_threshold'] = 0;


$config['log_path'] = '';


$config['log_file_extension'] = '';


$config['log_file_permissions'] = 0644;


$config['log_date_format'] = 'Y-m-d H:i:s';


$config['error_views_path'] = '';


$config['cache_path'] = '';


$config['cache_query_string'] = FALSE;


$config['encryption_key'] = '';

/*
  |re shared with the rest of the application,
  | except for 'cookie_prefix' and 'cookie_httponly', which are ignored here.
  |
 */
$config['sess_driver'] = 'database';
$config['sess_use_database'] = TRUE;
$config['sess_save_path'] = 'apsth_pos_system';
$config['sess_table_name'] = 'apsth_pos_system';
$config['sess_cookie_name'] = 'apsth_pos_system';
$config['sess_expiration'] = 2592000;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 300;
$config['sess_regenerate_destroy'] = FALSE;

//$config['sess_driver'] = 'files';
//$config['sess_cookie_name'] = 'apsth_pos_system';
//$config['sess_expiration'] = 2592000;
////$config['sess_save_path'] = NULL;
//$config['sess_save_path'] = sys_get_temp_dir();
//$config['sess_match_ip'] = FALSE;
//$config['sess_time_to_update'] = 300;
//$config['sess_regenerate_destroy'] = FALSE;


$config['cookie_prefix'] = '';
$config['cookie_domain'] = '';
$config['cookie_path'] = '/';
$config['cookie_secure'] = FALSE;
$config['cookie_httponly'] = FALSE;


$config['standardize_newlines'] = FALSE;


$config['global_xss_filtering'] = FALSE;

/*
  |--------------------------------------------------------------------------
  | Cross Site Request Forgery
  |--------------------------------------------------------------------------
  | Enables a CSRF cookie token to be set. When set to TRUE, token will be
  | checked on a submitted form. If you are accepting user data, it is strongly
  | recommended CSRF protection be enabled.
  |
  | 'csrf_token_name' = The token name
  | 'csrf_cookie_name' = The cookie name
  | 'csrf_expire' = The number in seconds the token should expire.
  | 'csrf_regenerate' = Regenerate token on every submission
  | 'csrf_exclude_uris' = Array of URIs which ignore CSRF checks
 */
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

/*
  
 */
$config['compress_output'] = FALSE;


$config['time_reference'] = 'local';


$config['rewrite_short_tags'] = FALSE;

/*
  |Here are a few examples:
  |
  | Comma-separated:	'10.0.1.200,192.168.5.0/24'
  | Array:		array('10.0.1.200', '192.168.5.0/24')
 */
$config['proxy_ips'] = '';
