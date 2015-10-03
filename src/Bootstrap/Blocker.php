<?php
namespace MD\Flavour\Bootstrap;

/**
 * Blocks web access based on several criteria, mainly IP.
 *
 * @author Michał Pałys-Dudek <michal@michaldudek.pl>
 *
 * @SuppressWarnings(PHPMD)
 */
class Blocker
{
    /**
     * Set of allowed IP numbers.
     *
     * @var array
     */
    protected $allowedIps = [
        '127.0.0.1',
        'fe80::1',
        '::1'
    ];

    /**
     * Constructor.
     *
     * @param array $allowedIps List of IP's that should be allowed in.
     */
    public function __construct(array $allowedIps = [])
    {
        $this->allowedIps = array_merge($this->allowedIps, $allowedIps);
    }

    /**
     * Check the access based on the passed environment params.
     *
     * If the access is restricted it will also exit the script with HTTP 403 Forbidden header.
     *
     * @param array $env Environment parameters. Usually just `$_SERVER`.
     *
     * @return boolean
     */
    public function checkAccess(array $env)
    {
        // cli server is only run for dev
        if (php_sapi_name() === 'cli-server') {
            return true;
        }

        // check if IP is allowed
        $clientIp = isset($env['REMOTE_ADDR']) ? $env['REMOTE_ADDR'] : 0;
        if (in_array($clientIp, $this->allowedIps)) {
            return true;
        }

        // check if dev domain
        $domain = isset($env['HTTP_HOST']) ? $env['HTTP_HOST'] : '';
        if (preg_match('/\.dev$/', $domain)) {
            return true;
        }

        header('HTTP/1.0 403 Forbidden');
        exit('You are not allowed to access this file.');
    }

    /**
     * Check if the allowed IP's can be allowed in based on the given environment parameters.
     *
     * If the access is restricted it will also exit the script with HTTP 403 Forbidden header.
     *
     * @param array $env Environment parameters. Usually just `$_SERVER`.
     * @param array $allowedIps List of IP's that should be allowed in.
     *
     * @return boolean
     */
    public static function check(array $env, array $allowedIps = [])
    {
        $blocker = new static($allowedIps);
        return $blocker->checkAccess($env);
    }
}
