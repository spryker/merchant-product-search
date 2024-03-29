<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MerchantProductSearch\Plugin\Search;

use Generated\Shared\Search\PageIndexMap;
use Generated\Shared\Transfer\FacetConfigTransfer;
use Generated\Shared\Transfer\SearchConfigExtensionTransfer;
use Spryker\Client\Kernel\AbstractPlugin;
use Spryker\Client\SearchExtension\Dependency\Plugin\SearchConfigExpanderPluginInterface;
use Spryker\Shared\Search\SearchConfig;

class MerchantProductMerchantNameSearchConfigExpanderPlugin extends AbstractPlugin implements SearchConfigExpanderPluginInterface
{
    /**
     * @var string
     */
    protected const FACET_CONFIG_NAME = 'merchant_name';

    /**
     * @var string
     */
    protected const FACET_CONFIG_PARAMETER_NAME = 'merchant_name';

    /**
     * {@inheritDoc}
     * - Expands facet configuration with merchant name filter.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\SearchConfigExtensionTransfer
     */
    public function getSearchConfigExtension(): SearchConfigExtensionTransfer
    {
        return (new SearchConfigExtensionTransfer())->addFacetConfig($this->createMerchantNameFacetConfig());
    }

    /**
     * @return \Generated\Shared\Transfer\FacetConfigTransfer
     */
    protected function createMerchantNameFacetConfig(): FacetConfigTransfer
    {
        return (new FacetConfigTransfer())
            ->setName(static::FACET_CONFIG_NAME)
            ->setParameterName(static::FACET_CONFIG_PARAMETER_NAME)
            ->setFieldName(PageIndexMap::STRING_FACET)
            ->setType(SearchConfig::FACET_TYPE_ENUMERATION)
            ->setIsMultiValued(true);
    }
}
