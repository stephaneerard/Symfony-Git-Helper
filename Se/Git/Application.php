<?php

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Se\Git;

use Symfony\Component\Console\Input\InputInterface as InputInterface;
use Symfony\Component\Console\Output\OutputInterface as OutputInterface;

use Symfony\Component\Console\Application as BaseApplication;
use Se\Git\Commands;

/**
 * Application.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class Application extends BaseApplication
{
	const VERSION 			= '0.0.1-DEV';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct('Git Helper ', self::VERSION);

		$this->add(new Commands\InitCommand());
		$this->add(new Commands\CloneCommand());
		$this->add(new Commands\InstallCommand());
	}
}
