<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProductSearch\Dependency\Facade;

use Generated\Shared\Transfer\MerchantProductAbstractCollectionTransfer;
use Generated\Shared\Transfer\MerchantProductAbstractCriteriaTransfer;
use Generated\Shared\Transfer\MerchantProductCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantProductSearchToMerchantProductFacadeInterface
{
    public function findMerchant(MerchantProductCriteriaTransfer $merchantProductCriteriaTransfer): ?MerchantTransfer;

    public function getMerchantProductAbstractCollection(
        MerchantProductAbstractCriteriaTransfer $merchantProductAbstractCriteriaTransfer
    ): MerchantProductAbstractCollectionTransfer;

    /**
     * @param array<string> $concreteProductSkus
     *
     * @return array<string, string>
     */
    public function getConcreteProductSkuMerchantReferenceMap(array $concreteProductSkus): array;
}
