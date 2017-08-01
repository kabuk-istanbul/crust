<?php

namespace Cruster\Factory\Files;

use Cruster\Cruster;
use Cruster\Factory\Objects\Theme;
use Cruster\Factory\Objects\PostType;
use Nette\PhpGenerator\GlobalFunction;
use Nette\PhpGenerator\Helpers;

class LanguageFile implements File {

    private $textDomain;
    private $languageCode;
    private $translationTexts;

    public function __construct($textDomain, $languageCode, $translationTexts = [])
    {
        $this->textDomain = $textDomain;
        $this->languageCode = $languageCode;
        $this->translationTexts = $translationTexts;
    }

    public function create()
    {
        touch($this->fileName());

        $content = $this->generateFileHeader();

        foreach ($this->translationTexts as $translationText) {
            $content .= PHP_EOL .
                'msgid "' . $translationText . '"' . PHP_EOL .
                'msgstr "' . $translationText . '"' . PHP_EOL;
        }

        file_put_contents($this->fileName(), $content);
    }

    public function fileName()
    {
        return Cruster::WP_DIR . DIRECTORY_SEPARATOR .
            'wp-content' . DIRECTORY_SEPARATOR .
            'languages' . DIRECTORY_SEPARATOR .
            $this->textDomain . '-' . $this->languageCode . '.po';
    }

    private function generateFileHeader()
    {
        return 'msgid ""' . PHP_EOL .
            'msgstr ""' . PHP_EOL;
    }
}