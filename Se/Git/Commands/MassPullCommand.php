<?php

/*
 * This file is part of the Git Bundle
 *
 * (c) Stéphane Erard <stephane.erard@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Se\Git\Commands;

use Symfony\Component\Console\Input\ArgvInput;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;

use Se\Git\Application;
use Se\Git\Commands\Base\GitCommand as BaseGitCommand;

/**
 * Mass Pull Git Repositories.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class MassPullCommand extends BaseGitCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		parent::configure()

		->addArgument('names', InputArgument::REQUIRED, 'The Git Repositories names')
		
		->setName('git:mpull')
		->setDescription('Pull a Git Repository')
		->setAliases(array('c'))
		;
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$names = explode(' ', $input->getArgument('names'));
		
		$dir = getcwd();
		
		foreach($names as $name)
		{
			chdir($dir);
			$args = array('git:pull');
			$args[] = $name;
			$pullInput = new ArgvInput($args);
			$pull = new PullCommand();
			$pull->run($pullInput, $output);
		}
	}
}
