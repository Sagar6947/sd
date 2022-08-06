<?php
defined('BASEPATH') or exit('No direct script access allowed');


$route['default_controller'] = 'Home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['search'] = "Home/search";
$route['login'] = "Home/login";
$route['signup'] = "Home/register";
$route['dashboard-add-profile'] = "Home/add_profile";
$route['dashboard'] = "Home/dashboard";
$route['logout'] = 'Home/logout';

// $route['blogdetails/(:any)/(:any)'] = 'home/blog/$1/$2';

