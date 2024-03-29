<?php
$cookie_file_path = "cookies.txt"; 

function get($url)
{
  global $cookie_file_path;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path); // Save cookies to file
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path); // Read cookies from file
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  $res = curl_exec($ch);
  return $res;
}

function post($url, $data)
{
  global $cookie_file_path;
  $data = http_build_query($data);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path); // Save cookies to file
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path); // Read cookies from file
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  $res = curl_exec($ch);
  return $res;
}

function html_replace_relative_url($html, $root_url)
{
 //Zamienia <link rel="stylesheet" href="/js/librus-component/panel/librus-panel.css?v1" /> na <link rel="stylesheet" href="https://synergia.librus.pl/js/librus-component/panel/librus-panel.css?v1" />
  $html = preg_replace('/(href|src)="(?!http)([^"]*)"/', '$1="'.$root_url.'$2"', $html);
  return $html;
}

?>