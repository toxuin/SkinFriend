Этот файл нужно установить как обработчик ошибки 404 и разрешить листинг директорий. При этом, он не должен переадресовывать вас на другой URL, т.е. если вы запрашивали asdasd.png, то адрес страницы не должен изменяться. Это можно сделать изменив настройки веб-сервера.

Для сервера Apache достаточно создать в папке со скинами файл .htaccess и написать в него:

     Options +Indexes
     ErrorDocument 404 fakefile.php

Для lighttpd нужно включить следующую настройку в конфиг-файле lighttpd.conf:

     dir-listing.activate = "enable"
     server.error-handler-404 = "/MinecraftSkins/fakefile.php"

Этот скрипт нужно положить в папку со скинами, именно ту, где лежат .png.
Не советую изменять что-либо в этом файле, кроме массива соседей - $locations. Скрипт не должен выдавать ошибок или текстовой информации. Вообще. Все должны думать что он - картинка.

Посмотреть кто является соседями ваших соседей и вообще установлен ли у них такой скрипт можно дописав **?showmeyourfriends** к урлу папки со скинами соседа (например http://mycoolfriend.com/MinecraftSkins/?showmeyourfriends ).

Важно: скрипт НЕ должен отдавать статус 404, он должен говорить что все ок, т.е. 200. 

Рекомендуйте скрипт друзьям, раздавайте направо и налево! Чем нас больше - тем проще находить скины персонажей!

Изменяйте скрипт как вам удобно. Только прошу вас не делать ничего такого, что помешает или может помешать работе скрипта или игры на других серверах или клиентах.

Я буду признателен, если вы поделитесь со мной вашими изменениями, чтобы я мог внести их в свой скрипт и выложить его для всех.