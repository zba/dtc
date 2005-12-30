<?php

$txt_lists_hlp_list_name = array (
  "fr" => "Nom de la liste.",
  "en" => "Name of the list",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_main_owner = array (
  "fr" => "Ce champ contient l'adresse de l'administrateur principal de la liste.",
  "en" => "This is the main owner of the list.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_listaddress = array (
  "fr" => "Ce champ contient toutes les adresses que MLMMJ voit comme adresse de liste (voire l\'option tocc). La première est l\'adresse principale.",
  "en" => "This feild contains all addresses which mlmmj sees as listaddresses (see tocc option). The first one is the one used as the primary one, when mlmmj sends out mail.",
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
  "fr" => "La liste est-elle fermée. Si elle est fermée, les inscription et désinscription sont désactivées.",
  "en" => "Is the list is open or closed. If it\'s closed subscribtion and unsubscription via mail is disabled.",
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
  "fr" => "Si ce drapeau est positionné, alors la liste est modérée",
  "en" => "If this flag is set, the email addresses in the field moderators will act as moderators for the list.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_moderators = array (
  "fr" => "Ceci est la liste des modérateurs.",
  "en" => "This is the list of moderators.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_tocc = array (
  "fr" => "Si ce drapeau est positionné, la liste ne doit pas forcément être présente dans les champs To: ou Cc: du header du message.",
  "en" => "If this flag is set, the list address does not have to be in the To: or Cc: header of the email to the list.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_subonlypost = array (
  "fr" => "Quand ce drapeau est positionné, seuls les inscrits peuvent envoyer. La vérification est faite en fonction du \"From:\" du header.",
  "en" => "When this flag is set, only people who are subscribed to the list, are allowed to post to it. The check is made against the \"From:\" header.",
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
  "fr" => "Préfixe de la ligne Sujet: (Subject:) des messages de la liste. Ceci va altéré la ligne Sujet: et ajouter un préfixe si il n\'est pas déjà présent.",
  "en" => "The prefix for the Subject: line of mails to the list. This will alter the Subject: line, and add a prefix if it\'s not present elsewhere.",
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
  "fr" => "Les adresses emails de ces champs (1 par ligne) recevront les messages pour liste-owner@nom-de-domaine.tld",
  "en" => "The email addresses in this fields (1 per line) will get mails to listname-owner@listdomain.tld",
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
  "fr" => "Ces champs contiennent les champs qui doivent être ajouté a chaques message. C\'est ici que vous pouvez ajouter le champs Reply-To: dans le header si cela est ce que vous voulez.",
  "en" => "These headers are added to every mail coming through. This is the place you want to add Reply-To: header in case you want such.",
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
  "fr" => "Dans ces champs est spécifié *UN* token du header par ligne. Si les champs sont comme il suit :<br><br>Received:<br>Message-ID:<br><br>Alors toutes les occurences de ces headers dans les messages entrants de la liste seront effacé. From: et Return-Path: sont effacé dans tous les cas.",
  "en" => "In those fields is specified *ONE* headertoken to match per line. If the fields are like this:<br><br>Received:<br>Message-ID:<br><br>Then all occurences of these headers in incoming list mail will be deleted. From: and Return-Path: are deleted no matter what.",
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
  "fr" => "Lire la partie README.access de la doc de MLMMJ.",
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

IMPORTANT: if \"moderate\" is used then don\'t forget to add people who should
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
  "fr" => "Ici est spécifié le nombre d\'octet que peut faire un message en mémoire pour être préparé à être envoyé. Default à 16Ko.",
  "en" => "Here is specified in bytes how big a mail can be and still be prepared for sending in memory. It\'s greatly reducing the amount of write system calls to prepare it in memory before sending it, but can also lead to denial of service attacks. Default is 16k (16384 bytes).",
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
  "fr" => "Lorsque ce drapeau est positionné un header To: incluant les adresses des destinataires sera ajouté aux messages sortants. La recommandation est de retiré les header To: grâce a l\'option delheaders au préalable (voir plus loin).",
  "en" => "When this flag is present, a To: header including the recipients emailaddress will be added to outgoing mail. Recommended usage is to remove existing To: headers with delheaders (see above) first.",
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
  "fr" => "Si ce drapeau est drapeau est positionné, alors le(s) propriétaire(s) recevront les adresses des (dé)enregistrement à la liste.",
  "en" => "If this flag is present, the owner(s) will get a mail with the address of
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
  "fr" => "Cette valeur spécifie combien de secondes doivent passé avant que le nouveau digest soit déclanché. Le defaut est de 604800 secondes, ce qui correspond à 7 jours.",
  "en" => "This value specifies how many seconds will pass before the next digest is sent. Defaults to 604800 seconds, which is 7 days.",
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
  "fr" => "Cette valeur définie combien de mails peuvent s\'accumulés avant qu\'un digest soit envoyé. Defaut de 50 message.",
  "en" => "This file specifies how many mails can accumulate before digest sending is triggered. Defaults to 50 mails, meaning that if 50 mails arrive to the list before digestinterval have passed, the digest is delivered.",
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
  "fr" => "Ici est spécifié le nombre de secondes qu\'une adresse peut bouncer avant d\'être désinscrit. Le defaut est de 432000 secondes, ce qui est 5 jours.",
  "en" => "Here is specified for how long time in seconds an address can bounce before it\'s unsubscribed. Defaults to 432000 seconds, which is 5 days.",
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
  "fr" => "Si ce drapeau est positionné, alors le mail n\'est pas sauvé dans l\'archive mais simplement effacé.",
  "en" => " If this flag exists, the mail won\'t be saved in the archive but simply deleted.",
  "hu" => "",
  "it" => "Se selezionato le email non verranno salvate nell\'archivio<br>ma semplicemente cancellate",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_nosubconfirm = array (
  "fr" => "Si ce drapeau existe, aucun mail de confirmation est nécéssaire pour s\'inscrire à la liste. Ceci ne doit en pratique être utilisé que dans un réseau local. A UTILISER AVEC PRECAUTION!",
  "en" => "If this flag exists, no mail confirmation is needed to subscribe to the list. This should in principle never ever be used, but there is times on local lists etc. where this is useful. HANDLE WITH CARE!",
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
  "fr" => "Si ce drapeau est positionné, récupérer des posts avec -get-N est désactivé.",
  "en" => "If this file exists, then retrieving old posts with -get-N is disabled",
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
  "fr" => "Si ce drapeau existe, récupéré des posts avec -get-N est possible uniquement pour les inscrits a la liste. Le drapeau \'noget\' est prioritaire.",
  "en" => "If this file exists, then retrieving old posts with -get-N is only possible for subscribers. The above mentioned \'noget\' have precedence.",
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
  "fr" => "Active le support VERP.",
  "en" => "Enable VERP support.",
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
  "fr" => "Combien de destinataire par mail doivent être envoyé au serveur SMTP. Le defaut est de 100.",
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

$txt_lists_hlp_notoccdenymails = array (
  "fr" => "Refuse les message si ni le To: ni le Cc: contient l'une des adresses de la liste.",
  "en" => "Reject mails that don\'t have the list adress in the To: or Cc:.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_noaccessdenymails = array (
  "fr" => "-",
  "en" => "-",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_nosubonlydenymails = array (
  "fr" => "-",
  "en" => "-",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_delimiter = array (
  "fr" => "Ne pas modifier a moins que vous sachiez vraiment ce que vous faites.",
  "en" => "Do not change unless you really know what you are doing.",
  "hu" => "",
  "it" => "",
  "nl" => "",
  "ru" => "",
  "de" => "",
  "zh" => "",
  "pl" => "",
  "es" => "",
  "pt" => "");

$txt_lists_hlp_relayhost = array (
  "fr" => "Serveur de mail utilisé pour les envois.",
  "en" => "Mail server used to send the messages.",
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