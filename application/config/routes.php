<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['play/(:any)'] = 'account/game_play/play/$1';
// $route['play/(:any)'] = 'account/game_play/$1';

$route['default_controller'] = 'home/index';
$route['404_override'] = 'short/index';
$route['error_404'] = 'short/error_404';

$route['home/games'] = 'home/allgames';
$route['dashboard'] = 'dashboard/index';
$route['seegames'] = 'home/seegames';
$route['gamedetails'] = 'home/gamedetails';
$route['about'] = 'about/index';
$route['faq'] = 'faq/index';
$route['login'] = 'home/login';
$route['news'] = 'news/index';
$route['blog'] = 'blog/index';
$route['board'] = 'home/board';
$route['register'] = 'home/register';
$route['reset'] = 'home/reset';
$route['reset_verify'] = 'user/reset_verify';
$route['covid'] = 'home/covid';
$route['profile'] = 'home/profile';
$route['cashout'] = 'buycredits/cashout';
$route['cashout/stripe'] = 'buycredits/stripe_account';
$route['cashout/paypal'] = 'buycredits/paypal_account';
$route['governance'] = 'home/governance';
$route['history'] = 'home/history';
$route['mission'] = 'home/mission';
$route['reports-bak'] = 'admin/index';
$route['jsError'] = 'home/jsError';
$route['disclosure'] = 'home/disclosure';

$route['sitemap\.xml'] = "Sitemap/index";

$route['translate_uri_dashes'] = FALSE;
