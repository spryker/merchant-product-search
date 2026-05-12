<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantProductSearch\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\StoreRelationBuilder;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\ProductAbstractMerchantTransfer;
use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use ReflectionClass;
use Spryker\Zed\MerchantProductSearch\Business\Expander\MerchantProductSearchExpander;
use Spryker\Zed\ProductPageSearch\Business\DataMapper\PageMapBuilder;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantProductSearch
 * @group Business
 * @group Facade
 * @group Facade
 * @group MerchantProductSearchFacadeTest
 * Add your own group annotations below this line
 */
class MerchantProductSearchFacadeTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\MerchantProductSearch\MerchantProductSearchBusinessTester
     */
    protected $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->clearExpanderCache();
    }

    public function testGetMerchantDataByProductAbstractIdsReturnsProductAbstractMerchantTransfers(): void
    {
        // Arrange
        $productConcrete1 = $this->tester->haveProduct([
            ProductConcreteTransfer::IS_ACTIVE => true,
        ]);
        $productConcrete2 = $this->tester->haveProduct([
            ProductConcreteTransfer::IS_ACTIVE => true,
        ]);

        /** @var \Generated\Shared\Transfer\StoreTransfer $storeTransfer */
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $storeRelationTransfer = (new StoreRelationBuilder())->seed([
            StoreRelationTransfer::ID_STORES => [$storeTransfer->getIdStore()],
        ])->build();

        $merchant = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => true, MerchantTransfer::STORE_RELATION => $storeRelationTransfer->toArray()]);

        $this->tester->addMerchantProductRelation($merchant->getIdMerchant(), $productConcrete1->getFkProductAbstract());
        $this->tester->addMerchantProductRelation($merchant->getIdMerchant(), $productConcrete2->getFkProductAbstract());

        $productAbstractMerchantTransfer1 = (new ProductAbstractMerchantTransfer())
            ->setIdProductAbstract($productConcrete1->getFkProductAbstract())
            ->setMerchantNames([$storeTransfer->getName() => [$merchant->getName()]]);

        $productAbstractMerchantTransfer2 = (new ProductAbstractMerchantTransfer())
            ->setIdProductAbstract($productConcrete2->getFkProductAbstract())
            ->setMerchantNames([$storeTransfer->getName() => [$merchant->getName()]]);

        $expectedResult = [
            $productAbstractMerchantTransfer1,
            $productAbstractMerchantTransfer2,
        ];

        // Act
        $productAbstractMerchantTransfers = $this->tester
            ->getFacade()
            ->getMerchantDataByProductAbstractIds([
                $productConcrete1->getFkProductAbstract(),
                $productConcrete2->getFkProductAbstract(),
            ]);

        // Assert
        $this->assertIsArray($productAbstractMerchantTransfers);
        $this->assertEquals($expectedResult, $productAbstractMerchantTransfers);
    }

    public function testGetMerchantDataByProductAbstractIdsForNotExistingAbstractProductReturnsEmptyArray(): void
    {
        // Arrange
        $notExistingProductAbstractIds = [0];
        $expectedProductAbstractMerchantTransfers = [];

        // Act
        $productAbstractMerchantTransfers = $this->tester
            ->getFacade()
            ->getMerchantDataByProductAbstractIds($notExistingProductAbstractIds);

        // Assert
        $this->assertEquals($expectedProductAbstractMerchantTransfers, $productAbstractMerchantTransfers);
    }

    public function testExpandProductConcretePageMapSuccess(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct([
            ProductConcreteTransfer::IS_ACTIVE => true,
        ]);
        $merchantTransfer = $this->tester->haveMerchant([
            MerchantTransfer::IS_ACTIVE => true,
        ]);
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $productConcreteTransfer->getFkProductAbstract(),
        ]);
        $productData = [
            ProductConcretePageSearchTransfer::FK_PRODUCT => $productConcreteTransfer->getIdProductConcrete(),
        ];

        // Act
        $pageMapTransfer = $this->tester->getFacade()->expandProductConcretePageMap(
            new PageMapTransfer(),
            new PageMapBuilder(),
            $productData,
            new LocaleTransfer(),
        );

        // Assert
        $this->assertContains($merchantTransfer->getMerchantReference(), $pageMapTransfer->getMerchantReferences());
    }

    public function testExpandProductConcretePageMapFailed(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct([
            ProductConcreteTransfer::IS_ACTIVE => true,
        ]);

        $productData = [
            ProductConcretePageSearchTransfer::FK_PRODUCT => $productConcreteTransfer->getIdProductConcrete(),
        ];

        // Act
        $pageMapTransfer = $this->tester->getFacade()->expandProductConcretePageMap(
            new PageMapTransfer(),
            new PageMapBuilder(),
            $productData,
            new LocaleTransfer(),
        );

        // Assert
        $this->assertCount(0, $pageMapTransfer->getMerchantReferences());
        $this->assertCount(0, $pageMapTransfer->getFullTextBoosted());
    }

    /**
     * @dataProvider preloadMerchantByProductConcreteTransfersDataProvider
     */
    public function testPreloadMerchantByProductConcreteTransfersPopulatesCache(
        int $merchantLinkedProductCount,
        int $nonMerchantProductCount,
        int $expectedCacheSize,
    ): void {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => true]);

        $merchantLinkedProducts = [];

        for ($i = 0; $i < $merchantLinkedProductCount; $i++) {
            $product = $this->tester->haveProduct([ProductConcreteTransfer::IS_ACTIVE => true]);
            $this->tester->haveMerchantProduct([
                MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
                MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $product->getFkProductAbstract(),
            ]);
            $merchantLinkedProducts[] = $product;
        }

        $nonMerchantProducts = [];

        for ($i = 0; $i < $nonMerchantProductCount; $i++) {
            $nonMerchantProducts[] = $this->tester->haveProduct([ProductConcreteTransfer::IS_ACTIVE => true]);
        }

        // Act
        $this->tester->getFacade()->preloadMerchantByProductConcreteTransfers(
            array_merge($merchantLinkedProducts, $nonMerchantProducts),
        );

        // Assert
        $cache = $this->getExpanderCache();

        $this->assertCount($expectedCacheSize, $cache);

        foreach ($merchantLinkedProducts as $product) {
            $idProductConcrete = $product->getIdProductConcreteOrFail();
            $this->assertArrayHasKey($idProductConcrete, $cache);
            $this->assertNotNull($cache[$idProductConcrete]);
            $this->assertSame($merchantTransfer->getMerchantReference(), $cache[$idProductConcrete]->getMerchantReference());
        }

        foreach ($nonMerchantProducts as $product) {
            $idProductConcrete = $product->getIdProductConcreteOrFail();
            $this->assertArrayHasKey($idProductConcrete, $cache, sprintf('Expected null cache entry for non-merchant product %d.', $idProductConcrete));
            $this->assertNull($cache[$idProductConcrete]);
        }
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function preloadMerchantByProductConcreteTransfersDataProvider(): array
    {
        return [
            'empty input leaves cache empty' => [
                'merchantLinkedProductCount' => 0,
                'nonMerchantProductCount' => 0,
                'expectedCacheSize' => 0,
            ],
            'single merchant-linked product is cached with merchant transfer' => [
                'merchantLinkedProductCount' => 1,
                'nonMerchantProductCount' => 0,
                'expectedCacheSize' => 1,
            ],
            'multiple merchant-linked products are all cached' => [
                'merchantLinkedProductCount' => 3,
                'nonMerchantProductCount' => 0,
                'expectedCacheSize' => 3,
            ],
            'non-merchant product is cached as null' => [
                'merchantLinkedProductCount' => 0,
                'nonMerchantProductCount' => 1,
                'expectedCacheSize' => 1,
            ],
            'mixed products: merchant-linked cached with transfer, others with null' => [
                'merchantLinkedProductCount' => 2,
                'nonMerchantProductCount' => 2,
                'expectedCacheSize' => 4,
            ],
        ];
    }

    /**
     * Verifies that a second preload call with already-cached product concretes
     * leaves their cache entries unchanged (no re-query to the database).
     */
    public function testPreloadMerchantByProductConcreteTransfersSkipsAlreadyCachedProducts(): void
    {
        // Arrange
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => true]);
        $firstProduct = $this->tester->haveProduct([ProductConcreteTransfer::IS_ACTIVE => true]);
        $secondProduct = $this->tester->haveProduct([ProductConcreteTransfer::IS_ACTIVE => true]);

        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $firstProduct->getFkProductAbstract(),
        ]);
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchant(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $secondProduct->getFkProductAbstract(),
        ]);

        $this->tester->getFacade()->preloadMerchantByProductConcreteTransfers([$firstProduct, $secondProduct]);

        $cacheAfterFirstPreload = $this->getExpanderCache();

        // Act: preload again with only the first product (already in cache)
        $this->tester->getFacade()->preloadMerchantByProductConcreteTransfers([$firstProduct]);

        // Assert: cache is identical — the already-cached entry was not re-fetched or overwritten
        $this->assertEquals($cacheAfterFirstPreload, $this->getExpanderCache());
        $this->assertCount(2, $this->getExpanderCache());
    }

    /**
     * @return array<int, \Generated\Shared\Transfer\MerchantTransfer|null>
     */
    protected function getExpanderCache(): array
    {
        $reflection = new ReflectionClass(MerchantProductSearchExpander::class);

        return $reflection->getProperty('merchantTransferByProductConcreteIdCache')->getValue();
    }

    protected function clearExpanderCache(): void
    {
        $reflection = new ReflectionClass(MerchantProductSearchExpander::class);
        $reflection->getProperty('merchantTransferByProductConcreteIdCache')->setValue(null, []);
    }
}
