<?php
namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "price", "created_at", "updated_at"})
 */
class ProductDTO extends BaseDTO
{
    /**
     * @Serializer\Type("string")
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(max=254)
     *
     * @var string
     */
    public $name;

    /**
     * @Serializer\Type("float")
     *
     * @Assert\NotBlank()
     * @Assert\Type("float")
     * @Assert\GreaterThanOrEqual(0)
     *
     * @var float
     */
    public $price;
}
