<?php
namespace App\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionListenerTest extends KernelTestCase
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function setUp(): void
    {
        self::bootKernel();

        $this->serializer = self::$container->get('serializer');
    }

    public function testOnKernelExceptionHandlesHttpExceptionInterfaceInstances(): void
    {
        $exception = new NotFoundHttpException(null);
        /** @var GetResponseForExceptionEvent|MockObject $eventMock */
        $eventMock = $this->createMock(GetResponseForExceptionEvent::class);
        $eventMock->expects($this->any())
            ->method('getException')
            ->willReturn($exception);
        $eventMock->expects($this->once())
            ->method('setResponse');

        $listener = new ExceptionListener($this->serializer);
        $listener->onKernelException($eventMock);
    }

    public function testOnKernelExceptionHandlesResourceNotFoundExceptionInstances(): void
    {
        $exception = new NotFoundHttpException(null, new ResourceNotFoundException);
        /** @var GetResponseForExceptionEvent|MockObject $eventMock */
        $eventMock = $this->createMock(GetResponseForExceptionEvent::class);
        $eventMock->expects($this->any())
            ->method('getException')
            ->willReturn($exception);
        $eventMock->expects($this->once())
            ->method('setResponse');

        $listener = new ExceptionListener($this->serializer);
        $listener->onKernelException($eventMock);
    }

    public function testOnKernelExceptionHandlesNotHttpExceptionInterfaceInstances(): void
    {
        $exception = new InternalErrorException();
        /** @var GetResponseForExceptionEvent|MockObject $eventMock */
        $eventMock = $this->createMock(GetResponseForExceptionEvent::class);
        $eventMock->expects($this->any())
            ->method('getException')
            ->willReturn($exception);
        $eventMock->expects($this->once())
            ->method('setResponse');

        $listener = new ExceptionListener($this->serializer);
        $listener->onKernelException($eventMock);
    }
}
