<?php

namespace Crust\Installers;

use Crust\Crust;
use Crust\Helpers\Utils;

class FrontEndInstaller extends Installer implements InstallerInterface {

    protected $progressBar;
    protected $currentStep = 0;

    public function __construct(Crust $crust, $settings = [])
    {
        parent::__construct($crust);
    }

    protected function initSettings($settings)
    {
        $defaultSettings = [
            'css_preprocessor' => 'stylus'
        ];
        $this->settings = Utils::join($defaultSettings, $settings);
        $this->settings['css_preprocessor_extension'] = $this->settings['css_preprocessor'] == 'stylus' ? 'styl' : $this->settings['css_preprocessor'];
    }

    public function install()
    {
        $this->crust->renderFile('gulpfile.js', ['settings' => $this->settings]);
    }
}