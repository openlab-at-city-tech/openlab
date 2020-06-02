<?php
/**
 * Class A_Non_Cachable_Pro_Film_Controller
 * @mixin C_Display_Type_Controller
 * @adapts I_Display_Type_Controller using "photocrati-nextgen_pro_film" context
 */
class A_Non_Cachable_Pro_Film_Controller extends Mixin
{
    function is_cachable()
    {
        return FALSE;
    }
}