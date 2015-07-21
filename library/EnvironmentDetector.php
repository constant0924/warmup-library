<?php
namespace library;
/**
 * User: roys <renshuai>
 * Date: 15/4/9
 * Time: 11:40
 */
use Closure;
class EnvironmentDetector {

    /**
     * Detect the application's current environment.
     *
     * @param  array|string  $environments
     * @param  array|null  $consoleArgs
     * @return string
     */
    public function detect($environments, $consoleArgs = null)
    {
        if ($consoleArgs)
        {
            return $this->detectConsoleEnvironment($environments, $consoleArgs);
        }

        return $this->detectWebEnvironment($environments);
    }

    /**
     * Set the application environment for a web request.
     *
     * @param  array|string  $environments
     * @return string
     */
    protected function detectWebEnvironment($environments)
    {
        if ($environments instanceof Closure)
        {
            return call_user_func($environments);
        }

        foreach ($environments as $environment => $hosts)
        {
            foreach ((array) $hosts as $host)
            {
                if ($this->isMachine($host)) return $environment;
            }
        }

        throw new \RuntimeException('找不到对应的配置');
    }

    /**
     * Set the application environment from command-line arguments.
     *
     * @param  mixed   $environments
     * @param  array  $args
     * @return string
     */
    protected function detectConsoleEnvironment($environments, array $args)
    {
        // First we will check if an environment argument was passed via console arguments
        // and if it was that automatically overrides as the environment. Otherwise, we
        // will check the environment as a "web" request like a typical HTTP request.
        if ( ! is_null($value = $this->getEnvironmentArgument($args)))
        {
            return reset(array_slice(explode('=', $value), 1));
        }

        return $this->detectWebEnvironment($environments);
    }

    /**
     * Get the environment argument from the console.
     *
     * @param  array  $args
     * @return string|null
     */
    protected function getEnvironmentArgument(array $args)
    {
        foreach($args as $v)
        {
            if ($v != '' && strpos($v,"--env") === 0) return $v;
        }

    }


    /**
     * Determine if the name matches the machine name.
     *
     * @param  string  $name
     * @return bool
     */
    public function isMachine($name)
    {
        return $name===gethostname();
    }

}
