<?php
namespace App\Controller;

use App\DTO\UserDTO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController
{
    /**
     * @Route(path="/register", name="app.users.register", methods={"POST"})
     * @ParamConverter("userDTO", converter="fos_rest.request_body")
     * @param UserDTO $userDTO
     * @return JsonResponse
     */
    public function create(UserDTO $userDTO)
    {
        var_dump($userDTO);

        return new JsonResponse('Hello');
    }
}
