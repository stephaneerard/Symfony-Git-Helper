<?php

/*
 * This file is part of the Git Bundle
 *
 * (c) Stéphane Erard <stephane.erard@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Se\Git\Commands\Base;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Util\Mustache;

use Se\Git\Application;

/**
 * Clone a Git Repository.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class GitCommand extends Command
{

	const ENV_VARIABLE 	= 'SF_GIT_REPO_DIC';
	protected $repositoriesDic;


	/**
	 * @see Command
	 * @return BaseGitCommand
	 */
	protected function configure()
	{
		$this
		->addOption('repositories', 'r', InputOption::VALUE_OPTIONAL, 'repositories file. will search for ./repositories if none given', false)
		;
		return $this;
	}
	

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->loadRepositoriesDictionnary($input, $output);
	}

	public function getRepositoryDefinition($name, $throwException = false)
	{
		$repositoriesDic = $this->getRepositoriesDictionnary();
		if(!isset($repositoriesDic[$name]))
		{
			if($throwException)
			{
				throw new \RuntimeException('Cannot find Git Repository definition for "' . $name . '"');
			}
			return false;
		}
		return $repositoriesDic[$name];
	}

	public function getRepositoriesDictionnary()
	{
		return $this->repositoriesDic;
	}

	public function loadRepositoriesDictionnary($input, $output)
	{
		$this->repositoriesDic = Yaml::load($this->getRepositoriesDataFile($input, $output));
	}

	/**
	 * @param InputInterface $input
	 * @return string The valid path to a repositories data file
	 */
	protected function getRepositoriesDataFile($input, $output)
	{
		if( $repositoriesDataFile = $input->getOption('repositories') )
		{
			if( $this->checkRepositoriesDatFile($repositoriesDataFile) )
			{
				return $repositoriesDataFile;
			}
		}

		elseif( $this->checkRepositoriesDatFile($repositoriesDataFile = getcwd() . '/.repositories') )
		{
			return $repositoriesDataFile;
		}

		if( isset($_ENV[Application::ENV_VARIABLE]) && $this->checkRepositoriesDatFile($repositoriesDataFile = $_ENV[Application::ENV_VARIABLE]) ){
			return $repositoriesDataFile;
		}

		throw new \RuntimeException(
			'No .repositories file can be found in current working directory' . PHP_EOL .
			'Or via argument --repositories' . PHP_EOL .
		sprintf('Or in environment variable "%s"', Application::ENV_VARIABLE) . PHP_EOL
		);
	}

	/**
	 * @param string $repositoriesDataFile The path to repositories data file
	 * @throws \RuntimeException If $repositoriesDataFile fiel doesnt exist or is unreadable
	 */
	protected function checkRepositoriesDatFile($repositoriesDataFile)
	{
		if($notFileExists = !file_exists($repositoriesDataFile) || $notFileReadable = !is_readable($repositoriesDataFile))
		{
			return false;
		}
		return $repositoriesDataFile;
	}



}
