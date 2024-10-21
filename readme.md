1. Clone or fork this repository
2. Make sure you have [Vercel CLI](https://vercel.com/docs/cli) installed
3. Make sure you have [Shopify CLI](https://shopify.dev/docs/api/shopify-cli#installation) installed (for testing)
4. Head to your Shopify store > Settings > Notifications - take note of the signature of the bottom of the page and add the key to your `.env` file against variable `SHOPIFY_SIGNATURE`
5. Make sure to upload your key file for Bing / Yandex to the root of your project (tip: upload the .txt file under Content > Files and then set up a redirect to those file link(s) under Online Store > Navigation > View URL Redirects)
4. Deploy to vercel using the `vercel` command in the repository
5. [Register the webhooks in your Shopify store](https://help.shopify.com/en/manual/fulfillment/setup/notifications/webhooks)
6. 