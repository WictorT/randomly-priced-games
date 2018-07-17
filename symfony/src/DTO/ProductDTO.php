<?php
namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var integer
     */
    public $id;

    /**
     * @Serializer\Type("float")
     *
     * @Assert\NotBlank()
     * @Assert\Type("float")
     *
     * @var float
     */
    public $price;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime
     */
    public $updatedAt;
}
