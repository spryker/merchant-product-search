<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProductSearch\Business\Expander;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\PageMapTransfer;
use Generated\Shared\Transfer\ProductConcretePageSearchTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Spryker\Zed\MerchantProductSearch\Dependency\Facade\MerchantProductSearchToMerchantProductFacadeInterface;
use Spryker\Zed\ProductPageSearchExtension\Dependency\PageMapBuilderInterface;

class MerchantProductSearchExpander implements MerchantProductSearchExpanderInterface
{
    /**
     * @var array<int, \Generated\Shared\Transfer\MerchantTransfer|null>
     */
    protected static array $merchantTransferByProductConcreteIdCache = [];

    /**
     * @var \Spryker\Zed\MerchantProductSearch\Dependency\Facade\MerchantProductSearchToMerchantProductFacadeInterface
     */
    protected $merchantProductFacade;

    public function __construct(MerchantProductSearchToMerchantProductFacadeInterface $merchantProductFacade)
    {
        $this->merchantProductFacade = $merchantProductFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\PageMapTransfer $pageMapTransfer
     * @param \Spryker\Zed\ProductPageSearchExtension\Dependency\PageMapBuilderInterface $pageMapBuilder
     * @param array<string, mixed> $productData
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\PageMapTransfer
     */
    public function expandProductConcretePageMap(
        PageMapTransfer $pageMapTransfer,
        PageMapBuilderInterface $pageMapBuilder,
        array $productData,
        LocaleTransfer $localeTransfer
    ): PageMapTransfer {
        $idProductConcrete = $productData[ProductConcretePageSearchTransfer::FK_PRODUCT];

        if (!$idProductConcrete) {
            return $pageMapTransfer;
        }

        $merchantTransfer = $this->findMerchantTransferByProductConcreteId($idProductConcrete);

        if (!$merchantTransfer) {
            return $pageMapTransfer;
        }

        $pageMapTransfer->addMerchantReference($merchantTransfer->getMerchantReference());

        return $pageMapTransfer;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransfers
     *
     * @return void
     */
    public function preloadMerchantByProductConcreteTransfers(array $productConcreteTransfers): void
    {
        $uncachedTransfers = array_filter(
            $productConcreteTransfers,
            static fn (ProductConcreteTransfer $transfer): bool => !array_key_exists(
                $transfer->getIdProductConcreteOrFail(),
                static::$merchantTransferByProductConcreteIdCache,
            ),
        );

        if (!$uncachedTransfers) {
            return;
        }

        $skuToIdProductConcreteMap = [];
        foreach ($uncachedTransfers as $transfer) {
            $skuToIdProductConcreteMap[$transfer->getSkuOrFail()] = $transfer->getIdProductConcreteOrFail();
        }

        $skuToMerchantReferenceMap = $this->merchantProductFacade->getConcreteProductSkuMerchantReferenceMap(
            array_keys($skuToIdProductConcreteMap),
        );

        foreach ($skuToIdProductConcreteMap as $sku => $idProductConcrete) {
            $merchantReference = $skuToMerchantReferenceMap[$sku] ?? null;

            static::$merchantTransferByProductConcreteIdCache[$idProductConcrete] = $merchantReference !== null
                ? (new MerchantTransfer())->setMerchantReference($merchantReference)
                : null;
        }
    }

    protected function findMerchantTransferByProductConcreteId(int $idProductConcrete): ?MerchantTransfer
    {
        if (!array_key_exists($idProductConcrete, static::$merchantTransferByProductConcreteIdCache)) {
            $merchantProductCriteriaTransfer = (new MerchantProductCriteriaTransfer())
                ->addIdProductConcrete($idProductConcrete);

            static::$merchantTransferByProductConcreteIdCache[$idProductConcrete] = $this->merchantProductFacade->findMerchant($merchantProductCriteriaTransfer);
        }

        return static::$merchantTransferByProductConcreteIdCache[$idProductConcrete];
    }
}
