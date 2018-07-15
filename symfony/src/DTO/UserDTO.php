<?php
namespace App\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO extends AbstractDTO
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
