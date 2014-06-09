Validating, a validation trait for Laravel
==========================================

[![Build Status](https://travis-ci.org/dwightwatson/validating.png?branch=master)](https://travis-ci.org/dwightwatson/validating)

Validating is a trait for Laravel 4.2+ Eloquent models which ensures that models meet their validation criteria before being saved. If they are not considered valid the model will not be saved and the validation errors will be made available.

Validating allows for multiple rulesets, injecting the model ID into `unique` validation rules and raising exceptions on failed validations. It's small and flexible to fit right into your workflow and help you save valid data only.

# Installation

Simply add the package to your `composer.json` file and run `composer update`.

```
"watson/validating": "0.7.*"
```

## Overview

First, add the trait to your model and add your validation rules and messages as needed.

```
use Watson\Validating\ValidatingTrait;

class Post extends Eloquent
{
	use ValidatingTrait;

	protected $rules = [
		'title'   => 'required',
		'slug'    => 'required|unique:posts,slug'
		'content' => 'required'
	];

	protected $messages = [
		'slug.unique' => "Another post is using that slug already."
	];

    /**
     * Whether the model should inject it's identifier to the unique
     * validation rules before attempting validation. If not set, it
     * will default to true.
     *
     * @var boolean
     */
    protected $injectIdentifier = true;
}
```

Now, you have access to some plesant functionality.

    // Check whether the model is valid or not.
    $post->isValid(); // true

    // Or check if it is invalid or not.
    $post->isInvalid(); // false

    // Once you've determined the validity of the model, 
    // you can get the errors.
    $post->getErrors(); // errors MessageBag

Also, the model will be prevented from saving if it doesn't pass validation!

    if ( ! $post->save())
    {
    	// Oops.
    	return Redirect::route('posts.create')
    		->withErrors($post->getErrors())
    		->withInput();
    }

    return Redirect::route('posts.show', $post->id)
    	->withSuccess("Your post was saved successfully.");

### Bypass validation

If you're using the model and you wish to perform a save that bypasses validation you can. This will return the same result as if you called `save()` on a model without the trait.

    $post->forceSave();

### Throwing exceptions

If you'd prefer to have validation exceptions thrown when validation fails instead of simply returning a boolean, simply add this to your model. You'll then want to catch a `Watson\Validating\ValidationException`.

```
/**
 * Whether the model should throw a ValidationException if it
 * fails validation. If not set, it will default to false.
 *
 * @var boolean
 */
protected $throwValidationExceptions = true;
```

The `ValidationException` gives you access to the validation errors too.

```
try
{
    $post->save();
}
catch (Watson\Validating\ValidationException $e)
{
    $errors = $e->getErrors();

    return Redirect::route('posts.create')
        ->withErrors($errors);
}
```

You can also just pass the exception to the `withErrors()` method, as Laravel will know how to handle it.

If you'd like to perform a one-off save using exceptions or return values, you can use the `saveWithException()` and `saveWithoutException()` methods.

### Multiple rulesets

In some instances you may wish to use different rulesets depending on the action that is occurring. For example, you might require different rules if a model is being created to when a model is being updated. Utilising different rules is easy.

    protected $rules = [
        'creating' => [
            'title' => 'required'
        ],

        'updating' => [
            'title'       => 'required',
            'description' => 'required'
        ]
    ];

The events that you are able to hook into with rules include `creating`, `updating`, `saving`, and `deleting`. You simply a certain event by listing rules under that key.

If you want to use a default ruleset which will be used for creating and updating, you can define a `saving` ruleset, as the `saving` event is called for both.

    protected $rules = [
        'deleting' => [
            'title'       => 'required',
            'description' => 'required'
            'user_id' => 'required|exists:users,id'
        ],

        'saving' => [
            'title'       => 'required',
            'description' => 'required'
        ]
    ];

You can check to see if the model is valid with a given rulset too.

    $post->isValid('updating');

Note that if you do not pass a ruleset to any method that takes one it will default to the saving ruleset or global ruleset.

You can also define your own custom rulesets. These won't be used by the trait when hooking into model events, but you can use them to validate for yourself.

    protected $rules = [
        'my_custom_rules' => [
            'title' => 'required'
        ]
    ];

    // 

    $post->isValid('my_custom_rules');

### Unique rules

You may have noticed we're using the `unique` rule on the slug, which wouldn't work if we were updating a persisted model. Luckily, Validation will take care of this for you and append the model's primary key to the rule so that the rule will work as expected; ignoring the current model.

You can adjust this functionality by setting the `$injectIdentifier` property on your model.

```
/**
 * Whether the model should inject it's identifier to the unique
 * validation rules before attempting validation.
 *
 * @var boolean
 */
protected $injectIdentifier = true;
```

### Accessors and mutators

You also have access to some really existing getters and setters, which allow you to get and set your validation rules and messages.

	// YOLO no rules
    $post->setRules([]);

    // Or set a specific ruleset
    $post->setRuleset([], 'creating');

	// Be a little nicer
    $post->setMessages(['title.required' => "Please, please set a title."])

These are handy if you need to adjust the rules or messages in a specific scenario differently.

## Controller usage

There are a few ways to go about using the validating model in your controllers, but here's the simple way I like to do it. Really clean, clear as to what is going on and easy to test. Of course you can mix it up as you need, it's just one approach.

```
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
                ->withErrors($post->getErrors())
                ->withInput();
        }

        // Post was saved successfully.
        return Redirect::route('posts.show', $post->id);
    }
}
```

It's important to note that `$post->save()` should only return false if validation fails (unless you have other observers watching your model events). If there is an issue with saving in the database your app would raise an exception instead.

You might also like to reduce the number of lines in your code by doing the above test all in one line...

    if ( ! $this->post->create(Input::all()))
    {
        //
    }

## Todo

* Allow for a core set of rules which can be modified/extended by other rulesets