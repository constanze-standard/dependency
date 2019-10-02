<?php

namespace ConstanzeStandard\Container;

use AbstractTest;
use ConstanzeStandard\Container\Interfaces\BootableEntryProviderInterface;
use ConstanzeStandard\Container\Interfaces\ContainerInterface;
use ConstanzeStandard\Container\Interfaces\EntryProviderInterface;

require_once __DIR__ . '/AbstractTest.php';

class EntryProviderCollectionTest extends AbstractTest
{
    public function testRegisterWithEntryProviderWithoutBootable()
    {
        [$entryProvider, $container] = $this->getEntryProviderAndContainer();
        $entryProvider->expects($this->once())->method('has')->with('id')->willReturn(true);

        $entryProviderCollection = new EntryProviderCollection($container);
        $entryProviderCollection->add($entryProvider);
        $entryProviderCollection->register('id');
        $registered = $this->getProperty($entryProviderCollection, 'registered');
        $this->assertEquals($registered[0], get_class($entryProvider));
    }

    public function testRegisterWithEntryProviderWithBootable()
    {
        /**
         * @var EntryProviderInterface $entryProvider
         * @var ContainerInterface $container
         */
        [, $container] = $this->getEntryProviderAndContainer();
        $entryProvider = $this->createMock(BootableEntryProviderInterface::class);
        $entryProvider->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProvider->expects($this->once())->method('boot')->with($container);

        $entryProviderCollection = new EntryProviderCollection($container);
        $entryProviderCollection->add($entryProvider);
        $entryProviderCollection->register('id');
        $registered = $this->getProperty($entryProviderCollection, 'registered');
        $this->assertEquals($registered[0], get_class($entryProvider));
    }

    public function testRegisterWithEntryProviderWithHad()
    {
        [$entryProvider, $container] = $this->getEntryProviderAndContainer();

        $entryProviderCollection = new EntryProviderCollection($container);
        $entryProviderCollection->add($entryProvider);
        $result = $entryProviderCollection->add($entryProvider);
        $entryProviders = $this->getProperty($entryProviderCollection, 'entryProviders');
        $this->assertCount(1, $entryProviders);
        $this->assertEquals($result, $entryProviderCollection);
    }

    public function testHasWithTrue()
    {
        [$entryProvider, $container] = $this->getEntryProviderAndContainer();
        $entryProvider->expects($this->once())->method('has')->with('id')->willReturn(true);
        $entryProviderCollection = new EntryProviderCollection($container);
        $entryProviderCollection->add($entryProvider);
        $result = $entryProviderCollection->has('id');
        $this->assertTrue($result);
    }

    public function testHasWithNotFound()
    {
        [$entryProvider, $container] = $this->getEntryProviderAndContainer();
        $entryProvider->expects($this->exactly(1))->method('has')->with('id_nothing')->willReturn(false);
        $entryProviderCollection = new EntryProviderCollection($container);
        $entryProviderCollection->add($entryProvider);
        $result = $entryProviderCollection->has('id_nothing');
        $this->assertFalse($result);
    }

    private function getEntryProviderAndContainer()
    {
        $entryProvider = $this->createMock(EntryProviderInterface::class);
        $container = $this->createMock(ContainerInterface::class);
        /**
         * @var EntryProviderInterface $entryProvider
         * @var ContainerInterface $container
         */
        return [$entryProvider, $container];
    }
}