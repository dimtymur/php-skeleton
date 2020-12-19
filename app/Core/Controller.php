<?php

namespace App\Core;

abstract class Controller extends Database {
    static public function view(string $view_name, array $view_data=[]) : View {
        return new View($view_name, $view_data);
    }
}
