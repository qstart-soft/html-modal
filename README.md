Html Modal Component
====================

The Html-Modal component provides all the tools needed to build modal windows, independent of the rest of the system.

It provides the infrastructure to separate modal processing logic from the rest of the view.

On the frontend, you can use any convenient library

Installation
------------

```
$ composer require qstart-soft/html-modal
```

Getting Started
---------------

First you need to create a container instance of [Qstart\HtmlModal\AbstractModalContainer] for working with modal windows.

The container uses two types of attributes:

___Qstart\HtmlModal\ModalName___

It is passes a list of modal window names for which the method is used.

___Qstart\HtmlModal\ModalBuilderMethod___

is used to set the method in which the content is wrapped if needed. This attribute can be set in two ways 
- globally for the whole class
- for a specific method

You can also exclude a method from wrapper processing in the following way: #[ModalBuilderMethod(ModalBuilderMethod::EMPTY_BUILDER)]

```php
use Qstart\HtmlModal\AbstractModalContainer;
use Qstart\HtmlModal\ModalBuilderMethod;
use Qstart\HtmlModal\ModalName;

#[ModalBuilderMethod('buildModal')]
class ModalContainer extends AbstractModalContainer
{
    #[ModalName('first-modal', 'second-modal')]
    public function viewFirstModal($modalId, $modalName)
    {
        return $this->templating->render('modal-template');
    }
    
    #[ModalName('another-modal')]
    #[ModalBuilderMethod(ModalBuilderMethod::EMPTY_BUILDER)]
    public function viewAnotherModal($modalId, $modalName)
    {
        return $this->templating->render('another-modal-template');
    }
    
    public function buildModal($content)
    {
        // For example with Bootstrap Modal Component
        return sprintf(
        '<div class="modal" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Modal title</h5>
              </div>
              <div class="modal-body">%s</div>
              <div class="modal-footer"></div>
            </div>
          </div>
        </div>',
        $content
        );
    }
}
``` 
Then, once you have the container with the required logic, we can set up the controller.

Let's say a request comes into a `ModalController` in an action named `show`.

We need to set up a configuration object and build a modal window.
You can do this as an example:

To connect the engine to work with templates, the library is used [Symfony Templating Component](https://packagist.org/packages/symfony/templating)

```php
use Qstart\HtmlModal\ModalBuilder;
use Qstart\HtmlModal\ModalConfig;
use Symfony\Component\Templating\PhpEngine;

class ModalController
{
    public function actionShow(PhpEngine $templating, $modalName, $modalId = null)
    {
        $config = new ModalConfig(new ModalContainer(), $templating);
        // Additional containers are connected by the following method:
        $config->setContainers([]);

        $builder = new ModalBuilder($config, $modalName, $modalId);
        $content = $builder->getContent();

        return $content;
    }
}
```
___Wonderful!___

We've separated the modal rendering logic from the rest of the view. On the frontend, you can use any convenient library

Resources
---------

* [Symfony Templating Component](https://packagist.org/packages/symfony/templating)