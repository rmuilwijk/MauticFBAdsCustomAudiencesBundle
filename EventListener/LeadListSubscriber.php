<?php

/*
 * @copyright   2017 Trinoco. All rights reserved
 * @author      Trinoco
 *
 * @link        http://trinoco.nl
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */


namespace MauticPlugin\MauticFBAdsCustomAudiencesBundle\EventListener;

use FacebookAds\Object\CustomAudienceMultiKey;
use FacebookAds\Object\Fields\CustomAudienceMultikeySchemaFields;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadListEvent;
use Mautic\LeadBundle\Event\ListChangeEvent;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\LeadBundle\LeadEvents;

use MauticPlugin\MauticFBAdsCustomAudiencesBundle\Helper\FbAdsApiHelper;


/**
 * Class LeadListsSubscriber.
 */
class LeadListSubscriber extends CommonSubscriber
{
  /**
   * @var \FacebookAds\Api
   */
  protected $fbAPI;

  /**
   * @var IntegrationHelper
   */
  protected $helper;

  /**
   * LeadSubscriber constructor.
   */
  public function __construct(IntegrationHelper $helper)
  {
    $this->helper = $helper;
    $this->fbAPI = $this->fbApiInit();
  }

  /**
   * @return array
   */
  public static function getSubscribedEvents()
  {
    return [
      LeadEvents::LIST_POST_SAVE => ['onLeadListPostSave', 0],
      LeadEvents::LIST_POST_DELETE => ['onLeadListPostDelete', 0],
      LeadEvents::LEAD_LIST_BATCH_CHANGE => ['onLeadListBatchChange', 0],
      LeadEvents::LEAD_LIST_CHANGE       => ['onLeadListChange', 0],
    ];
  }

  /**
   * Initializes the Facebook Ads API.
   *
   * @return bool|\FacebookAds\Api|null
   */
  protected function fbApiInit() {
    $integration = $this->helper->getIntegrationObject('FBAdsCustomAudiences');
    if (!$integration || !$integration->getIntegrationSettings()->isPublished()) {
      return FALSE;
    }

    return FbAdsApiHelper::init($integration);
  }

  /**
   * Add list to facebook.
   *
   * @param ListChangeEvent $event
   */
  public function onLeadListPostSave(LeadListEvent $event) {
    if (!$this->fbAPI) {
      return;
    }

    $list = $event->getList();
    FbAdsApiHelper::addList($list);
  }

  /**
   * Delete list from facebook.
   *
   * @param ListChangeEvent $event
   */
  public function onLeadListPostDelete(LeadListEvent $event)
  {
    if (!$this->fbAPI) {
      return;
    }

    $list = $event->getList();
    FbAdsApiHelper::deleteList($list->getName());
  }

  /**
   * Add/remove leads from facebook based on batch lead list changes.
   *
   * @param ListChangeEvent $event
   */
  public function onLeadListBatchChange(ListChangeEvent $event)
  {
    if (!$this->fbAPI) {
      return;
    }

    if ($audience = FbAdsApiHelper::getFBAudience($event->getList()->getName())) {
      $users = array();
      foreach ($event->getLeads() as $lead_id) {
        $lead = $this->em->getRepository('MauticLeadBundle:Lead')->getEntity($lead_id);

        if ($lead->getEmail()) {
          $users[] = array(
            $lead->getFirstname(),
            $lead->getLastname(),
            $lead->getEmail(),
            $lead->getMobile(),
            $lead->getCountry()
          );
        }
      }

      if ($event->wasAdded()) {
        FbAdsApiHelper::addUsers($audience, $users);
      }
      else {
        FbAdsApiHelper::removeUsers($audience, $users);
      }
    }

    // Save memory with batch processing
    unset($event, $users, $audience);
  }

  /**
   * Add/remove leads from campaigns based on lead list changes.
   *
   * @param ListChangeEvent $event
   */
  public function onLeadListChange(ListChangeEvent $event)
  {
    if (!$this->fbAPI) {
      return;
    }

    /** @var \Mautic\LeadBundle\Entity\Lead $lead */
    $lead   = $event->getLead();

    if ($audience = FbAdsApiHelper::getFBAudience($event->getList()->getName())) {
      $users = array(
        array($lead->getFirstname(), $lead->getLastname(), $lead->getEmail(), $lead->getMobile(), $lead->getCountry())
      );

      if ($event->wasAdded()) {
        FbAdsApiHelper::addUsers($audience, $users);
      }
      else {
        FbAdsApiHelper::removeUsers($audience, $users);
      }
    }
  }
}
