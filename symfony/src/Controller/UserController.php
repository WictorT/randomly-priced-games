<?php
namespace App\Controller;

use App\DTO\UserDTO;
use App\Handler\UserHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends Controller
{
    /** @var UserHandler $userHandler */
    private $userHandler;

    /**
     * @param UserHandler $userHandler
     */
    public function __construct(UserHandler $userHandler)
    {
        $this->userHandler = $userHandler;
    }

    /**
     * @Route(path="/register", name="app.users.register", methods={"POST"})
     * @ParamConverter("userDTO", converter="fos_rest.request_body")
     *
     * @param UserDTO $userDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function create(UserDTO $userDTO, ConstraintViolationListInterface $validationErrors)
    {
        if ($validationErrors->count() > 0) {
            throw new BadRequestHttpException($validationErrors);
        }

        $user = $this->userHandler->create($userDTO);

        return $this->json($user, Response::HTTP_CREATED);
    }
}
