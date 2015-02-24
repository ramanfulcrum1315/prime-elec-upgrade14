<?php

class OCM_Mods_Helper_Wishlist_Data extends Mage_Wishlist_Helper_Data
{
    public function isAllowInCart()
    {
        return false;
    }

}