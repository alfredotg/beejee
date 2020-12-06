<?php

namespace BeeJee;

class Di
{
    protected $instances = [];
    protected $providers = [];
    protected $aliases = [];

    public function __construct()
    {
    }

    /*
     * @param callable $provider
     */
    public function registre(string $class_name, $provider, ?string $alias = null): void
    {
        $this->providers[$class_name] = $provider;
        if ($alias !== null) {
            $this->aliases[$alias] = $class_name;
        }
    }

    public function get(string $class_name)
    {
        if (!array_key_exists($class_name, $this->instances)) {
            $class_name = $this->aliases[$class_name] ?? $class_name;
            $provider = $this->findProvider($class_name);
            if (!$provider) {
                throw new \Exception("Dependency provider for \"{$class_name}\" not found");
            }
            $this->instances[$class_name] = $provider();
        }
        return $this->instances[$class_name];
    }

    private function findProvider(string $required_interface)
    {
        if (isset($this->providers[$required_interface])) {
            return $this->providers[$required_interface];
        }
        foreach ($this->providers as $class => $provider) {
            if (is_subclass_of($class, $required_interface)) {
                return $provider;
            }
        }
        return null;
    }

    public function __get(string $key)
    {
        return $this->get($key);
    }

    public function __isset(string $key)
    {
        return array_key_exists($key, $this->instances) ||
            isset($this->aliases[$key]) ||
            isset($this->providers[$key]);
    }

    public function set(string $class_name, $instance): void
    {
        $this->instances[$class_name] = $instance;
    }
}
