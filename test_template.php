<?php
include('fonctions/divers/renderTemplate.php');
define("TEMPLATES_DIR", 'templates');

$args = array(
    "simple" => "very simple",
    "nest" => "nesting",
    "iterable" => array("iteration" => "iterred", "nesting" => "nested"),
    "petit" => 4,
    "petit_text" => 'petit',
    "grand" => 8,
    "grand_text" => 'moins petit',
    "tres_petit" => 1,
    "tres_grand" => 10,
    "tres_petit_text" => 'petit',
    "tres_grand_text" => 'grand',
    "faux" => false,
    "vrai" => true,
    "key_tab" => array("a"=>1, "b"=>2, "c"=>3),
    "fib" => array(1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89, 144),
);
echo renderTemplate("test.html", $args);
?>
