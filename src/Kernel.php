<?php
namespace MD\Flavour;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as HttpKernel;

use MD\Foundation\Debug\Debugger;

/**
 * Flavoured Kernel that alters default configuration of Symfony Kernel.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 */
abstract class Kernel extends HttpKernel
{
    /**
     * Cache directory path.
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * Logs directory path.
     *
     * @var string
     */
    protected $logDir;

    /**
     * Configs directory path.
     *
     * @var string
     */
    protected $configDir;

    /**
     * Project directory path.
     *
     * @var string
     */
    protected $projectDir;

    /**
     * Build version.
     *
     * @var string
     */
    protected $build;

    /**
     * {@inheritdoc}
     *
     * @param ContainerBuilder $container Container builder.
     */
    protected function prepareContainer(ContainerBuilder $container)
    {
        parent::prepareContainer($container);
        
        $loader = $this->getContainerLoader($container);

        $loader->load($this->getConfigDir() .'/parameters.yml');
        $loader->load($this->getRootDir() .'/Resources/config/config_'.$this->getEnvironment().'.yml');
        $loader->load($this->getRootDir() .'/Resources/services/services.yml');

        $this->configure($container, $loader);
    }

    /**
     * Does not do anything.
     *
     * @param LoaderInterface $loader File loader.
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        // noop
    }

    /**
     * You should implement this method in your application Kernel if you want to pass some additional configuration
     * to the container. Examples would be additional config files or registering compiler passes.
     *
     * @param ContainerBuilder $container Container builder.
     * @param LoaderInterface  $loader    File loader.
     */
    protected function configure(ContainerBuilder $container, LoaderInterface $loader)
    {
        // to be implemented in the application
    }

    /**
     * Returns the name of this kernel - its class name by default.
     *
     * @return string
     */
    public function getName()
    {
        if (!$this->name) {
            $this->name = Debugger::getClass($this, true);
        }

        return $this->name;
    }

    /**
     * Returns the project directory path.
     *
     * Project directory (as opposed to root directory) is usually where `composer.json`, `src/`, `web/` and other
     * project resources are located, probably main dir of your git repository.
     *
     * This is not normally available in Symfony2.
     *
     * @return string
     */
    public function getProjectDir()
    {
        if (!$this->projectDir) {
            $this->projectDir = realpath($this->getRootDir() .'/../..');
        }

        return $this->projectDir;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getCacheDir()
    {
        if (!$this->cacheDir) {
            $this->cacheDir = $this->getProjectDir() .'/.cache/'. $this->getEnvironment();
        }

        return $this->cacheDir;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getLogDir()
    {
        if (!$this->logDir) {
            $this->logDir = $this->getProjectDir() .'/logs';
        }

        return $this->logDir;
    }

    /**
     * Returns path to the configuration dir (where all config files are stored).
     *
     * @return string
     */
    public function getConfigDir()
    {
        if (!$this->configDir) {
            $this->configDir = $this->getProjectDir() .'/config';
        }

        return $this->configDir;
    }

    /**
     * Returns the current build version.
     *
     * This is read from `.BUILD` file found in the project dir. If no such file exists, then it will default to `dev`.
     *
     * @return string
     */
    public function getBuild()
    {
        if (!$this->build) {
            $this->build = 'dev';
            $buildFile = $this->getProjectDir() .'/.BUILD';
            if (file_exists($buildFile)) {
                $this->build = trim(file_get_contents($buildFile));
            }
        }

        return $this->build;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();

        return array_merge(
            $parameters,
            [
                'kernel.not_debug' => !$this->debug,
                'kernel.project_dir' => $this->getProjectDir(),
                'kernel.config_dir' => $this->getConfigDir(),
                'kernel.build' => $this->getBuild()
            ]
        );
    }
}
