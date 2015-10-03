<?php
namespace MD\Flavour\Twig;

use Twig_Extension;

use MD\Flavour\Kernel;

/**
 * Twig Extension that adds a global `kernel` variable that contains some kernel parameters.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 */
class KernelVariableExtension extends Twig_Extension
{
    /**
     * Kernel.
     *
     * @var Kernel
     */
    protected $kernel;

    /**
     * Constructor.
     *
     * @param Kernel $kernel Kernel.
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getGlobals()
    {
        return [
            'kernel' => [
                'name' => $this->kernel->getName(),
                'env' => $this->kernel->getEnvironment(),
                'debug' => $this->kernel->isDebug(),
                'build' => $this->kernel->getBuild()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'kernel_variable';
    }
}
