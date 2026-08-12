# Changelog

## 11.1.1 / 2026-08-12

### Breaking changes

* None

### Features / changes

* None

### Fixes

* KUSTOM-6: Hyvä theme compatibility support

## 11.1.0 / 2026-06-24

### Breaking changes

* None

### Features / changes

* KUSTOM-93: Loosened up PHP version constraint to allow installing the package on Magento 2.4.9
  and PHP 8.5. Adjusted integration and unit tests in the package to run on currently supported
  Magento, PHP and PHPUnit versions.
* KUSTOM-94: Updated labels and strings from Klarna to Kustom in few places.

### Fixes

* None

## 11.0.17 / 2026-03-16

* KUSTOM-55 Fix missing error message when Klarna API exception is thrown
* KUSTOM-68 Fix guzzlehttp/guzzle compatibility issue for magento <2.4.5
* KUSTOM-20 Fix broken merchant portal link in order detail page

## 11.0.15 / 2026-02-18

* KUSTOM-51 Changing the direct linking on the admin panel to Kustom

## 11.0.14 / 2026-01-15

* KUSTOM-36 Adjusts the endpoints to direct to Kustom instead of Klarna API

## 11.0.13 / 2025-11-13

* KUSTOM-33 Change version info in admin panel
* KUSTOM-4 Fix C/O address sync from Kustom to Magento

## 11.0.12 / 2025-06-03

* PPP-2016 Add Klarna Badge as an asset instead of using CDN

## 11.0.11 / 2025-05-21

* PPP-2055 Compatibility with AC 2.4.8 and PHP 8.4

## 11.0.10 / 2025-04-03

* PPP-1504 Add new helper method for integration tests.
* PPP-1860 Simplified repository classes for database abstractions
* PPP-1938 Update Merchant Portal links in the admin payment section
* PPP-1978 Added integration tests for checking the payload for the capture and refund request
* PPP-1984 Added helper for new integration tests
* PPP-1994 Added new data sample for integration tests

## 11.0.9 / 2025-03-26

* PPP-1903 Fetch available Klarna features from Klarna API

## 11.0.8 / 2025-02-11

* PPP-1772 Added fixture for downloadable products
* PPP-1773 Added integration tests for configurable products
* PPP-1774 Added integration tests for grouped products
* PPP-1775 Added fixtures for Integration tests for dynamic bundled products
* PPP-1924 Show selected payment method for a KP order in the admin order view
* PPP-1962 Update order details in Express Checkout orders
* PPP-1973 Fixed type error at \Klarna\Base\Model\Quote\Address\Country::getCountryByAddress
* PPP-1974 Setting the virtual flag in the post purchase workflow

## 11.0.7 / 2025-01-22

* PPP-1767 Added fixture for API tests for new zealand
* PPP-1859 Simplified unit tests by using a helper which includes the mocking logic.
* PPP-1881 Klarna\Base\Helper\VersionInfo: Add missing unit tests
* PPP-1882 Klarna\Base\Model\Quote\SalesRule: Add missing unit tests
* PPP-1883 Added missing unit tests for Klarna\Base\Model\Responder\Result
* PPP-1886 Extended unit test test case class
* PPP-1949 Fixed case when the shipping address of the quote is null when fetching the country of it
* PPP-1954 Fix database connection pooling issue

## 11.0.6 / 2024-12-03

* PPP-1744 Added for KP request timeout value of 4 seconds for the server side API requests

## 11.0.5 / 2024-10-18

* PPP-1704 Create unit tests for Base/Helper/DataConverter.php
* PPP-1714 Simplify composer.json files
* PPP-1731 Moved class \Klarna\Base\Plugin\ConfigPlugin to the AdminSettings module
* PPP-1732 Remove unused class \Klarna\Base\Plugin\Sales\Block\Adminhtml\Order\View\InfoPlugin

## 11.0.4 / 2024-09-26

* PPP-1011 Using const keys when converting a Klarna address to a adobe address
* PPP-1521 Using the store instance to fetch the locale
* PPP-1669 Fix the locale mapping for norway

