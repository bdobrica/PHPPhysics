<?php
class ENG_Exception extends Exception {
        const Database_Error		= 1;
        const Invalid_ID                = 2;
        const Invalid_Assignment        = 3;
        const Forgettable_Object        = 4;
        const Object_Exists             = 5;
        const Saving_Failure            = 6;
        const Unknown_Email             = 7;
        const Unknown_UIN               = 8;
        const Unknown_Object            = 9;
        const Missing_Security          = 10;
        const Event_Misfired            = 11;
        const Action_Failure            = 12;
        const Action_Missing            = 13;
        const Invoiceless_Client        = 14;
        const Missing_Seller            = 15;
        const Missing_Buyer             = 16;
        const Missing_Products          = 17;
        const Invalid_Coupon            = 18;
	const Invalid_Expression	= 19;
	const Invalid_Operator_Arity	= 20;
	const Invalid_Operand		= 21;

        public function __construct ($code = 0, $message = null) {
                parent::__construct ($message, $code);
                }

        public function get ($key = null) {
                if ($key == 'code') return parent::getCode();
                return parent::getMessage ();
                }
        };
?>
