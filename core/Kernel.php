<?php

namespace core;

/**
 * Core of the application. Loads in core files,
 * necessary controllers, loads middleware, and passes
 * the request of to the Route handler
 * @package core
 */
class Kernel{

    /**
     * Kernel constructor.
     */
    public function __construct() {
        $this->load();
    }

    /**
     * Loads all required files for this application
     * and passes the request to the Route Handler
     * @return void
     */
    function load(): void{
        $this->loadCore();
        $this->loadControllers();
        $this->loadMiddleware();

        if(isset($_SERVER['REQUEST_URI'])){
            $this->routeRequest();
        }
    }

    /**
     * Loads composer packages as well as core files to
     * the application
     * @return void
     */
    public function loadCore(): void{
        require_once 'vendor/autoload.php';
        require_once "core/EnvironmentHandler.php";
        require_once "core/RouteHandler.php";
        require_once "core/ViewHandler.php";

    }

    /**
     * Lods controllers for handling request
     * @return void
     */
    public function loadControllers(): void{
        require_once "src/Controller/DefaultController.php";
        require_once "src/Controller/SlackController.php";
        require_once "src/Controller/TwilioController.php";

        require_once "src/Controller/API/Slack/SlackCall.php";
        require_once "src/Controller/API/Slack/Channel.php";
        require_once "src/Controller/API/Slack/File.php";
        require_once "src/Controller/API/Slack/Message.php";
        require_once "src/Controller/API/Slack/UserGroup.php";

        require_once "src/Controller/API/Twilio/TextMessage.php";
    }

    /**
     * Calls on necessary middleware needed to run
     * before handling the request
     * @return void
     */
    public function loadMiddleware(): void{
        new EnvironmentHandler();
    }

    /**
     * Calls the Route Handler to process the request
     * @return void
     */
    public function routeRequest(): void{
        new RouteHandler();
    }

}

new Kernel();