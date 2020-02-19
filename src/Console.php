<?php
/**
 * This file is part of the Potara (https://potara.org)
 *
 * @see       https://github.com/potara/core
 * @copyright Copyright (c) 2018-2020 Bruno Lima
 * @author    Bruno Lima <brunolimame@gmail.com>
 * @license   https://github.com/potara/core/blob/master/LICENSE (MIT License)
 */

namespace Potara\Console;

use Symfony\Component\Console\Application;
use Potara\Console\Command as PotaraCommand;

class Console
{
    static public function run()
    {
        $application = new Application();

        $application->add(new PotaraCommand\Init());
        $application->add(new PotaraCommand\Docker());

        return $application->run();
    }
}