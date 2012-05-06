<?php
set_time_limit(10);
header("HTTP/1.1 200 OK");
$version = "1.5";
$locations = array( // ниже идут ваши соседи. Не в коем случае не пишите свой собственный сервер сюда! - Это нагрузит ваш сервер по самые помидоры и, может быть, приведет к зависону.
	"http://s3.amazonaws.com/MinecraftSkins/"
 );
// Все, дальше можно ничего не трогать. Если не работает - проверьте настройки (у вас должен быть PHP и в нем должна быть поддержка cURL, также скрипт не работает без параметра "allow_url_fopen" в php.ini – включите его, хотя бы для нужной папки.

// -----------------------------------------------------
$this_page = basename(htmlspecialchars($_SERVER['REQUEST_URI']));
if (isset($_GET["showmeyourfriends"]) || $this_page == basename(__FILE__) || $this_page == basename(__DIR__)) {
	if (glob(__DIR__."/" . "*.png") != false) { $filecount = count(glob(__DIR__."/" . "*.png")); } else { $filecount = 0; } echo "<h1>Привет!</h1>"; $i=0;
	foreach ($locations as $count => $url) { $count++; echo "Сосед ".$count.": ".$url."<br />"; $friends["$i"] = $url; $i++;}
	echo "\r\n<br />Моя версия: 1.5\r\n<br />скинов в базе: ".$filecount."\r\n<br />меня зовут: ".basename(__FILE__)."\r\n<br />мой md5: ".md5_file(__FILE__)."<br /><br /><span style='font-size: 11px; color: #EEEDED;'>А вот вам json, на всякий случай:<br />\r\n\r\n".json_encode(array('version' => $version, 'scriptname' => basename(__FILE__), 'count' => $filecount, 'friends' => $friends)); exit;
}

if (!function_exists('curl_multi_init')) { // проверка требований. Для работы скрипта нужен cURL.
echo ('Не обнаружен модуль cURL! Сделайте "apt-get install php5-curl"'); exit;
} elseif (ini_get('allow_url_fopen') != 1) { @ini_set('allow_url_fopen', '1'); }
if (file_exists(ucfirst($this_page))) { // На самом деле это фикс для linux-серверов и AuthMe-авторизации. Даже если у вас нет AuthMe - не советую убирать. Даже если у вас сервер на Windows - это не повредит.
	header("Content-type: image/png"); // йа картинко
	readfile(ucfirst($this_page));
	exit; //иначе будет кромешный пиздец!
} else {
	// GOTO амазоновое хранилище майнкрафта
	$file = "http://s3.amazonaws.com/MinecraftSkins/".$this_page;
	$readfile = file_get_contents($file);
	if ($readfile != "") {
		header("Content-type: image/png"); // йа картинко, чесна-чесна
		echo $readfile;
		exit; //иначе будет вы знаете што.
	} else {

		foreach ($locations as $name => $url) {
			if ($url == "http://".$_SERVER['SERVER_NAME']."/".basename(__DIR__)."/") { exit; } // защита от идиота, добавившего самого себя в соседи. Не гарантирует точность отбора идиотов, но она хотя бы старается.
			$locations[$name] = $url.$this_page;
		}
		
			$mh = curl_multi_init(); // магия начинается тут.
			foreach ($locations as $i => $url) {
			    $conn[$i] = curl_init($url);
			    curl_setopt_array($conn[$i], array(CURLOPT_TIMEOUT => 5, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true));
			    curl_multi_add_handle($mh, $conn[$i]);
			}
			
			// часть, которая убивает скрипт при первом ответившем 200 ОК соединении
			$running = null;
			do {
				$status = curl_multi_exec($mh,$running);
			    $ready = curl_multi_select($mh); // проверка, паузит цикл!
			    if ($ready <= 0){
			        while ($info = curl_multi_info_read($mh)){
			            if (curl_getinfo($info['handle'],CURLINFO_HTTP_CODE) == 200){
			                $successUrl = curl_getinfo($info['handle'],CURLINFO_EFFECTIVE_URL);
							$html = curl_multi_getcontent($info['handle']);
			                break 2; // выходим из двух циклов
			            }
			        }
			    }
			} while ($running > 0 && $ready != -1);
			
				//ну все, делаем дела.
				header("Content-type: image/png"); // йа картинко, все еще
				echo $html; // показываем картинку
			
			foreach ($locations as $i => $url) {
			    curl_close($conn[$i]);
			    curl_multi_remove_handle($mh,$conn[$i]);
			}
			curl_multi_close($mh); // конец магии :(
	} // это конец поиска в хранилище амазона
} // это конец поиска альтернативной картинки
?>