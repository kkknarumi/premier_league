<?php

$url = "https://www.google.co.jp/search?q=%E6%B2%96%E7%B8%84%E3%80%80%E9%AB%98%E7%B4%9A%E3%83%9B%E3%83%86%E3%83%AB";
$html = file_get_contents($url);
$html = mb_convert_encoding($html,"utf-8","sjis");

//<h3 class=r>タグ内のみ取得する正規表現
$pattern = "/\<h3 class=\"r\">.*?\<\/h3>/";

//上記、正規表現で取得できるHTMLを配列で取得
preg_match_all($pattern , $html , $a_results);

if($a_results){
	$i = 0;

	foreach($a_results[0] as $val){
		if($i >= 10){
			break;
		}

		//タイトル部分を抽出する
		$title = preg_replace('/\<h3 class=\"r\">\<a.*?\">(.*?)\<\/a>\<\/h3>/' , '$1' , $val);
		$search_b_tag = array('<b>','</b>');
		$title = str_replace($search_b_tag , '' , $title);

		//URL部分を抽出する
		$url = preg_replace('/\<h3 class=\"r\">\<a href=\"\/url\?q=(.*?)&.*?$/', ' $1 ' , $val);

		//タイトルとURLを出力
		echo("<<< ".mb_convert_encoding ($title,"utf-8","auto")." >>>\r\n");
		echo($url."\r\n");
		echo("--------------\r\n");

		$i++;
	}
}

?>
