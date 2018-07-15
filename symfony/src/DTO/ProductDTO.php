<?php
namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO extends AbstractDTO
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
     *
     * @var float
     */
    public $price;
}
