<?php

return array_merge_recursive(array(
    'css' => array(
        'src/assets/css/pattern.css'
    ),
    'js' => array(),
    'markup' => '_markup/'
), include dirname(__FILE__) . '/../../config.php');