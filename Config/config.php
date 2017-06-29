<?php

/*
 * @copyright   2017 Trinoco. All rights reserved
 * @author      Trinoco
 *
 * @link        http://trinoco.nl
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
  'name'        => 'Advertising',
  'description' => 'Enables integration with Facebook Ads Custom Audiences Syncing your segments.',
  'version'     => '1.0',
  'author'      => 'Trinoco',
  'services' => [
    'events' => [
      'mautic.plugin.fbadsaudience.lead.subscriber' => [
        'class'     => 'MauticPlugin\MauticFBAdsCustomAudiencesBundle\EventListener\LeadListSubscriber',
        'arguments' => [
          'mautic.helper.integration',
        ],
      ],
      'mautic.plugin.fbadsaudience.plugin.subscriber' => [
        'class'     => 'MauticPlugin\MauticFBAdsCustomAudiencesBundle\EventListener\PluginSubscriber',
      ],
    ],
    'integrations' => [
      'mautic.integration.fbadsaudience' => [
        'class'     => \MauticPlugin\MauticFBAdsCustomAudiencesBundle\Integration\FBAdsCustomAudiencesIntegration::class,
      ],
    ],
  ],
];