## 11.0.3 / 2024-08-21

* PPP-330 Added missing tests for Klarna\Base\Model\Quote\Address\Handler
* PPP-754 Added Sign-in with Klarna
* PPP-910 Moved logic of getting back the used country from the KP to the Base module
* PPP-1014 Deprecated Klarna\Base\Helper\KlarnaConfig
* PPP-1606 Refactor the Logger/Model/Logger class
* PPP-1616 Added first API integration test
* PPP-1632 Added timestamps to the database table.

## 11.0.2 / 2024-08-12

* PPP-754 Added Sign-in with Klarna

## 11.0.1 / 2024-07-26

* PPP-1553 Make the extension compatible with Adobe Commerce app assurance program requirements
* PPP-1585 Fix Content Security Policy console errors in the checkout payment

## 11.0.0 / 2024-06-20

* PPP-1437 Updated the admin UX and changed internally the API credentials handling

## 10.0.20 / 2024-05-30

* PPP-923 Add Klarna role to the admin panel
* PPP-1475 Make KP compatible with third-party plugins that try to call getRequest method on the frontend controllers.

## 10.0.19 / 2024-04-24

* PPP-1391 Added support for Adobe Commerce 2.4.7 and PHP 8.3

## 10.0.18 / 2024-04-11

* PPP-1327 Added support for writing controller integration tests for all HTTP types

## 10.0.17 / 2024-03-30

* PPP-1013 Using instead of \Klarna\Base\Helper\ConfigHelper logic from other classes to get back Klarna specific configuration values.
* PPP-1312 Showing the plugin version in the admin

## 10.0.16 / 2024-03-15

* PPP-1305 Updated the coding style to fix the marketplace warnings.

## 10.0.15 / 2024-03-04

* PPP-851 Changed the type of an object value in \Klarna\Base\Model\Api\OrderLineProcessor::fillDataHolderPostPurchase to avoid a type error
* PPP-916 Retrieve and add more debugging related data to the admin support request form.
* PPP-1016 Moved the class \Klarna\Base\Model\MerchantPortal to the new namespace \Klarna\Base\Model\System\MerchantPortal

## 10.0.13 / 2024-02-01

* PPP-30 Added fixtures for fixed bundled products for integration tests
* PPP-1086 Added method to return an empty order

## 10.0.12 / 2024-01-19

* PPP-748 Moved shipping method update logic from KCO to the Base module

## 10.0.11 / 2024-01-19

* PPP-913 Using new KP request builder class
* PPP-917 Added integration tests for the repository
* PPP-1042 Extended the ControllerTestCase logic for integration tests

## 10.0.10 / 2024-01-05

* PPP-914 Extended test logic to also check cases when a customer is logged in
* PPP-1015 Moved the logic of Klarna\Base\Model\Config to new namespaces

## 10.0.9 / 2023-11-15

* PPP-929 Increased the version because of a new version of the Logger module

## 10.0.8 / 2023-09-27

* PPP-704 Moved the integration test helper to the KP module

## 10.0.7 / 2023-08-25

* PPP-59 Add m2-klarna package version to User-Agent

## 10.0.6 / 2023-08-01

* MAGE-4283 Added orderline item calculation integration tests

## 10.0.5 / 2023-07-14

* MAGE-4141 Map Magento supported locales (BPC 47) with Klarna supported ones (RFC1766)
* MAGE-4228 Removed the composer caret version range for Klarna dependencies
* MAGE-4251 Fixing usage of a Sales rule by calling the init() method

## 10.0.4 / 2023-05-22

* MAGE-3857 Moved the Klarna\Kco\Controller\Api\CsrfAbstract class to the module

## 10.0.3 / 2023-04-03

* MAGE-4175 Added one more check for \Klarna\Base\Model\Filter\Sanitization::sanitize()

## 10.0.2 / 2023-03-28

* MAGE-4162 Added support for PHP 8.2

