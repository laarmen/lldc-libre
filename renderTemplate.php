<?php
/* Copyright 2012 Simon Chopin <chopin.simon@gmail.com>
 * This file is published under the terms of the WTFPLv2,
 * please see COPYING for more details.
 *
 * These functions are provided in the hope that they will help, but
 * with absolutely NO WARRANTY whatsoever.
 */
function expandVariable(&$obj, $string, &$replVariables) {

    $matches = array();

    if (preg_match('/^$/', $string))
        return $obj;

    if (preg_match('/^\.([a-zA-Z_][a-zA-Z_0-9]*)(.*)$/', $string, $matches))
        return expandVariable($obj->{$matches[1]}, $matches[2], $replVariables);

    if (preg_match('/^\[(.*)\](.*)$/', $string, $matches))
        // Here, we have to interpret the value between the [], so we reuse the callback.
        // The only problem being, the callback takes a regexp match, so we simulate one.
        return expandVariable($obj[$replVariables(array(1=>$matches[1]))], $matches[2], $replVariables);
}

function boolEvaluation($string, &$replVariables) {
    // Put all the operators in an array. Hopefully PHP devs are smart enough to use a BT behind the scene... Mwahaha, who am I kidding !?
    $token_eval = function($token) use (&$replVariables) {
        if ($token[0] == '!')
            return !($replVariables(substr($token, 1)));
        $return_value = $replVariables($token);
        return $replVariables($token);
    };
    $operators = array(
        '>=' => function($a, $b) use (&$token_eval) {return $token_eval($a) >= $token_eval($b);},
        '<=' => function($a, $b) use (&$token_eval) {return $token_eval($a) <= $token_eval($b);},
        '<' => function($a, $b) use (&$token_eval) {return $token_eval($a) < $token_eval($b);},
        '>' => function($a, $b) use (&$token_eval) {return $token_eval($a) > $token_eval($b);},
        '==' => function($a, $b) use (&$token_eval) {return $token_eval($a) == $token_eval($b);},
        '!=' => function($a, $b) use (&$token_eval) {return $token_eval($a) != $token_eval($b);}
    );

    // Don't sweat it, and just allow for the very simple case.
    $tokens = explode(" ", (trim($string)));
    $nb_tokens = count($tokens);
    if ($nb_tokens == 1)
        return $token_eval($tokens[0]);
    if ($nb_tokens == 3)
        return $operators[$tokens[1]]($tokens[0], $tokens[2]);
    throw 'Ta maman !';
}

function parse_for($string) {
    $matches = array();
    if (preg_match('/^(?:(\w+)(?: ?,(\w+))?) in (.+)$/', trim($string), $matches)) {
        if ($matches[2])
            return array($matches[3], $matches[1], $matches[2]);
        return array($matches[3], $matches[1]);
    }
    throw "Ton frangin !";
}

/*
 * Note: skip sert à passer tous les calculs pour du texte qu'on n'incluera pas.
 * Ça sert si la condition d'un if est fausse, dans les else, et aussi quand un
 * block a déjà été écrit par un fils. On doit quand même parcourir le reste
 * à cause des balises embriquées, d'où le flag.
 *
 * TOUS les arguments passés en référence sont susceptibles d'être modifiés.
 * L'itérateur, bien sûr, ainsi que les renderedBlocks, mais aussi les arguments :
 * ils contiennent aussi les variables locales des boucles.
 */
