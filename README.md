# Bauassets - Manage loans of assets

Stopped working on this project.

## Usage
There is assets (items) that can be part of a loan. For every time a person comes
to borrow assets, a loan must be created and assets will be assigned to it.
To be able to create and edit loans, the user must be logged in and have the
`moderator` role.

After creation, you can add assets to the loan. When you finished setting up the
loan, you mark it as handed out (in the yellow box). The borrower will be 
notified by email. As soon as a loan is handed out, it cannot be edited anymore
to make sure the borrower can't make claims that something got changed.

When the loan is returned, you look for the loan in the *show loans* section and
tick the *returned* box and click update.

If you want to know, where the asset/item might be, you can do it this way:
click on *asset catalog* in the menu and search for the asset that you need 
information about. On the following page you'll see in which loans the asset was
recently used. If everybody always remembers to tick the "returned" box, it will
be fairly easy to see, who has the asset: The asset is always with the person 
that has the loan with the status *Awaiting return*.

## Requirements
- PHP >= 7.3
- MariaDB server >= 10.1 (or MySQL equivalent)
- composer

## Installation (for development)
- clone or download the master branch of this git repo
- cd into the *bauassets* directory
- `composer install` to install all dependencies
- `cp .env.example .env`
- `php artisan key:generate`
- Setup .env file according to your needs, especially database connection
- `php artisan migrate`
- `php artisan serve` to start the development webserver

## Testing
- run from the project's root: `./vendor/bin/phpunit`

## Contributing
The software is built using the Laravel Framework (Version 6) and we try to
follow the framework's best practices.

To help you finding your way through the code:
- Models are in their default directory: /App
- Controllers follow a CRUD pattern wherever possible (resource controllers)
- Each controller has its own directory in the views folder. Each controller
action has one file
- every view that is meant to be @include'd goes into views/includes
- there is no fancy frontend frameworks or packaging yet. Everything included
from the views is listed in layouts/app.blade.php and placed in /public
