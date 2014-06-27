Validating, a validation trait for Laravel
==========================================

[![Build Status](https://travis-ci.org/dwightwatson/validating.png?branch=master)](https://travis-ci.org/dwightwatson/validating)

Validating is a trait for Laravel 4.2+ Eloquent models which ensures that models meet their validation criteria before being saved. If they are not considered valid the model will not be saved and the validation errors will be made available.

Validating allows for multiple rulesets, injecting the model ID into `unique` validation rules and raising exceptions on failed validations. It's small and flexible to fit right into your workflow and help you save valid data only.

# Installation

Simply add the package to your `composer.json` file and run `composer update`.

```
"watson/validating": "0.8.*"
```

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

	protected $validationMessages = [
		'slug.unique' => "Another post is using that slug already."
	];
}
```

If you'd prefer a slightly easier route, just have your models extend from `Watson\Validating\ValidatingModel` instead of Eloquent.

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

Now, an exception will be raised when you attempt to save an invalid model.

```php
try
{
    $post->save();

}
catch (Watson\Validating\ValidationException $e)
{
    $errors = $e->getErrors();
    return Redirect::route('posts.create')
        ->withErrors($errors)
        ->withInput();
}

return Redirect::route('posts.show', $post->id)
    ->withSuccess("Your post was saved successfully.");
```

Note that you can just pass the exception to the `withErrors()` method like `withErrors($e)` and Laravel will know how to handle it.

### Bypass validation

If you're using the model and you wish to perform a save that bypasses validation you can. This will return the same result as if you called `save()` on a model without the trait.

```php
$post->forceSave();
```

### Return value over exception

If you would prefer to have the `save()` method return a boolean indicating the success of the save instead of catching an exception you can. Just set the following property on your model.

```php
/**
 * Whether the model should throw a ValidationException if it
 * fails validation. If not set, it will default to true.
 *
 * @var boolean
 */
protected $throwValidationExceptions = false;
```

Now, you can perform model validation like so.

```php
if ( ! $post->save())
{
    // Oops.
    return Redirect::route('posts.create')
        ->withErrors($post->getErrors())
        ->withInput();
}   
```

If you'd like to perform a one-off save using exceptions or return values, you can use the `saveWithException()` and `saveWithoutException()` methods.

### Multiple rulesets

In some instances you may wish to use different rulesets depending on the action that is occurring. For example, you might require different rules if a model is being created to when a model is being updated. Utilising different rules is easy.

```php
protected $rulesets = [
    'creating' => [
        'title' => 'required'
    ],

    'updating' => [
        'title'       => 'required',
        'description' => 'required'
    ]
];
```

The events that you are able to hook into with rules include `creating`, `updating`, `saving`, and `deleting`. You simply hook into a certain event by listing rules under that key.

If you want to use a default ruleset which will be used for creating and updating, you can define a `saving` ruleset, as the `saving` event is called for both.

```php
protected $rulesets = [
    'deleting' => [
        'title'       => 'required',
        'description' => 'required',
        'user_id' => 'required|exists:users,id'
    ],

    'saving' => [
        'title'       => 'required',
        'description' => 'required'
    ]
];
```

You can check to see if the model is valid with a given ruleset too.

```php
$post->isValid('updating');
```

Note that if you do not pass a ruleset to any method that takes one it will default to the saving ruleset or global ruleset.

You can also define your own custom rulesets. These won't be used by the trait when hooking into model events, but you can use them to validate for yourself.

```php
protected $rulesets = [
    'my_custom_rules' => [
        'title' => 'required'
    ]
];

// 

$post->isValid('my_custom_rules');
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

### Accessors and mutators

You also have access to some really existing getters and setters, which allow you to get and set your validation rules and messages.

```php
// YOLO no rules
$post->setRules([]);

// Or set a specific ruleset
$post->setRuleset([], 'creating');

// Be a little nicer
$post->setMessages(['title.required' => "Please, please set a title."])
```

These are handy if you need to adjust the rules or messages in a specific scenario differently.

### Events

Various events are fired by the trait during the validation process which you can hook into to impact the validation process.

When validation is about to occur, the `validating.$event` event will be fired, where `$event` will be `saving`, `creating`, `updating`, `deleting` or `restoring`. If you listen for any of these events and return a value you can prevent validation from occurring completely.

```php
Event::listen('validating.*', function($model)
{
    // Psuedo-Russian roulette validation.
    if (rand(1, 6) === 1)
    {
        return false;
    }
}
});
```

After validation occurs, either `validating.passed` or `validating.failed` will be fired depending on the state of the validation.

## Controller usage

There are a few ways to go about using the validating model in your controllers, but here's the simple way I like to do it. Really clean, clear as to what is going on and easy to test. Of course you can mix it up as you need, it's just one approach.

```php

use Watson\Validating\ValidationException;

class PostsController extends BaseController
{
    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    // ...

    public function store()
    {
        // We can use all input if we have the $fillable property
        // set on our model.
        $input = Input::all();

        try
        {
            $post = $this->post->create($input);
        }
        catch (ValidationException $e)
        {
            // The post did not save due to validation errors.
            return Redirect::route('posts.create')
                ->withErrors($e)
                ->withInput();
        }

        // Post was saved successfully.
        return Redirect::route('posts.show', $post->id);
    }
}
```

It's important to note that `$post->save()` should only return false if validation fails (unless you have other observers watching your model events). If there is an issue with saving in the database your app would raise an exception instead.

You might also like to reduce the number of lines in your code by doing the above test all in one line...

```php
if ( ! $this->post->create(Input::all()))
{
    //
}
```
