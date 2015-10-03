<?php
namespace MD\Flavour\Bootstrap;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

/**
 * Symfony runner.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 */
abstract class Run
{
    /**
     * Runs Symfony2 in web mode, creating a `Request`, handling it in the given kernel class and sending out
     * the resulting `Response`.
     *
     * @param string $kernelClass Class name for the kernel to be ran.
     * @param string $env         Environment name. Default: `prod`.
     * @param boolean $debug      Debug mode on or off. Default: `false`.
     */
    public static function web($kernelClass, $env = 'prod', $debug = false)
    {
        if ($debug) {
            Debug::enable();
        }

        $kernel = new $kernelClass($env, $debug);
        $kernel->loadClassCache();

        $request = Request::createFromGlobals();
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
    }

    /**
     * Runs Symfony2 in console mode.
     *
     * @param string $kernelClass Class name for the kernel to be ran.
     */
    public static function console($kernelClass)
    {
        set_time_limit(0);

        $input = new ArgvInput();

        // decide env and debug info based on cli params
        $env = $input->getParameterOption(['--env', '-e'], getenv('SYMFONY_ENV') ?: 'dev');
        $debug = getenv('SYMFONY_DEBUG') !== '0'
            && !$input->hasParameterOption(['--no-debug', ''])
            && $env !== 'prod';

        if ($debug) {
            Debug::enable();
        }

        $kernel = new $kernelClass($env, $debug);

        $application = new Application($kernel);
        $application->run($input);
    }
}
