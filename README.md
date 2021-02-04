# Slack/Twilio Integration Application
This is a PHP application to integrate incoming message from Twilio with a Slack environment. This allows multiple 
people from within an organization to receive and respond to text messages.

## Documentation
Documentation for the application can be found in the "documentation" folder.
It contains a n index.xhtml file, that when opened in a browser, will outline
the structure of the application and explain the code.

#### Generating Documentation
After the source code has been modified, the documentation can be recompiled
using the following commands:
```console
./phploc.phar . --exclude vendor --log-xml phploc.xml
./phpdox.phar
```
## Install
Download these files to your desired location and run:
```console
composer update
```

## Configuration
After pulling a fresh copy of the source code, you will need to configure 
the application. In the "config" folder, simply <b>copy the env.ini.dist to env.ini</b>
and fill out the environmental variables. <b>NEVER push an env.ini with real API
credentials to a git repository.</b>

#### CRON Job
Set up a CRON Job to run once per day for the following command:
```console
php {PATH_TO_APPLICATION_ROOT}/console/command.php channels:clear-unused
```

#### Slack Settings
- **token**: OAuth Access Token
- **bot_token**: Slack bot auth token
- **group_id**: The id of the user group that should be added to new channels
- **message_prefix**: A statement that will be added to the beginning of all Slack messages

#### Twilio Settings
- **sid**: Twilio account SID
- **token**: Bot User OAuth Access Token
- **number**: Number owned by the Twilio account which messages should be sent from
- **message_prefix**: A statement that will be added to the beginning of all outgoing text messages

## Slack Setup
1.  Create a new Slack App, and add in a Legacy Bot
2.  Give the bot all the following permissions:
    1.  channels:history
    2.  channels:read
    3.  channels:write
    3.  chat:write:bot
    4.  chat:write:user
    5.  files:write:user
    6.  groups:history
    7.  groups:read
    8.  groups:write
    9.  im:read
    10.  im:write
    11.  mpim:history
    12.  mpim:write
    13.  usergroups:read
    14.  usergroups:write
3.  Install the bot to your Slack workspace, endure the installing user has admin privileges 
4.  After installing the bot, you should now see a OAuth Access Token and Bot User OAuth Access Token.
add there to the corresponding variables in this applications env.ini file.
5.  Create a new user group, and add in users which you wish to be added to each new channel that is created.
6.  Copy the id for this user group into the "group_id" variable in the env.ini file.
7.  _(Optional)_ You may find it useful to add a tag for your newly created user group in the env.ini file, under Slack, for the "message_prefix"
variable. This way, the user group will get an alert every time a new text message is sent to the Slack channel
8.  Add a Webhook (Event Subscription) in the Slack App that points to https://{YOUR_DOMAIN}/{PATH_TO_APPLICATION_ROOT}/outgoing-message
9.  Subscribe the Event Subscription to the following events:
    1.  Subscribe to bot events:
        1.  message.channels
        2.  message.groups
    2.  Subscribe to workspace events:
        1.  message.channels
        2.  message.groups

## Twilio Setup
1.  Ensure that you have an active Twilio account and have purchase a phone number capable of SMS, and MMS
2.  Go to https://www.twilio.com/console/phone-numbers/incoming and click the number you wish to use for this application
3.  Scroll down to "Messaging" and under "A MESSAGE COMES IN" ensure:
    1.  The first dropdown is set to "Webhook"
    2.  The text box is set to https://{YOUR_DOMAIN}/{PATH_TO_APPLICATION_ROOT}/incoming-message
    3.  The HTTP Method is set to "HTTP POST"
4.  Set the "number" setting in this applications env.ini to the Twilio number you have just updated
5.  Finally locate your Twilio account's SID and AUTH token and fill out the corresponding variables in the env.ini
to complete the Twilio setup

