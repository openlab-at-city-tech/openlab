# How to Contribute

## Local Development Setup

The quickest way to set up a local development environment is by way of Docker.  Steps:
1. `svn co http://plugins.svn.wordpress.org/an-gradebook/ an-gradebook`
2. `cd an-gradebook/trunk`
3. `docker-compose up -d`
4. That's it!

At this point point your WordPress test site is accessible via `localhost:8000/wp-admin`.  You will need to go through the WordPress setup process if its a fresh install; otherwise, you'll see the login screen.
