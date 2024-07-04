<?php
require 'core/bootstrap.php';
require_once 'core/db_config.php';

$routes = [
	/* Startseite */
	'' => 'TaskController@home',
	'home' => 'TaskController@home',

	/* About */
	'about' => 'TaskController@about',

	/* Aufgaben hinzufügen, bearbeiten und löschen */
	'add_task' => 'TaskController@add_task',
	'edit_task' => 'TaskController@edit_task',
	'delete_task' => 'TaskController@delete_task',
	'complete_task' => 'TaskController@complete_task',
	'higherPrio' => 'TaskController@higherPrio',
	'lowerPrio' => 'TaskController@lowerPrio',

	/* Zeiten erfassen, bearbeiten und löschen */
	'zeituebersicht' => 'TimeController@zeituebersicht',
	'addTimeRecord' => 'TimeController@addTimeRecord',
	'edit_time' => 'TimeController@edit_time',
	'delete_time' => 'TimeController@delete_time',
	'showHistory' => 'TimeController@showHistory',

	/* Essay schreiben, akzeptieren und ablehnen */
	'essay' => 'EssayController@essay',
	'add_essay' => 'EssayController@add_essay',
	'accept' => 'EssayController@accept',
	'refuse' => 'EssayController@refuse',

	/* Adminbereich */
	'admin' => 'EssayController@admin',

	/* Login | Register | Logout */
	'login' => 'LoginController@login',
	'register' => 'LoginController@register',
	'logout' => 'LoginController@logout',
];

$db = [
	'name'     => DB_NAME,
    'username' => DB_USERNAME,
    'password' => DB_PASSWORD,
];

$router = new Router($routes);
$router->run($_GET['url'] ?? '');