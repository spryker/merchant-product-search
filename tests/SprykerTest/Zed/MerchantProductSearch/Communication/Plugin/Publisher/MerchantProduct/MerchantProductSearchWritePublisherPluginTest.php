<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MerchantProductSearch\Communication\Plugin\Publisher\MerchantProduct;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\StoreRelationBuilder;
use Generated\Shared\Transfer\EventEntityTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\MerchantProduct\Dependency\MerchantProductEvents;
use Spryker\Zed\MerchantProductSearch\Communication\Plugin\Publisher\MerchantProduct\MerchantProductSearchWritePublisherPlugin;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantProductSearch
 * @group Communication
 * @group Plugin
 * @group Publisher
 * @group MerchantProduct
 * @group MerchantProductSearchWritePublisherPluginTest
 * Add your own group annotations below this line
 */
class MerchantProductSearchWritePublisherPluginTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\MerchantProductSearch\MerchantProductSearchCommunicationTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->tester->addDependencies();
    }

    /**
     * @return void
     */
    public function testMerchantProductSearchWritePublisherPluginStoresData(): void
    {
        // Arrange
        $beforeCount = $this->tester->getProductAbstractPageSearchPropelQuery()->count();

        /** @var \Generated\Shared\Transfer\StoreTransfer $storeTransfer */
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => 'DE']);
        $storeRelationTransfer = (new StoreRelationBuilder())->seed([
            StoreRelationTransfer::ID_STORES => [$storeTransfer->getIdStore()],
        ])->build();
        $productConcreteTransfer = $this->tester->haveProduct();
        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => true, MerchantTransfer::STORE_RELATION => $storeRelationTransfer->toArray()]);
        $merchantProductAbstractEntity = $this->tester->addMerchantProductRelation($merchantTransfer->getIdMerchant(), $productConcreteTransfer->getFkProductAbstract());
        $this->tester->addProductRelatedData($productConcreteTransfer);

        $merchantProductSearchWritePublisher = new MerchantProductSearchWritePublisherPlugin();
        $eventTransfers = [
            (new EventEntityTransfer())->setId($merchantProductAbstractEntity->getIdProductAbstractMerchant()),
        ];

        // Act
        $merchantProductSearchWritePublisher->handleBulk($eventTransfers, MerchantProductEvents::MERCHANT_PRODUCT_ABSTRACT_PUBLISH);
        $afterCount = $this->tester->getProductAbstractPageSearchPropelQuery()->count();

        // Assert
        $this->assertGreaterThan($beforeCount, $afterCount);
        $this->tester->assertProductPageAbstractSearch($merchantTransfer, $productConcreteTransfer);
    }
}
