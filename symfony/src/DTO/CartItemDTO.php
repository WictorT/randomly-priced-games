<?php
namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CartItemDTO extends BaseDTO
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @Serializer\Type("integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     * @Assert\GreaterThan(0)
     *
     * @var integer
     */
    public $productId;

    /**
     * @var ProductDTO
     */
    public $product;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var \DateTime
     */
    public $updatedAt;
}
