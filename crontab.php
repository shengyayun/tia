<?php
require_once "vendor/autoload.php";

$time = time();
$model = new \Tia\Consign();
$table = $model->table();
$origin = file_get_contents("table.json");
file_put_contents(__DIR__ . "/table.json", json_encode($table));
file_put_contents(__DIR__ . "/crontab.log", date('H:i:s'));