function renderLineArray(&$iterator, &$tplArgs, &$renderedBlocks, &$parent, $ending = NULL, $skip=false, &$for_preread=NULL) {

    $replVariables = function($m) use (&$tplArgs, &$replVariables) {
        $matches = array();
        $string = trim($m[1]);

        // A litteral string.
        if (preg_match('/^[\'"](.*)[\'"]/', $string, $matches))
            return $matches[1];

        if (preg_match('/^[0-9]+$/', $string, $matches))
            return (int)$string;

        // A symbolic value. We determin the base value, and hand
        // the rest to expandVariable to derive the value through [] and .
        if (preg_match('/^(\w+)(.*)$/', $string, $matches))
            return expandVariable($tplArgs[$matches[1]], $matches[2], $replVariables);
    };
    $usableReplVariables = function($a) use(&$replVariables) { return $replVariables(array(0, $a));};
    $return_str = '';
    $ending_markups = array(
        'if'=>array('endif'),
        'block'=>array('endblock'),
        'for'=>array('endfor'),
    );
    /* else is here a special case, since its exit markup is already declared, so we consider it a standalone markup WRT the prereading. */
    $skip_monocommands = array("extends", "else");
    while ($iterator->valid()) {
        $line = $iterator->current();
        if (is_array($for_preread)) {
            if (!array_key_exists($iterator->key(), $for_preread)) {
                $for_preread[$iterator->key()] = $line;
            }
        }

        $matches = array();
        if (preg_match('/^(.*?)\{%(.*?)%\}(.*)$/', $line, $matches)) {
            if (!$skip && $for_preread == NULL)
                $return_str .= preg_replace_callback('/\{\{(.*?)\}\}/', $replVariables, $matches[1]);
            $commands = explode(' ', trim($matches[2]), 2);
            //echo '<br />Detecting a new template control markup : '.$commands[0].'<br />';
            $iterator->offsetSet($iterator->key(), $matches[3]);
            if ($ending) {
                //echo "Looking for an ending.<br />";
                foreach ($ending as $e)
                    if ($commands[0] == $e) {
                        //echo "It is an ending :-)<br />";
                        return array($return_str, $e);
                    }
            }
            if ($for_preread != NULL) {
                if (array_search($commands[0], $skip_monocommands) === FALSE) {
                    //echo "Command : ".$commands[0].' ; ending : ';
                    //foreach($ending as $e)
                        //echo $e.', ';
                    //echo '<br />';

                    $return_array = renderLineArray(
                        $iterator, $tplArgs,
                        $renderedBlocks, $parent,
                        $ending_markups[$commands[0]],
                        $skip, $for_preread);
                }
            } else if ($commands[0] == "block") {
                $tmp_skip = $skip;
                if (!$skip && isSet($renderedBlocks[$commands[1]])) {
                    $return_str .= $renderedBlocks[$commands[1]];
                    $tmp_skip = true;
                }
                $tmp = renderLineArray(
                    $iterator, $tplArgs,
                    $renderedBlocks, $parent, array('endblock'), $tmp_skip);
                $new_block = $tmp[0];
                $renderedBlocks[$commands[1]] = $new_block;
                if (!$tmp_skip)
                    $return_str .= $new_block;

            } else if ($commands[0] == "if") {
                $if_skip = ($skip || !boolEvaluation($commands[1], $usableReplVariables));
                $return_array = renderLineArray(
                    $iterator, $tplArgs,
                    $renderedBlocks, $parent,
                    array('else', 'endif'),
                    $if_skip);
                if (!$if_skip) {
                    $return_str .= $return_array[0];
                }
                if ($return_array[1] == "else") {
                    $else_array = renderLineArray($iterator, $tplArgs,
                        $renderedBlocks, $parent, array('endif'),
                        ($skip || !$if_skip));
                    if (!$skip && $if_skip) {
                        $return_str .= $else_array[0];
                    }
                }
            } else if ($commands[0] == "for") {
                if ($skip) {
                    renderLineArray($iterator, $tplArgs, $renderedBlocks,
                        $parent, array("endfor"), $skip);
                } else {
                    $for_args = parse_for($commands[1]);
                    // Make a first pass to save the lines.
                    $save = array();
                    renderLineArray(
                        $iterator, $tplArgs,
                        $renderedBlocks, $parent,
                        $ending_markups[$commands[0]],
                        $skip, $save
                    );
                    if (count($for_args) == 2) {
                        $iterable = $usableReplVariables($for_args[0]);
                        foreach($iterable as $value) {
                            $tplArgs[$for_args[1]] = $value;
                            $return = renderLineArray(new ArrayIterator($save), $tplArgs, $renderedBlocks,
                                $parent, array("endfor"), $skip);
                            $return_str .= $return[0];
                        }
                    } else if (count($for_args) == 3) {
                        $iterable = $usableReplVariables($for_args[0]);
                        foreach($iterable as $key=>$value) {
                            $tplArgs[$for_args[1]] = $key;
                            $tplArgs[$for_args[2]] = $value;
                            $return = renderLineArray(new ArrayIterator($save), $tplArgs, $renderedBlocks,
                                $parent, array("endfor"), $skip);
                            $return_str .= $return[0];
                        }
                    } else throw "Ton papa !";
                }
            } else if (!$skip && $commands[0] == "extends") {
                $filename_matches = array();
                if (preg_match('%"([-A-Za-z0-9_./]+\.html)"%',
                    $commands[1], $filename_matches)) {
                    $parent = $filename_matches[1];
                }
                else
                    throw "Ill-formed extension file name.";
            }
        } else {
            if (!$skip)
                $return_str .= preg_replace_callback('/\{\{(.*?)\}\}/', $replVariables, $line);
            $iterator->next();
        }
    }

    if (count($ending) > 0) {
        echo $return_str;
        echo "\nExpected endings (".count($ending).") :";
        foreach($ending as $e)
            echo "\n".$e;
        echo "\n";
        throw "$ending expected !";
    }
    return array($return_str, NULL);
}

/**
 * Renders a template using the given parameters. The template syntax is similar
 * to Django's, with support for if's, 'for .. in ..' loops, blocks and template inheritance.
 * The ignoreInheritance parameter is a bonus to allow the rendering of only part of a page.
 * It might come handy with AJAX.
 */
function renderTemplate($fileName, &$tplArgs, $ignoreInheritance = False, &$renderedBlocks = NULL) {

    $iterator = new ArrayIterator(file($fileName));

    if (!$renderedBlocks)
        $renderedBlocks = array();

    $parent = NULL;
    $tmp = renderLineArray($iterator, $tplArgs, $renderedBlocks, $parent);
    $str = $tmp[0];
    if ($ignoreInheritance || !$parent)
        return $str;

    return renderTemplate($parent, $tplArgs, $ignoreInheritance, $renderedBlocks);
}

