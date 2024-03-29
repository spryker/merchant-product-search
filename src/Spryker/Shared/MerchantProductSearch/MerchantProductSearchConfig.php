<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\MerchantProductSearch;

use Spryker\Shared\Kernel\AbstractSharedConfig;

class MerchantProductSearchConfig extends AbstractSharedConfig
{
    /**
     * Specification
     * - Constant is used to group merchant product-related product page data expanders.
     *
     * @api
     *
     * @var string
     */
    public const PLUGIN_MERCHANT_PRODUCT_DATA = 'PLUGIN_MERCHANT_PRODUCT_DATA';

    /**
     * Specification
     * - This events will be used for spy_merchant_product_abstract publishing.
     *
     * @api
     *
     * @var string
     */
    public const MERCHANT_PRODUCT_ABSTRACT_PUBLISH = 'MerchantProduct.merchant_product_abstract.publish';

    /**
     * Specification
     * - This events will be used for spy_merchant publishing.
     *
     * @api
     *
     * @var string
     */
    public const MERCHANT_PUBLISH = 'Merchant.merchant.publish';

    /**
     * Specification
     * - This events will be used for spy_merchant entity changes.
     *
     * @api
     *
     * @var string
     */
    public const ENTITY_SPY_MERCHANT_UPDATE = 'Entity.spy_merchant.update';

    /**
     * Specification:
     * - This event is used for merchant product publishing.
     *
     * @api
     *
     * @var string
     */
    public const MERCHANT_PRODUCT_SEARCH_RESOURCE_NAME = 'merchant_product_search';
}
