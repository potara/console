<?php
/**
 * This file is part of the Potara (https://potara.org)
 *
 * @see       https://github.com/potara/core
 * @copyright Copyright (c) 2018-2020 Bruno Lima
 * @author    Bruno Lima <brunolimame@gmail.com>
 * @license   https://github.com/potara/core/blob/master/LICENSE (MIT License)
 */

namespace Potara\Console\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Docker extends Command
{
    protected static $defaultName = 'potara:docker';

    protected function configure()
    {
        $this->setDescription('Create a development environment using docker')
             ->addArgument('server-name', InputArgument::REQUIRED, 'Server_name name of nginx server', 'potara.local')
             ->setHelp('Create a development environment using docker');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting development environment with docker + nginx');
        $io->section('Verifying that the environment has already started');


        $factoryCreatePath = function ($path, $namePath) use (&$io)
        {
            if (!file_exists($path)) {
                $createPath = mkdir($path, 0755);
                if (!$createPath) {
                    $io->error("Could not create folder: {$namePath}");
                    return 0;
                }
            }
            return 1;
        };

        $defaultPathProject = getcwd() . DIRECTORY_SEPARATOR . '.docker';
        $createDockerPath   = $factoryCreatePath($defaultPathProject, '.docker');
        if ($createDockerPath == 0) {
            return $createDockerPath;
        }

        $nginxPath     = $defaultPathProject . DIRECTORY_SEPARATOR . 'nginx';
        $nginxConfPath = $nginxPath . DIRECTORY_SEPARATOR . 'conf';

        $createNginxPath = $factoryCreatePath($nginxPath, '.docker/nginx');
        if ($createNginxPath == 0) {
            return $createNginxPath;
        }

        $createNginxConfPath = $factoryCreatePath($nginxConfPath, '.docker/nginx/conf');
        if ($createNginxConfPath == 0) {
            return $createNginxConfPath;
        }

        $createPhpPath = $factoryCreatePath($defaultPathProject . DIRECTORY_SEPARATOR . 'php', '.docker/php');
        if ($createPhpPath == 0) {
            return $createPhpPath;
        }

        $samplePath = __DIR__ . DIRECTORY_SEPARATOR . 'Sample' . DIRECTORY_SEPARATOR . 'Docker' . DIRECTORY_SEPARATOR;

        $io->section('Verifying that the configuration file has already been created');
        $nginxConfFile = $nginxPath . DIRECTORY_SEPARATOR . 'nginx.conf';
        if (!file_exists($nginxConfFile)) {
            $copyFileConf = copy($samplePath . 'nginx.conf', $nginxConfFile);
            if (!$copyFileConf) {
                $io->error('Could not create configuration file for nginx');
                return 0;
            }
        }

        $serverNameConfFile = $nginxPath . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'app.conf';
        if (!file_exists($serverNameConfFile)) {

            $saveServerNameConfFile = file_put_contents($serverNameConfFile, $this->getContentAppConf($input->getArgument('server-name')));
            if (!$saveServerNameConfFile) {
                $io->error('Could not create nginx app.conf configuration file');
                return 0;
            }
        }

        $composerFile = getcwd() . DIRECTORY_SEPARATOR . 'docker-compose.yml';
        if (!file_exists($composerFile)) {

            $copyFileConf = copy($samplePath . 'docker-compose.yml', $composerFile);
            if (!$copyFileConf) {
                $io->error('It was not possible to create the docker-compose.yml');
                return 0;
            }
        }

        $io->success('Development environment successfully created');

        return 0;
    }

    /**
     * @param $servername
     *
     * @return string|string[]
     */
    protected function getContentAppConf($servername)
    {
        $getSampleContent = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Sample' . DIRECTORY_SEPARATOR . 'Docker' . DIRECTORY_SEPARATOR . 'app.conf.txt');
        return str_replace("##SERVERNAME##", $servername, $getSampleContent);
    }
}