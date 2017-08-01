<?php

namespace Cruster\Console;

use Cruster\Cruster;
use Cruster\Helpers\Klasor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class InstallWordpressCommand extends Command
{
    private $progressBar;
    private $currentStep = 0;

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Download and install Wordpress.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Cruster::init();
        Cruster::setOutput($output);
        Cruster::setInput($input);
        $this->downloadWP();
        $this->extractWP();
    }

    protected function downloadWP()
    {
        $wp_zip_file = Cruster::TEMP_DIR . '/latest.zip';

        if (!Cruster::fs()->exists($wp_zip_file)) {

            Cruster::getOutput()->writeln('<title>Downloading Wordpress</title>');
            $this->progressBar = Cruster::progressBar();
            $this->progressBar->setMessage('Starting');
            $this->progressBar->start();

            $file = fopen($wp_zip_file, 'w');

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
    }

    protected function downloading($resource, $downloadSize, $downloaded, $uploadSize, $uploaded)
    {
        $step = floor($downloaded / $downloadSize * 100);

        if ($downloadSize == 0) {
            $this->progressBar->setMessage('Connecting');
        }
        else {
            $this->progressBar->setMessage('Downloading');
            if ($step != $this->currentStep) {
                $this->currentStep = $step;
                if ($this->currentStep == 100) {
                    $this->progressBar->setMessage('<success>Downloaded</success>');
                    $this->progressBar->finish();
                    Cruster::getOutput()->writeln('');
                }
                else {
                    $this->progressBar->advance();
                }
            }
        }
    }

    protected function extractWP()
    {
        Cruster::getOutput()->write('Extracting files');

        $wp_zip_file = Cruster::TEMP_DIR . '/latest.zip';

        $zip = new ZipArchive();
        if ($zip->open($wp_zip_file)) {
            $zip->extractTo(Cruster::TEMP_DIR);
            $zip->close();

            if (Klasor::copyDirContents(Cruster::TEMP_DIR . '/wordpress', './')) {
                Cruster::getOutput()->writeln(' <success>✓</success>');
            }
        }
        else {
            Cruster::getOutput()->writeln('<error>Cannot extract zip file.</error>');
        }
    }
}