## 10.0.1 / 2023-03-28

* MAGE-4147 Sanitizing all admin inputs for all Klarna Products

## 10.0.0 / 2023-03-09

* MAGE-76 Refactored Model Base/Model/Fpt and moved the logic to new locations and adjusted the calls.
* MAGE-3980 Added some unit tests to the Address\Fields class and a small change on the class itself
* MAGE-4037 Prevent to capture a fully captured order
* MAGE-4063 Removd deprecated classes
* MAGE-4073 Moved the input of the orderline items to the class Klarna\Base\Model\Api\Parameter
* MAGE-4075 Removed not needed events
* MAGE-4077 Added "declare(strict_types=1);" to all production class files
* MAGE-4079 Refactored most of the orderline item classes
* MAGE-4084 Indicating the payment code when fetching payment specific configurations from the Base module
* MAGE-4085 Removed the usage of \Klarna\Base\Model\Api\BuilderFactory
* MAGE-4086 Simplified logic when checkingif a sales rule with the rule "apply to shipping" is used
* MAGE-4087 Moved \Klarna\Base\Model\Api\Parameter to the orderline module and adjusted the calls
* MAGE-4092 Move the DataHolder class from the Base module to the Orderlines module

## 9.1.10 / 2022-09-27

* MAGE-4000 Simplified the generation of the orderline item class instances
* MAGE-4001 Removed redundant setting of data in the class Klarna\Base\Model\Api\OrderLineProcessor
* MAGE-4002 Removed the whole logic at Klarna\Base\Model\Api\Validator since its not needed anymore.
* MAGE-4015 Not showing the company logo for B2B orders

## 9.1.9 / 2022-09-14

* MAGE-3987 Refactored the update of the selected shipping method for KCO what improves the performance in this respective workflow.
* MAGE-3988 Added method to add custom data to \Klarna\Base\Model\Api\Parameter

## 9.1.8 / 2022-09-01

* MAGE-640 Added validations for the admin API credentials
* MAGE-3434 Improved the execution checks in the plugins
* MAGE-3621 Improved the software design and performance of the quote address update
* MAGE-3712 Using constancts instead of magic numbers

## 9.1.7 / 2022-08-18

* MAGE-3951 Added translations for the admin payment start text.

## 9.1.6 / 2022-08-12

* MAGE-1678 Add docs link, fix support link
* MAGE-3575 Add log link to order
* MAGE-3876 Reordered translations and set of missing translations
* MAGE-3894 Removed dead methods from Klarna\Base\Config\ApiVersion and Klarna\Base\Helper\KlarnaConfig
* MAGE-3910 Updated the copyright text
* MAGE-3920 Add orderline processor integration test

## 9.1.5 / 2022-07-11

* MAGE-3888 Removed object creations via "new ..."
* MAGE-3918 Removed the filtering of shipping methods since they are all unique
* MAGE-3921 Returning the logo url via class method
* MAGE-3892 Removed KCO keys in the xml configuration files
* MAGE-3893 Removed kco_order_id entry in the db_schema_whitelist.json file

## 9.1.4 / 2022-06-23

* MAGE-3847 Replaced the asset URLs
* MAGE-3866 Added new field to the klarna_core_order table: used_mid

## 9.1.3 / 2022-06-13

* MAGE-3785 Fix PHP requirements so that it matches the PHP requirement from Magento 2.4.4
* MAGE-3332 Removed the dependency to ramsey/uuid
* MAGE-3841 Centralized the onboarding link url text in the Base module

## 9.1.2 / 2022-05-31

* MAGE-3851 Fix partial capture
* MAGE-3782 Add *.klarnacdn.net to CSP whitelist for images and scripts

## 9.1.1 / 2022-05-09

* MAGE-3687 Replace link to Merchant Onboarding

## 9.1.0 / 2022-03-01

* Move from klarna/m2-marketplace

## 8.3.1 / 2021-10-25

* MAGE-2431 Add CSP whitelist
* MAGE-3304 Removed not needed KSA logic

