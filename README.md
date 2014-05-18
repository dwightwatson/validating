Validating
==========

[![Build Status](https://travis-ci.org/dwightwatson/validating.png?branch=master)](https://travis-ci.org/dwightwatson/validating)

## An Eloquent model Validation trait for Laravel 4.2+.

Validating is a trait for Laravel 4.2+ Eloquent models which requires that models meet their validation criteria before being saved. If they are not considered valid the model will not be saved and the validation errors will be made available.

_Validating is still in beta, so please be aware that things aren't perfect yet. However, it will adhere to semantic versioning._

### Installation

Simply add the package to your `composer.json` file and run `composer update`.

```
"watson/validating": "0.1.*"
```

Because we're running on Laravel 4.2 which is currently in beta, you may also need to set your `minimum-stability`:

```
"minimum-stability": "beta"
```

### Using Validating

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
    	->withSuccess("How easy was that, Batman?");

However, if you're the kind of cowboy who wants to save without performing model validation you can too. This will return the same result as if you called `save()` on a model without the trait.

    $post->forceSave();

#### Unique rules

You may have noticed we're using the `unique` rule on the slug, which wouldn't work if we were updating a persisted model. Luckily, Validation will take care of this for you and append the model's primary key to the rule so that the rule will work as expected; ignoring the current model.

#### Accessors and mutators

You also have access to some really existing getters and setters, which allow you to get and set your validation rules and messages.

	// YOLO no rules
    $post->setRules([]);

	// Be a little nicer
    $post->setMessages(['title.required' => "Please, please set a title."])

These are handy if you need to adjust the rules or messages in a specific scenario differently.