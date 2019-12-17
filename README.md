<p align="center"><a href="https://www.sellfino.com" target="_blank" rel="noopener noreferrer"><img width="200" src="https://www.sellfino.com/images/logo.png" alt="Sellfino logo"></a></p>

---

## Pre-Order Creator
Manage pre-order & coming-soon products. Pre-order are products that can be ordered and shipment is set in the future. Coming soons are products that cannot be ordered and are just information about upcoming stock.

## Demo & Screenshots
Check how this app works in the live store: [DEMO](https://sellfino.myshopify.com/products/green-dress-preorder-coming-soon)

<a href="https://sellfino.com/images/screens/preorder/preorder-1.jpg" target="_blank" rel="noopener noreferrer"><img width="30%" src="https://sellfino.com/images/screens/preorder/preorder-1.jpg"></a> <a href="https://sellfino.com/images/screens/preorder/preorder-2.jpg" target="_blank" rel="noopener noreferrer"><img width="30%" src="https://sellfino.com/images/screens/preorder/preorder-2.jpg"></a> <a href="https://sellfino.com/images/screens/preorder/preorder-3.jpg" target="_blank" rel="noopener noreferrer"><img width="30%" src="https://sellfino.com/images/screens/preorder/preorder-3.jpg"></a> <a href="https://sellfino.com/images/screens/preorder/preorder-4.jpg" target="_blank" rel="noopener noreferrer"><img width="30%" src="https://sellfino.com/images/screens/preorder/preorder-4.jpg"></a> <a href="https://sellfino.com/images/screens/preorder/preorder-5.jpg" target="_blank" rel="noopener noreferrer"><img width="30%" src="https://sellfino.com/images/screens/preorder/preorder-5.jpg"></a> <a href="https://sellfino.com/images/screens/preorder/preorder-6.jpg" target="_blank" rel="noopener noreferrer"><img width="30%" src="https://sellfino.com/images/screens/preorder/preorder-6.jpg"></a>

## Installation
- **1.** Add this app to Sellfino App Store.
- **2.** Go to the app and open *Products* tab. Click *Add Product*. Choose the product you want to create preorder settings for.
:exclamation: Make sure that pre-order product/variants have settings to order without tracking stock. Coming soon products/variants should have opposite setting.
- **3.** You can set settings for whole product or every variant of your product can have different type (some of them can be pre-orders, some of them coming soons). **Those settings are saved in product metafields and are accessible in liquid templates.**
- **4.** Modify your frontend code. Because of the differences in Shopify themes, there is no specific template that can be used for this app. However, in the demo you can notice, that by selecting pre-order product, hidden input is inserted in the code (when you select coming soon - we treat it as unavailable product, but we show the info already set in the app). It looks like this:

``` <input name="properties[Pre-order]" value="NOTE" hidden /> ```

where *NOTE* should be derived from your PreOrder settings. Adding `properties` to the ADD-TO-CART form will result in displaying info in the cart/checkout/order views.

- **5.** Optionally, you can also add info about pre-order product in the email notifications. To do that, go to the Shopify Settings > Notifications > Order confirmation and put following code inside the template (in the ordered products loop):

```
{% if line.properties | size > 0 %}
  {% for p in line.properties %}
    <span class="order-list__item-variant" style="color:#f19753">{{ p.first }}: {{ p.last }}</span>
  {% endfor %}
{% endif %}
```

## Sellfino Open Source Shopify App Store
This is the app for [Sellfino](https://github.com/sellfino/sellfino) platform.

#### Support and Contribution

Join our awesome community! Here is how you can connect with us:
- [Website](https://www.sellfino.com) - all info here + live chat
- [Discord](https://discordapp.com/invite/wrFnzZ3) - channels to discuss new ideas and ask for help
- [Messanger](https://m.me/104484064333760) - if you want to chat on Facebook
- [Email](mailto:contact@sellfino.com) - whenever we are out of touch, drop us a message


## Copyright
Copyright (c) 2019-present, Lucas Szarzynski
