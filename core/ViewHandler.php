<?php

namespace core;

/**
 * Class designated to serving HTML files
 * @package core
 */
class ViewHandler{

    /**
     * Grabs the contents of a given template file
     * and echos it out.
     * @param string $template_path the path of the file to be rendered
     * @return void
     */
    public function render(string $template_path): void{
        $absolute_template_path = $GLOBALS['ROOT_PATH']."/template/".$template_path;
        $template_file_contents = file_get_contents($absolute_template_path);

        echo $template_file_contents;

    }

    /**
     * Call for the render function to server the 404 error HTML file
     * @return void
     */
    public function notFound(): void{
        http_response_code(404);
        $this->render("error/404.html");
    }


}