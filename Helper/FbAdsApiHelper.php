<?php

/*
 * @copyright   2017 Trinoco. All rights reserved
 * @author      Trinoco
 *
 * @link        http://trinoco.nl
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticFBAdsCustomAudiencesBundle\Helper;

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\CustomAudience;
use FacebookAds\Object\CustomAudienceMultiKey;
use FacebookAds\Object\Fields\CustomAudienceFields;
use FacebookAds\Object\Fields\CustomAudienceMultikeySchemaFields;
use FacebookAds\Object\Values\CustomAudienceSubtypes;
use Mautic\PluginBundle\Integration\AbstractIntegration;

use FacebookAds\Api;

class FbAdsApiHelper {
  public static $adAccount;

  /**
   * Initialize the FB Ads API.
   *
   * @param \Mautic\PluginBundle\Integration\AbstractIntegration $integration
   *
   * @return \FacebookAds\Api|null
   */
  public static function init(AbstractIntegration $integration) {
    $keys = $integration->getDecryptedApiKeys();
    static::$adAccount = 'act_' . $keys[$integration->getAdAccountIdKey()];
    Api::init($keys[$integration->getClientIdKey()], $keys[$integration->getClientSecretKey()], $keys[$integration->getAuthTokenKey()]);
    return Api::instance();
  }


  public static function getFBAudiences() {
    $name_mapping = array();

    if (!static::$adAccount) {
      return $name_mapping;
    }

    $account = new AdAccount(static::$adAccount);
    foreach ($account->getCustomAudiences([CustomAudienceFields::ID, CustomAudienceFields::NAME]) as $list) {
      $data = $list->getData();
      $name_mapping[$data['name']] = $data['id'];
    }
    return $name_mapping;
  }

  public static function getFBAudienceID($listName) {
    $audiences = static::getFBAudiences();
    if (isset($audiences[$listName])) {
      return $audiences[$listName];
    }

    return FALSE;
  }

  public static function getFBAudience($listName) {
    if ($audience_id = static::getFBAudienceID($listName)) {
      return new CustomAudienceMultiKey($audience_id);
    }
  }

  public static function deleteList($name) {
    $audiences = static::getFBAudiences();
    if (isset($audiences[$name])) {
      $audience_id = $audiences[$name];
      $audience = new CustomAudienceMultiKey($audience_id);
      $audience->deleteSelf();
    }
  }

  public static function addList(\Mautic\LeadBundle\Entity\LeadList $list) {
    // Get the name of the list, or the old one to rename.
    $changes = $list->getChanges();
    if (isset($changes['name']) && is_array($changes['name'])) {
      $orig_name = $changes['name'][0];
    }
    else {
      $orig_name = $list->getName();
    }

    $audiences = static::getFBAudiences();
    if (isset($audiences[$orig_name])) {
      $audience_id = $audiences[$orig_name];
      $audience = new CustomAudienceMultiKey($audience_id);
      $audience->setData(array(
        CustomAudienceFields::NAME => $list->getName(),
        CustomAudienceFields::DESCRIPTION => 'Mautic Segment: ' . $list->getDescription()
      ));
      $audience->update();
    }
    else {
      $audience = new CustomAudienceMultiKey();
      $audience->setParentId(static::$adAccount);
      $audience->setData(array(
        CustomAudienceFields::NAME => $list->getName(),
        CustomAudienceFields::SUBTYPE => CustomAudienceSubtypes::CUSTOM,
        CustomAudienceFields::DESCRIPTION => 'Mautic Segment: ' . $list->getDescription()
      ));
      $audience->create();
    }

    return $audience;
  }

  public static function addUsers(CustomAudienceMultiKey $audience, array $users) {
    $audience->addUsers($users, static::getFBSchema());
  }

  public static function removeUsers(CustomAudienceMultiKey $audience, array $users) {
    $audience->removeUsers($users, static::getFBSchema());
  }

  protected static function getFBSchema() {
    return array(
      CustomAudienceMultikeySchemaFields::FIRST_NAME,
      CustomAudienceMultikeySchemaFields::LAST_NAME,
      CustomAudienceMultikeySchemaFields::EMAIL,
      CustomAudienceMultiKeySchemaFields::PHONE,
      CustomAudienceMultiKeySchemaFields::COUNTRY,
    );
  }
}