# Automated tests

This repository uses Codeception for automated testing and leverages [`slic`](https://github.com/stellarwp/slic) for running the tests.

## Pre-requisites

* Docker
* A system-level PHP installation with MySQL libraries
* [`slic`](https://github.com/stellarwp/slic) set up and usable on your system (follow setup instructions in that repo)

## Running tests

### First time run

To run tests for the first time, there are a couple of things you need to do:

1. Run `slic here` in the parent directory from where this library is cloned. (e.g. If you ran `git clone` in your `wp-content/plugins` directory, run `slic here` from `wp-content/plugins`)
2. Run `slic use uplink` to tell `slic` to point to the uplink library.
2. Run `slic composer install` to bring in all the dependencies.

### Running the tests

You can simply run `slic run` or `slic run SUITE_YOU_WANT_TO_RUN` to quickly run automated tests for this library. If you want to use xdebug with your tests, you'll need to open a `slic ssh` session and turn xdebugging on (there's help text to show you how).
