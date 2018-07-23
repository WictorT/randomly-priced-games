<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionListener
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();
        $json = $this->getResponseJson($exception);
        $response = new JsonResponse($json, 200, [], true);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $response->headers->add(['Content-Type' => 'application/json']);
        } else {
            $response = new JsonResponse($json, 200, [], true);
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    private function getResponseJson(\Exception $exception): string
    {
        $message = [
            'code' => $this->getResponseCode($exception),
            'message' => $this->getErrorMessage($exception),
        ];

        return $this->serializer->serialize($message, 'json', array_merge(array(
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        )));
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    private function getErrorMessage(\Exception $exception): string
    {
        if ($exception instanceof NotFoundHttpException) {
            if ($exception->getPrevious() instanceof ResourceNotFoundException) {
                return $exception->getMessage();
            }

            return 'Entity not found';
        }

        return $exception->getMessage();
    }

    /**
     * @param \Exception $exception
     * @return int
     */
    private function getResponseCode(\Exception $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
