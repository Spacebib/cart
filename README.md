# Cart
A Shopping Cart Abstraction

## Usage

+ Setup initial state

```php
$event = Event::init($data)
```

+ Create Cart

```php
$cart = new Cart('xuding@spacebib.com', $event)
```

+ Create Registration Form

```php
$registration = new Registration($cart->getParticipants());
```

## Cart API

+ Add ticket to cart

```php
$cart->addTicket($event->getCategoryById(1), 1);
```

+ Get participants

```php
$cart->getParticipants();
```

+ Get order details

```php
$tickets = $cart->tickets()
```

```php
$subTotal = $cart->subTotal()
```

```php
$total = $cart->total()
```

+ Add product to cart 

```php
$cart->addProduct($product)
```

+ Remove product from cart

```php
$cart->removeProduct($productId, $productVariantId)
```

+ Get product details

```php
$cart->getProducts()
$cart->countProducts()
$cart->productsSubtotal()
```

+ Coupon  

The cart can only use one coupon, but a coupon can be used for multiple tickets 
```php
$cart->getCoupon()
$cart->setCoupon($coupon)
$cart->applyCoupon()
$cart->canceloCoupon()
$cart->getDiscount()
$cart->usedCouponQuantity()
```

+ Get currency

```php
$cart->currency()   
```
## Registration API

+ Render a form

```php
$registration->renderParticipant($trackId);
```

+ Fill a form

```php
$registration->fillParticipant($trackId, $data);
```

+ Get errors of a participant form

```php
$registration->getErrors($trackId);
```

+ Redirect to next page

```php
$registration->redirectTo();
```