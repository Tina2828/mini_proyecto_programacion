<?php

require_once __DIR__."/../core/Routes.php";

Routes::get("/", "HomeController@index");
