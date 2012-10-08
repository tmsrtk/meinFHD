<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
|  Define the login page to which the user will be redirected
| -------------------------------------------------------------------
*/

$firewall['login_page'] = 'login';

/*
| -------------------------------------------------------------------
|  Firewall config initialization
| -------------------------------------------------------------------
*/

$firewall['access_control'] = array();

/*
| -------------------------------------------------------------------
|  Firewall routes
| -------------------------------------------------------------------
|  
|  Routes to be protected by the Firewall.
|  
|  Prototype:
|
|    $access_control = array(
|        'some_name' => array(
|            'pattern' => '/^regex$/',
|            'roles' => array('guest', 'user', '...'),
|        ),
|    )
|
*/

$firewall['access_control'] = array(
	
	'dashboard' => array(
		'pattern' => '^/dashboard',
		'roles' => array('user'),
	),
	
	'stundenplan' => array(
		'pattern' => '^/stundenplan',
		'roles' => array('user'),
	),
	
	'studienplan' => array(
		'pattern' => '^/studienplan',
		'roles' => array('student'),
	),
	
	'hilfe' => array(
		'pattern' => '^/hilfe',
		'roles' => array('user'),
	),
	
	'faq' => array(
		'pattern' => '^/faq',
		'roles' => array('user'),
	),
	
	'faq' => array(
		'pattern' => '^/faq',
		'roles' => array('user'),
	),
	
	'modul' => array(
		'pattern' => '^/modul',
		'roles' => array('user'),
	),
	
	'dozent' => array(
		'pattern' => '^/dozent',
		'roles' => array('user'),
	),
	
	'veranstaltungen' => array(
		'pattern' => '^/veranstaltungen',
		'roles' => array('user'),
	),
);

/*
| -------------------------------------------------------------------
|  Add firewall configuration to global config
| -------------------------------------------------------------------
*/

$config['firewall'] = $firewall;