## 8.3.0 / 2021-09-08

* MAGE-2956 KSA: Fixed discount applied on shipping usage for the new KSA logic
* MAGE-3087 KSA: Use Klarna's version of order instead of internal one

## 8.2.2 / 2021-08-17

* MAGE-3331 Update ramsey version

## 8.2.1 / 2021-04-08

* MAGE-2924 Fix not logged failed requests for Logs+

## 8.2.0 / 2021-03-09

* MAGE-2342 Support for non-US merchants using shop setting "excluding tax" for catalog prices and shipping fees
* MAGE-2727 Add support for Logs++
* MAGE-2916 Fix different shipping reference and name between the order creation and ordermanagement requests

## 8.1.1 / 2020-11-23

* MAGE-2404 Fix display of shipping units in admin

## 8.1.0 / 2020-08-12

* MAGE-2055 Add support for PHP 7.4
* MAGE-2059 Fix type error when no product for a bundled product could be found
* MAGE-2110 Add configuration fields for KSS
* MAGE-2141 Fix wrong styles.less file usage
* MAGE-2153 Fix issue with empty shipping address on KCO prefill

## 8.0.0 / 2020-04-23

* MAGE-1447 Defined sensitive and environment specific fields
* MAGE-1516 Remove a not needed quote collectTotals() call when using Fixed Product Taxes
* MAGE-1607 Wrong link to merchant portal from Magento admin
* MAGE-1655 Product discount is listed on each order line instead of being a separate order line
* MAGE-1821 Remove class Klarna\Base\Model\Calculator\Discounts
* MAGE-1828 Fix Division by zero issue when using giftwrap and without taxes
* MAGE-1837 Fix merchant portal link was displayed in the magento pdf invoice
* MAGE-1871 Fix not working invoice and credit memo creation when using a giftwrap
* MAGE-1966 Fix wrong selected shipping method when changing country
* MAGE-1993 Fix exception logging issue when using the Klarna production environment

## 7.1.1 / 2020-04-17

* MAGE-1661 Fix wrong available shipping methods after updating the shipping address
* MAGE-1774 Fix issue with minicart qty counter not getting reset
* MAGE-1851 Fix issue with missing shipping method when using KSS
* MAGE-1861 Fix issue with admin "Update Status" button not working
* MAGE-1909 Fix missing shipping address issue
* MAGE-1994 Fix exception logging issue when using the Klarna production environment
* MAGE-1995 Update system.xml to work with 2.3.5 changes

## 7.1.0 / 2020-03-09

* MAGE-1655 Changed product discounts to be listed at the product level (instead of as their own order line item)
* MAGE-1725 Fix capturing and refund exception when using giftwrap
* MAGE-1751 Fix capturing and refund empty orderline item quantity when using giftwrap
* MAGE-1752 Fix division by zero when using giftwrap without tax
* MAGE-1807 Fix giftwrapping not working for guests
* MAGE-1824 Fix removed merchant portal link from invoice PDF

## 7.0.1 / 2020-02-07

* MAGE-1357 Solved issue where KCO wouldn't load with certain customizable product options
* MAGE-1447 Defined sensitive and environment specific fields
* MAGE-1607 Update Merchant Portal URLs

## 7.0.0 / 2019-11-18

* Rename module to "module-base" and update namespaces
* MAGE-867 Only clean up shipping address when shipping_address index is created
* MAGE-1220 Fix issue with shipping discounts
* MAGE-1324 Fix issue with cleaning up empty shipping addresses
* MAGE-1357 Fix issue with custom options and skus
* MAGE-1471 Cleanup Logger class
* MAGE-1481 Fix issue with company name being copied to organization_name when B2B is disabled
* MAGE-1520 Enable PHP 7.3 support
* MAGE-1531 Fix new Magento Coding Standards changes

## 6.1.1 / 2019-10-03

* MAGE-1193 Fix issue with whole cart coupon with mixed bundle/simple products

