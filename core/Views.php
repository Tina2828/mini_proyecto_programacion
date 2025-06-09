<?php

class Views {
    /**
     * Renders a view file with the provided data.
     *
     * @param string $view The name of the view file (without extension).
     * @param array $data An associative array of data to be passed to the view.
     * @return string The rendered content of the view.
     * @throws Exception If the view file does not exist.
     */
     public function render($view, $data = []) {
        // Ensure the view file exists
        if (empty($view)) {
            throw new Exception("View name cannot be empty.");
        }

        // Construct the file path
       $file = __DIR__ . "/../app/views/{$view}.phtml";
        if (!file_exists($file)) {
            throw new Exception("View file not found: {$file}");
        }

        ob_start();
        if (!empty($data)) {
          extract($data, EXTR_SKIP);
        }
        include $file;
        $content = ob_get_contents();
        ob_end_clean();

       return $content;
     }
}
