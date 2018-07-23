<?php
namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Serializer\AccessorOrder("custom", custom = {"id", "product_id", "count" , "product", "created_at", "updated_at"})
 */
class CartItemDTO extends BaseDTO
{
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
     * @var integer
     */
    public $count;

    /**
     * @var ProductDTO
     */
    public $product;
}
