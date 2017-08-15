<?php
require_once "vendor/autoload.php";

$model = new \Tia\Consign();
$table = $model->table();
$last = json_decode(file_get_contents(__DIR__ . "/table.json"), true);
file_put_contents(__DIR__ . "/table.json", json_encode($table));
file_put_contents(__DIR__ . "/update.log", date('H:i:s'));

$diff = array_diff(array_column($table, 'id'), array_column($last, 'id'));
foreach ($diff as $id) {
	if (!$id) {
		continue;
	}
	foreach ($table as $item) {
		if ($item['id'] == $id && time() - strtotime($item['day']) < 900 && $item['type'] == '游戏币' && $item['extra'] >= 1700) {
			var_export($item);
			$content = "{$item['name']} ({$item['day']})\n>> ￥{$item['price']} × {$item['count']}\n";
			if (array_key_exists('extra', $item)) {
				$content .= ">> 1元={$item['extra']}金\n";
			}
			$content .= ">> {$item['href']}\n";
			//echo "\r\n" . file_get_contents("http://127.0.0.1:5000/openqq/send_friend_message?uid=625523521&content=" . urlencode($content));
			echo "----------------------------------------------------------------------\r\n";
		}
	}
}
