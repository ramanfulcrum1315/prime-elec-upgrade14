<?php

/**
 * Config model that is aware of all Mage_Paypal payment methods
 * Works with PayPal-specific system configuration
 */
class Redstage_PaypalBuildNotation_Model_Config
    extends Mage_Paypal_Model_Config
{
    /**
     * PayPal Build Notation
     * @var string
     */
    const BUILD_NOTATION    = 'RedstageNetworks_SI_MagentoCE';

    /**
     * BN code getter
     * override method
     *
     * @param string $countryCode ISO 3166-1
     */
    public function getBuildNotationCode($countryCode = null)
    {
        $newBnCode = self::BUILD_NOTATION;

        return $newBnCode;
    }
}
