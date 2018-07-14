<?php
namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserController extends Controller
{
    /**
     * @Route(path="/register", name="app.users.register", methods={"POST"})
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @param User $user
     * @param ConstraintViolationListInterface $validationErrors
     * @return JsonResponse
     */
    public function create(User $user, ConstraintViolationListInterface $validationErrors)
    {
        if ($validationErrors->count() > 0) {
            throw new BadRequestHttpException($validationErrors);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse();
    }
}
