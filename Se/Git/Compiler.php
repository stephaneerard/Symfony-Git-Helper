<?php

/*
 * This file is part of the Git Bundle.
 *
 * It uses the code form symfony-bootstrapper by Fabien Potencier & al.
 *
 * (c) Stéphane Erard <stephane.erard@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Se\Git;

use Symfony\Component\Finder\Finder;

/**
 * Compiler.
 *
 * @author Stéphane Erard <stephane.erard@gmail.com>
 */
class Compiler
{
	const PHAR_NAME = 'sf-git.phar';
	const PHAR_ALIAS = 'Sf-Git';

	public function compile()
	{
		if (file_exists(Compiler::PHAR_NAME)) {
			unlink(Compiler::PHAR_NAME);
		}

		$phar = new \Phar(Compiler::PHAR_NAME, 0, Compiler::PHAR_ALIAS);
		$phar->setSignatureAlgorithm(\Phar::SHA1);
		$phar->startBuffering();

		// Files
		foreach ($this->getFiles() as $file) {
			$path = str_replace(__DIR__.'/', '', $file);

			if (false !== strpos($file, '.php') && false === strpos($file, '/skeleton/')) {
				$content = self::stripComments(file_get_contents($file));
			} else {
				$content = file_get_contents($file);
			}

			$phar->addFromString($path, $content);
		}

		// Stubs
		$phar['_cli_stub.php'] = $this->getCliStub();
		$phar['_web_stub.php'] = $this->getWebStub();
		$phar->setDefaultStub('_cli_stub.php', '_web_stub.php');
		$phar->stopBuffering();
		$phar->compressFiles(\Phar::GZ);

		unset($phar);
	}

	protected function getCliStub()
	{
		return <<<'EOF'
<?php

require_once __DIR__ . '/Se/Git/autoload.php';

use Se\Git\Application;

$application = new Application();
$application->run();

__HALT_COMPILER();
EOF;
	}

	protected function getWebStub()
	{
		return "<?php throw new \LogicException('This PHAR file can only be used from the CLI.'); __HALT_COMPILER();";
	}

	protected function getFiles()
	{
		$files = array(
            'LICENSE',
            'README.rst',
		);

		$dirs = array(
						'Se',
						'Symfony',
		);

		$iterator = new Finder();
		$iterator->files()->name('*.php')->in($dirs);

		return array_merge($files, iterator_to_array($iterator));
	}

	/**
	 * Removes comments from a PHP source string.
	 *
	 * We don't use the PHP php_strip_whitespace() function
	 * as we want the content to be readable and well-formatted.
	 *
	 * @param string $source A PHP string
	 *
	 * @return string The PHP string with the comments removed
	 */
	static public function stripComments($source)
	{
		if (!function_exists('token_get_all')) {
			return $source;
		}

		$output = '';
		foreach (token_get_all($source) as $token) {
			if (is_string($token)) {
				$output .= $token;
			} elseif (!in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
				$output .= $token[1];
			}
		}

		// replace multiple new lines with a single newline
		$output = preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $output);

		return $output;
	}
}
