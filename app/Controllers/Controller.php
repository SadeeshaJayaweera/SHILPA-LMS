<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\View;

abstract class Controller {
    protected function render(string $view, array $data = []): Response {
        $html = View::render($view, $data);
        return new Response($html, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    protected function json(mixed $data, int $status = 200): Response {
        return Response::json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): Response {
        return Response::redirect($url, $status);
    }

    protected function validate(Request $request, array $rules): array {
        $errors = [];
        $data = [];

        foreach ($rules as $field => $ruleStr) {
            $value = $request->input($field);
            $ruleList = explode('|', $ruleStr);

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && (empty($value) && $value !== '0')) {
                    $errors[$field][] = "The $field field is required.";
                } elseif ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "The $field must be a valid email address.";
                } elseif (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (is_string($value) && strlen($value) < $min) {
                        $errors[$field][] = "The $field must be at least $min characters.";
                    }
                }
            }

            $data[$field] = $value;
        }

        if (!empty($errors)) {
            session()->flash('errors', $errors);
            session()->flash('old', $request->all());
        }

        return [$data, $errors];
    }
}
