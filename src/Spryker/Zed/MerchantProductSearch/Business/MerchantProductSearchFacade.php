<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProductSearch\Business;

use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\MerchantProductSearch\Business\MerchantProductSearchBusinessFactory getFactory()
 * @method \Spryker\Zed\MerchantProductSearch\Persistence\MerchantProductSearchRepositoryInterface getRepository()
 */
class MerchantProductSearchFacade extends AbstractFacade implements MerchantProductSearchFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $eventTransfers
     *
     * @return void
     */
    public function writeCollectionByIdMerchantEvents(array $eventTransfers): void
    {
        $this->getFactory()
            ->createMerchantProductSearchWriter()
            ->writeCollectionByIdMerchantEvents($eventTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $eventTransfers
     *
     * @return void
     */
    public function writeCollectionByIdMerchantProductEvents(array $eventTransfers): void
    {
        $this->getFactory()
            ->createMerchantProductSearchWriter()
            ->writeCollectionByIdMerchantProductEvents($eventTransfers);
    }
}
