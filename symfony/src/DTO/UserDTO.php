<?php
namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Serializer\AccessorOrder("custom", custom = {"id", "username", "password" , "email", "created_at", "updated_at"})
 */
class UserDTO extends BaseDTO
{
    /**
     * @Serializer\Type("string")
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(max=25)
     *
     * @var string
     */
    public $username;

    /**
     * @Serializer\Type("string")
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(max=64)
     *
     * @var string
     */
    public $password;

    /**
     * @Serializer\Type("string")
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(max=254)
     * @Assert\Email()
     *
     * @var string
     */
    public $email;
}
