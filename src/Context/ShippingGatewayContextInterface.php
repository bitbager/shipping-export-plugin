<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\ShippingExportPlugin\Context;

use BitBag\ShippingExportPlugin\Exception\ShippingGatewayNotFoundException;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
interface ShippingGatewayContextInterface
{
    /**
     * @return string
     */
    public function getFormType();

    /**
     * @return string
     * @throws ShippingGatewayNotFoundException
     */
    public function getCode();

    /**
     * @param string $code
     *
     * @return string
     */
    public function getLabelByCode($code);
}