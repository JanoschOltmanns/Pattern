<?php

class Pattern {

    protected $arrConfig;

    public function __construct($arrConfig)
    {
        $this->arrConfig = $arrConfig;
        require_once('Template.php');
    }

    public function getPage()
    {


        $pageTemplate = new Template('wrapper');

        $arrPatternInfos = $this->getPattern();

        $pageTemplate->sections = $arrPatternInfos['sections'];
        $pageTemplate->navigation = $this->getNavigationTemplate($arrPatternInfos['navigation']);

        echo $pageTemplate->parse();
        exit;

    }


    public function getStyleguidePage()
    {


        $pageTemplate = new Template('base');

        $pageTemplate->cssFiles = $this->getAssetFiles('css');
        $pageTemplate->jsFiles = $this->getAssetFiles('js');

        $arrPatternInfos = $this->getPattern();

        $pageTemplate->sections = $arrPatternInfos['sections'];
        $pageTemplate->navigation = $this->getNavigationTemplate($arrPatternInfos['navigation']);

        echo $pageTemplate->parse();
        exit;

    }

    private function getPattern()
    {

        $arrPatternGroups = array();

        $arrPatternGroups['sections'] = array();
        $arrPatternGroups['navigation'] = array();

        if (is_array($this->arrConfig['markup']))
        {
            $arrPatternFolder = array();
            foreach ($this->arrConfig['markup'] as $markupFolder)
            {
                $arrPatternFolder = array_merge($arrPatternFolder, glob($markupFolder . '*'));
            }
        }
        else
        {
            $arrPatternFolder = glob($this->arrConfig['markup'] . '*');
        }

        if ($arrPatternFolder)
        {
            foreach ($arrPatternFolder as $index => $strPatternFolder)
            {
                if (is_dir($strPatternFolder))
                {

                    $arrPathinfos = pathinfo($strPatternFolder);

                    $patternGroupTemplate = new Template('pattern');

                    $strPatternGroupName = $this->getSectionNameFromFolder($arrPathinfos['basename']);
                    $strPatternGroupKey = $this->getPatternIdFromFolder($arrPathinfos['basename']);

                    $patternGroupTemplate->name = $strPatternGroupName;
                    $patternGroupTemplate->key = $strPatternGroupKey;

                    $arrPatternGroups['navigation'][$index] = array(
                        'name' => $strPatternGroupName,
                        'key' => $strPatternGroupKey
                    );

                    $arrSectionFiles = glob($strPatternFolder . '/*.{html5,png}', GLOB_BRACE);
                    if ($arrSectionFiles)
                    {
                        $arrPatternGroups['navigation'][$index]['items'] = array();
                        $arrPatternContents = array();
                        foreach ($arrSectionFiles as $strSectionFile)
                        {
                            if (is_file($strSectionFile))
                            {
                                $arrFileInfos = pathinfo($strSectionFile);

                                $patternTemplate = new Template('section');

                                if ('.png' == substr($strSectionFile, -4)) {
                                    // Handling von Platzhalter-Bilder
                                    $strPatternName = $this->getSectionNameFromFolder($arrFileInfos['filename']);
                                    $strPatternKey = $this->getPatternIdFromFolder($arrFileInfos['filename']);

                                    $patternTemplate->name = $strPatternName;
                                    $patternTemplate->image = true;
                                    $patternTemplate->imageSrc = $strSectionFile;

                                    $arrPatternContents[] = $patternTemplate->parse();

                                    $arrPatternGroups['navigation'][$index]['items'][] = array(
                                        'name' => $strPatternName,
                                        'key' => $strPatternKey
                                    );

                                } else {
                                    $strPatternName = $this->getSectionNameFromFolder($arrFileInfos['filename']);
                                    $strPatternKey = $this->getPatternIdFromFolder($arrFileInfos['filename']);
                                    $patternTemplate->name = $strPatternName;
                                    $patternTemplate->key = $strPatternKey;
                                    $patternTemplate->src = $strSectionFile;
                                    $patternTemplate->content = file_get_contents($strSectionFile);

                                    $arrPatternContents[] = $patternTemplate->parse();

                                    $arrPatternGroups['navigation'][$index]['items'][] = array(
                                        'name' => $strPatternName,
                                        'key' => $strPatternKey
                                    );
                                }

                            }
                        }

                        $patternGroupTemplate->contents = $arrPatternContents;
                    }
                    //$sectionTemplate->contents

                    $arrPatternGroups['sections'][] = $patternGroupTemplate->parse();
                }

            }
        }

        return $arrPatternGroups;

    }

    private function getPatternIdFromFolder($strFolder)
    {
        $strKey = 'pattern_' . $strFolder;
        preg_match('/^(\d*)-(.*)$/', $strFolder, $arrMatches);

        if ($arrMatches[1]) {
            $strKey = $arrMatches[2] . "_" . $arrMatches[1];
        }

        return $strKey;
    }

    private function getSectionNameFromFolder($strFolder)
    {

        $strName = $strFolder;
        preg_match('/^\d*-(.*)$/', $strFolder, $arrMatches);
        if ($arrMatches[1]) {
            $strName = ucwords(str_replace('_', ' ', $arrMatches[1]));
        }

        return $strName;
    }

    private function getNavigationTemplate($arrNavigationStructure)
    {

        $navigationTemplate = new Template('navigation');

        $navigationTemplate->items = $arrNavigationStructure;


        return $navigationTemplate->parse();
    }

    private function getAssetFiles($strSuffix)
    {
        $arrFiles = array();

        if ($this->arrConfig[$strSuffix])
        {

            foreach ($this->arrConfig[$strSuffix] as $varAssetFile)
            {

                if (is_file($varAssetFile))
                {
                    $arrFiles[] = $varAssetFile;
                }
                elseif (is_dir($varAssetFile))
                {
                    $arrAssetFiles = glob($varAssetFile . "*." . $strSuffix);

                    if ($arrAssetFiles)
                    {
                        foreach ($arrAssetFiles as $varFolderAssetFile)
                        {
                            if (is_file($varFolderAssetFile))
                            {
                                $arrFiles[] = $varFolderAssetFile;
                            }
                        }
                    }
                }
            }

        }

        return $arrFiles;
    }

}
