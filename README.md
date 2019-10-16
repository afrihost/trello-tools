# Trello Tools

This repo contains internal CLI tools that we use to manage our [Trello](https://trello.com/) Boards. We've released it 
as OpenSource in the hope that it can help others.

## Usage
Currently the main tool in this repo allows for advanced filtering of Cards on a board.

It can be run as follows:
```bash
 $ bin/console filter:cards --board_id=<board_id>
```
You can the `<board_id>` from the URL when you load a Board in your browser (its the bit after 'trello.com/b/')

You'll then be able to build up advanced filters like the following
```
Total Cards: 993 Filtered Cards: 13
Active Filters
 OR               - (List Name: Next) OR (List Name: OnHold) OR (List Name: WIP)
 Has No Members   - Any card that has no members
 NOT              - NOT( Colour: Green )
 Activity Since   - Since: 2019-10-09 18:38:03
Options:
  [0] Add Filter
  [1] Remove Filter
  [2] Print Cards
  [3] Refresh Cards
  [4] Exit
 >
```

## Installation

### 1) Download

Clone this repo
```
$ git clone git@github.com:afrihost/trello-tools.git
```

Change to its directory
```
$ cd trello-tools.
```

### 2) Install Packages
```
$ composer install
```

During the Composer install, it will ask you for the following Symfony parameters:
```
trello_api_key (getyoursonceloggedintotrello): XXXXXXXXXXXXX
trello_api_token (authoriseoneforyourownaccount): XXXXXXXXXXXXX
```
You can get your Key and Token from Trello at https://trello.com/app-key

If it asks you for database or SMTP credentials, simply accept the defaults as these are not currently used.
