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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;
use Symfony\Component\Finder\Finder;

/**
 * Initializes a new Git project.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class InitCommand extends Command
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
		->setDefinition(array(
			new InputOption('force', 'f', InputOption::VALUE_NONE, 'Force re-initialization of Git Repository'),
		))
		->setName('git:init')
		->setDescription('Initialize a new Git Repository')
		->setAliases(array('i'))
		;
	}

	/**
	 * @see Command
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$finder = new Finder();
		$already = file_exists(getcwd() . '/.git');
		if($already)
		{
			if($input->getOption('force'))
			{
				$output->write('<info>Reinitializing Git Repository... </info>', false);
			}else{
				throw new \RuntimeException(
					'Can\'t initialize a Git Repository. A Git Repository already exists' . PHP_EOL .
					PHP_EOL .
					'Add -f or --force to reinitialize Git Repository'
				);
			}
		}else{
			$output->write('<info>Initializing Git Repository... </info>', false);
		}
    exec('git init', $result, $exitCode);
    $status = $exitCode === 0 ? true : false;
    $output->write(sprintf(
    	'<%s>%s</%s>',
    	$status ? 'info' : 'error',
    	$status ? 'OK' : sprintf('ERROR: %s', PHP_EOL . implode(PHP_EOL, $result)),
    	$status ? 'info' : 'error'
    ), true);
	}
}
