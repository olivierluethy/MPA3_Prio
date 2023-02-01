<?php
require 'core/bootstrap.php';

$routes = [
	/* Startseite */
	'' => 'TaskController@home',
	'home' => 'TaskController@home',

	/* About */
	'about' => 'TaskController@about',

	/* Aufgaben hinzufügen, bearbeiten und löschen */
	'add_task' => 'TaskController@add_task',
	'edit_task' => 'TaskController@edit_task',
	'edit_time' => 'TimeController@edit_time',
	'delete_task' => 'TaskController@delete_task',
	'delete_time' => 'TimeController@delete_time',

	'higherPrio' => 'TaskController@higherPrio',
	'lowerPrio' => 'TaskController@lowerPrio',

	'complete_task' => 'TaskController@complete_task',

	'accept' => 'EssayController@accept',
	'refuse' => 'EssayController@refuse',

	'essay' => 'EssayController@essay',
	'add_essay' => 'EssayController@add_essay',

	'addTimeRecord' => 'TimeController@addTimeRecord',

	'zeituebersicht' => 'TimeController@zeituebersicht',

	/* Adminbereich */
	'admin' => 'EssayController@admin',

	/* Login | Register | Logout */
	'login' => 'LoginController@login',
	'register' => 'LoginController@register',
	'logout' => 'LoginController@logout',
];

$db = [
	'name'     => 'prio',
	'username' => 'root',
	'password' => '',
];

$router = new Router($routes);
$router->run($_GET['url'] ?? '');