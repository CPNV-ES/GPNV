<?php
//This is variable is an example - Just make sure that the urls in the 'idp' config are ok.
$idp_host = env('SAML2_IDP_HOST', 'http://intranet.cpnv.ch/saml');
return $settings = array(
    /**
     * If 'useRoutes' is set to true, the package defines five new routes:
     *
     *    Method | URI                      | Name
     *    -------|--------------------------|------------------
     *    POST   | {routesPrefix}/acs       | saml_acs
     *    GET    | {routesPrefix}/login     | saml_login
     *    GET    | {routesPrefix}/logout    | saml_logout
     *    GET    | {routesPrefix}/metadata  | saml_metadata
     *    GET    | {routesPrefix}/sls       | saml_sls
     */
    'useRoutes' => true,
    'routesPrefix' => '/saml2',
    /**
     * which middleware group to use for the saml routes
     * Laravel 5.2 will need a group which includes StartSession
     */
    'routesMiddleware' => ['web'],
    /**
     * Indicates how the parameters will be
     * retrieved from the sls request for signature validation
     */
    'retrieveParametersFromServer' => false,
    /**
     * Where to redirect after logout
     */
    'logoutRoute' => '/',
    /**
     * Where to redirect after login if no other option was provided
     */
    'loginRoute' => '/',
    /**
     * Where to redirect after login if no other option was provided
     */
    'errorRoute' => '/saml2/error',
    /*****
     * One Login Settings
     */
    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => false, //@todo: make this depend on laravel config
    // Enable debug mode (to print errors)
    'debug' => env('APP_DEBUG', true),
    // If 'proxyVars' is True, then the Saml lib will trust proxy headers
    // e.g X-Forwarded-Proto / HTTP_X_FORWARDED_PROTO. This is useful if
    // your application is running behind a load balancer which terminates
    // SSL.
    'proxyVars' => false,
    // Service Provider Data that we are deploying
    'sp' => array(
        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => env('SAML2_SP_x509',''),
        'privateKey' => env('SAML2_SP_PRIVATEKEY',''),
        // Identifier (URI) of the SP entity.
        // Leave blank to use the 'saml_metadata' route.
        'entityId' => env('HOST').'/saml2/metadata',
        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-POST binding.
            // Leave blank to use the 'saml_acs' route
            // http://sc-c332-pc06.cpnv.ch/saml2/login/acs
            'url' => url('/').'/saml2/acs',
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        // Remove this part to not include any URL Location in the metadata.
        'singleLogoutService' => array(
            // URL Location where the <Response> from the IdP will be returned,
            // using HTTP-Redirect binding.
            // Leave blank to use the 'saml_sls' route
            'url' => url('/').'/saml2/sls',
        ),
    ),
    // Identity Provider Data that we want connect with our SP
    'idp' => array(
        // Identifier of the IdP entity  (must be a URI)
        'entityId' => env('SAML2_IDP_ENTITYID', $idp_host . '/saml2/idp/metadata.php'),
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array(
            // URL Target of the IdP where the SP will send the Authentication Request Message,
            // using HTTP-Redirect binding.
            'url' => $idp_host . '/saml2/idp/SSOService.php',
        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array(
            // URL Location of the IdP where the SP will send the SLO Request,
            // using HTTP-Redirect binding.
            'url' => $idp_host . '/saml2/idp/SingleLogoutService.php',
        ),
        // Public x509 certificate of the IdP
        'x509cert' => env('SAML2_IDP_x509', 'MIIDOTCCAiGgAwIBAgIJAJwhcvx0rVHTMA0GCSqGSIb3DQEBBQUAMDMxCzAJBgNVBAYTAkNIMRUwEwYDVQQHDAxEZWZhdWx0IENpdHkxDTALBgNVBAoMBENQTlYwHhcNMTgwMjEyMTMwMjI2WhcNMTkwMjEyMTMwMjI2WjAzMQswCQYDVQQGEwJDSDEVMBMGA1UEBwwMRGVmYXVsdCBDaXR5MQ0wCwYDVQQKDARDUE5WMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsf4qDKXKcXMR2HIpYYeo+9caVLoLTIG1crM/rWBb0vhPeF09mDcu81HPzW+/sCNxIuUXlBD9L2j3zeG7JO+ZFoLl/Z+WGtdW+x6nZRFV5LVqIKRKZNikltw21ZB9gAqtLY5c8mpdwuHubR3L2KcEfE6a3u5zne+E6CPKvHY7DqD0Bim8Hl3deQAatVrWKMjB5ileMc71+qUdCD4/V3hWuAV7IP/4BNK5I8AtK5Stkev/3xmK3+tuEGBAMocXr3hOnwF4NkWfkxdfeKNKZ4Lp7UN5VQ/gTto1Br/plezOFdHUhIxZjTPsKPwiNYTqajVtQwdWpB0crZwUDXPFym7BlQIDAQABo1AwTjAdBgNVHQ4EFgQUWpn/qiYkpm7rvZPvTIq2tWEZqKowHwYDVR0jBBgwFoAUWpn/qiYkpm7rvZPvTIq2tWEZqKowDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOCAQEAbIPu2qkaYRxre4wmaUlH0b5EpZgBSI/qNNsnIhZTfuZ13jn7OS1YON8X+ZItKDrOlMGmK9s/v+TvyL5XCrFcm5+metWyE/04xF1kTnp2mebIq1pU5yC3FOgLYLIsiYCX83EmS/E+fROGSNmmG1JUj4erm60hWghMuXIUrqcZFhElsyixegTdNMIit3YuRTG3GTE66bbq5AhYt8WFSgTIHcAtIPFuCT0MB1/wiOEVGstyYJmQnDUW82eeP/0j+gjyKNC1SPAc8D04K+rqJIU9t+lCgxKMT7l2PdhCd2fY4ko0VcRQt0vyt4UfhtBjlfsVsYe/SC2o/9UVgIT49rkiKw=='),

        /*
         *  Instead of use the whole x509cert you can use a fingerprint
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
         */
        // 'certFingerprint' => '',
    ),
    /***
     *
     *  OneLogin advanced settings
     *
     *
     */
    // Security settings
    'security' => array(
        /** signatures and encryptions offered */
        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,
        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed.              [The Metadata of the SP will offer this info]
        'authnRequestsSigned' => false,
        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,
        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,
        /* Sign the Metadata
         False || True (use sp certs) || array (
                                                    keyFileName => 'metadata.key',
                                                    certFileName => 'metadata.crt'
                                                )
        */
        'signMetadata' => false,
        /** signatures and encryptions required **/
        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,
        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed.        [The Metadata of the SP will offer this info]
        'wantAssertionsSigned' => false,
        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,
        // Authentication context.
        // Set to false and no AuthContext will be sent in the AuthNRequest,
        // Set true or don't present thi parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
        'requestedAuthnContext' => true,
    ),

    // Contact information template, it is recommended to suply a technical and support contacts
    'contactPerson' => array(
        'technical' => array(
            'givenName' => 'name',
            'emailAddress' => 'no@reply.com',
        ),
        'support' => array(
            'givenName' => 'Support',
            'emailAddress' => 'no@reply.com',
        ),
    ),
    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'en-US' => array(
            'name' => 'Name',
            'displayname' => 'Display Name',
            'url' => 'http://url'
        ),
    ),

    /* Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current
       'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
                                          // MUST NOT assume that the IdP validates the sign
       'wantAssertionsSigned' => true,
       'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
       'wantNameIdEncrypted' => false,
    */
);