# Shopify Craft CMS 4 starter backend
Started a small (big) plugin to handle data sync between Craft CMS entries and Shopify.
Basically only use shopify for customers and analytics, all products that are created in Craft will be synced to Shopify and also the other way around.

The idea is to create a fully synchronized setup to use with any frontend framework and Craft CMS without ever going in Shopify.

# Currently working

- Product creation in Craft that sync to Shopify on save
- Products synchronization between Craft/Shopify and Shopify/Craft
- Current fields: Title, Description, Shopify id, Json data

# TODO

- Add more products data fields
- Add variants
- Handle stock synchronization
