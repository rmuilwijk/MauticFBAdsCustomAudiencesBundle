# MauticFBAdsCustomAudiencesBundle
Enables integration with Facebook Ads Custom Audiences Syncing your Mautic segments.

Development was sponsored by [Trinoco](https://www.trinoco.nl) for the [Qeado](https://www.qeado.com) project.

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

8) Enable the plugin and enter your app_id, app_secret, access_token and ad accoun id. . If you have more then 10 segments you might get errors enabling the plugin on install. When your facebook App is in development mode the requrest rates are limited. remove some segments en readd them later.

9) Visis your facebook ads audiences page and check whether your segments have been added.

10) Have your application running for a while. After you have reached 1000 succesfull api calls to your facebook app you can submit it for review and have proper rate limiting.
