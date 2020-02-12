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

class Init extends Command
{
    protected static $defaultName = 'potara:init';

    protected function configure()
    {
        $this
            ->setDescription('Start new project')
            ->addArgument('no-check-install', InputArgument::OPTIONAL, 'Checks if Potara is already installed')
            ->setHelp('Starts a new project using Potara, for creating modules');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting project with Potara');

        if (!$input->getArgument('no-check-install') == 'no-check-install') {
            if (!class_exists("\Potara\Core\Crud\ConfigModuleInterface")) {
                $io->error('Potara not installed');
                $io->note(['run the command to install potara', 'composer require potara/core']);
                return 0;
            }
        } else {
            $io->note('Disabled: Check if Potara is installed');
        }

        $io->section('Checking if the project has already started');
        $currentPath        = getcwd();
        $defaultPathProject = $currentPath . DIRECTORY_SEPARATOR . 'app';
        if (!file_exists($defaultPathProject)) {
            $createPathProject = mkdir($defaultPathProject, 0755);
            if (!$createPathProject) {
                $io->error('Could not create project paw');
                return 0;
            } else {
                $io->success('Project folder created');
            }
        } else {
            $io->success('Project folder created');
        }

        $io->section('Verifying that the configuration file, ConfigModule.php, has already been created');
        $defaultConfigModuleProject = $defaultPathProject . DIRECTORY_SEPARATOR . 'ConfigModule.php';
        if (!file_exists($defaultConfigModuleProject)) {
            $saveFileConfigModule = file_put_contents($defaultConfigModuleProject, $this->getContentConfigModule());
            if (!$saveFileConfigModule) {
                $io->error('Could not create the project configuration file');
                return 0;
            } else {
                $io->success('Main project configuration file was created');
            }
        } else {
            $io->success('Project configuration file already created');
        }

        $io->success('Project started successfully');

        return 0;
    }

    /**
     * @return false|string|string[]
     */
    protected function getContentConfigModule()
    {
        $getSampleContent = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Sample' . DIRECTORY_SEPARATOR . 'ConfigModule.php.txt');
        $dataContent      = [
            "##YEAR##"      => date("Y"),
            "##NAMESPACE##" => "App"
        ];
        $getSampleContent = str_replace(array_keys($dataContent), array_values($dataContent), $getSampleContent);
        return $getSampleContent;
    }
}