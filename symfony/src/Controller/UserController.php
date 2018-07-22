<?php
namespace App\Controller;

use App\DTO\UserDTO;
use App\Handler\UserHandler;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Rest\Route("/api")
 */
class UserController extends Controller
{
    /**
     * @var UserHandler
     */
    private $userHandler;

    /**
     * @param UserHandler $userHandler
     */
    public function __construct(UserHandler $userHandler)
    {
        $this->userHandler = $userHandler;
    }

    /**
     * @Rest\Route(path="/sign-up", name="app.users.sign_up", methods={"POST"})
     * @ParamConverter("userDTO", converter="fos_rest.request_body")
     *
     * @param UserDTO $userDTO
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @throws BadRequestHttpException
     *
     * @return View
     */
    public function signUpAction(UserDTO $userDTO, ConstraintViolationListInterface $validationErrors): View
    {
        $this->userHandler->handleValidationErrors($validationErrors);

        $user = $this->userHandler->create($userDTO);

        return View::create($user, Response::HTTP_CREATED);
    }
}
