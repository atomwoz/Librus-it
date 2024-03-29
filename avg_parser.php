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


function parse_avg($html) {
  @$doc = new DOMDocument();
  @$doc->loadHTML($html);

  $xpath = new DOMXPath($doc);
  $ee = [];

  $elements = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' line0 ')]");
  foreach ($elements as $element) {
    $ee[] = process_element($element);
  }

  $elements = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' line1 ')]");
  foreach ($elements as $element) {
    $ee[] = process_element($element);
  }

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

function replace_img_with_text($element, $text)
{
  $img = $element;
  set_inner_html($img, $text);
}

function process_element($element)
{
  $grades = $element->getElementsByTagName("span");
  $sum = 0;
  $count = 0;
  $ee = [];
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
      echo "<span style=\"; ". $bg_color. "\"> " . $value . "</span>:". get_grade_weight($grade) ." + ";
    }
    else
    {
      echo $value . " + ";
    }
  }
  if ($count > 0) {
    $avg = $sum / $count;
    $avg = round($avg, 2);
    $avg = str_replace(".", ",", $avg);
    $avg_elem = $element->getElementsByTagName("td")->item(9);
    set_inner_html($avg_elem,  $avg);
    $ee[] = $avg;
    echo "<span style=\"color:green\"> Średnia: " . $avg . "</span><br>";
  }
  return $ee;
}













function process_element_2($element) {
  $avg = 0;
  $count = 0;
  $sum = 0;
  $children = $element->childNodes;

  foreach ($children as $child) {
    if ($child->nodeName == "td") {
      $text = $child->nodeValue;
      
      if (is_numeric($text) && $text > 0) {
        $sum += $text;
        $count++;
        echo  "<span style=\"color:red\">" . $text . "</span> + ";
      }
      else
      {
        echo $text . " + ";
      }
    }
  }

  if ($count > 0) {
    $avg = $sum / $count;
    $avg = round($avg, 2);
    $avg = str_replace(".", ",", $avg);
    echo "<span style=\"color:green\"> Średnia: " . $avg . "</span><br>";
    $element->nodeValue .= " Średnia: " . $avg;
  }
}

?>