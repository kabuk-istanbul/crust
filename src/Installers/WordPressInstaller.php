<?php

namespace Crust\Installers;

use Crust\Crust;
use ZipArchive;

class WordPressInstaller extends Installer implements InstallerInterface {

    protected $progressBar;
    protected $currentStep = 0;

    public function __construct(Crust $crust, $settings = [])
    {
        parent::__construct($crust);
    }

    public function install()
    {
        $wpZipFile = Crust::TEMP_DIR . '/latest.zip';

        if (!file_exists($wpZipFile)) {
            $this->crust->output->writeln('<title>Installing WordPress...</title>');
            $this->progressBar = $this->crust->progressBar();
            $this->progressBar->setMessage('Starting');
            $this->progressBar->start();

            $file = fopen($wpZipFile, 'w');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://wordpress.org/latest.zip");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, [$this, 'downloading']);
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_FILE, $file);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
        }
        $this->extractWP();
    }

    protected function downloading($resource, $downloadSize, $downloaded, $uploadSize, $uploaded)
    {
        if ($downloadSize == 0) {
            $this->progressBar->setMessage('Connecting');
        }
        else {
            $this->progressBar->setMessage('Downloading');
            $step = floor($downloaded / $downloadSize * 100);
            if ($step != $this->currentStep) {
                $this->currentStep = $step;
                if ($this->currentStep == 100) {
                    $this->progressBar->setMessage('<success>Downloaded</success>');
                    $this->progressBar->finish();
                    $this->crust->output->writeln('');
                }
                else {
                    $this->progressBar->advance();
                }
            }
        }
    }

    protected function extractWP()
    {
        $this->crust->output->write('Extracting files');

        $wpZipFile = Crust::TEMP_DIR . '/latest.zip';
        $zip = new ZipArchive();

        if ($zip->open($wpZipFile)) {
            $zip->extractTo(Crust::TEMP_DIR);
            $zip->close();

            if ($this->crust->fs->copyDir(Crust::TEMP_DIR . '/wordpress', $this->crust->wpDir)) {
                $this->crust->output->writeln(' <success>✓</success>');
            }
        }
        else {
            $this->crust->output->writeln('<error>Cannot extract zip file.</error>');
        }
    }
}