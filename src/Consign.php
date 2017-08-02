<?php
namespace Tia;

class Consign {
	public function table() {
		$list = array_merge($this->uu(), $this->dd());
		foreach ($list as $key => $value) {
			if (in_array($value['type'], ['激活码', '通用点数'])) {
				unset($list[$key]);
				continue;
			}
		}
		return array_values($list);
	}

	public function uu() {
		$list = array();
		for ($i = 1; $i < 99; $i++) {
			$url = "http://www.uu898.com/newTrade.aspx?gm=531&area=2782&srv=29839&o=0&sa=0&p=$i";
			$content = file_get_contents($url);
			if (strpos($content, "NonCommodity") !== false) {
				break;
			}
			preg_match_all("/<ul id=\"ES[\s\S]*?<\/ul>/i", $content, $matches);
			foreach ($matches[0] as $ul) {
				$item = array();
				$item['from'] = 'uu';
				preg_match("/<h2><a href=\"([^\"]+)\".*?<\/i>(.*?)<\/a> <\/h2>/i", $ul, $current);
				$item['href'] = $current[1];
				$item['name'] = preg_replace("/<i.*?<\/i>/i", '', $current[2]);
				preg_match("/<p>([\s\S]*?)<\/p>/i", $ul, $current);
				$item['type'] = str_replace("物品类型：", "", trim($current[1]));
				preg_match("/<h5 title=\"\">(\d+)<\/h5>/i", $ul, $current);
				$item['count'] = $current[1];
				if ($item['type'] == '游戏币') {
					preg_match("/<h6.*?<span>1元=([\.\d]+)([^<]+)<\/span><span>.*?<\/span><\/h6>/i", $ul, $current);
					$unit = $current[1];
					if ($current[2] == '万金') {
						$unit = $unit * 10000;
					}
					$item['extra'] = round($unit, 2);
				}
				preg_match("/&yen;<span style=\"font-size: 18px;\"> ([\d\.]+)<\/span>/", $ul, $current);
				$item['price'] = $current[1];
				preg_match("/<ul id=\"(ES.*?)\"/i", $ul, $current);
				$item['id'] = $current[1];
				preg_match("/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/i", $item['id'], $current);
				$item['day'] = $current[1] . '-' . $current[2] . '-' . $current[3] . ' ' . $current[4] . ':' . $current[5] . ':' . $current[6];
				$list[] = $item;
			}
		}
		return $list;
	}

	public function dd() {
		$list = array();
		for ($i = 1; $i < 99; $i++) {
			$url = "http://www.dd373.com/s/u4jfm7-ed0vjn-ma3bff-0-0-0-0-0-0-0-0-ct-0-512-{$i}.html";
			$content = file_get_contents($url);
			preg_match_all("/<div class=\"box money_ner\">([\s\S]*?)ensure ensure_normal left/i", $content, $matches);
			if (count($matches[0]) == 0) {
				break;
			}
			foreach ($matches[0] as $div) {
				$item = array();
				$item['from'] = 'dd';
				preg_match("/<a id=\"biz.*?href=\"([^\"]+)\" title=\"([^\"]+)/i", $div, $current);
				$item['href'] = 'http://www.dd373.com' . $current[1];
				$item['name'] = $current[2];
				preg_match("/<div class=\"zhonglei\"[^>]+>物品种类：<a[^>]+>(.*?)<\/a>/", $div, $current);
				$item['type'] = $current[1];
				preg_match("/<div class=\"num left\">([\s\S]*?)<\/div>/i", $div, $current);
				$item['count'] = trim($current[1]);
				preg_match("/<strong><span>([\d\.]+)<\/span>元/", $div, $current);
				$item['price'] = $current[1];
				$detail = file_get_contents($item['href']);
				preg_match("/pytext=\"(.*?)\">\[复制物品编号\]/", $detail, $current);
				$item['id'] = $current[1];
				preg_match("/.{2}(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/", $detail, $current);
				$item['day'] = $current[1] . '-' . $current[2] . '-' . $current[3] . ' ' . $current[4] . ':' . $current[5] . ':' . $current[6];
				if ($item['type'] == '游戏币') {
					preg_match("/<dt class=\"left\">单件数量：(\d+)([^<]+)<\/dt>/", $detail, $matches);
					$unit = $matches[1] / $item['price'];
					if ($matches[2] == '万金') {
						$unit = $unit * 10000;
					}
					$unit = round($unit, 4);
					$item['extra'] = round($unit, 2);
				}
				$list[] = $item;
			}
		}
		return $list;
	}
}
