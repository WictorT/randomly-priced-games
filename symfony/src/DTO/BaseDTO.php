<?php
namespace App\DTO;

abstract class BaseDTO
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime
     */
    public $updatedAt;
}
