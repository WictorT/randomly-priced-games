<?php
namespace App\Controller;

use App\DTO\UserDTO;
use App\Handler\UserHandler;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route(path="/api/sign-up", name="app.users.sign_up", methods={"POST"})
     * @ParamConverter("userDTO", converter="fos_rest.request_body")
     *
     * @param UserDTO $userDTO
     * @param ConstraintViolationListInterface $validationErrors
     * @return View
     */
    public function signUpAction(UserDTO $userDTO, ConstraintViolationListInterface $validationErrors): View
    {
        if ($validationErrors->count() > 0) {
            throw new BadRequestHttpException($validationErrors);
        }

        $user = $this->userHandler->create($userDTO);

        return View::create($user, Response::HTTP_CREATED);
    }
}
