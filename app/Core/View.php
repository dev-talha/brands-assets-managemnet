<?php
/**
 * View - Template renderer with layout support.
 */

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        extract($data);

        $viewPath = BASE_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$view} ({$viewPath})");
        }

        if ($layout) {
            ob_start();
            include $viewPath;
            $content = ob_get_clean();

            $layoutPath = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
            if (!file_exists($layoutPath)) {
                throw new \RuntimeException("Layout not found: {$layout}");
            }
            include $layoutPath;
        } else {
            include $viewPath;
        }
    }

    public static function component(string $name, array $data = []): void
    {
        extract($data);
        $path = BASE_PATH . '/app/Views/components/' . $name . '.php';
        if (!file_exists($path)) {
            throw new \RuntimeException("Component not found: {$name}");
        }
        include $path;
    }

    public static function partial(string $path, array $data = []): void
    {
        extract($data);
        $fullPath = BASE_PATH . '/app/Views/' . str_replace('.', '/', $path) . '.php';
        if (file_exists($fullPath)) {
            include $fullPath;
        }
    }
}
