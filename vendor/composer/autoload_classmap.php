<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
    'SheerID\\Client\\AbstractClient' => $vendorDir . '/paymentplugins/sheerid-php/src/Client/AbstractClient.php',
    'SheerID\\Client\\BaseClient' => $vendorDir . '/paymentplugins/sheerid-php/src/Client/BaseClient.php',
    'SheerID\\Client\\ClientInterface' => $vendorDir . '/paymentplugins/sheerid-php/src/Client/ClientInterface.php',
    'SheerID\\Exception\\BadRequestException' => $vendorDir . '/paymentplugins/sheerid-php/src/Exception/BadRequestException.php',
    'SheerID\\Exception\\BaseException' => $vendorDir . '/paymentplugins/sheerid-php/src/Exception/BaseException.php',
    'SheerID\\Exception\\ConflictException' => $vendorDir . '/paymentplugins/sheerid-php/src/Exception/ConflictException.php',
    'SheerID\\Exception\\LimitExceededException' => $vendorDir . '/paymentplugins/sheerid-php/src/Exception/LimitExceededException.php',
    'SheerID\\Exception\\NotAuthorizedException' => $vendorDir . '/paymentplugins/sheerid-php/src/Exception/NotAuthorizedException.php',
    'SheerID\\Exception\\NotFoundException' => $vendorDir . '/paymentplugins/sheerid-php/src/Exception/NotFoundException.php',
    'SheerID\\Exception\\PermissionException' => $vendorDir . '/paymentplugins/sheerid-php/src/Exception/PermissionException.php',
    'SheerID\\Http\\HttpFactory' => $vendorDir . '/paymentplugins/sheerid-php/src/Http/HttpFactory.php',
    'SheerID\\Http\\HttpInterface' => $vendorDir . '/paymentplugins/sheerid-php/src/Http/HttpInterface.php',
    'SheerID\\Model\\AbstractModel' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/AbstractModel.php',
    'SheerID\\Model\\Audience' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Audience.php',
    'SheerID\\Model\\Collection' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Collection.php',
    'SheerID\\Model\\CustomCssRequest' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/CustomCssRequest.php',
    'SheerID\\Model\\CustomerTagging' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/CustomerTagging.php',
    'SheerID\\Model\\DisplayInfo' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/DisplayInfo.php',
    'SheerID\\Model\\InstallOptions' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/InstallOptions.php',
    'SheerID\\Model\\LastResponse' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/LastResponse.php',
    'SheerID\\Model\\LoginResponse' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/LoginResponse.php',
    'SheerID\\Model\\Metadata' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Metadata.php',
    'SheerID\\Model\\Organization' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Organization.php',
    'SheerID\\Model\\PersonInfo' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/PersonInfo.php',
    'SheerID\\Model\\Program' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Program.php',
    'SheerID\\Model\\SegmentDescription' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/SegmentDescription.php',
    'SheerID\\Model\\SegmentDetails' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/SegmentDetails.php',
    'SheerID\\Model\\SupportedLanguage' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/SupportedLanguage.php',
    'SheerID\\Model\\Verification\\ActiveMilitaryVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/ActiveMilitaryVerification.php',
    'SheerID\\Model\\Verification\\AgeVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/AgeVerification.php',
    'SheerID\\Model\\Verification\\BaseVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/BaseVerification.php',
    'SheerID\\Model\\Verification\\FirstResponderVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/FirstResponderVerification.php',
    'SheerID\\Model\\Verification\\InactiveMilitaryVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/InactiveMilitaryVerification.php',
    'SheerID\\Model\\Verification\\LicensedProfessionalVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/LicensedProfessionalVerification.php',
    'SheerID\\Model\\Verification\\SeniorVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/SeniorVerification.php',
    'SheerID\\Model\\Verification\\StudentVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/StudentVerification.php',
    'SheerID\\Model\\Verification\\TeacherVerification' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/TeacherVerification.php',
    'SheerID\\Model\\Verification\\VerificationDetails' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Verification/VerificationDetails.php',
    'SheerID\\Model\\Webhook' => $vendorDir . '/paymentplugins/sheerid-php/src/Model/Webhook.php',
    'SheerID\\Platform\\PlatformInterface' => $vendorDir . '/paymentplugins/sheerid-php/src/Platform/PlatformInterface.php',
    'SheerID\\Platform\\WordPress\\WordPressHttp' => $vendorDir . '/paymentplugins/sheerid-php/src/Platform/WordPress/WordPressHttp.php',
    'SheerID\\Platform\\WordPress\\WordPressPlatform' => $vendorDir . '/paymentplugins/sheerid-php/src/Platform/WordPress/WordPressPlatform.php',
    'SheerID\\Service\\AbstractService' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/AbstractService.php',
    'SheerID\\Service\\Factory\\AbstractServiceFactory' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/Factory/AbstractServiceFactory.php',
    'SheerID\\Service\\Factory\\BaseServiceFactory' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/Factory/BaseServiceFactory.php',
    'SheerID\\Service\\LoginService' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/LoginService.php',
    'SheerID\\Service\\ProgramService' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/ProgramService.php',
    'SheerID\\Service\\SegmentService' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/SegmentService.php',
    'SheerID\\Service\\ServiceInterface' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/ServiceInterface.php',
    'SheerID\\Service\\VerificationDetailsService' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/VerificationDetailsService.php',
    'SheerID\\Service\\VerificationService' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/VerificationService.php',
    'SheerID\\Service\\WebhookService' => $vendorDir . '/paymentplugins/sheerid-php/src/Service/WebhookService.php',
    'SheerID\\Utils\\ModelTypes' => $vendorDir . '/paymentplugins/sheerid-php/src/Utils/ModelTypes.php',
    'SheerID\\Utils\\Util' => $vendorDir . '/paymentplugins/sheerid-php/src/Utils/Util.php',
);