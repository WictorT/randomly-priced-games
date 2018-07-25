<?php

namespace App\Handler;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class BaseHandler
{
    /**
     * @return EntityRepository
     */
    abstract public function getRepository(): EntityRepository;

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
