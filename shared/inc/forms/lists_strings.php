<?php

$txt_lists_hlp_listaddress = array (
  "fr" => "",
  "en" => "This file contains all addresses which mlmmj sees as listaddresses (see
tocc option). The first one is the one used as the primary one, when mlmmj
sends out mail.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_closedlist = array (
  "fr" => "La liste est-elle fermée. Si elle est fermée, les inscription et
désinscription sont désactivées.",
  "en" => "Is the list is open or closed. If it's closed subscribtion and
unsubscription via mail is disabled.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_moderated = array (
  "fr" => "",
  "en" => "If this flag is set, the email addresses in the field
moderators will act as moderators for the list.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_tocc = array (
  "fr" => "",
  "en" => "If this flag is set, the list address does not have to be in the To:
or Cc: header of the email to the list.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_subonlypost = array (
  "fr" => "",
  "en" => "When this flag is set, only people who are subscribed to the list,
are allowed to post to it. The check is made against the \"From:\" header.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_prefix = array (
  "fr" => "",
  "en" => "The prefix for the Subject: line of mails to the list. This will alter the
Subject: line, and add a prefix if it's not present elsewhere.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_owner = array (
  "fr" => "",
  "en" => "The emailaddresses in this fields (1 pr. line) will get mails to
listname-owner@listdomain.tld",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_customheaders = array (
  "fr" => "",
  "en" => "These headers are added to every mail coming through. This is the place you
want to add Reply-To: header in case you want such.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_delheaders = array (
  "fr" => "",
  "en" => "In this fields is specified *ONE* headertoken to match per line. If the fields are like this:
<pre>
Received:
Message-ID:</pre>
Then all occurences of these headers in incoming list mail will be deleted.
\"From:\" and \"Return-Path:\" are deleted no matter what.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_access = array (
  "fr" => "",
  "en" => "If this file exists, all headers of a post to the list is matched against
the rules. The first rule to match wins. See README.access for syntax and examples.

NOTE: the default action is to deny access (reject the mail), so an empty
access control file will cause mlmmj to reject all posts, whereas a non-
existant file will change nothing, and mlmmj will behave as usual.

Each header in the mail is tested against each rule, rule by rule. That is,
all headers are first tested against the first rule, then all headers are
tested against the second rule, and so on.

The first rule to match a header decides which action to take - allow, deny
or moderate the post.

The syntax is quite simple: action[ [!]regexp]
- \"Action\" can be \"allow\", \"deny\" or \"moderate\".
- The optional \"!\" makes the rule a match, if NO header matches the regular expression.
- \"Regexp\" is a POSIX.2 extended regular expression. Matching is done case insensitive.

IMPORTANT: if \"moderate\" is used then don't forget to add people who should
function as moderators in those fields.<br><br>
 
First a simple example. This rule set will reject any mail that is NOT plain
text, or has a subject that contains \"BayStar\", and allow anything else:
 
deny !^Content-Type: text/plain
deny ^Subject:.*BayStar
allow
    
To allow only text mails, but have the moderators moderate every html mail one
would use this:
    
allow ^Content-Type: text/plain
moderate ^Content-Type: text/html
deny
       
Now on to a more advanced example. Morten can post anything, Mads Martin can
post if the subject does not contain \"SCO\". Everything else is denied:

allow ^From: Morten
deny ^Subject:.*SCO
allow ^From: Mads Martin
deny
    
The last rule (deny) can be left out, as deny is the default action.
    
A third example. Deny any mails with \"discount\", \"weightloss\", or \"bonus\" in
the subject. Allow PGP signed and plain text mails. Anything else is denied:
    
deny ^Subject:.*discount
deny ^Subject:.*weightloss
deny ^Subject:.*bonus
allow ^Content-Type: multipart/signed
allow ^Content-Type: text/plain",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_memorymailsize = array (
  "fr" => "",
  "en" => "Here is specified in bytes how big a mail can be and still be prepared for
sending in memory. It's greatly reducing the amount of write system calls to
prepare it in memory before sending it, but can also lead to denial of
service attacks. Default is 16k (16384 bytes).",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_addtohdr = array (
  "fr" => "",
  "en" => "When this flag is present, a To: header including the recipients
emailaddress will be added to outgoing mail. Recommended usage is to remove
existing To: headers with delheaders (see above) first.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_notifysub = array (
  "fr" => "",
  "en" => "If this file is present, the owner(s) will get a mail with the address of
someone sub/unsubscribing to a mailinglist.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_digestinterval = array (
  "fr" => "",
  "en" => "This file specifies how many seconds will pass before the next digest is
triggered. Defaults to 50 mails, meaning that if 50 mails arrive to the list
before digestinterval have passed, the digest is delivered.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_digestmaxmails = array (
  "fr" => "",
  "en" => "   This file specifies how many mails can accumulate before digest sending is
triggered. Defaults to 50 mails, meaning that if 50 mails arrive to the list
before digestinterval have passed, the digest is delivered.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_bouncelife = array (
  "fr" => "",
  "en" => "Here is specified for how long time in seconds an address can bounce before
it's unsubscribed. Defaults to 432000 seconds, which is 5 days.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_noarchive = array (
  "fr" => "",
  "en" => " If this file exists, the mail won't be saved in the archive but simply deleted.",
  "hu" => "",
  "it" => "Se selezionato, le email non verranno salvate nell'archivio<br>ma semplicemente cancellate.",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_nosubconfirm = array (
  "fr" => "",
  "en" => "If this file exists, no mail confirmation is needed to subscribe to the
list. This should in principle never ever be used, but there is times
on local lists etc. where this is useful. HANDLE WITH CARE!",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_noget = array (
  "fr" => "",
  "en" => "If this file exists, then retrieving old posts with +get-N is disabled",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_subonlyget = array (
  "fr" => "",
  "en" => "If this file exists, then retrieving old posts with +get-N is only
possible for subscribers. The above mentioned 'noget' have precedence.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_verp = array (
  "fr" => "",
  "en" => "Enable VERP support. Anything added in this variable will be appended the
MAIL FROM: line. If \"postfix\" is put in the file, it'll make postfix use
VERP by adding XVERP=-= to the MAIL FROM: line.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_maxverprecips = array (
  "fr" => "",
  "en" => "How many recipients pr. mail delivered to the smtp server. Defaults to 100.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

?>