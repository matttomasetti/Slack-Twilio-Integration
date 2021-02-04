<?php


namespace core;


/**
 * Middleware to load all variables in the env.ini
 * file in PHP's $_ENV variable
 * @package core
 */
class EnvironmentHandler
{
    /**
     * EnvironmentHandler constructor.
     */
    public function __construct()
    {
        $this->defineEnvironmentalVariables();
    }

    /**
     * Grabs the contents of the env.ini config file
     * and stores each entry in the $_ENV array.
     * @return void
     */
    private function defineEnvironmentalVariables(): void{
        $env_vars = parse_ini_file($GLOBALS['ROOT_PATH']."/config/env.ini", true);

        foreach ($env_vars as $root_var => $env_var){
            $_ENV[$root_var] = $env_var;
        }
    }

}