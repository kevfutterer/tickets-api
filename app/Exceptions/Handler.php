<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiResponses;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];  

    protected $handlers = [
        ValidationException::class => 'handleValidation',
        ModelNotFoundException::class => 'handleModelNotFound'
    ];

    private function handleValidation(ValidationException $exception)
    {
        foreach($exception->errors() as $key => $value) 
        foreach($value as $message) {
            $errors[] = [
                'status' => 422,
                'message' => $message,
                'source' => $key
            ];
        }
        return $errors;
    }

    private function handleModelNotFound(ModelNotFoundException $exception)
    {
        
        return [
            [
                'status' => 404,
                'message' => 'The Resource can no be found',
                'source' => $exception->getModel(),
            ]
            ];
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $className = get_class($exception);

        if (array_key_exists($className, $this->handlers)) {
            $method = $this->handlers[$className];
            return $this->error($this->$method($exception));
        }

        $index = strrpos($className, '\\');

        return $this->error([
            [
                'type' => substr($className, $index + 1),
                'status' => 0,
                'message' => $exception->getMessage(),
                'source' => 'Line' . $exception->getLine() . ':' . $exception->getFile()
            ]
            ]);
    }
}