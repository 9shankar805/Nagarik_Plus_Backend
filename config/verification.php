<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Government Verification Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Controls which adapters are active and what their rate limits are.
    | All providers default to disabled until an official API agreement
    | is established with the relevant government authority.
    |
    */

    /*
     * Set to true ONLY after receiving an official API key / MOU from DONIDCR.
     * When false, the CAPTCHA proxy endpoint returns 503.
     */
    'captcha_proxy_enabled' => env('VERIFICATION_CAPTCHA_PROXY_ENABLED', false),

    /*
     * Per-user per-hour rate limits for each document type.
     * Adjust conservatively — government endpoints are sensitive.
     */
    'rate_limits' => [
        'nid'         => env('VERIFICATION_RATE_LIMIT_NID',         5),
        'licence'     => env('VERIFICATION_RATE_LIMIT_LICENCE',      5),
        'pan'         => env('VERIFICATION_RATE_LIMIT_PAN',          5),
        'citizenship' => env('VERIFICATION_RATE_LIMIT_CITIZENSHIP',  5),
    ],

    /*
     * HTTP timeout (seconds) for all government API calls.
     */
    'timeout' => env('VERIFICATION_TIMEOUT', 15),

    /*
     * Known government API base URLs.
     * These are documented here for reference.
     * DO NOT call them without an authorized API key.
     */
    'endpoints' => [
        'donidcr_api'     => 'https://api-citizenportal.donidcr.gov.np',
        'donidcr_captcha' => 'https://captcha-citizenportal.donidcr.gov.np',
        'dotm_edl'        => 'https://edl.dotm.gov.np',   // currently offline
        'ird_portal'      => 'https://ird.gov.np',
        'moha_portal'     => 'https://moha.gov.np',
    ],

];
