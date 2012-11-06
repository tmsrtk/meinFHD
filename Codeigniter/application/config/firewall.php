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

    'admin' => array(
        'pattern' => '^/admin',
        'roles' => array('admin'),
    ),

    'achievement' => array(
        'pattern' => '^/achievement',
        'roles' => array('student'),
    ),

    'ajax' => array(
        'pattern' => '^/ajax',
        'roles' => array('user'),
    ),

    'attendance' => array(
        'pattern' => '^/attendance',
        'roles' => array('student'),
    ),
	
	'dashboard' => array(
		'pattern' => '^/dashboard',
		'roles' => array('user'),
	),

    'dozent' => array(
        'pattern' => '^/dozent',
        'roles' => array('student', 'dozent', 'tutor'),
    ),

    'einstellungen' => array(
        'pattern' => '^/einstellungen',
        'roles' => array('user'),
    ),

    'faq' => array(
        'pattern' => '^/faq',
        'roles' => array('user'),
    ),

    'hilfe' => array(
        'pattern' => '^/hilfe',
        'roles' => array('user'),
    ),

    'kursverwaltung' => array(
        'pattern' => '^/kursverwaltung',
        'roles' => array('dozent', 'tutor'),
    ),

    'logbuch' => array(
        'pattern' => '^/logbuch',
        'roles' => array('student'),
    ),

    'logbuch_analysis' => array(
        'pattern' => '^/logbuch_analysis',
        'roles' => array('student'),
    ),

    'logbuch_analysis_widget' => array(
        'pattern' => '^/logbuch_analysis_widget',
        'roles' => array('student'),
    ),
	
	'modul' => array(
		'pattern' => '^/modul',
		'roles' => array('student', 'dozent', 'tutor'),
	),

    'sso' => array(
        'pattern' => '^/sso',
        'roles' => array('guest', 'user'),
    ),

    'stundenplan' => array(
        'pattern' => '^/stundenplan',
        'roles' => array('student', 'dozent', 'tutor'),
    ),

    'studienplan' => array(
        'pattern' => '^/studienplan',
        'roles' => array('student'),
    ),

	'veranstaltungen' => array(
		'pattern' => '^/veranstaltungen',
		'roles' => array('dozent', 'student', 'tutor'),
	),

);

/*
| -------------------------------------------------------------------
|  Add firewall configuration to global config
| -------------------------------------------------------------------
*/

$config['firewall'] = $firewall;