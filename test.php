<?php

echo "<pre>";
print_r($_GET);
print_r($_SERVER);
$url_sections = explode("/", $_SERVER["REDIRECT_URL"]);
$is_list = (BOOL)$url_sections[count($url_sections)-2] == "frestlist";
$id = array_pop($url_sections);

