Validating, a validation trait for Laravel
==========================================

[![Build Status](https://travis-ci.org/dwightwatson/validating.svg?branch=master)](https://travis-ci.org/dwightwatson/validating)
[![Total Downloads](https://poser.pugx.org/watson/validating/downloads.svg)](https://packagist.org/packages/watson/validating)
[![Latest Stable Version](https://poser.pugx.org/watson/validating/v/stable.svg)](https://packagist.org/packages/watson/validating)
[![Latest Unstable Version](https://poser.pugx.org/watson/validating/v/unstable.svg)](https://packagist.org/packages/watson/validating)
[![License](https://poser.pugx.org/watson/validating/license.svg)](https://packagist.org/packages/watson/validating)

Validating is a trait for Laravel Eloquent models which ensures that models meet their validation criteria before being saved. If they are not considered valid the model will not be saved and the validation errors will be made available.

Validating allows for multiple rulesets, injecting the model ID into `unique` validation rules and raising exceptions on failed validations. It's small and flexible to fit right into your workflow and help you save valid data only.

## Laravel 4.2+
Looking to use Validating on Laravel 4.2+? [Take a look at the 0.10.x branch for documentation and installation instructions](https://github.com/dwightwatson/validating/tree/0.10.x).

The Laravel 4.2 version is better suited to doing form validation; it supports custom validation messages, confirmation rules and multiple rulesets. Because Laravel 5.0 has `FormRequest` validation Validating is now designed to keep your core data valid and leave form validation to the framework.

## Laravel 5.0 - 5.2
Looking to use Validating on Laravel 5.0 to 5.2? [Take a look at the 2.x branch for documentation and installation instructions](https://github.com/dwightwatson/validating/tree/2.x).

The Laravel 5.0 - 5.2 version used a since-deprecated `ValidationException` contract from the Laravel framework. For Laravel 5.3 we now extend the core validation `ValidationException` which means the framework will automatically redirect back with errors when a validation error occurs, much like a `FormRequest` would.

## Laravel 5.3
Just read on - these instructions are for you!

# Installation
Simply go to your project directory where the `composer.json` file is located and type:

```sh
composer require watson/validating
```

[View installation instructions for Laravel 4.2+](https://github.com/dwightwatson/validating/tree/0.10.x).
[View installation instructions for Laravel 5.0 - 5.2](https://github.com/dwightwatson/validating/tree/2.x).

## Overview
First, add the trait to your model and add your validation rules and messages as needed.

```php
use Watson\Validating\ValidatingTrait;

class Post extends Eloquent
{
	use ValidatingTrait;

	protected $rules = [
		'title'   => 'required',
		'slug'    => 'required|unique:posts,slug',
		'content' => 'required'
	];
}
```

You can also add the trait to a `BaseModel` if you're using one and it will work on all models that extend from it, otherwise you can just extend `Watson\Validating\ValidatingModel` instead of `Eloquent`.

*Note: you will need to set the `$rules` property on any models that extend from a `BaseModel` that uses the trait, or otherwise set an empty array as the `$rules` for the `BaseModel`. If you do not, you will inevitably end up with `LogicException with message 'Relationship method must return an object of type Illuminate\Database\Eloquent\Relations\Relation'`.*

Now, you have access to some plesant functionality.

```php
// Check whether the model is valid or not.
$post->isValid(); // true

// Or check if it is invalid or not.
$post->isInvalid(); // false

// Once you've determined the validity of the model,
// you can get the errors.
$post->getErrors(); // errors MessageBag
```

Model validation also becomes really simple.

```php
if ( ! $post->save()) {
    // Oops.
    return redirect()->route('posts.create')
        ->withErrors($post->getErrors())
        ->withInput();
}

return redirect()->route('posts.show', $post->id)
    ->withSuccess("Your post was saved successfully.");
```

Otherwise, if you prefer to use exceptions when validating models you can use the `saveOrFail()` method. Now, an exception will be raised when you attempt to save an invalid model.

```php
$post->saveOrFail();
```

You *don't need to catch the exception*, if you don't want to. Laravel knows how to handle a `ValidationException` and will automatically redirect back with form input and errors. If you want to handle it yourself though you may.

```php
try {
    $post->saveOrFail();

} catch (Watson\Validating\ValidationException $e) {
    $errors = $e->getErrors();

    return redirect()->route('posts.create')
        ->withErrors($errors)
        ->withInput();
}
```

Note that you can just pass the exception to the `withErrors()` method like `withErrors($e)` and Laravel will know how to handle it.

### Bypass validation
If you're using the model and you wish to perform a save that bypasses validation you can. This will return the same result as if you called `save()` on a model without the trait.

```php
$post->forceSave();
```

### Validation exceptions by default
If you would prefer to have exceptions thrown by default when using the `save()` method instead of having to use `saveOrFail()` you can just set the following property on your model or `BaseModel`.

```php
/**
 * Whether the model should throw a ValidationException if it
 * fails validation. If not set, it will default to false.
 *
 * @var boolean
 */
protected $throwValidationExceptions = true;
```

If you'd like to perform a one-off save using exceptions or return values, you can use the `saveOrFail()` and `saveOrReturn` methods.

### Validation messages
To show custom validation error messages, just add the `$validationMessages` property to your model.

```php
/**
 * Validation messages to be passed to the validator.
 *
 * @var array
 */
protected $validationMessages = [
    'slug.unique' => "Another post is using that slug already."
];
```

### Unique rules
You may have noticed we're using the `unique` rule on the slug, which wouldn't work if we were updating a persisted model. Luckily, Validation will take care of this for you and append the model's primary key to the rule so that the rule will work as expected; ignoring the current model.

You can adjust this functionality by setting the `$injectUniqueIdentifier` property on your model.

```php
/**
 * Whether the model should inject it's identifier to the unique
 * validation rules before attempting validation. If this property
 * is not set in the model it will default to true.
 *
 * @var boolean
 */
protected $injectUniqueIdentifier = true;
```

Out of the box, we support the Laravel provided `unique` rule. We also support the popular [felixkiss/uniquewith-validator](https://github.com/felixkiss/uniquewith-validator) rule, but you'll need to opt-in. Just add `use \Watson\Validating\Injectors\UniqueWithInjector` after you've imported the validating trait.

It's easy to support additional injection rules too, if you like. Say you wanted to support an additional rule you've got called `unique_ids` which simply takes the model's primary key (for whatever reason). You just need to add a camel-cased rule which accepts any existing parameters and the field name, and returns the replacement rule.

```php
/**
 * Prepare a unique_ids rule, adding a model identifier if required.
 *
 * @param  array  $parameters
 * @param  string $field
 * @return string
 */
protected function prepareUniqueIdsRule($parameters, $field)
{
    // Only perform a replacement if the model has been persisted.
    if ($this->exists) {
        return 'unique_ids:' . $this->getKey();
    }

    return 'unique_ids';
}
```

In this case if the model has been saved and has a primary key of `10`, the rule `unique_ids` will be replaced with `unique_ids:10`.

### Events
Various events are fired by the trait during the validation process which you can hook into to impact the validation process.

To hook in, you first need to add the `$observeables` property onto your model (or base model). This simply lets Eloquent know that your model can respond to these events.

```php
/**
 * User exposed observable events
 *
 * @var array
 */
protected $observables = ['validating', 'validated'];
```

When validation is about to occur, the `eloquent.validating: ModelName` event will be fired, where the `$event` parameter will be `saving` or `restoring`. For example, if you were updating a namespaced model `App\User` the event would be `eloquent.validating: App\User`. If you listen for any of these events and return a value you can prevent validation from occurring completely.

```php
Event::listen('eloquent.validating:*', function($model, $event) {
    // Pseudo-Russian roulette validation.
    if (rand(1, 6) === 1) {
        return false;
    }
});
```

After validation occurs, there are also a range of `validated` events you can hook into, for the `passed`, `failed` and `skipped` events. For the above example failing validation, you could get the event `eloquent.validated: App\User`.

## Testing
There is currently a bug in Laravel (see issue [#1181](https://github.com/laravel/framework/issues/1181)) that prevents model events from firing more than once in a test suite. This means that the first test that uses model tests will pass but any subseqeuent tests will fail. There are a couple of temporary solutions listed in that thread which you can use to make your tests pass in the meantime.

**Since Laravel has switched to Liferaft for the purpose of tracking bugs and pull requests, the issue mentioned above may not be available. [This Gist has an example `TestCase.php`](https://gist.github.com/dwightwatson/a645e7f5f6c8c52445d8) which shows you how to reset the events of all your models between tests so that they work as expected.**

## Controller usage
There are a number of ways you can go about using the validating validating model in your controllers, however here is one example that makes use of the new FormRequest in Laravel 5 (if you'd like to see another controller example without the FormRequest, check the [4.2+ version of this package](https://github.com/dwightwatson/validating/tree/0.10).

This example keeps your code clean by allowing the FormRequest to handle your form validation and the model to handle it's own validation. By enabling validation exceptions you can reduce repetitive controller code (try/catch blocks) and handle model validation exceptions globally (your form requests should keep your models valid, so if your model becomes invalid it's an *exceptional* event).

```php
<?php namespace App\Http\Controllers;

use App\Http\Requests\PostFormRequest;
use Illuminate\Routing\Controller;

class PostsController extends Controller
{
    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    // ...

    public function store(PostFormRequest $request)
    {
        // Post will throw an exception if it is not valid.
        $post = $this->post->create($request->input());

        // Post was saved successfully.
        return redirect()->route('posts.show', $post);
    }
}
```

You can then catch a model validation exception in your `app/Exceptions/Handler.php` and deal with it as you need.

```php
public function render($request, Exception $e)
{
    if ($e instanceof \Watson\Validating\ValidationException) {
        return back()->withErrors($e)->withInput();
    }

    parent::render($request, $e);
}
```
