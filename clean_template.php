<?php
$file = 'c:/xampp/htdocs/sms/resources/views/frontend/secondary.blade.php';
$content = file_get_contents($file);

$content = str_replace('<meta name="author" content="TemplateMo">', '', $content);
$content = str_replace('TemplateMo 569 Edu Meeting', '', $content);
$content = str_replace('https://templatemo.com/tm-569-edu-meeting', '', $content);
$content = str_replace('TemplateMo is the best website for Free CSS', 'Learn with the best resources available', $content);

file_put_contents($file, $content);
echo "Cleaned up TemplateMo references.\n";
