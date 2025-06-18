<?php

require_once __DIR__."/../core/Routes.php";

Routes::get("/", "HomeController@index");
Routes::get("/tareas", "HomeController@index");
Routes::post("/tareas/create", "HomeController@createTarea");
Routes::get("/tareas/delete", "HomeController@destroyTarea");
Routes::get("/tareas/new", "HomeController@newTarea");
Routes::get("tareas/edit", "HomeController@editTarea");
