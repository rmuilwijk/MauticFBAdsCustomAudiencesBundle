# MauticFBAdsCustomAudiencesBundle
Enables integration with Facebook Ads Custom Audiences Syncing your Mautic segments.

# Installation
1) Require the FB ads library in the Mautic root directory
composer require facebook/php-ads-sdk:2.9.*

The library has been tested on 5.6 to also work so if you get requirements errors try:
composer require --ignore-platform-reqs facebook/php-ads-sdk:2.9.*

2) Create a new Facebook App:
https://developers.facebook.com/apps/

3) Add the Marketing API Product.

4) Visit the Marketing API -> Tools page in your app and check the scopes and hit Get Token. Store this access_token for later use.

5) Go into your Facebook Ads account management and get your ad account id. 

6) Visit the Marketing API -> Settings page in your app and add your ad account id.

7) Visit the app Dashboard page and store the app_id and app_key for later use.

8) Enable the plugin and enter your app_id, app_secret, access_token and ad accoun id.

9) Visis your facebook ads audiences page and check whether your segments have been added.
