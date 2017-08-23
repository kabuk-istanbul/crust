<?php

namespace Crust\Console;

use Crust\Crust;
use Crust\Helpers\Klasor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

class InstallWordpressCommand extends Command
{
    private $progressBar;
    private $currentStep = 0;
    private $scope;

    protected function configure()
    {
        $this
            ->setName('install')
            ->setDescription('Download and install Wordpress.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->scope = new Crust($input, $output);
        $this->downloadWP();
        $this->extractWP();
    }

    protected function downloadWP()
    {
        $wp_zip_file = Crust::TEMP_DIR . '/latest.zip';

        if (!$this->scope->fs->exists($wp_zip_file)) {

            $this->scope->output->writeln('<title>Downloading Wordpress</title>');
            $this->progressBar = $this->scope->progressBar();
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
                    $this->scope->output->writeln('');
                }
                else {
                    $this->progressBar->advance();
                }
            }
        }
    }

    protected function extractWP()
    {
        $this->scope->output->write('Extracting files');

        $wp_zip_file = Crust::TEMP_DIR . '/latest.zip';

        $zip = new ZipArchive();
        if ($zip->open($wp_zip_file)) {
            $zip->extractTo(Crust::TEMP_DIR);
            $zip->close();

            if (Klasor::copyDirContents(Crust::TEMP_DIR . '/wordpress', './')) {
                $this->scope->output->writeln(' <success>✓</success>');
            }
        }
        else {
            $this->scope->output->writeln('<error>Cannot extract zip file.</error>');
        }
    }
}