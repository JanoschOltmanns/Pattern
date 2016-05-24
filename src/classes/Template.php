<?php

class Template {

    private $strTemplatePath;

    public function __construct($strTemplatePath)
    {
        $this->strTemplatePath = $strTemplatePath;
    }

    public function parse()
    {

        ob_start();
        include "src/templates/" . $this->strTemplatePath . ".html5";

        $strBuffer = ob_get_contents();

        ob_end_clean();

        return $strBuffer;
    }

}