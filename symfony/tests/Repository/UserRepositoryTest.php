<?php
namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    public function testLoadUserByUsername()
    {
        /** @var ClassMetadata|MockObject $classMetadataMock */
        $classMetadataMock = $this->createMock(ClassMetadata::class);
        /** @var EntityManagerInterface|MockObject $entityManagerMock */
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock->expects($this->any())
            ->method('select')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->any())
            ->method('from')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
            ->method('where')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
            ->method('setParameter')
            ->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->once())
            ->method('setParameter')
            ->willReturn(new Query($entityManagerMock));


        $entityManagerMock->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilderMock);

        $userRepository = new UserRepository($entityManagerMock, $classMetadataMock);
        $userRepository->loadUserByUsername('username');
    }
}
