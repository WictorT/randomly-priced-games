<?php

namespace App\Handler;

use App\Repository\BaseRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class BaseHandler
{
    /**
     * @return BaseRepository
     */
    abstract public function getRepository(): BaseRepository;

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