## 6.1.0 / 2019-06-19

* MAGE-272 Add support for Klarna Shipping Service
* MAGE-504 Fix issue with Packstation on KCOv2 DACH API
* MAGE-692 Completed translations for all phrases. Covering da_DK, de_AT, de_DE, fi_FI, nl_NL, nb_NO and sv_SE

## 6.0.1 / 2019-03-26

* MAGE-277 Hid all Klarna settings on the store view level
* MAGE-312 Add missing translations to en_US base
* MAGE-429 Add index to the table klarna_core_order for the columns klarna_order_id and is_acknowledged

## 6.0.0 / 2019-02-22

* MAGE-324 Fix wrong coupon tax calculation
* MAGE-327 Fix type error
* MAGE-331 Fix unknown array key
* MAGE-344 Fix wrong tax extraction (did not worked when virtual products has taxes)
* MAGE-406 Fix empty qty for invoicing virtual products
* MAGE-410 Fix method calls

## 6.0.0-alpha / 2019-02-05

* MAGE-13 Refactor Orderlines - extract the calculations
* MAGE-105 Refactor abstract class Model\Api\Builder
* MAGE-232 Improve validation notices in Magento admin
* MAGE-249 Fix errors with di:compile on Magento 2.1
* MAGE-251 Switch to Marketplace coding standards
* PPI-516 Refactor Order Lines - Items
* PPI-532 Refactor Helper class CartHelper
* PPI-545 Refactor abstract class AbstractLine
* PPI-561 Update composer requirements
* PPI-562 Refactor: Logging
* PPI-572 Remove reference of isTotalCollector

## 5.2.1 / 2018-12-06

* Allow Magento 2.2.0 - 2.2.1 to install again

## 5.2.0 / 2018-12-05

* Allow Magento 2.2.0 - 2.2.1 to install again
* MAGE-128 Added a try-catch block around checking customer default addresses
* MAGE-147 Fix error with virtual products

## 5.1.2 / 2018-11-07

* PI-509 Ensure company name is sent to Klarna API

## 5.1.1 / 2018-11-01

* PPI-580 Force billing and shipping address to have same email address

## 5.1.0 / 2018-10-17

* PI-471 Fix setting customer tax class id
* PI-473 Add shipping item to order lines even when costs = 0.
* PI-488 Add index to klarna_core_order table
* PI-507 Remove merchant portal link in confirmation email
* PPI-254 Using the type "discount" for the reward items.
* PPI-258 Add Link to Merchant Portal
* PPI-467 Added more fields to log cleanser
* PPI-500 Add support for PHP 7.2
* PPI-517 Refactor Order Lines - Discounts
* PPI-557 Remove FK constraint
* PPI-573 Using the correct method for getting the whole FPT taxes.
* PPI-581 Fix DB upgrade script for table prefixes
* Fix composer requirements for 2.1

## 5.0.1 / 2018-08-16

* PPI-419 Update blacklist for logging
* PPI-419 Add check for table already existing

## 5.0.0 / 2018-08-14

* Rename module and namespace due to Marketplace limitations
* PPI-317 Add support for Fixed Product Tax
* PPI-403 Using the onboarding model.
* PPI-419 Move functionality from DACH module
* PPI-449 Feedback from Magento for 2.2.6 release

## 4.3.2 / 2018-06-26

* BUNDLE-1462 Validation issues
* PI-198 Fix giftwrapping tax issues

## 4.3.1 / 2018-06-08

* PPI-259 Add Shipping and discount order lines to OM calls
* PI-254 Fix order with discount fails

## 4.2.3 / 2018-05-22

* Dummy release to bypass Marketplace submission block because of out of order release

## 4.2.1 / 2018-05-18

* PPI-413 Remove isConfigFlag method in favor of direct calling ConfigHelper
* PPI-410 Fix issue invoicing v2 orders

## 4.2.0 / 2018-05-14

