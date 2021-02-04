<?php

namespace console;

$GLOBALS['ROOT_PATH'] = dirname(__FILE__)."/..";
chdir($GLOBALS['ROOT_PATH']);

require_once "core/Kernel.php";


use src\Controller\SlackController;

/**
 * Class for handling terminal commands.
 * While the goal of this application is to be commandless,
 * this is used by a CRON job
 *
 * Usage: php console/command {command}.
 */
class Command{


    /**
     * Retrieves the desired command entered in the command line.
     * If no command is present, it will prompt the user for one.
     * @param array $argv The arguments entered in the commandline at runtime
     * @return void
     */
    public function __construct(array $argv){

        //Get command
        if (isset($argv[1])) {
            $command = $argv[1];
        } else {
            echo "Available Commads:\n";
            echo "\tchannels:clear-unused\n";
            $command = readline('What would you like to do: ');
        }

        //Run command
        $this->runCommand($command);

    }

    /**
     * Routes a given command string to it's corresponding function.
     * @param string $command_name the name of the command to run
     * @return void
     */
    private function runCommand(string $command_name): void{

        switch ($command_name){
            case "channels:clear-unused":
                $this->clear();
                break;
            default:
                echo "Command not found.\n";
                die();

        }
    }


    /**
     * Calls on the SlackController to remove users from Slack Channels
     * that have been inactive for over 24 hours.
     * @return void
     */
    private function clear(): void{
        echo "Clearing...\n";
        $slack = new SlackController();
        $channels = $slack->getInactiveTextMessageChannels();

        foreach ($channels as $channel){
            $slack->removeMembers($channel["id"]);
        }
    }

}

new Command($argv);




