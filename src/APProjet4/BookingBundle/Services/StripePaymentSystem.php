<?php

namespace APProjet4\BookingBundle\Services;

class StripePaymentSystem {

    public function stripePayment() {
// Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey("sk_test_WN4j9w64qu6hUUTWm6zltrrh");
        // Token is created using Checkout or Elements!
        // Get the payment token ID submitted by the form:
        $token = $_POST['stripeToken'];

        $charge = \Stripe\Charge::create([
                    'amount' => 20*100, 
                    'currency' => 'eur',
                    'source' => '$token',
                    'receipt_email' => 'pictapica@gmail.com',
        ]);
        return $charge;
    }

}
