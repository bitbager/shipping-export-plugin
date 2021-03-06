<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace Tests\BitBag\ShippingExportPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use BitBag\ShippingExportPlugin\Entity\ShippingGatewayInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Tests\BitBag\ShippingExportPlugin\Behat\Mock\EventListener\FrankMartinShippingExportEventListener;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class ShippingGatewayContext implements Context
{
    /**
     * @var FactoryInterface
     */
    private $shippingMethodFactory;

    /**
     * @var FactoryInterface
     */
    private $shippingGatewayFactory;

    /**
     * @var RepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var RepositoryInterface
     */
    private $shippingGatewayRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @param FactoryInterface $shippingMethodFactory
     * @param FactoryInterface $shippingGatewayFactory
     * @param RepositoryInterface $shippingMethodRepository
     * @param RepositoryInterface $shippingGatewayRepository
     * @param EntityManagerInterface $entityManager
     * @param SharedStorageInterface $sharedStorage
     * @internal param ObjectManager $objectManager
     */
    public function __construct(
        FactoryInterface $shippingMethodFactory,
        FactoryInterface $shippingGatewayFactory,
        RepositoryInterface $shippingMethodRepository,
        RepositoryInterface $shippingGatewayRepository,
        EntityManagerInterface $entityManager,
        SharedStorageInterface $sharedStorage
    )
    {
        $this->shippingMethodFactory = $shippingMethodFactory;
        $this->shippingGatewayFactory = $shippingGatewayFactory;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shippingGatewayRepository = $shippingGatewayRepository;
        $this->entityManager = $entityManager;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given there is a registered :code shipping gateway for this shipping method named :name
     */
    public function thereIsARegisteredShippingGatewayForThisShippingMethod($code, $name)
    {
        /** @var ShippingGatewayInterface $shippingGateway */
        $shippingGateway = $this->shippingGatewayFactory->createNew();
        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->sharedStorage->get('shipping_method');

        $shippingGateway->setShippingMethod($shippingMethod);
        $shippingGateway->setCode($code);
        $shippingGateway->setLabel($name);
        $shippingGateway->setConfig(['iban' => '123', 'address' => 'foo bar']);

        $this->sharedStorage->set('shipping_gateway', $shippingGateway);
        $this->shippingGatewayRepository->add($shippingGateway);
    }

    /**
     * @Given it has :field field set to :value
     */
    public function itHasFieldSetTo($field, $value)
    {
        /** @var ShippingGatewayInterface $shippingGateway */
        $shippingGateway = $this->sharedStorage->get('shipping_gateway');

        $this->config[$field] = $value;
        $shippingGateway->setConfig($this->config);

        $this->sharedStorage->set('shipping_gateway', $shippingGateway);
        $this->entityManager->flush($shippingGateway);
    }

    /**
     * @Given the external shipping API is down
     */
    public function theExternalShippingApiIsDown()
    {
        FrankMartinShippingExportEventListener::toggleSuccess(false);
    }
}
