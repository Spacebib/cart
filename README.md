# cart
A Shopping Cart Abstraction

## Usage

+ Setup initial state

```php
$event = Event::init($data)
```

+ Create Cart

```php
$cart = new Cart('xuding@spacebib.com',new DataStore())
```

+ Create Registration Form

```php
$registration = new Registration($event->getParticipants(), new DataStore());
```

## Cart API

+ Add ticket to cart

```php
$cart->addTicket($event->getCategoryById(1), 1);
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

+ Get participants' track IDs

```php
$registration->getParticipantsTrackIds();
```

+ Render a form

```php
$registration->renderParticipant();
```

+ Fill a form

```php
$registration->fillParticipant();
```