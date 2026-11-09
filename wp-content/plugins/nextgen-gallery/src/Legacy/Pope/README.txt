==============
POPE FRAMEWORK
==============

WHY "POPE"?
-----------
Pope is an attempt to provide a component framework, similiar to Python's Zope 3
framework. In otherwords, Pope is PHP's version of Zope.
See: http://wiki.zope.org/zope3/ComponentArchitectureOverview

In short it adds polymorphism or plugin-like functionality into your PHP classes.
With it you can build applications with plugins, plugins to existing applications,
and extend or change third party libraries without modifying their source.

The unit tests in the "tests" directory double as a tutorial to Pope and how to use
it. For best clarity read the source in this order:
* core
* pre_hooks
* registry
* factories
* modules
* wrappers
* advanced
* method_properties
To run the tests yourself modify run_tests.php to point to your own SimpleTest
checkout.

A component framework puts a strong emphasis on interface design, and designing
by contract. However, Pope also tries to be less restrictive by adopting duck typing
and the philosophy, "if it walks like a duck and talks like a duck then it is
a duck". This is sometimes also referred to as "monkey patching".

A component frameworks relies on the following:

- Interfaces: 
    
    Interfaces define the contracts which the design must follow.
    See: http://en.wikipedia.org/wiki/Design_by_contract

- Components:

    Components implement interfaces to provide specific functionality in a
    desired context. The context of an object is important, as a component
    can behave differently when used in a different context.

- Adapters:

    Adapters modify the behavior of a component to adapt to a particular context.
    For example, in a component framework there might be difference between an
    image and a thumbnail - they are both images, but used in different contexts.
    Adapters would be used to make a thumbnail image behave differently.

- Utilities:
    
    Utilities are registered implementations of a particular interface. An
    example of a utility is an object factory, based on the factory pattern.

- Factory:

    Factories create objects.
    See: http://en.wikipedia.org/wiki/Factory_method_pattern



EXTENSIBLE OBJECT
-----------------
Pope is able to use duck typing and monkey patching through the use of a class
called ExtensibleObject, which provides these capabilities. ExtensibleObject
provides a means of polymorphism and multiple inheritance using something
called "mixins".
See: http://en.wikipedia.org/wiki/Mixin

An understanding of how to use ExtensibleObject is fundamental to the understanding
of how to use Pope, and what makes it a unique and powerful tool.

ExtensibleObject is quite unique in that it brings a lot of features to PHP 5.2
that are only available in PHP 5.3 and above. It inherits a lot of it's design
from Ruby. For example,

i) Methods can be added and removed from objects at runtime through the use of
Pope extensions
 
ii) Pre-executed and post-executed hooks can be registered at runtime, which are 
methods that are executed when a particular method has been called on an 
ExtensibleObject instance.

iii) Method implementations can be replaced and restored at runtime.

To get a better understanding of how these things can truly benefit you as a
programmer and a designer, please watch David Heinemeier Hansson's keynote about
Ruby: http://vimeo.com/17420638


