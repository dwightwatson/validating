Validating, a validation trait for Laravel
==========================================

[![Build Status](https://travis-ci.org/dwightwatson/validating.png?branch=master)](https://travis-ci.org/dwightwatson/validating)
[![Dependency Status](https://www.versioneye.com/php/watson:validating/0.10.0/badge.svg)](https://www.versioneye.com/php/watson:validating/0.10.0)
[![Total Downloads](https://poser.pugx.org/watson/validating/downloads.svg)](https://packagist.org/packages/watson/validating)
[![Latest Stable Version](https://poser.pugx.org/watson/validating/v/stable.svg)](https://packagist.org/packages/watson/validating)
[![Latest Unstable Version](https://poser.pugx.org/watson/validating/v/unstable.svg)](https://packagist.org/packages/watson/validating)
[![License](https://poser.pugx.org/watson/validating/license.svg)](https://packagist.org/packages/watson/validating)

Validating is a trait for Laravel 4.2+ Eloquent models which ensures that models meet their validation criteria before being saved. If they are not considered valid the model will not be saved and the validation errors will be made available.

Validating allows for multiple rulesets, injecting the model ID into `unique` validation rules and raising exceptions on failed validations. It's small and flexible to fit right into your workflow and help you save valid data only.

# Installation
Simply add the package to your `composer.json` file and run `composer update`.

```
"watson/validating": "0.10.*"
```

Or go to your project directory where the `composer.json` file is located and type:

```sh
composer require "watson/validating:0.10.*"
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

You can also add the trait to a `BaseModel` if you're using one and it will work on all models that extend from it, otherwise you can just extend `Watson\Validating\ValidatingModel` instead of `Eloquent`.

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
if ( ! $post->save())
{
    // Oops.
    return Redirect::route('posts.create')
        ->withErrors($post->getErrors())
        ->withInput();
}

return Redirect::route('posts.show', $post->id)
    ->withSuccess("Your post was saved successfully.");
```

Otherwise, if you prefer to use exceptions when validating models you can use the `saveOrFail()` method. Now, an exception will be raised when you attempt to save an invalid model.

```php
try
{
    $post->saveOrFail();

}
catch (Watson\Validating\ValidationException $e)
{
    $errors = $e->getErrors();
    return Redirect::route('posts.create')
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

The events that you are able to hook into with rules include `creating`, `updating`, `saving`, `deleting` and `restoring`. You simply hook into a certain event by listing rules under that key.

If you want to define some default rules that will be used for all events, use the `saving` ruleset. All other rulesets will extend from `saving`.

```php
protected $rulesets = [
    'creating' => [
        'description' => null
    ],

    'updating' => [
        'description' => 'required|min:50'
    ],

    'deleting' => [
        'user_id'     => 'required|exists:users,id'
    ],

    'saving' => [
        'title'       => 'required',
        'description' => 'required'
    ]
];
```

In the above example you can see how the `saving` ruleset is the default and the others extend it. When `creating` this model the description will not be required, when `updating` it the description must exist and be at least 50 characters and when it is being deleted it must have a valid `user_id` set. For any event, the title will be required.

You can check to see if the model is valid with a given ruleset too.

```php
// Is valid with 'updating' rules merged with 'saving'.
$post->isValid('updating');

// Don't merge with the 'saving' rules
$post->isValid('updating', false);
```

Note that if you do not pass a ruleset to any method that takes one it will default to the saving ruleset or global ruleset.

You can also define your own custom rulesets. These won't be used by the trait when hooking into model events, but you can use them to validate for yourself.

```php
protected $rulesets = [
    'my_custom_rules' => [
        'title' => 'required'
    ]
];

// Note, your custom rules are merged with 'saving' too.
$post->isValid('my_custom_rules');

// Test if your custom rules are valid standalone.
$post->isValid('my_custom_rules', false);
```

#### Merging rulesets
There's a small helper method for merging rulesets. Pass the names of rulesets where the later ones will override the earlier ones.

```php
$mergedRules = $post->mergeRulesets('saving', 'creating');
```

### Confirmation rules
If you are using confirmation rules, any `*_confirmation` input will be passed to the validator as well. You won't need to pass it to your model, the trait will simply look at the request input and pass through the required attributes.

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

To hook in, you first need to add the `$observeables` property onto your model (or base model). This simply lets Eloquent know that your model can response to these events.

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
Event::listen('eloquent.validating.*', function($model, $event)
{
    // Psuedo-Russian roulette validation.
    if (rand(1, 6) === 1)
    {
        return false;
    }
}
});
```

After validation occurs, there are also a range of `validated` events you can hook into, for the `passed`, `failed` and `skipped` events. For the above example failing validation, you could get the event `eloquent.validated: App\User`.

## Testing
There is currently a bug in Laravel (see issue [#1181](https://github.com/laravel/framework/issues/1181)) that prevents model events from firing more than once in a test suite. This means that the first test that uses model tests will pass but any subseqeuent tests will fail. There are a couple of temporary solutions listed in that thread which you can use to make your tests pass in the meantime.

**Since Laravel has switched to Liferaft for the purpose of tracking bugs and pull requests, the issue mentioned above may not be available. [This Gist has an example `TestCase.php`](https://gist.github.com/dwightwatson/a645e7f5f6c8c52445d80) which shows you how to reset the events of all your models between tests so that they work as expected.

## Controller usage
There are a few ways to go about using the validating model in your controllers, but here's the simple way I like to do it. Really clean, clear as to what is going on and easy to test. Of course you can mix it up as you need, it's just one approach.

```php
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

        $post = $this->post->fill($input);

        if ( ! $post->save())
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
