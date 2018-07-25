<?php

namespace App\Handler;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class BaseHandler
{
    /**
     * @param ConstraintViolationListInterface $validationErrors
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function handleValidationErrors(ConstraintViolationListInterface $validationErrors): void
    {
        if ($validationErrors->count() > 0) {
            $errorMessage = '';

            foreach ($validationErrors as $validationError) {
                $errorMessage .= $validationError->getPropertyPath() . ': ' . $validationError->getMessage() . '     ';
            }

            throw new BadRequestHttpException(substr($errorMessage, 0, -5));
        }
    }
}
