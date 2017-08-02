<?php
require_once "vendor/autoload.php";

$model = new \Tia\Consign();
$table = $model->table();
$last = json_decode(file_get_contents("table.json"), true);
file_put_contents(__DIR__ . "/table.json", json_encode($table));
file_put_contents(__DIR__ . "/crontab.log", date('H:i:s'));

$list = array_diff(array_column($table, 'id'), array_column($last, 'id'));
foreach ($list as $id) {
	foreach ($table as $item) {
		if ($item['id'] == $id && time() - strtotime($item['day']) < 86400) {
			$group = "453639001";
			$content = "{$item['name']} ({$item['day']})\n  ￥{$item['price']} × {$item['count']}\n";
			if (array_key_exists('extra', $item)) {
				$content .= "  {$item['extra']}\n";
			}
			$content .= "  {$item['href']}\n";
			$url = "http://www.langdaren.com:5000/openqq/send_group_message?uid={$group}&content=" . urlencode($content);
			file_get_contents($url);
		}
	}
}