* Restrict to Magento 2.2 or later
* PPI-410 Fix qty on v2 invoice items
* PPI-349 Add cancellation_terms URL (for DE/AT)
* PI-198 Fix issue with tax on gift wrap applied on order level
* PPI-394 Move method to KCO module

## 4.1.5 / 2018-04-27

* PPI-390 Move config stuff out of PayPal's exclusive section

## 4.1.4 / 2018-04-26

* PPI-390 Fix setting of response object in return value
* PPI-390 Add PayPal module to dependencies

## 4.1.3 / 2018-04-20

* Fix logging code

## 4.1.2 / 2018-04-10

* Fix typo in config name

## 4.1.1 / 2018-04-10

* Fix issue with trying to instantiate interface because of invalid di.xml reference

## 4.1.0 / 2018-04-09

* Combine all CHANGELOG entries related to CBE program
* Update code per CBE program
* Add Gift Wrap Support
* Fix admin notifications on Magento 2.1
* Change method to return KP builder by default
* Move API config into Payments section
* Change logging style of request/response to match KP module
* Implement methods instead of rely on magic methods
* Change all bool methods to be 'is' instead of 'get'
* Drop support for Magento 2.0

## 3.3.0 / 2018-04-09

* Add support for other logging methods

## 3.2.5 / 2018-03-05

* Fix getFailureUrl to return a string instead of a boolean

## 3.2.4 / 2018-02-09

* Fix PHPDOC return value
* Fix XSS issue

## 3.2.3 / 2018-01-31

* Update compoesr.lock file
* Change shipping line to no longer calculate total
* Update admin payment config display

## 3.2.2 / 2018-01-24

* Exclude composer.lock from packages

## 3.2.1 / 2018-01-24

* Fix for refactored ApiHelper in KCO
* Add B2B Support
* Move base admin config stuff to to core module
* Sort API versions before displaying as options
* Change display of payment method info in admin
* Remove abandoned package
* Add composer.lock file to repo
* Update composer.json for dev dependencies
* Remove extra phpunit.xml.dist file
* Add testing configs
* Add GrumPHP
* Remove errand use statement

## 3.1.1 / 2017-12-19

* Allow Magento 2.1.1
* Allow Magento 2.2.2

## 3.1.0 / 2017-11-13

* Add better error handling when invalid API version selected
* Fix mapping of modules for version info
* Fixes for logging after Guzzle 6 update
* Add support for disabling shipping in iframe in markets that support it

## 3.0.0 / 2017-10-30

* Update Guzzle to 6.0
* Update to new logos

## 2.7.1 / 2017-10-04

* Change constant to regular field

## 2.7.0 / 2017-10-04

* Move Enterprise classes into core module to support single Marketplace release

## 2.6.0 / 2017-10-04

* Change the way module versions are retrieved

## 2.5.5 / 2017-10-02

* Handle for neither KCO or KP being enabled
* Allow Magento 2.2.0 to be installed

## 2.5.4 / 2017-09-28

* Allow 2.0.16
* Fix PHPDOC and update imports

## 2.5.3 / 2017-09-25

* Allow 2.1.9

## 2.5.2 / 2017-09-19

* Remove reference to magento-base, because Marketplace!

## 2.5.1 / 2017-09-18

* Exclude tests as well as Tests from composer package

## 2.5.0 / 2017-09-15

* Add support for bundled products PPI-62

## 2.4.0 / 2017-09-11

* Refactor code to non-standard directory structure to make Magento Marketplace happy 😢

## 2.3.0 / 2017-08-30

* Fix conflict dependency to comply with Marketplace logic but still block 2.0.11 and 2.1.3
* Update code with fixes from MEQP2 to prepare for Marketplace release
* Add check for countries requiring region that shouldn't
* Refactor tax calculations for discount lines

## 2.2.5 / 2017-08-25

* Fix to handle for customer default shipping/billing address

## 2.2.4 / 2017-08-22

* Bump require-dev version of pdepend

## 2.2.3 / 2017-08-15

* Allow 2.1.8 to be installed

