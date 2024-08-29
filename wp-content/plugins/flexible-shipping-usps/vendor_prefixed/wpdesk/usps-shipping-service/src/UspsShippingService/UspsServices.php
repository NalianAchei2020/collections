<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService;

/**
 * A class that defines USPS services.
 */
class UspsServices
{
    const API_SERVICES = ['PRIORITY COMMERCIAL', 'PRIORITY MAIL CUBIC', 'PRIORITY MAIL EXPRESS COMMERCIAL', 'GROUND ADVANTAGE COMMERCIAL', 'GROUND ADVANTAGE CUBIC', 'MEDIA', 'LIBRARY', 'BPM'];
    const FIRST_CLASS_LARGE_ENVELOPE = '0L';
    const FIRST_CLASS_PACKAGE_SERVICE_RETAIL = '0P';
    const FIRST_CLASS_PACKAGE_SERVICE_RETAIL_NAME = 'First-Class Package Service - Retail&lt;sup&gt;&#8482;&lt;/sup&gt;';
    const FIRST_CLASS_MAIL_LARGE_ENVELOPE_NAME = 'First-Class Mail&lt;sup&gt;&#174;&lt;/sup&gt; Large Envelope';
    /**
     * @return array
     */
    private function get_services() : array
    {
        return ['domestic' => [self::FIRST_CLASS_LARGE_ENVELOPE => \__('First-Class Mail; Large Envelope', 'flexible-shipping-usps'), self::FIRST_CLASS_PACKAGE_SERVICE_RETAIL => \__('First-Class Package Service - Retail', 'flexible-shipping-usps'), '1' => \__('Priority Mail', 'flexible-shipping-usps'), '2' => \__('Priority Mail Express; Hold For Pickup', 'flexible-shipping-usps'), '3' => \__('Priority Mail Express', 'flexible-shipping-usps'), '4' => \__('Standard Post', 'flexible-shipping-usps'), '6' => \__('Media Mail', 'flexible-shipping-usps'), '7' => \__('Library Mail', 'flexible-shipping-usps'), '13' => \__('Priority Mail Express; Flat Rate Envelope', 'flexible-shipping-usps'), '15' => \__('First-Class Mail; Large Postcards', 'flexible-shipping-usps'), '16' => \__('Priority Mail; Flat Rate Envelope', 'flexible-shipping-usps'), '17' => \__('Priority Mail; Medium Flat Rate Box', 'flexible-shipping-usps'), '22' => \__('Priority Mail; Large Flat Rate Box', 'flexible-shipping-usps'), '23' => \__('Priority Mail Express; Sunday/Holiday Delivery', 'flexible-shipping-usps'), '25' => \__('Priority Mail Express; Sunday/Holiday Delivery Flat Rate Envelope', 'flexible-shipping-usps'), '27' => \__('Priority Mail Express; Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '28' => \__('Priority Mail; Small Flat Rate Box', 'flexible-shipping-usps'), '29' => \__('Priority Mail; Padded Flat Rate Envelope', 'flexible-shipping-usps'), '30' => \__('Priority Mail Express; Legal Flat Rate Envelope', 'flexible-shipping-usps'), '31' => \__('Priority Mail Express; Legal Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '32' => \__('Priority Mail Express; Sunday/Holiday Delivery Legal Flat Rate Envelope', 'flexible-shipping-usps'), '33' => \__('Priority Mail; Hold For Pickup', 'flexible-shipping-usps'), '34' => \__('Priority Mail; Large Flat Rate Box Hold For Pickup', 'flexible-shipping-usps'), '35' => \__('Priority Mail; Medium Flat Rate Box Hold For Pickup', 'flexible-shipping-usps'), '36' => \__('Priority Mail; Small Flat Rate Box Hold For Pickup', 'flexible-shipping-usps'), '37' => \__('Priority Mail; Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '38' => \__('Priority Mail; Gift Card Flat Rate Envelope', 'flexible-shipping-usps'), '39' => \__('Priority Mail; Gift Card Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '40' => \__('Priority Mail; Window Flat Rate Envelope', 'flexible-shipping-usps'), '41' => \__('Priority Mail; Window Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '42' => \__('Priority Mail; Small Flat Rate Envelope', 'flexible-shipping-usps'), '43' => \__('Priority Mail; Small Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '44' => \__('Priority Mail; Legal Flat Rate Envelope', 'flexible-shipping-usps'), '45' => \__('Priority Mail; Legal Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '46' => \__('Priority Mail; Padded Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '47' => \__('Priority Mail; Regional Rate Box A', 'flexible-shipping-usps'), '48' => \__('Priority Mail; Regional Rate Box A Hold For Pickup', 'flexible-shipping-usps'), '49' => \__('Priority Mail; Regional Rate Box B', 'flexible-shipping-usps'), '50' => \__('Priority Mail; Regional Rate Box B Hold For Pickup', 'flexible-shipping-usps'), '53' => \__('First-Class; Package Service Hold For Pickup', 'flexible-shipping-usps'), '55' => \__('Priority Mail Express; Flat Rate Boxes', 'flexible-shipping-usps'), '56' => \__('Priority Mail Express; Flat Rate Boxes Hold For Pickup', 'flexible-shipping-usps'), '57' => \__('Priority Mail Express; Sunday/Holiday Delivery Flat Rate Boxes', 'flexible-shipping-usps'), '58' => \__('Priority Mail; Regional Rate Box C', 'flexible-shipping-usps'), '59' => \__('Priority Mail; Regional Rate Box C Hold For Pickup', 'flexible-shipping-usps'), '61' => \__('First-Class; Package Service', 'flexible-shipping-usps'), '62' => \__('Priority Mail Express; Padded Flat Rate Envelope', 'flexible-shipping-usps'), '63' => \__('Priority Mail Express; Padded Flat Rate Envelope Hold For Pickup', 'flexible-shipping-usps'), '64' => \__('Priority Mail Express; Sunday/Holiday Delivery Padded Flat Rate Envelope', 'flexible-shipping-usps'), '78' => \__('First-Class Mail; Metered Letter', 'flexible-shipping-usps'), '84' => \__('Priority Mail; Cubic', 'flexible-shipping-usps'), '922' => \__('Priority Mail 1-Day Return Service Padded Flat Rate Envelope', 'flexible-shipping-usps'), '932' => \__('Priority Mail 1-Day Return Service Gift Card Flat Rate Envelope', 'flexible-shipping-usps'), '934' => \__('Priority Mail 1-Day Return Service Window Flat Rate Envelope', 'flexible-shipping-usps'), '936' => \__('Priority Mail 1-Day Return Service Small Flat Rate Envelope', 'flexible-shipping-usps'), '938' => \__('Priority Mail 1-Day Return Service Legal Flat Rate Envelope', 'flexible-shipping-usps'), '939' => \__('Priority Mail 1-Day Return Service Flat Rate Envelope', 'flexible-shipping-usps'), '946' => \__('Priority Mail 1-Day Return Service Regional Rate Box A', 'flexible-shipping-usps'), '947' => \__('Priority Mail 1-Day Return Service Regional Rate Box B', 'flexible-shipping-usps'), '962' => \__('Priority Mail 1-Day Return Service', 'flexible-shipping-usps'), '963' => \__('Priority Mail 1-Day Return Service Large Flat Rate Box', 'flexible-shipping-usps'), '964' => \__('Priority Mail 1-Day Return Service Medium Flat Rate Box', 'flexible-shipping-usps'), '965' => \__('Priority Mail 1-Day Return Service Small Flat Rate Box', 'flexible-shipping-usps'), '966' => \__('Priority Mail Military Return Service Large Flat Rate Box APO/FPO/DPO', 'flexible-shipping-usps'), '967' => \__('Priority Mail 1-Day Return Service Cubic', 'flexible-shipping-usps'), '968' => \__('First-Class Package Return Service', 'flexible-shipping-usps'), '969' => \__('Ground Return Service', 'flexible-shipping-usps'), '4058' => \__('USPS Ground Advantage HAZMAT', 'flexible-shipping-usps'), '1058' => \__('USPS Ground Advantage', 'flexible-shipping-usps'), '2058' => \__('USPS Ground Advantage Hold For Pickup', 'flexible-shipping-usps'), '6058' => \__('USPS Ground Advantage Parcel Locker', 'flexible-shipping-usps'), '4096' => \__('USPS Ground Advantage Cubic HAZMAT', 'flexible-shipping-usps'), '1096' => \__('USPS Ground Advantage Cubic', 'flexible-shipping-usps'), '2096' => \__('USPS Ground Advantage Cubic Hold For Pickup', 'flexible-shipping-usps'), '6096' => \__('USPS Ground Advantage Cubic Parcel Locker', 'flexible-shipping-usps')], 'international' => ['1' => \__('Priority Mail Express International', 'flexible-shipping-usps'), '2' => \__('Priority Mail International', 'flexible-shipping-usps'), '4' => \__('Global Express Guaranteed; (GXG)**', 'flexible-shipping-usps'), '5' => \__('Global Express Guaranteed; Document', 'flexible-shipping-usps'), '6' => \__('Global Express Guarantee; Non-Document Rectangular', 'flexible-shipping-usps'), '7' => \__('Global Express Guaranteed; Non-Document Non-Rectangular', 'flexible-shipping-usps'), '8' => \__('Priority Mail International; Flat Rate Envelope**', 'flexible-shipping-usps'), '9' => \__('Priority Mail International; Medium Flat Rate Box', 'flexible-shipping-usps'), '10' => \__('Priority Mail Express International; Flat Rate Envelope', 'flexible-shipping-usps'), '11' => \__('Priority Mail International; Large Flat Rate Box', 'flexible-shipping-usps'), '12' => \__('USPS GXG; Envelopes**', 'flexible-shipping-usps'), '13' => \__('First-Class Mail; International Letter**', 'flexible-shipping-usps'), '14' => \__('First-Class Mail; International Large Envelope**', 'flexible-shipping-usps'), '15' => \__('First-Class Package International Service**', 'flexible-shipping-usps'), '16' => \__('Priority Mail International; Small Flat Rate Box**', 'flexible-shipping-usps'), '17' => \__('Priority Mail Express International; Legal Flat Rate Envelope', 'flexible-shipping-usps'), '18' => \__('Priority Mail International; Gift Card Flat Rate Envelope**', 'flexible-shipping-usps'), '19' => \__('Priority Mail International; Window Flat Rate Envelope**', 'flexible-shipping-usps'), '20' => \__('Priority Mail International; Small Flat Rate Envelope**', 'flexible-shipping-usps')]];
    }
    /**
     * @return array
     */
    public function get_services_domestic() : array
    {
        return $this->get_services()['domestic'];
    }
    /**
     * @return array
     */
    public function get_services_international() : array
    {
        return $this->get_services()['international'];
    }
}
