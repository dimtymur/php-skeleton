<?php

namespace App\Core;

class View {
    private $view_file;
    private $view_data;

    public function __construct(string $view_file, array $view_data) {
        $this->execView($view_file, $view_data);
    }

    private function execView(string $view_file, array $view_data) : bool {
        $this->view_file = str_replace(".", DIRECTORY_SEPARATOR, $view_file);
        $this->view_data = $view_data;
        if (file_exists(VIEWS . $this->view_file . ".phtml")) {
            extract($this->view_data);
            include VIEWS . $this->view_file . ".phtml";
            return true;
        } return false;
    }
}
