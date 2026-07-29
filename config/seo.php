<?php

return [
    'site_name' => env('SEO_SITE_NAME', 'The Redeemed Christian Church of God, GOALS Parish'),
    'site_short_name' => env('SEO_SITE_SHORT_NAME', 'RCCG GOALS Parish'),
    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Worship with RCCG GOALS Parish in Ajah, Lagos. Discover Bible-based sermons, upcoming church events, active departments, testimonies, and live worship updates.'
    ),
    'default_keywords' => env(
        'SEO_DEFAULT_KEYWORDS',
        'RCCG GOALS Parish, church in Ajah Lagos, RCCG church Lagos, Christian church Ajah, Bible teaching church, worship services Lagos, church events Ajah, online sermons RCCG'
    ),
    'default_share_image' => env('SEO_DEFAULT_SHARE_IMAGE', 'assets/images/resources/goals_logo_real.png'),
    'organization' => [
        'email' => env('SEO_ORG_EMAIL', 'info@rccggoalsparish.com'),
        'phone' => env('SEO_ORG_PHONE', '+2348065799999'),
        'street_address' => env('SEO_ORG_STREET', 'Plot 27 Mobil Road, Off Ilaje Bustop'),
        'locality' => env('SEO_ORG_LOCALITY', 'Ajah'),
        'region' => env('SEO_ORG_REGION', 'Lagos'),
        'country' => env('SEO_ORG_COUNTRY', 'NG'),
        'same_as' => array_values(array_filter([
            env('SEO_SOCIAL_FACEBOOK'),
            env('SEO_SOCIAL_INSTAGRAM'),
            env('SEO_SOCIAL_TWITTER'),
            env('SEO_SOCIAL_YOUTUBE'),
        ])),
    ],
];
