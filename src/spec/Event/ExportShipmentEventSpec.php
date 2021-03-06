<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace spec\BitBag\ShippingExportPlugin\Event;

use BitBag\ShippingExportPlugin\Entity\ShippingExportInterface;
use BitBag\ShippingExportPlugin\Event\ExportShipmentEvent;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class ExportShipmentEventSpec extends ObjectBehavior
{
    function let(
        ShippingExportInterface $shippingExport,
        FlashBagInterface $flashBag,
        EntityManagerInterface $shippingExportManager,
        Filesystem $filesystem,
        TranslatorInterface $translator
    )
    {
        $this->beConstructedWith(
            $shippingExport,
            $flashBag,
            $shippingExportManager,
            $filesystem,
            $translator,
            "/shipping_labels"
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ExportShipmentEvent::class);
    }

    function it_extends_event()
    {
        $this->shouldHaveType(Event::class);
    }

    function it_returns_shipping_export(ShippingExportInterface $shippingExport)
    {
        $this->getShippingExport()->shouldReturn($shippingExport);
    }

    function it_adds_success_flash(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag
    )
    {
        $translator
            ->trans('bitbag.ui.shipment_data_has_been_exported')
            ->willReturn("Shipment data has been exported.");
        $flashBag->has('success')->willReturn(false);

        $flashBag
            ->add('success', "Shipment data has been exported.")
            ->shouldBeCalled();

        $this->addSuccessFlash();
    }

    function it_adds_error_flash(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag
    )
    {
        $translator
            ->trans('bitbag.ui.shipping_export_error')
            ->willReturn("An external error occurred while trying to export shipping data.");
        $flashBag->has('error')->willReturn(false);

        $flashBag
            ->add('error', "An external error occurred while trying to export shipping data.")
            ->shouldBeCalled();

        $this->addErrorFlash();
    }

    function it_saves_shipping_label(
        ShippingExportInterface $shippingExport,
        ShipmentInterface $shipment,
        OrderInterface $order
    )
    {
        $shippingExport->getShipment()->willReturn($shipment);
        $shipment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('#0000001');
        $shipment->getId()->willReturn(1);
        $shippingExport->setLabelPath('/shipping_labels/1_0000001.pdf')->shouldBeCalled();

        $this->saveShippingLabel("Length 46 cm x Width 38 cm x Height 89 cm", 'pdf');
    }

    function it_exports_shipment(ShippingExportInterface $shippingExport)
    {
        $shippingExport->setState(ShippingExportInterface::STATE_EXPORTED)->shouldBeCalled();

        $this->exportShipment();
    }
}
