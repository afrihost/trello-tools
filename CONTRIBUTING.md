# Contributing

We welcome additions and bug fixes.
Please open a issue prior to working on a Pull Request to this project. Use this to specify what you intend to add. That
way you can avoid doubling up the same change as someone else. It also allows us to give you feedback on your change ahead 
of time

## Adding a New Card Filter

If you'd like to add a new card filter, simply add a class to the `src/AppBundle/Helper/CardFilter/` directory that implements
the `AppBundle\Helper\CardFilter\CardFilterInterface` Interface. This can most easily be done by simply extending the `AbstractCardFilter`
class in the same directory.

For your new filter to be loaded, you then need to add the class name to the constructor of the `CardFilterFactory` class
also in the same directory:

```php
public function __construct()
    {
        // Configure available filter classes here
        $availableFilterClasses = [
            'AppBundle\Helper\CardFilter\ListCardFilter',
            // ...
            // Add your filer class here
        ];
```