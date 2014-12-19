<?php

/**
 * Copyright 2014 SURFnet bv
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Surfnet\StepupGateway\GatewayBundle\Service;

use Surfnet\SamlBundle\Entity\ServiceProvider;
use Surfnet\SamlBundle\Entity\ServiceProviderRepository;
use Surfnet\StepupGateway\GatewayBundle\Entity\SamlEntityRepository;
use Surfnet\StepupGateway\GatewayBundle\Exception\RuntimeException;

class SamlEntityService implements ServiceProviderRepository
{
    /**
     * @var \Surfnet\StepupGateway\GatewayBundle\Entity\SamlEntityRepository
     */
    private $samlEntityRepository;

    /**
     * @var array
     */
    private $loadedServiceProviders;

    public function __construct(SamlEntityRepository $samlEntityRepository)
    {
        $this->samlEntityRepository = $samlEntityRepository;
        $this->loadedServiceProviders = [];
    }

    /**
     * @param string $entityId
     * @return ServiceProvider
     */
    public function getServiceProvider($entityId)
    {
        if (!array_key_exists($entityId, $this->loadedServiceProviders) && !$this->hasServiceProvider($entityId)) {
            throw new RuntimeException(sprintf(
                'Failed at attempting to load unknown ServiceProvider with EntityId "%s"',
                $entityId
            ));
        }

        return $this->loadedServiceProviders[$entityId];
    }

    /**
     * @param string $entityId
     * @return bool
     */
    public function hasServiceProvider($entityId)
    {
        $samlEntity = $this->samlEntityRepository->getServiceProvider($entityId);

        if (!$samlEntity) {
            return false;
        }

        $serviceProvider = $samlEntity->toServiceProvider();
        $this->loadedServiceProviders[$serviceProvider->getEntityId()] = $serviceProvider;

        return true;
    }
}