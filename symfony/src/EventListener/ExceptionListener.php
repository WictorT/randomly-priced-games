<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionListener
{
    /** @var SerializerInterface */
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

        $message = [
            'message' => 'Oops... an expected feature occurred',
            'error' => [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]
        ];

        $json = $this->serializer->serialize($message, 'json', array_merge(array(
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        )));

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
}
