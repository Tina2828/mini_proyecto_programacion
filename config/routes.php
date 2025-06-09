<?php

require_once __DIR__."/../core/Routes.php";

Routes::get("/", "HomeController@index");
Routes::get("/tareas", "HomeController@index");
Routes::get("/tareas/delete", "HomeController@destroy_tarea");
