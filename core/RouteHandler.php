<?php

namespace core;

use Exception;
use stdClass;
use src\Controller\DefaultController;

/**
 * Handles the routing of requests to their
 * corresponding controller and function
 * @package core
 */
class RouteHandler{

    /**
     * Object holding on controllers that
     * handle routes
     * @var stdClass
     */
    private $controller;

    /**
     * RouteHandler constructor.
     * Initializes the controller member property
     * and calls the route function on the requested URI
     * @return void
     */
    public function __construct()
    {
        $this->controller = new stdClass();
        $this->controller->default = new DefaultController();

        $this->route($_SERVER['REQUEST_URI']);
    }

    /**
     * Routes the given request to it's corresponding controller.
     * Throws a 404 Not Found error is no route found or on error.
     * @param string $request the URI of the current request to the server
     * @return void
     */
    public function route(string $request): void{
        try{
            switch ($request) {

                // route for message coming in from Twilio
                case '/incoming-message' :
                    $this->controller->default->incomingMessageRoute();
                    break;

                // route for Slack's webhook to pass outgoing message to our application
                case '/outgoing-message' :
                    $this->controller->default->outgoingMessageRoute();
                    break;
                default:
                    $viewHandler = new ViewHandler();
                    $viewHandler->notFound();
                    break;
            }
        }catch(Exception $e){
            $viewHandler = new ViewHandler();
            $viewHandler->notFound();

        }
    }
}