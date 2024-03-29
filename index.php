<?php
require_once "preambule.php";
require_once ".passy.php";
require_once "avg_parser.php";

get("https://api.librus.pl/OAuth/Authorization?client_id=46&response_type=code&scope=mydata");
$auth_arr = [
  'login' => $login,
  'pass' => $password,
  'action' => 'login'
];
post("https://api.librus.pl/OAuth/Authorization?client_id=46",$auth_arr);
get("https://api.librus.pl/OAuth/Authorization/2FA?client_id=46");
$c = get("https://synergia.librus.pl/przegladaj_oceny/uczen");
$c = html_replace_relative_url($c, "https://synergia.librus.pl/"); 

//Remove sorting (class sort_box)
$c = preg_replace('/<table class="right sort_box">.*?<\/table>/s', '', $c);

//Remove trailing </html>
$c = preg_replace('/<\/html>.*/s', '', $c);

//Add averages
$a = parse_avg($c);

$c = $a[0];
$grades = $a[1];


$c .= <<<__EOI__

<style>@font-face {
  font-family: 'librus';
  src: url('librus.woff');
  src: url('librus.woff') format('embedded-opentype'), url('librus.woff') format('woff');
  font-weight: normal;
  font-style: normal;
}</style> </html>
__EOI__;




print_r($c);

?>