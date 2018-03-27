# Cart
A Shopping Cart Abstraction

## Usage

+ Setup initial state

```php
$event = Event::init($data)
```

+ Create Cart

```php
$cart = new Cart('xuding@spacebib.com')
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

## Registration API

+ Render a form

```php
$registration->renderParticipantForm($trackId);
```

+ Fill a form

```php
$registration->fillParticipantForm($trackId, $data);
```

+ Get errors of a participant form

```php
$registration->getErrors($trackId);
```

+ Redirect to next page

```php
$registration->redirectTo();
```