## 2.2.2 / 2017-08-10

* Change to ensure using street_address2 instead of street_address_2

## 2.2.1 / 2017-08-10

* Add additional block to prevent early upgrading of Magento

## 2.2.0 / 2017-08-04

* Add ability to pass context to logger

## 2.1.1 / 2017-08-03

* Add code to handle for when Klarna order is not set

## 2.1.0 / 2017-08-02

* Add failure_url lookup
* Add admin CSS file and load it in Stores->Configuration section
* Add warning message in admin panel for misconfigured settings per PPI-319

## 2.0.6 / 2017-07-11

* Fix missing import in CommonController trait

## 2.0.5 / 2017-07-10

* Change labels to make it more understandable cross-market what the values should be
* Fix sort order of config settings
* Add getPackage method to VersionInfo
* Change all trait properties/methods to public in CommonController

## 2.0.4 / 2017-06-27

* Update name from Klarna AB to Klarna Bank AB (publ)

## 2.0.3 / 2017-06-09

* Change to pass correct store to order line collector to ensure correct classes are used
* Change composer setup to block upgrades to Magento until supported by Klarna

## 2.0.2 / 2017-05-17

* Fix issue with checking country on a null object
* PPI-281 Add workaround for class rename/replace done in Magento 2.1.3

## 2.0.1 / 2017-05-15

* Log exception to request response
* PPI-269 Move UTF-8 conversion code to Kred module as only relevant to v2 API
* Change region to 2 letter code if country is US for PPI-267

## 2.0.0 / 2017-05-01

* Add support for multibyte characters
* Change code to replace non-UTF8 characters in sku and name with question marks for PPI-218
* Set store on product before pulling product URL
* Move methods from discount line to abstract
* Add plugin to set invoice_id on credit memo
* Cast discount title to string before sending to API
* Port over tax rate stuff from M1 for PPI-177
* Handle for apply tax before discount
* Handle for tax on discount when not using a separate tax line
* Handle for when orderline is processed by OM
* Fix DOB on prefill
* Disable editing order to resolve PPI-202
* Add passing of store to config check
* Display Klarna logo instead of plain text in admin
* Remove check on merchant_prefill and have this done in each builder instead
* Fix scope setting for stores
* Add status code and message to response array and throw exception when status is 401
* Fix for PPI-185 not sending colors for KP
* Fix tests directory in composer.json
* Update license header
* Refactor klarna.xml to use options inside api_version
* Refactor code to better handle for which builder is used by OM
* Move address split into Kred module
* Add product image URL to API call
* Add product URL to API call
* Move API credentials to core module
* Change logger to support enabling per store
* Add getApiConfig and getApiConfigFlag methods
* Fix shipping reference to match shipping method code
* Add is_acknowledged setter/getter to interface and implementation for order
* Split Magento Edition out of version string
* Add getOmBuilderType method
* Move version info into it's own class
* Update copyright years
* Refactor to abstract processing of klarna.xml
* Add handling of payments_order_lines in klarna.xml
* Move orderlines from KCO to Core module
* Add reading from kp's klarna.xml file
* Refactor to properly handle KP vs KCO payment methods
* Add preferences for Order and OrderRepository interfaces
* Fix create API call to not set the street_address field for DE markets
* Move CommonController trait to core as it is used by multiple modules
* Add preference for service class
* Relocate quote to kco module
* Fix missing methods from interface
* Update BuilderInterface for KP support
* Remove unused dependencies
* Move payment info block to core module
* Rename order table and add session_id column
* Fix PPI-149 merchant checkbox text not being sent in API call
* Add override of user-agent from Guzzle client
* Update interface and implementation classes
* Fix to create quote if one doesn't exist
* Add getPaymentConfigFlag method
* Refactor class to be more generic to add support for KP
* Change how loading of quote works
* Change how delete of quote works
* Add SaveHandler
* Add member fields for db caching
* Remove getList method as unused
* Add gitattributes file to exclude certain files from composer
* Add CHANGELOG.md
