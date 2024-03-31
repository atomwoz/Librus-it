<?php

//Getted from https://stackoverflow.com/questions/2778110/change-innerhtml-of-a-php-domelement
//Couse, myślałem że padne pisząc to sam
function set_inner_html( $element, $content ) {
  $DOM_inner_HTML = new DOMDocument();
  $internal_errors = libxml_use_internal_errors( true );
  $DOM_inner_HTML->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
  libxml_use_internal_errors( $internal_errors );
  $content_node = $DOM_inner_HTML->getElementsByTagName('body')->item(0);
  $content_node = $element->ownerDocument->importNode( $content_node, true );
  while ( $element->hasChildNodes() ) {
      $element->removeChild( $element->firstChild );
  }
  $element->appendChild( $content_node );
}

class Grade
{
  public function __construct($var = null, $weight = 0) {
    $this->var = $var;
    $this->weight = $weight;
  }
  public function get_my_avg()
  {
    $a = round($this->var,2);
    return number_format($a, 2, '.', '');
  }
  public function get_avg(Grade $grade)
  {

    
    $a = ($this->var * $this->weight + $grade->var * $grade->weight) / ($this->weight + $grade->weight);
    $a = round($a,2);
    $a = number_format($a, 2, '.', '');
    return $a;
  }
  public function __toString()
  {
    return $this->var;
  }
}

function parse_avg($html) {
  @$doc = new DOMDocument();
  @$doc->loadHTML($html);

  $xpath = new DOMXPath($doc);
  $ee = [];
  $next_avg= 0;
  $subj_avg = 0;

  $elements = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' line0 ')]");
  foreach ($elements as $element) {
    array_push($ee, []);
    $tdki = $element->getElementsByTagName("td");
    foreach ($tdki as $td) {
      if($next_avg > 0)
      {
        set_inner_html($td, $next_avg);
        $next_avg = 0;
      }
      $avg = process_element($td);
      if($avg != "")
      {
        array_push($ee[count($ee)-1], $avg);
        $next_avg = $avg->get_my_avg();
      }
    }
    if(count($ee[count($ee)-1]) == 0)
      continue;
    $year_avg_td = $element->getElementsByTagName("td")->item(9);
    $x = $ee[count($ee)-1][0]->get_avg($ee[count($ee)-1][1]);
    set_inner_html($year_avg_td, $x);
   
  }
  $html = $doc->saveHTML();
  return [$html, $ee];
}

function is_year_grade($grade)
{
  $grade = $grade->getElementsByTagName("a")->item(0)->getAttribute("title");
  return strpos($grade, "Kategoria: śródroczna") !== false || strpos($grade, "Kategoria: przewidywana śródroczna") !== false;
}

function escape_pluses_minuses_grades($grade)
{
  //Replace grades with pluses and minuses with numbers
  $grade = str_replace("3+", 3.5, $grade);
  $grade = str_replace("3-", 2.75, $grade);
  $grade = str_replace("4+", 4.5, $grade);
  $grade = str_replace("4-", 3.75, $grade);
  
  //Its posible that below grades doesn't exists
  $grade = str_replace("5+", 5.5, $grade);
  $grade = str_replace("5-", 4.75, $grade);
  $grade = str_replace("6-", 5.25, $grade);
  $grade = str_replace("6+", 6.5, $grade);
  $grade = str_replace("2-", 1.75, $grade);
  $grade = str_replace("2+", 2.5, $grade);
  return $grade;
}

function get_grade_weight($grade)
{
  $grade = $grade->getElementsByTagName("a")->item(0)->getAttribute("title");
  @$weight = explode("Waga: ", $grade)[1];
  @$weight = explode("<", $weight)[0];
  $weight = intval($weight);
  return $weight;
}

function process_element($element)
{
  $grades = $element->getElementsByTagName("span");
  $sum = 0;
  $count = 0;
  foreach ($grades as $grade) {
    if($grade->getAttribute("class") != "grade-box") continue;
    if(is_year_grade($grade)) continue;
    if(get_grade_weight($grade) == 0) continue;
    
    $bg_color = $grade->getAttribute("style");
    $value = $grade->nodeValue;
    $value = escape_pluses_minuses_grades($value);
    if (is_numeric($value)) {
      $sum += ($value*get_grade_weight($grade));
      $count += get_grade_weight($grade);
    }
  }
  if ($count > 0) {
    return new Grade($sum / $count, $count);
  }
  
}

?>