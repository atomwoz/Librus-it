<?php
require_once "preambule.php";
require_once ".passy.php";
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
//Remove trailing </html>
$c = preg_replace('/<\/html>.*/s', '', $c);

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