<?php

$txt_draw_help_content = array(
	"fr" => "<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>AIDE EN LIGNE DE DTC</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. Qu'est-ce que DTC</u></font><br><br>
DTC est un outil fait spécialement pour vous. Avec DTC, vous
pouvez prendre le contrôle de l'administration de votre domaine :
vous pouvez configurer vos sous-domaines, vos boites emails et vos
comptes ftp.<br><br>

Ce logiciel a été distribué sous licence L<a
href=\"http://www.gnu.org/\">GPL</a>
(Gnu Public Licence), ce qui signifie que vous pouvez optenir
une copie des sources de l'interface et les utiliser, les modifier,
et faire ce que vous voulez avec aussi longtemps que vous fassiez
profiter des éventuels ajouts, et que vous ne tiriez pas de profit
de cet outil. Nous (chez GPLHost) croyons en l'effort pour le
logiciel libre, et nous espérons que notre contribution va encourager
le developpement d'autres logiciels. Nous avons aussi considéré le fait
que nous n'utilisons que du logiciel libre pour notre service
d'hébergement, il est donc moralement normal de redistribuer nos
efforts de développement à la communauté.<br><br>

<font size=\"+1\"><u>2. Emails</u></font><br>
<u>2.1. A quoi ca sert ?</u><br><br>
Vous pouvez ajouter, supprimer, ou modifier des boites électroniques
avec cet outil.<br><br>

<u>2.2. Redirection et livraison en local</u><br><br>
Chaque boite email peut être redirigée vers au moins une adresse email,
ce qui signifie que lorsqu'un message sera reçu, celui-ci sera retransmit
vers une ou deux adresse(s) de courrier électronique. La case a cocher
\"Déliver en local\" indique si oui on non les messages pour cette boite
seront écrit sur nos disques durs, pour que vous puissier ensuite
lire vos message sur nos server en utilisant un client mail. Ce
client se connectera ensuite sur nos serveurs pour lire le courrier.
N'oubliez pas de lire vos courriers souvent si vous avez du
traffic, puisque les boites sont inclus dans votre quota disque.<Br><br>

<u>2.3. Delai pour l'ajout / l'effacement de comptes</u><br><br>
Lorsque vous ajoutez, on effacez des boites mails, ne vous attendez
pas à voir cela fonctionner immédiatement : nous devons valider
les changements dans le système pour que vos changements soient
actifs. Nous devons dire a Qmail (notre serveur de messagerie) de
recharger sa liste d'utilisateurs.<br><br>
La plus part du temps, nous validons les changements a la fin
de chaque jour ouvrable, mais si vous avez d'une validation
immédiate, veuillez cliquer <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email account 
validation\">ici</a>.<br><br>

<u><2.4 Pas de spam !</u><br><br>
Attention, si vous utilisez notre serveur de messagerie de manière
abusive (envois de spam, de carte postales, de liste de diffusion,
revente d'email gratuit, etc... (liste non exhaustive)), les
sanctions pourraient être immédiates. Rappelez vous que nous
ne désirons pas que notre serveur de mail soit sur les listes
noires !!!<br><br>
Nous réfléchissons a l'ajout de fonction de mailling list avec
un demon spécialisé (sympa, marjodomo, etc...).<br><br>

<font size=\"+1\"><u>3. Sous-domaines</u></font><br>
<u>3.1. A quoi ca sert ?</u><br><br>
Cette partie de l'interface permet la configuration des
sous-domaines de votre site web, ce qui signifie que
vous pourrez créer des adresses de la forme :
<pre>
http://ce.que.vous.voulez.votre-nom-de-domaine.com
</pre>
<u>3.2. Qu'est-ce que le sous-domaine par défaut ?</u><br><br>
Lorsque quelqu'un essaye de contacter votre site web avec
une URL sans sous-domaine, celui-ci sera rédirigé vers
le sous-domaine que vous aurez configuré comme étant
celui par défaut. En d'autre termes, si vous
configurez :
<pre>
www
</pre>
comme le sous-domaine par défaut, alors si quelqu'un tape :
<pre>
http://votre-nom-de-domaine.com
</pre>
dans son browser web, alors il sera redirigé vers :
<pre>
http://www.votre-nom-de-domaine.com
</pre>
En fait, l'URL est conservée, et aucune redirection
n'est réellement construite dans une page HTML,
plus simplement, un site web pointant vers les
répertoires de votre sous-domaine par défaut à
été fabriqué, donc il utilise les memes pages
html (ou php) et utilise les memes fichiers de
log.<br><br>

<u>3.3. Sous-domaines interdits</u><br><br>
Puisque nous avont déjà configuré ces sous-domaines
pour d'autres services que le web, vous ne pouvez
pas utiliser ces sous-domaines pour des sites apache :
<ul><li>ftp</li>
<li>pop</li>
<li>smtp</li>
</ul>

<u>3.4. Effacement des sous-domaines</u><br><br>
C'est a vous d'effacer les fichiers utilisés par les
sous-domaines que vous voulez effacer. Vous pouvez
effacer ces fichiers avec un clients FTP. Mais s'il
vous plaît, faites TRES attention a ne pas effacer les
fichiers d'un sous-domaine que vous n'avez pas effacé
dans DTC. En effet, le serveur web Apache n'est pas
content lorsqu'un dossier n'existe pas tout en étant
configuré, et cela posera des problèmes a nos administrateurs.<br><br>

<u>3.5. Delai de l'ajout / l'effacement de sous-domaines</u><br><br>
Nous devons relancer apache (notre serveur web) et bind (notre serveur
de nom) pour que vos sous-domaines soit actifs. En d'autres termes,
nous devons valider vos changement.

La plus part du temps, nous validons les changements a la fin
de chaques jour ouvrable, mais si vous avez d'une validation
immédiate, veuillez cliquer <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]subdomain creation
: please restart apache and named now !\">ici</a>.<br><br>

<u>3.6. Statistiques de votre site</u><br><br>
Puisque votre trafic est en fichier de log, nous calculons le
trafic total pour les 12 derniers mois de statistics en utilisant
<a href=\"http://www.mrunix.net/webalizer/\">webalizer</a>.
Les statistiques sont calculés chaques jours a 4 heure du matin,
lorsqu'il y a le moins de trafic, et sont accessible dans le répertoire
\"stat\" de votre site.<br><br>

Si vous avez enregistré :
<pre>
http://www.mydomain.com
</pre>
vos statistiques se trouverons dans :
<pre>
http://www.mydomain.com/stats/
</pre>

Attention ! Si vous faite vraiment beaucoup
de trafic, votre fichier de log poura être effacé et vos statisques remis
a zéro. Nous ne garantissons rien pour ces statistiques, elle sont
simplement
présente pour vous aider.<br><br>

<font size=\"+1\"><u>4. Comptes FTP</u></font><br>
<u>4.1. A quoi ca sert ?</u><br><br>
Pour avoir des pages qui fonctionne et qui marche, vous
devez les envoyer sur nos serveurs. Mais puisque vous
pouvez être plusieurs a travailler sur votre site, vous
pouvez avoir besoin de plusieurs comptes FTP. DTC est
l'outil pour administrer ces comptes et mots de passes.<br><br>

<u>4.2. Delai de l'ajout / l'effacement de comptes FTP</u><br><br>
Puisque nous utilisons ProFTP avec un module spécial pour utiliser
une base de donnée, tous les changements sur vos comptes FTP seront
pris en comptes immédiatement en temps réel.<br><br>

<u>4.3. Limiter un utilisateur a un chemin spécifique</u><br><br>
Pour le moment, vous ne pouvez pas le faire (mais c'est prévu).
Par contre, nous pouvons le faire sur demande motivée, si vous
nous écrivez en cliquant <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Ask for a
user path change in FTP\">ici</a>, en précisant le nom d'utilisateur le son
chemin.<br><br>

<u>4.4. Pas de piratage ou d'échange de fichier sur nos serveurs
!</u><br><br>
Si nous vous donnons un espace FTP sur nos serveurs, c'est pour y placer
un contenu WEB. Ce que veut dire que les fichiers binaires dont vous
n'avez pas les droits n'ont pas leurs place ici ! Attention aux abus,
nous pourrions fermer votre compte sans avertissement.<br><br>

<font size=\"+1\"><u>5. Pourquoi le FTP ou le POP est parfois lent
?</u></font><br><br>
Il y a plusieurs raisons a cela. La première, c'est qu'il peut arriver
que de nombreux utilisateurs envois des fichiers en meme temps. La seconde,
c'est que nous utilison un system de login par base de donnée (donc le login
est focément plus lent, surtout avec plusieurs millier d'utilisateurs).
De plus, le partage de la bande passante sur internet (de manière plus
globale)
est rarement équitable, et il se peut qu'un des utilisateurs utilise toute
la bande passante et qu'il ne vous reste plus rien. Enfin, nous
avons favorisé l'envois en http pour que vos pages soient affichées plus
vite.<br><br><br>
</div>
<center>Pour l'équipe de GPLHost,</center><br>
<div align=\"right\"><i><a href=\"mailto:thomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>",

"en" => "
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>ONLINE DTC
HELP</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. What is DTC</u></font><br><br>
DTC is a tool we made especialy for you. With it, you can take the
control of your domain administration : you can
manage all your subdomains, emails, and ftp accounts.<br><br>
All this tool had been release under the <a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence),
which means that you can have a copy of this interface source
code, modify it and use it as you wish, as long as you redistribute
all thoses changes. We (at GPLHost) believe in the Free
Software effort, and we hope this participation will encourage
other developpements. We consider that because we use only
open-source software for our hosting service, it is normal
to redistribute our developpements.<br><br>

<font size=\"+1\"><u>2. Emails</u></font><br>
<u>2.1. What will it do ?</u><br><br>
You can add, delete or modify a mailbox with this tool.<br><br>

<u>2.2. Redirection and local delivery</u><br><br>
Each mailbox can be redirected to one or more email addresses. This
means that when a message is recieved, it is forwared to one
or two email adresse(s). The \"deliver locally\" checkbox
tells wether or not all message for this mailbox will be
written on our hard disk, so thenafter you will be able to
read your message using a mail client, connecting to
our server. Don't forget to checkup your mails often if
you have trafic, because the mailbox are included in the
quota<br><br>
<u>2.3. Delay when adding / deleting accounts</u><br><br>
When you add or delete a mail account, don't expect it to
work immediatly : you will have to wait until the next
cron job to start, so the mail, ssh or web server
reloads it's database.<br><br>
Most of the time, we validate all changes at the end of
each working days, but if you need an immediate validation,
click <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email account 
validation\">here</a>.<br><br>

<font size=\"+1\"><u>3. Subdomains</u></font><br>
<u>3.1. What will it do ?</u><br><br>
This part of the interface is for configurating your
somain's sites, which means that you will be able
to populate your web site with url of the form :
<pre>
http://anything.u.want.mydomain.com
</pre>
<u>3.2. What is the default subdomain ?</u><br><br>
Whe someone trys to contact your web site with an
URL without a subdomain, he is redirected to the
subdomain you said it was the default. In other
words, if you tell that:
<pre>
www
</pre>
is the default subdomain, someone that trys to
connect using an url starting with:
<pre>
http://mydomain.com
</pre>
will be redirected to:
<pre>
http://www.mydomain.com
</pre>
In fact, the URL is kept, and no URL redirection
in a HTML page has been created, but simply, a
website with that URL has been configurated to
the same location of the \"www\" subdomain, so
it accesses the same html (or php) files, and
shares the same log file.<br><br>

<u>3.3. Forbidden subdomains</u><br><br>
Because we have configurated those subdomains for
other services than web, you cannot use the following
subdomains for apache web sites :
<ul><li>ftp</li>
<li>pop</li>
<li>smtp</li>
</ul>

<u>3.4. Deleting subdomains</u><br><br>
It is up to you to delete the files used by your subdomain.
You can delete all the files using a standard ftp client.
But PLEASE take realy care not to delete a subdomain files
without deleting it using DTC. Indeed, the Apache web server
will complain if the directory does not exist but a web site
is configurated for it, and this will be anoying when restarting
apache.<br><br>

<u>3.5. Delay when adding / deleting subdomain</u><br><br>
We will have to restart our Apache web server in order
to have your changes taking effect. Most of the time, we validate all
changes at the end of
each working days, but if you need an immediate validation,
click <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]subdomain creation
: please restart apache now !\">here</a>.<br><br>

<u>3.6. Trafic statistics for your subdomains</u><br><br>
Because all your trafic is loged, we calculate the overall
last 12 month statistics using <a href=\"http://www.mrunix.net/webalizer/\">
webalizer</a>. The statistics are calculated each days at
4 in the morning (this is when there is less trafic), and
can be reach under the \"/stats\" directory on each
subdomains. That means that if you have registerd :
<pre>
http://www.mydomain.com
</pre>
all statistics will be generated under :
<pre>
http://www.mydomain.com/stats/
</pre>

<font size=\"+1\"><u>4. FTP accounts</u></font><br>
<u>4.1. What will it do ?</u><br><br>
To have your page working and running, you have to upload
them. But because you may not be only one to work on your
web site, you may want to have more that one FTP account
for accessing your web site. DTC will be the tool for
managing thoses accounts and passwords.<br><br>

<u>4.2. Delay when adding / deleting FTP accounts</u><br><br>
Because we use ProFTP with a special module for handling accounts in
our MySql database, all changes to your FTP accounts take effect
in realtime.<br><br>

<u>4.3. Limiting user to specified path</u><br><br>
For the moment you cannont limit one user to access to only
a part of your web site. But we (the administrators) can
do it if you ask sending an <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Ask for a
user path change in FTP\">email</a>, telling what user
and what path you need.<br><br>


<u>4.4. No piracy or file exchange on our servers please !</u><br><br>
If we provide a user space on our ftp servers, this is
for you to upload HTML content. This means no binary
files you don't own the rights ! Take care if you abuse,
we could close your accounts without notification.<br><br>

<font size=\"+1\"><u>5. Why ftp or pop is slow, sometimes ?</u></font><br><br>
There are many reasons for that. The first one is because
we don't have unlimited bandwidth with for uploading, and sometimes,
+there are really a lot of people uploading. The ones that
+are closer to our servers usually use all the
+bandwidth, so one user has most of it, and some have
+none. There is nothing we can do about that...<br><br>
Another reason is because we have decided to limit the
bandwidth for pop, smtp, and ftp, so that web browsing
on our server is faster.<br><br><br>
</div>
<center>For the GPLHost team,</center><br>
<div align=\"right\"><i><a href=\"mailto:thomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>
",

"hu" => "
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>ONLINE DTC
SÚGÓ</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. Mi is a DTC ?</u></font><br><br>
DTC egy eszköz amit direkt neked készítettünk. Segítségével kezedben tarthatod
a domain adminisztráiót : menedzselheted az összes aldomaint, levelezéseket, ftp
hozzáféréseket.<br><br>
Ez az eszköz a <a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence),
licencelési formát használja, ami azt jelenti, hogy lehet neked belõle egy forráskód példányod
módosíthatod, használhatod tetszés szerint ameddig ezeket a változtatásokat közzé teszed.
. Mi (a GPLHost-nál) hisszünk a Szabad Szoftverbe fektetett erõfeszítésbe és reméljük
hogy a mi közremûködésünk inspirál más fejlesztõket is.
A mi elgondolásunk az, hogy mivel mi kizárólag szabad szoftvert használunk a webhosting szolgáltatásunk
üzemeltetéséhez ezért értetõ, hogy közzétesszük a fejlesztéseinket.<br><br>

<font size=\"+1\"><u>2. E-mail-ek</u></font><br>
<u>2.1. Mit fog nekem ez csinálni ?</u><br><br>
Hozzáadhat, törölhet, módosíthat vele levelesládákat.<br><br>

<u>2.2. Átirányítás és helyi kézbesítés</u><br><br>
Minden levelesládát átirányíthat egy vagy több címre, ami azt jelenti
hogyha egy üzenete érkezik az továbbítódik egy vagy több címre.
A \"helyileg kézbesítés\" jelölõnégyzet meghatározza
,hogy az összes üzenet a mi szerverünk merevlemezén tárolódjék e,
ahonnan majd egy levelezõkliens segítségével letöltheti azokat.
Ne felejtse el letölteni a leveleit, mert a levelesládájának
is van tárhely kvótája.<br><br>
<u>2.3. Késleltetés hozzáférések hozzáadása / törlése közben</u><br><br>
Ha hozzáad vagy töröl levelesládákat, akkor ne várja hogy azok rögtön mûködnek is:
: elõször át kell vezetnünk a rendszerben a változásokat, ahhoz hogy az ön hozzáférésében
bekövetkezett módosítások életbe lépjenek : meg kell mondanunk a Qmail-nek (a levelezõ szerverünk)
hogy olvassa újra a felhasználói adatbázisát.<br><br>
Legtöbbször ezt munkanapokon a nap végén tesszük meg, de ha rögtön szüksége van az érvényesítésre
, akkor kattintson <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email hozzáférés
érvényesítése\">ide</a>.<br><br>

<font size=\"+1\"><u>3. Aldomain-ek</u></font><br>
<u>3.1. Mit fog ez nekem csinálni ?</u><br><br>
Ez a területe a programnak arra szolgál, hogy beállítsa a domain-jeihez kapcsolódó
oldalakat, ami azt jelenti hogy létrehozhat oldalakat a következõ formában :
<pre>
http://barmi.amit.szeretne.domain.hu
</pre>
<u>3.2. Mi az alapértelmezett aldomain ?</u><br><br>
Ha valaki szeretne csatlakozni az ön weboldalához egy olyan
URL-el, ahol nincs megadva az aldomain, akkor a kérés át lesz irányítva arra az aldomain-re
amit ön alapértelmezettek itt megad. Más szóval
ha ön azt mondja, hogy a:
<pre>
www
</pre>
lesz az alapértelmezett aldomain, akkor ha valaki próbálkozik csatlakozni az
ön weboldalához ilyen formában:
<pre>
http://mydomain.com
</pre>
az át lesz irányítva a:
<pre>
http://www.mydomain.com
</pre>címre.
Tulajdonképpen az URL maga nem változik és nem lesz átirányítás a html oldalban sem
csak egyszerûen ugyanazokat a html vagy php oldalakat fogja tudni elérni
és ugyanazokat a naplófájlokat használja majd.
<br><br>

<u>3.3. Tiltott aldomain-ek</u><br><br>
Mivel ezeket az aldomain-eket a web-tõl eltérõ szolgáltatásokra
használjuk, ezért a következõ aldomain neveket nem használhatók apache weboldalként:
<ul><li>ftp</li>
<li>pop</li>
<li>smtp</li>
</ul>

<u>3.4. Aldomain-ek törlése</u><br><br>
Önön múlik, hogy törli e azokat a fájlokat amiket az aldomain-je használ.
Az összes fájlt törölheti egy egyszerû ftp kliensprogram segíségével.
Azonban kérem, hogy figyeljen arra, hogy ne törölje anélkül az aldomain fájljait
, hogy magát az aldomain-t törölné a DTC-n keresztül. Máskülönben az Apache web szerver
panaszkodni fog arra, hogy a könyvtár nem létezik a weboldal mégis be van konfigurálva
annak használatára és ez elég bosszantó lenne, amikor legközelebb újraindítjuk az
apache-ot.<br><br>

<u>3.5. Késleltetés aldomain-ek hozzáadása / törlése esetén</u><br><br>
Újra kell indítanunk az Apache webszerverünket ahhoz hogy bekövetkezett módosítások
életbe lépjenek.
Legtöbbször ezt munkanapokon a nap végén tesszük meg, de ha rögtön szüksége van az újraindításra
, akkor kattintson <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]aldomain létrehozás:
kérem indítsák újra az apache-ot most ! \">ide</a>.<br><br>

<u>3.6. Látogatói statisztikák az aldomain-ekhez</u><br><br>
Mivel az összes forgalmat naplózzuk, ezért összesített 12 hónapos statisztikát generálunk
a <a href=\"http://www.mrunix.net/webalizer/\">
webalizer program segítségével</a>. A statisztikákat minden nap
4 órakor készítjük (ez az az idõpont, amikor a legkisebb a forgalom). A statisztikák
elérhetõk a \"/stats\" könyvtár alatt az összes aldomain-re lebontva.
Ez azt jelenti, hogyha ön regisztrálta a következõ domain-t :
<pre>
http://www.mydomain.com
</pre>
akkor az összes statisztika a :
<pre>
http://www.mydomain.com/stats/
</pre>
alatt lesz elérhetõ.
<font size=\"+1\"><u>4. FTP hozzáférések</u></font><br>
<u>4.1. Mit fog ez nekem csinálni ?</u><br><br>
Ahhoz hogy az oldalai mûködjenek önnek fel kell õket tölteni a szerverünkre.
De mivel lehet, hogy nem ön az egyetlen aki az ön weboldalán dolgozik
,ezért szüksége lehet arra hogy több  FTP hozzáférése legyen az weboldalához.
A DTC az az eszköz amivel menedzselheti ezeket a hozzáféréseket és jelszavakat.
<br><br>

<u>4.2. Késleltetés FTP hozzáférés hozzáadása / törlése esetén</u><br><br>
Mivel mi a ProFTP programot használjuk egy különleges modullal, ami lehetõvé teszi
a hozzáférések tárolását a MySql adatbázisunkban, ezért az összes változtatás
ami az FTP hozzáféréseket érinti azonnal végrehajtódik.<br><br>

<u>4.3. Felhasználók korlátozása egy elérési úthoz</u><br><br>
Jelenleg nem tudja korlátozni a felhasználókat úgy, hogy a weboldalának
csak egy részéhez férjenek hozzá . De mi (az adminisztrátorok) meg tudjuk ezt tenni
ha ön kéri egy <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Felhasználó
elérési út korlátoza az FTP hozzáférésben\">e-mail-ben</a>, amiben leírja hogy melyik az érintett
felhasználó és melyik az elérési út amire szüksége van.<br><br>

<u>4.4. Fájcserelésre ne használja és illegális tartalmat kérem NE tároljon az FTP szerverén !</u><br><br>
Az hogy mi tárhelyet biztosítunk önnek az ftp szerverünkön az azért van, hogy
fel tudja tölteni a HTML oldalait. Ez azt jelenti, hogy nincs joga olyan
bináris fájlokat tárolni a szerveren, amik nem az ön tulajdonát képezik.
Amennyiben erre utaló jelet észlelünk a hozzáférését minden elõzetes
figyelmeztetés nélkül megszüntethetjük.<br><br>

<font size=\"+1\"><u>5. Miért lassú az ftp vagy a pop hozzáférés néha ?</u></font><br><br>
Ennek sok oka lehet. Az elsõ hogy nincs elég feltöltési sávszélességünk
és tényleg nagyon sokan töltenek fel egyszerre.
A másik hogy valaki a szervereink \"közelébõl\" tölt fel és elviszi az egész sávszélességet
tehát egy felhasználó használja az egészet mások pedig emiatt nem jutnak elég
sávszélességhezt. Sajnos ez nem rajtunk múlik és nem tudunk ellene semmit tenni.<br><br>
A másik oka az lehet, hogy úgy döntöttünk, hogy limitáljuk a pop, smtp, és ftp
által használható sávszélességet és így a  web böngészés gyorsabb lesz a szerverünkön.
<br><br><br>
</div>
<center>For the GPLHost team,</center><br>
<div align=\"right\"><i><a href=\"mailto:thomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |
|___________\    |    |__________/|____|     /|___________\___________\GPL |
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>
",

	"it" => "
<div align=\"center\" >
  <p>DTC &egrave; un'applicazione opensource su licenza GPL che ti permette di amministrare con semplicit&agrave; il tuo dominio: profili ftp, caselle di posta, sottodomini, reindirizzamenti, database, ecc. Qui di seguito trovi una guida passo passo per usare il tuo pannello.</p>
  </div>
<div align=\"justify\" >
<ul>
<li><a href=\" #posta\" >Caselle di posta</a></li>
<li><a href=\" #sottodomini\" >Sottodomini</a></li>
<li><a href=\" #statistiche\" >Statistiche sottodomini</a></li>
<li><a href=\" #dns\" >Preferenze DNS</a> </li>
<li><a href=\" #ftp\" >Profili FTP</a></li>
<li><a href=\" #database\" >Database</a></li>
<li><a href=\" #mailing\" >Mailing list</a> </li>
<li> <a href=\" #pacchetti\" >Installazione pacchetti</a></li>
</ul>  <h3><a name=\"posta\" id=\"posta\" ></a>1. Caselle di posta </h3>
  <p>
  Per creare una nuova casella di posta clicca sul nome del tuo dominio e quindi su Caselle di posta. Configura la tua casella come indicato nella seguente schermata:</p>
  <p><img src=\"http://itcs.areaserver.it/posta.png\" alt=\"Casella di posta\" width=\"844\" height=\"728\" > </p>
  <p>Tieni spuntata l'opzione &quot;Copia messaggi in locale&quot; per scaricare i messaggi sul tuo programma di posta. Configura il tuo programma di posta elettronica come di seguito:</p>
  <ol>
    <li>Nome utente &raquo;  tuaemail@tuodominio.xx </li>
    <li>Password &raquo; tuapassword </li>
    <li>Server di posta in entrata (POPMAIL o IMAP) &raquo;  mail.tuodominio.xx</li>
    <li>Server di posta in uscita (SMTP) &raquo;  mail.tuodominio.xx</li>
  </ol>
  <p>Nelle preferenze del tuo programma di posta spunta l'opzione &quot;Cancella copia messaggi sul server&quot; per non occupare lo spazio della casella.  Ricorda infine di controllare periodicamente  la posta per non occupare tutto lo spazio della casella. Le modifiche alle caselle email sono pressoch&eacute; immediate: se noti dei rallentamenti o problemi di connessione alla posta puoi <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email account 
validation\" >comunque segnalarcelo. </a></p>
  <p>&nbsp;</p>
  <h3><a name=\"sottodomini\" id=\"sottodomini\" ></a>2. Sottodomini</h3>
<p>
    Per creare un sottodominio del tipo <strong>http://nome.tuodominio.xx</strong> clicca alla voce sottodominio e 
  configura i parametri come indicato nella seguente schermata:</p>
  <p><img src=\"http://itcs.areaserver.it/sottodominio.png\" alt=\"Sottodomini\" width=\"796\" height=\"636\" > </p>
  <p><br>
    Per ogni dominio da te attivato esiste un sottodominio predefinito del tipo <strong>www</strong> per permettere agli utenti di collegarsi al tuo sito anche senza specificare l'indirizzo completo. Pertanto digitando <strong>http://tuodominio.it</strong> si verr&agrave; reindirizzati su <strong>http://www.tuodominio.xx</strong><br>
    Non &egrave; possibile attivare sottodomini con le estensioni <strong>ftp, smtp, pop</strong> perch&eacute; vengono usate dal server Apache per gestire la posta e la connessione ftp.</p>
  <p>    Per modificare un sottodominio clicca sul suo nome e apporta le modifiche necessarie.
  Da qui puoi anche cancellare il sottodominio: <strong>non rimuovere via ftp le cartelle del sottodominio senza prima averlo cancellato dal pannello di controllo. </strong></p>
  <p> Le modifiche ai sottodomini avvengono al riavvio del server Apache: se noti dei rallentamenti o ritardi  nell'attivazione dei sottodomini puoi <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]subdomain account 
validation\" >comunque segnalarcelo. </a></p>
  <p>&nbsp;</p>
  <h3><a name=\"statistiche\" id=\"statistiche\" ></a>3. Statistiche sottodomini </h3>
  <p>
    Tutto il traffico sul tuo dominio viene tracciato dal server attraverso  <a href=\"http://www.mrunix.net/webalizer/\" >
  webalizer</a>. Le statistiche dei vari sottodomini sono raggiungibili  cliccando su &quot;Statistiche&quot; o collegandosi alla cartella &quot;/stats&quot; dei tuoi sottodomini. Ad esempio se vuoi leggere le statistiche del dominio <strong>www.tuodominio.xx</strong> dovrai collegarti all'indirizzo <strong>http://www.tuodominio.xx/stats</strong></p>
  <p><img src=\"http://itcs.areaserver.it/statistiche.png\" alt=\"Statistiche\" width=\"844\" height=\"383\" /> </p>
  <p>&nbsp;</p>
  <h3><a name=\"dns\" id=\"dns\" ></a>4. Preferenze DNS </h3>
  <p>Se vuoi che il dominio sia ospitato su un altro server devi specificare gli indirizzi DNS primario e secondario del server (chiedi al tuo amministratore):</p>
  <p><img src=\"http://itcs.areaserver.it/dns.png\" alt=\"Indirizzi DNS\" width=\"844\" height=\"534\" /> </p>
  <p>&nbsp;</p>
  <h3><a name=\"ftp\" id=\"ftp\" ></a>5.  Profili FTP</h3>
  <p>Dal pannello puoi creare/modificare uno o pi&ugrave; profili ftp per caricare/prelevare file nei tuoi sottodomini:</p>
  <p><img src=\"http://itcs.areaserver.it/ftp.png\" alt=\"profili FTP\" width=\"844\" height=\"405\" /></p>
  <p>Configura il tuo programma FTP  come di seguito:</p>
  <ol>
    <li>Hostname/Server name  &raquo;  www.tuodominio.xx </li>
    <li>Username &raquo; tuonome@tuodominio.xx </li>
    <li>Password &raquo; tuapassword  </li>
    <li>Remote path  &raquo;  /tuodominio.xx/subdomains/www/    </li>
  </ol> 
  <p>&nbsp;</p>
  <h3><a name=\"database\" id=\"database\" ></a>6. Database  </h3>
      <p>Dal pannello puoi creare/modificare fino a 5 database:</p>
      <p><img src=\"http://itcs.areaserver.it/db.png\" alt=\"database\" width=\"844\" height=\"412\" /></p>
   
      <p>Dal pannello di controllo puoi accedere a <a href=\"https://itcs.areaserver.it/index.php?sousrub=phpmyadmin\" >phpMyAdmin</a> con il tuo nome utente e password per gestire in piena libert&agrave; i tuoi database (eseguire query, modificare tabelle, importare file sql, ecc.):</p>
      <p><img src=\"http://itcs.areaserver.it/phpMyAdmin.png\" alt=\"database\" width=\"646\" height=\"485\" /></p>
      <p>&nbsp;</p>
      <h3><a name=\"mailing\" id=\"mailing\" ></a>7. Mailing list </h3>
      <p>Dal pannello puoi creare/modificare fino a 10 mailing list (o liste di distribuzione):</p>
      <p><img src=\"http://itcs.areaserver.it/mailing-list.png\" alt=\"mailing-list\" width=\"844\" height=\"401\" /></p>
      <p><br />
        La mailing list pu&ograve; essere controllata via email o tramite il pannello di controllo.<br />
      Di seguito gli indirizzi ai quali puoi inviare una email per eseguire il rispettivo comando:</p>
      <p> tuamailing-help@www.tuodominio.xx -&gt; restituisce la lista dei comandi pi&ugrave; comuni<br />
        tuamailing-list@www.tuodominio.xx -&gt; restituisce la lista degli iscritti alla lista<br />
        tuamailing-get-N@www.tuodominio.xx -&gt; restituisce il messaggio N della lista<br />
        tuamailing-owner@www.tuodominio.xx -&gt; permette di inviare una email al proprietario della lista<br />
        tuamailing-unsubscribe@www.tuodominio.xx -&gt; elimina l'indirizzo email del mittente dalla lista<br />
        tuamailing-subscribe@www.tuodominio.xx -&gt; iscrive l'indirizzo email del mittente alla lista<br />
        tuamailing-subscribe-digest@www.tuodominio.xx -&gt; iscrive l'indirizzo email del mittente alla versione digest della lista<br />
        tuamailing-subscribe-nomail@www.tuodominio.xx -&gt; iscrive l'indirizzo email del mittente alla versione nomail della lista</p>
      <p>Per ulteriori informazioni visita il sito ufficiale di mlmmj all'indirizzo <a href=\"http://mlmmj.mmj.dk\" >http://mlmmj.mmj.dk</a></p>
      <p>&nbsp;</p>
      <h3><a name=\"pacchetti\" id=\"pacchetti\" ></a>8. Installazione pacchetti </h3>
      <p>Dal pannello puoi installare diverse applicazioni web: cms (content management system), newsletter, carrelli elettronici, forum di discussione, ecc.:</p>
      <p><img src=\"http://itcs.areaserver.it/pacchetti.png\" alt=\"pacchetti\" width=\"844\" height=\"782\" /></p>
      <p> La procedura di installazione &egrave; molto semplice:</p>
      <ol>
        <li>seleziona il database e la password per il pacchetto</li>
        <li>inserisci una email, login e password per l'amministratore          </li>
      </ol>
      <p><img src=\"http://itcs.areaserver.it/pacchetti2.png\" alt=\"pacchetti\" width=\"844\" height=\"663\" /></p>
      <p><br>
    <br>
  </p>
</div>
",
	"nl" => "
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>ONLINE DTC
HELP</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. Wat is DTC</u></font><br><br>
DTC is een programma dat we speciaal voor eindgebruikers
zoals jij hebben gemaakt. Met dit programma neem je het beheer
van je domein in eigen handen. Je kan eigen subdomeinen aanmaken en
eigen e-mail- en ftp accounts instellen.<BR><BR>
Dit programma is uitgegeven onder de <a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence),
wat in zoverre betekend dat je een kopie van de vrijgegeven broncode
mag bezitten, wijzigen en gebruiken zoals je het zelf wilt. Zolang je
het maar verder distribueert met alle veranderingen er in.
Wij (bij GPLHost) geloven in de Vrije Software en we hopen
dat deze participatie in de vrije software anderen aanspoort met dit 
product verder te gaan of ook een stuk vrije software op de markt
te brengen. Wij geloven hier in omdat we zelf alleen maar gebruik maken
van open-source software voor onze diensten en wij het heel normaal vinden
om onze vorderingen met anderen te delen. <BR><BR>
<font size=\"+1\"><u>2. E-mails</u></font><br>
<u>2.1. Wat kun je met dit onderdeel?</u><br><br>
Je kan e-mail accounts aanmaken, wijzigen en verwijderen met dit programma.<br><br>
<u>2.2. Doorverwijzingen en lokaal afleveren.</u><br><br>
Elk e-mail account kan je doorverwijzen naar 1 of meer e-mail adressen.
Dit houd in dat zodra er een bericht voor dit account binnenkomt het gelijk
word doorgestuurd naar de 1 of meer ingevoerde e-mail adressen.
Het vinkje \"lokaal afleveren\" vertelt het systeem of de binnengekomen e-mail
wel of niet lokaal op de harde schijf moet worden gezet. Dit is noodzakelijk om
je e-mail later met een e-mail programma op te kunnen halen of om je e-mail via
de webmail te bekijken. <i> Als je dit doet, moet je niet vergeten periodiek
even te controleren of je mail hebt en dit te verwijderen. Je e-mail account heeft
namelijk een quota waar je niet overheen mag gaan. Dit quota is gebasseerd op
harde schijfruimte.</i><br><br>
<u>2.3. Vertraging tijdens het aanmaken of verwijderen van emailaccounts.</u><br><br>
Wanneer je een e-mail account aanmaakt of verwijdert dat wordt de verandering
niet direct doorgevoerd. Dit kan ook niet omdat we bij elke verandering het 
programma Qmail (onze mailserver) moeten herstarten om de veranderingen op te nemen.
Daarom word de server bijna altijd aan het eind van elke werkdag even herladen.
Indien het echt noodzakelijk is dat de accountverandering DIRECT word doorgevoerd dan 
kun je even een e-mail sturen naar de beheerder door <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email account
validation\">hier</a> te klikken.<br><br>
<font size=\"+1\"><u>3. Subdomeinen</u></font><br>
<u>3.1. Wat kun je met dit onderdeel?</u><br><br>
Dit is een onderdeel van uw website configuratie.
Dit betekent dat uw zelf kan instellen hoe uw domein word
benadert, zoals bijv. :
<pre>
http://Alles.wat.u.wilt.mijndomein.nl
</pre>
<u>3.2. Wat is het standaard subdomein?</u><br><br>
Wanner iemand contact probeert te maken naar een domein
waarvan geen subdomein bekend is, dan zal deze automatisch
naar het subdomein gaan waarvan u hebt gezegd dat dit het
standaard domein moet worden. Dus als u bijvoorbeeld 
instelt dat:
<pre>
www
</pre>
het standaard subdomein is, dan zal iemand die contact probeert
te maken met:
<pre>
http://mijndomein.nl
</pre>
automatisch worden doorverwezen naar:
<pre>
http://www.mijndomein.nl
</pre>.
Het gaat zelfs zover dat de persoon die
<pre>
http://mijndomein.nl
</pre>
niet eens te zien krijgt dat deze pagina op een
andere website staat. De website is zo geconfigureerd
dat deze dezelfde lokatie deelt als het subdomein zo dat
dezelfde html bestanden worden gedeelt en ook de logbestanden.<br><br>
<u>3.3. \"Verboden\" subdomeinen</u><br><br>
Er is een aantal subdomeinen die u niet mag gebruiken.
Dit is so ingesteld om te voorkomen dat u bijvoorbeeld uw web en
mail verkeer door elkaar haalt. Daarom kunt u de volgende subdomeinen niet
aanmaken als website:
<ul>
<li>ftp</li>
<li>pop</li>
<li>smtp</li>
</ul>
<u>3.4. Verwijderen van subdomeinen</u><br><br>
Bij het verwijderen van subdomeinen geldt hetzelfde als het
aanmaken van subdomeinen. Pas zodra de webserver herladen is
dan is het subdomein echt verwijderd. Verder laten we het aan
jou over om alle gegevens van het subdomein echt van de 
harde schijf te verwijderen. Dit kan je doen met een standaard 
FTP programma. Vergeet niet dat je EERST het subdomein met het 
DTC programma moet wissen en pas daarna de bestanden met het FTP programma.
Verwijder je alleen de gegevens op de harde schijf en niet in het
DTC programma dan zal de webserver bij de volgende herstart
niet goed starten. Hiermee benadeel je je andere subdomeinen omdat
deze dan niet meer zichtbaar zijn. Tevens zullen de andere mensen
op deze server er niet echt vrolijk van worden. 
WEES DUS VOORZICHTIG!<br><br>
<u>3.5. Vertraging bij het aanmaken en verwijderen van subdomeinen</u><br><br>
Bij het aanmaken en het verwijderen van subdomeinen zal u enige vertraging
ondervinden. Dit komt omdat alle wijzigingen bekrachtigd moeten worden
door een herstart van de webserver. We proberen dit elke werkdag aan
het einde van de dag te doen. Door dit eens per dag te doen ondervind 
niet iedereen last van een webserver die telkens moet herstarten.
Mocht het echt noodzakelijk zijn om de webserver per direct 
te herstarten dan kun je een e-mail sturen naar
<a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]subdomain creation
: please restart apache now !\">de beheerder</a>.<br><br>
<u>3.6. Statistieken voor jouw websites en subdomeinen</u><br><br>
Omdat al het verkeer naar jouw websites en subdomeinen zijn vastgelegd
in logbestanden is het voor ons mogelijk om statistieken te genereren
over de afgelopen 12 maanden. Het programma wat we hiervoor gebruiken
is <a href=\"http://www.mrunix.net/webalizer/\">webalizer</a>.
De statistieken worden op dit moment elk uur berekend en dan kan je
inzien onder de \"/stats/\" directory op elk subdomein.
Een klein voorbeeld:
Als je het domein:
<pre>
http://www.mijndomein.nl
</pre>
hebt, dan kan je de statistieken vinden onder:
<pre>
http://www.mijndomein.nl/stats/
</pre>
<font size=\"+1\"><u>4. FTP accounts</u></font><br>
<u>4.1. Wat kun je met dit onderdeel?</u><br><br>
Om een website zichtbaar te maken voor iedereen die hem
bezoekt moet je webpagina's op de server zetten. Dit kan met
FTP. Omdat je misschien niet de enige bent die gegevens op de
website mag zetten, hebben we het mogelijk gemaakt voor je om meerdere
FTP accounts aan te maken. DTC is het programma waarmee jij
de FTP accounts kan aanmaken en verwijderen. Je kan zelf de
namen en wachtwoorden bepalen.<br><br>
<u>4.2. Vertraging bij aanmaken / verwijderen FTP accounts?</u><br><br>
Omdat we gebruik maken van een speciale module van het ProFTP pakket, worden alle
wijzigingen direct doorgevoerd. Er zijn geen handelingen van de beheerders
noodzakelijk.<br><br>
<u>4.3. Gebruikers limiteren in hun mogelijkheden</u><br><br>
Op dit moment kunnen we FTP gebruikers niet limiteren binnen
hun domeingebeid. Op het moment dat je iemand FTP toegang geeft
kan deze dus zowel bij de website als de websites van de subdomeinen en 
ook bij de e-mailgegevens.
Indien het ECHT NOODZAKELIJK is dat iemand zich alleen binnen
een bepaald gedeelte van het webdomein zich mag bewegen, dan kun je 
een e-mail sturen naar <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Ask for a
user path change in FTP\">de beheerders</a>. Vergeet
er niet bij te vermelden om welke FTP gebruiker het gaat en bij welk
domein de gebruiker mag.
<u>4.4. Op deze servers word GEEN piraterij en illegale bestandsuitwisseling toegestaan!</u><br><br>
Als wij u toegang bieden tot ruimte op onze ftp servers, dan is dit alleen voor 
het uploaden van bestanden die noodzakelijk zijn voor uw website. Dit betekent dat u geen bestanden mag uploaden
waar u de rechten niet van bezit! <B>PAS DUS OP!!</B> Als je misbruik maakt
van deze situatie dan word je account onmiddelijk en zonder melding vooraf verwijderd.<BR><BR>
<font size=\"+1\"><u>5. Waarom kunnen FTP en POP toegangen soms langzaam zijn?</u></font><br><br>
Daarvoor kunnen we meerdere redenen aanwijzen. De bandbreedte die niet
al te hoog is word verdeeld over het aantal gebruikers dat er op dat
moment gebruik van maakt. Dus zodra jij en bijvoorbeeld 20 andere gebruikers
te gelijk mail aan het ophalen zijn of bestanden aan het uitwisselen voor
de website, dan kan dat dus vertraging opleveren.
<BR><BR>
Daarnaast is een limiet gesteld aan de bandbreedte voor mail en ftp. Hierdoor krijgen
de bezoekers van de websites meer bandbreedte tot hun beschikking en krijgen ze 
uw webpagina's sneller geserveerd. 
<br><br><br>
</div>
<center>Voor het GPLHost team,</center><br>
<div align=\"right\"><i><a href=\"mailto:NOSPAMthomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>
",
	"ru" => "
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>ïÎÌÁÊÎ-ĞÏÍÏİØ
DTC</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. şÔÏ ÔÁËÏÅ DTC</u></font><br><br>
DTC ÜÔÏ ÕÔÉÌÉÔÁ ËÏÔÏÒÕÀ ÍÙ ÎÁĞÉÓÁÌÉ ÄÌÑ ×ÁÓ. ó ÎÅÊ ×Ù ÍÏÖÅÔÅ ËÏÎÔÒÏÌÉÒÏ×ÁÔØ
ÁÄÍÉÎÉÓÔÒÉÒÏ×ÁÎÉÅ ×ÁÛÅÇÏ ÄÏÍÅÎÁ : ×Ù ÍÏÖÅÔÅ ÁÄÍÉÎÉÓÔÒÉÒÏ×ÁÔØ
×ÁÛÉ ÓÕÂÄÏÍÅÎÙ, ĞÏŞÔÙ É æôğ-ÁËËÁÕÎÔÙ.<br><br>
÷ÓÅ ÜÔÉ ÕÔÉÌÉÔÙ ÏĞÕÂÌÉËÏ×ÁÎÙ ĞÏÄ ÌÉÃÅÎÚÉÅÊ <a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence),
ËÏÔÏÒÁÑ ĞÏÄÒÁÚÕÍÅ×ÁÅÔ ŞÔÏ ×Ù ÍÏÖÅÔÅ ÄÅÌÁÔØ ËÏĞÉÉ ÉÓÈÏÄÎÏÇÏ ËÏÄÁ,
ÍÏÄÉÆÉÃÉÒÏ×ÁÔØ ÉÈ
ËÁË ×ÁÍ ÕÇÏÄÎÏ É ÒÁÓÔÒÏÓÔÒÁÎÑÔØ Ó ÜÔÉÍÉ ÉÚÍÅÎÅÎÉÑÍÉ. íÙ ( GPLHost)
ĞÏÏİÒÑÅÍ É 
Õ×ÁÖÁÅÍ Ó×ÏÂÏÄÎÏÅ ğï É ÍÙ ÎÁÄÅÅÍÓÑ ÎÁ ĞÏÎÉÍÁÎÉÅ ÓÏ ÓÔÏÒÏÎÙ ÄÒÕÇÉÈ
ÒÁÚÒÁÂÏÔŞÉËÏ×
íÙ ÄÅÌÁÅÍ ÜÔÏ ĞÏÔÏÍÕ ŞÔÏ ÉÓĞÏÌØÚÕÅÍ ÔÏÌØËÏ ğï Ó ÉÓÈÏÄÎÙÍ ËÏÄÏÍ × ÎÁÛÅÍ
ÈÏÓÔÉÎÇÏ×ÏÍ
ÓÅÒ×ÉÓÅ É ÓŞÉÔÁÅÍ ×ĞÏÌÎÅ ÎÏÒÍÁÌØÎÙÍ ÒÁÓĞÒÏÓÔÒÁÎÑÔØ ÅÇÏ ÄÌÑ ÄÒÕÇÉÈ
ÒÁÚÒÁÂÏŞÉËÏ×.<br><br>
<font size=\"+1\"><u>2. ğÏŞÔÁ</u></font><br>
<u>2.1. şÔÏ ÜÔÏ ÄÅÌÁÅÔ ?</u><br><br>
ó ĞÏÍÏİØÀ ÜÔÏÊ ÕÔÉÌÉÔÙ ×Ù ÍÏÖÅÔÅ ÄÏÂÁ×ÌÑÔØ, ÉÚÍÅÎÑÔØ ÉÌÉ ÕÄÁÌÑÔØ ĞÏŞÔÏ×ÙÅ
ÑİÉËÉ.<br><br>
<u>2.2. ğÅÒÅÎÁÚÎÁŞÅÎÉÅ É ÌÏËÁÌØÎÁÑ ÄÏÓÔÁ×ËÁ</u><br><br>
ğÏŞÔÁ Ó ËÁÖÄÏÇÏ ÑİÉËÁ ÍÏÖÅÔ ÂÙÔØ ĞÅÒÅÎÁÚÎÁŞÅÎÁ ÎÁ ÏÄÉÎ ÉÌÉ ÂÏÌÅÅ ÄÒÕÇÉÈ
ÁÄÒÅÓÏ×, Ô.Å.
ĞÒÉ ĞÏÌÕŞÅÎÉÉ ÓÏÏÂİÅÎÉÑ - ÏÎÏ ĞÅÒÅÓÙÌÁÅÔÓÑ ÎÁ ÏÄÉÎ ÉÌÉ Ä×Á ÁÄÒÅÓÁ.
\"äÏÓÔÁ×ËÁ ÌÏËÁÌØÎÏ\"- ÏÚÎÁŞÁÅÔ ŞÔÏ ÎÅ ×ÓÅ ÓÏÏÂİÅÎÉÑ ÄÌÑ ÜÔÏÇÏ ÑİÉËÁ
ÍÏÇÕÔ ÂÙÔØ ÚÁĞÉÓÁÎÙ ÎÁ ×ÁÛ ÖÅÓÔËÉÊ ÄÉÓË, ĞÏÜÔÏÍÕ ×Ù ÍÏÖÅÔÅ ŞÉÔÁÔØ ÓÏÏÂİÅÎÉÑ
ÉÓĞÏÌØÚÕÑ ĞÏŞÔÏ×ÏÇÏ ËÌÉÅÎÔÁ, ÓÏÅÄÉÎÑÀİÅÇÏÓÑ Ó ×ÁÛÉÍ ÓÅÒ×ÅÒÏÍ.
îÅ ÚÁÂÙ×ÁÊÔÅ ĞÒÏ×ÅÒÑÔØ ×ÁÛÕ ĞÏŞÔÕ, ÅÓÌÉ ÔÒÁÆÉË Õ ×ÁÓ ×ÓÅ-ÔÁËÉ ÅÓÔØ,
ĞÏÔÏÍÕ ŞÔÏ ÎÁ ĞÏŞÔÏ×ÙÅ ÑİÉËÉ ÓÕİÅÓÔ×ÕÀÔ ÏÇÒÁÎÉŞÅÎÉÑ :)<br><br>
<u>2.3. úÁÄÅÒÖËÁ ËÏÇÄÁ ÄÏÂÁ×ÌÑÀÔÓÑ / ÕÄÁÌÑÅÔÓÑ ÁËËÁÕÎÔÙ</u><br><br>
ëÏÇÄÁ ×Ù ÄÏÂÁ×ÌÑÅÔÅ ÉÌÉ ÕÄÁÌÑÅÔÅ ĞÏŞÔÏ×ÙÊ ÑİÉË, ÎÅ ÔÒÅÂÕÅÊÔÅ ÏÔ ÜÔÏÇÏ
ÎÅÍÅÄÌÅÎÎÏÊ
ÒÁÂÏÔÙ : ÎÅÏÂÈÏÄÉÍÏ ÓÄÅÌÁÔØ ÉÚÍÅÎÅÎÉÑ × ÓÉÓÔÅÍÅ, × ĞÏÒÑÄËÅ ŞÔÏÂÙ ×ÁÛÉ ÎÏ×ÙÅ
ÎÁÓÔÒÏÊËÉ ×ÓÔÕĞÉÌÉ 
× ÓÉÌÕ: ÎÅÏÂÈÏÄÉÍÏ ĞÏÔÒÅÂÏ×ÁÔØ ÏÔ Qmail (ÎÁÛÅÇÏ ĞÏŞÔÏ×ÏÇÏ ÓÅÒ×ÅÒÁ)
ŞÔÏÂÙ ÏÎ ĞÅÒÅÚÁÇÒÕÚÉÌ ÂÁÚÕ ĞÏÌØÚÏ×ÁÔÅÌÅÊ.
<br><br>
ó ÔÅŞÅÎÉÅÍ ×ÒÅÍÅÎÉ ÍÙ ĞÒÏ×ÅÒÑÅÍ ×ÓÅ ÉÚÍÅÎÅÎÉÑ × ËÏÎÃÅ ËÁÖÄÙÈ ÒÁÂÏŞÉÈ ÄÎÅÊ,
ÎÏ ÅÓÌÉ ×Ù ÎÕÖÄÁÅÔÅÓØ × ÎÅÍÅÄÌÅÎÎÏÊ ĞÒÏ×ÅÒËÅ, ÎÁÖÍÉÔÅ <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email account 
validation\">here</a>.<br><br>
<font size=\"+1\"><u>3. óÕÂÄÏÍÅÎÙ</u></font><br>
<u>3.1. şÔÏ ÜÔÏ ÔÁËÏÅ ?</u><br><br>
üÔÁ ŞÁÓÔØ ÉÎÔÅÒÆÅÊÓÁ ÄÌÑ ËÏÎÆÉÇÕÒÁÃÉÉ ÄÒÕÇÉÈ ÓÁÊÔÏ×, ŞÔÏ ÏÚÎÁŞÁÅÍ
ŞÔÏ ×Ù ÍÏÖÅÔÅ ÎÁÚÎÁŞÁÔØ ×ÁÛÅÍÕ ÕÚÌÕ ÁÄÒÅÓÁ ×ÒÏÄÅ :
<pre>
http://anything.u.want.mydomain.com
</pre>
<u>3.2. şÔÏ ÔÁËÏÅ ÓÕÂÄÏÍÅÎ ĞÏ ÕÍÏÌŞÁÎÉÀ ?</u><br><br>
ëÏÇÄÁ ÎÅËÏÔÏÒÙÅ ÈÏÔÑÔ ÓÏÅÄÉÎÉÔØÓÑ Ó ×ÁÛÉÍ ÓÁÊÔÏÍ ËÁË 
URL ÂÅÚ ÓÕÂÄÏÍÅÎÁ, ÏÎ ĞÅÒÅÎÁĞÒÁ×ÌÑÅÔÓÑ ÎÁ ÓÕÂÄÏÍÅÎ- ËÏÔÏÒÙÊ ×Ù ×ÙÂÅÒÅÔÅ
ĞÏ ÕÍÏÌŞÁÎÉÀ, ÄÒÕÇÉÍÉ ÓÌÏ×ÁÍÉ ÜÔÏ ÏÚÎÁŞÁÅÔ ŞÔÏ :
<pre>
www
</pre>
ÜÔÏ ÓÕÂÄÏÍÅÎ ĞÏ ÕÍÏÌŞÁÎÉÀ, ĞÏÜÔÏÍÕ ÔÅ, ËÔÏ ĞÙÔÁÅÔÓÑ ÓÏÅÄÉÎÉÔØÓÑ ĞÏ ÁÄÒÅÓÕ:
<pre>
http://mydomain.com
</pre>
ÂÕÄÕÔ ĞÅÒÅÎÁĞÒÁ×ÌÑÔØÓÑ ÎÁ:
<pre>
http://www.mydomain.com
</pre>
æÁËÔÉŞÅÓËÉ- ÁÄÒÅÓ ÓÏÈÒÁÎÑÅÔÓÑ, ÎÅÔ ĞÅÒÅÎÁĞÒÁ×ÌÅÎÉÑ ÎÁ ÓÏÚÄÁÎÎÕÀ ÓÔÒÁÎÉÃÕ, 
ĞÒÏÓÔÏ ×ÅÂÓÁÊÔ Ó ÁÄÒÅÓÏÍ ÏÂÙŞÎÏ ËÏÎÆÉÇÕÒÉÒÕÅÔÓÑ × ÒÁÚÎÙÈ ÍÅÓÔÁÈ \"www\"
ÓÕÂÄÏÍÅÎÁ, ÄÌÑ
ÄÏÓÔÕĞÁ Ë ÏĞÒÅÄÅÌÅÎÎÙÍ html (ÉÌÉ php) ÆÁÊÌÁÍ, É ÏÂİÅÇÏ ÄÏÓÔÕĞÁ Ë ÎÅËÏÔÏÒÙÍ
ÌÏÇ-ÆÁÊÌÁÍ.<br><br>
<u>3.3. úÁĞÒÅİÅÎÎÙÅ ÓÕÂÄÏÍÅÎÙ</u><br><br>
ğÏÔÏÍÕ ŞÔÏ ÍÙ ËÏÎÆÉÇÕÒÉÒÏ×ÁÌÉ ÜÔÉ ÓÕÂÄÏÍÅÎÙ ÄÌÑ ÄÒÕÇÉÈ ÓÅÒ×ÉÓÏ× ŞÅÍ web, 
×Ù ÎÅ ÍÏÖÅÔÅ ÉÓĞÏÌØÚÏ×ÁÔØÓÑ ÓÌÅÄÕÀİÉÅ ÓÕÂÄÏÍÅÎÙ ÄÌÑ ÓÌÅÄÕÀİÉÈ apache ÓÁÊÔÏ×
:
<ul><li>ftp
</li>
<li>pop</li>
<li>smtp</li>
</ul>
<u>3.4. õÄÁÌÅÎÉÅ ÓÕÂÄÏÍÅÎÏ×</u><br><br>
üÔÏ ÚÎÁŞÉÔ ŞÔÏ ×Ù ÕÄÁÌÑÅÔÓÑ ×ÓÅ ÆÁÊÌÙ, ÉÓĞÏÌØÚÕÅÍÙÅ ×ÁÛÉÍ ÓÕÂÄÏÍÅÎÏ×.
÷Ù ÍÏÖÅÔÅ ÕÄÁÌÉÔØ ÜÔÉ ÆÁÊÌÙ, ÉÓĞÏÌØÚÕÑ ÓÔÁÎÄÁÒÔÎÏÇÏ ÆÔĞ-ËÌÉÅÎÔÁ.
îÏ ğïöáìõêóôá ÂÕÄØÔÅ ÏÓÔÏÒÏÖÎÙ ĞÒÉ ÕÄÁÌÅÎÉÉ, É ÕÄÁÌÑÊÔÅ ÔÏÌØËÏ × ÓÌÕŞÁÅ ÎÅ
ÉÓĞÏÌØÚÏ×ÁÎÉÑ
× DTC. ÷ ÓÌÕŞÁÅ ÒÁÂÏÔÙ Apache Ó ÄÉÒÅËÔÏÒÉÅÊ, ËÏÔÏÒÁÑ ÎÅ ÓÕİÅÓÔ×ÕÅÔ, ÎÏ
ÓËÏÎÆÉÇÕÒÉÒÏ×ÁÎÁ
ËÁË ×ÅÂ-ÓÁÊÔ, ÓÁÍÉ ĞÏÎÉÍÁÅÔÅ, ÎÉŞÅÇÏ ÈÏÒÏÛÅÇÏ × ÜÔÏÍ ÎÅÔ, ×ĞÌÏÔØ ÄÏ
ÒÅÓÔÁÒÔÁ.<br><br>
<u>3.5. úÁÄÅÒÖËÁ ËÏÇÄÁ ÄÏÂÁ×ÌÑÀÔÓÑ / ÕÄÁÌÑÀÔÓÑ ÓÕÂÄÏÍÅÎÙ</u><br><br>
îÁÍ ÎÅÏÂÈÏÄÉÍÏ ×ÒÅÍÑ ŞÔÏÂÙ ĞÅÒÅÚÁÇÒÕÚÉÔØ ×ÅÂ-ÓÅÒ×ÅÒ × ĞÏÒÑÄËÅ, ÎÅÏÂÈÏÄÉÍÏÍ,
ŞÔÏÂÙ 
ÉÚÍÅÎÅÎÉÑ ×ÓÔÕĞÉÌÉ × ÓÉÌÕ. 
óÏ ×ÒÅÍÅÎÅÍ, ÍÙ ĞÒÏ×ÅÒÑÅÍ ×ÓÅ ÉÚÍÅÎÅÎÉÑ × ËÏÎÃÅ ËÁÖÄÏÇÏ ÒÁÂÏŞÅÇÏ ÄÎÑ, ÎÏ
ÅÓÌÉ ×ÁÍ ÔÒÅÂÕÅÔÓÑ
ÎÅÍÅÄÌÅÎÎÁÑ ĞÒÏ×ÅÒËÁ, ÎÁÖÍÉÔÅ <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]subdomain creation
: please restart apache now !\">ÚÄÅÓØ</a>.<br><br>
<u>3.6. óÔÁÔÉÓÔÉËÁ ÔÒÁÆÉËÁ ÄÌÑ ×ÁÛÉÈ ÓÕÂÄÏÍÅÎÏ×</u><br><br>
ôÁË ËÁË ×ÅÓØ ×ÁÛ ÔÒÁÆÉË ÆÉËÓÉÒÕÅÔÓÑ, ÍÙ ÓŞÉÔÁÅÍ ÓÔÁÔÉÓÔÉËÕ ÉÓĞÏÌØÚÕÑ <a
href=\"http://www.mrunix.net/webalizer/\">
webalizer</a>. óÔÁÔÉÓÔÉËÁ ÓŞÉÔÁÅÔÓÑ ËÁÖÄÙÊ ÄÅÎØ × 4 ÕÔÒÁ (Ô.Å. ËÏÇÄÁ ÏÂÙŞÎÏ
ÔÒÁÆÉË ÎÅÂÏÌØÛÏÊ), É ÍÏÖÅÔ ÂÙÔØ
ĞÒÏÓÍÏÔÒÅÎÁ × ÄÉÒÅËÔÏÒÉÉ \"/stats\" ËÁÖÄÏÇÏ ÓÕÂÄÏÍÅÎÁ. ô.Å. ÅÓÌÉ ×Ù
ÒÅÇÉÓÔÒÉÒÕÅÔÅ :
<pre>
http://www.mydomain.com
</pre>
×ÓÑ ÓÔÁÔÉÓÔÉËÁ ÓÏÂÉÒÁÅÔÓÑ × :
<pre>
http://www.mydomain.com/stats/
</pre>
<font size=\"+1\"><u>4. æôğ ÁËËÁÕÔÎÙ</u></font><br>
<u>4.1. What will it do ?</u><br><br>
şÔÏÂÙ ×ÁÛÁ ÓÔÒÁÎÉŞËÁ ÒÁÂÏÔÁÌÁ, ÎÅÏÂÈÏÄÉÍÏ ÓÎÁŞÁÌÁ ĞÏÍÅÓÔÉÔØ ÔÕÄÁ ÆÁÊÌÙ.
ôÁË ËÁË ×Ù ÎÅ ×ÓÅÇÄÁ ÍÏÖÅÔÅ ÒÁÂÏÔÁÔØ ÎÁ ×ÁÛÅÍ ÓÁÊÔÅ, ×ÁÍ ÍÏÖÅÔ ĞÏÔÒÅÂÏ×ÁÔØÓÑ
ÂÏÌØÛÅ ŞÅÍ ÏÄÉÎ æôğ-ÁËËÁÕÎÔ ÄÌÑ ÒÁÂÏÔÙ ÎÁ ×ÁÛÅÍ ÓÁÊÔÅ. 
DTC ÜÔÏ ÕÔÉÌÉÔÁ ÄÌÑ ÁÄÍÉÎÉÓÔÒÉÒÏ×ÁÎÉÑ ÜÔÉÈ ĞÏÌØÚÏ×ÁÔÅÌÅÊ É ĞÁÒÏÌÅÊ.<br><br>
<u>4.2. úÁÄÅÒÖËÁ ËÏÇÄÁ ÄÏÂÁ×ÌÑÀÔÓÑ / ÕÄÁÌÑÀÔÓÑ æôğ ÁËËÁÕÎÔÙ</u><br><br>
ôÁË ËÁË ÍÙ ÉÓĞÏÌØÚÕÅÍ ProFTP ÓÏ ÓĞÅÃÉÁÌØÎÙÍ ÍÏÄÕÌÅÍ ÄÌÑ ÈÒÁÎÅÎÉÑ ÚÁĞÉÓÅÊ × 
ÎÁÛÅÊ MySQL ÂÁÚÅ, ×ÓÅ ÉÚÍÅÎÅÎÉÑ ×ÁÛÉÈ ÚÁĞÉÓÅÊ ×ÙĞÏÌÎÑÀÔÓÑ × ÒÅÁÌØÎÏÍ
×ÒÅÍÅÎÉ.<br><br>
<u>4.3. ïÇÒÁÎÉŞÅÎÉÅ ĞÏÌØÚÏ×ÁÔÅÌÀ ÏĞÒÅÄÅÌÅÎÎÏÇÏ ĞÕÔÉ</u><br><br>
÷ ÄÁÎÎÙÍ ÍÏÍÅÎÔ ×Ù ÎÅ ÍÏÖÅÔÅ ÏÇÒÁÎÉŞÉÔØ ÏĞÒÅÄÅÌÅÎÎÏÍÕ ĞÏÌØÚÏ×ÁÔÅÌÀ 
ÄÏÓÔÕĞ Ë ŞÁÓÔÉ ×ÁÛÅÇÏ ÓÁÊÔÁ. îÏ ÍÙ (ÁÄÍÉÎÉÓÔÒÁÔÏÒÙ) ÍÏÖÅÍ ÓÄÅÌÁÔØ ÜÔÏ, 
ÅÓÌÉ ×Ù ĞÒÉÛÌÅÔÅ ÎÁÍ <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Ask for a
user path change in FTP\">ĞÉÓØÍÏ</a>, ĞÏÑÓÎÑÀİÅÅ ËÁËÏÍÕ ĞÏÌØÚÏ×ÁÔÅÌÀ É ŞÔÏ
ÎÁÄÏ 
ÏÇÒÁÎÉŞÉÔØ :)<br><br>
<u>4.4. îÅÔ ĞÉÒÁÔÓÔ×Õ ÉÌÉ ÆÁÊÌÏ×ÏÍÕ ÏÂÍÅÎÕ ÎÁ ÎÁÛÉÈ ÓÅÒ×ÅÒÁÈ !</u><br><br>
åÓÌÉ ÍÙ ĞÒÅÄÏÓÔÁ×ÌÑÅÍ ÍÅÓÔÏ ÎÁ ÎÁÛÉÈ æôğ-ÓÅÒ×ÅÒÁÈ, ÜÔÏ 
ÔÏÌØËÏ ÄÌÑ ÚÁÇÒÕÚËÉ HTML ÓÏÄÅÒÖÁÎÉÑ. üÔÏ ÚÎÁŞÉÔ- ÌÕŞÛÅ ÎÅ ÓÔÏÉÔ ÈÒÁÎÉÔØ
ÄÒÕÇÉÅ ÆÁÊÌÙ
ğÏÖÁÌÕÊÓÔÁ ÏÔÎÅÓÉÔÅÓØ ÓÅÒØÅÚÎÏ, ÍÙ ÍÏÖÅÍ ÚÁËÒÙÔØ ×ÁÛ ÁËËÁÕÎÔ ÂÅÚ
ĞÒÅÄÕĞÒÅÖÄÅÎÉÑ.<br><br>
<font size=\"+1\"><u>5. ğÏŞÅÍÕ ftp ÉÌÉ pop ÉÎÏÇÄÁ ÒÁÂÏÔÁÅÔ ÔÁË ÍÅÄÌÅÎÎÏ
?</u></font><br><br>
âÙ×ÁÅÔ ÍÎÏÇÏ ĞÒÉŞÉÎ. ×Ù ÍÏÖÅÔÅ ÎÅ ÉÍÅÔØ ÎÅÏÂÈÏÄÉÍÏÊ ĞÒÏĞÕÓËÎÏÊ ÓĞÏÓÏÂÎÏÓÔÉ, 
ÉÌÉ ÂÏÌØÛÏÅ ËÏÌÉŞÅÓÔ×Ï ÏÄÎÏ×ÒÅÍÅÎÎÙÈ ĞÏÌØÚÏ×ÁÔÅÌÅÊ × ÓÉÓÔÅÍÅ.
ïÓ×ÏÂÏÖÄÅÎÉÅ ÎÅÏÂÈÏÄÉÍÙÈ ÓÅÒ×ÅÒÏ× ÍÏÖÅÔ ÚÁÎÑÔØ ×ÒÅÍÑ.
îÉŞÅÇÏ ÎÅÌØÚÑ ĞÏÄÅÌÁÔØ..<br><br>
éÎÏÇÄÁ ÂÙ×ÁÅÔ ÍÙ ÍÏÖÅÍ ÏÇÒÁÎÉŞÉÔØ ÔÒÁÆÉË.<br><br><br>
</div>
<center>äÌÑ GPLHost team,</center><br>
<div align=\"right\"><i><a href=\"mailto:NOSPAMthomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>",
	"de" => "
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>DTC ONLINE-HILFE</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. Was ist DTC?</u></font><br><br>
DTC ist eine Software, die wir speziell für Sie entwickelt haben. Mit dieser
Software können Sie Ihre Domains administrieren: Sie können all Ihre
Subdomains, E-Mail- und FTP-Konten verwalten.<br><br>
Dieses Tool wurde unter der <a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence) freigegeben,
das heißt, Sie können eine Kopie des Quellcodes erhalten, modifizieren
und verwenden, solange Sie etwaige Änderungen wieder anderen bereitstellen.
Wir (bei GPLHost) glauben an die freie Softwareentwicklung und glauben,
damit ein gutes Beispiel für andere Softwareentwicklungen zu sein.
Wir wollten Sie nur darauf hinweisen, dass wir einzig und allein
Open-Source Software für unsere Hosting-Services verwenden und
etwaige Weiterentwicklungen natürlich anderen wieder zugänglich
machen.<br><br>
<font size=\"+1\"><u>2. E-Mails</u></font><br>
<u>2.1. Was kann ich damit tun?</u><br><br>
Sie können mit dieser Software E-Mail-Konten hinzufügen, löschen oder
ändern.<br><br>
<u>2.2. Umleitung und lokale Zustellung</u><br><br>
Jedes E-Mail-Konto kann auf eine oder mehrer E-Mail-Adressen umgeleitet
werden. Das heisst, wenn eine Nachricht empfangen wird, wird Sie
automatisch an eine oder zwei E-Mail-Adresse(n) weitergeleitet.
Die Auswahl \"lokale Zustellung\" bestimmt, ob alle Nachrichten
für dieses E-Mail-Konto auf dem Server gespeichert werden sollen,
so dass sie dann die Nachrichten mit einem E-Mail-Programm von diesem
Server abrufen können. Bitte prüfen Sie Ihre E-Mails regelmäßig, 
denn die E-Mail-Konten verbrauchen ebenfalls Ihren Webspace.<br><br>
<u>2.3. Verzögerung, wenn Sie Konten hinzufügen oder löschen</u><br><br>
Wenn Sie ein E-Mail-Konto hinzufügen oder löschen, bedenken
Sie bitte, dass diese Änderung nicht sofort sichtbar ist, denn
wir müssen die Änderungen automatisiert überprüfen, bevor sie aktiv werden können.
Nach spätestens 10 Minuten sind Ihre Änderungen aktiv.<br><br>
<font size=\"+1\"><u>3. Subdomains</u></font><br>
<u>3.1. Was kann ich damit tun?</u><br><br>
Dieser Teil der Kundenoberfläche wird benutzt, um Ihre Domains zu konfigurieren. 
Das heißt, Sie können Ihre Websites in der Form
<pre>
http://alles.was.sie.wollen.meinedomain.de
</pre>
anlegen.<br><br>
<u>3.2. Was ist die Standard-Subdomain?</u><br><br>
Versucht jemand, Ihre Seite mit einer URL ohne Subdomain
aufzurufen, so wird er automatisch zu der Subdomain
umgeleitet, die Sie als Standard eingestellt haben.
Mit anderen Worten: Wenn Sie schreiben:
<pre>
www
</pre>
sei die Standard-Subdomain und jemand verbindet sich zu einer
URL in der Form: 
<pre>
http://meinedomain.de
</pre>,
wird dieser jemand automatisch zu:
<pre>
http://www.meinedomain.de
</pre>
weitergeleitet.
<u>3.3. Reservierte Subdomains</u><br><br>
Folgende Subdomains haben wir für andere Zwecke als
für das Web konfiguriert. Diese können daher nicht
als Sub-Domains für Webseiten benutzt werden:
<ul><li>ftp</li>
<li>pop</li>
<li>smtp</li>
<li>mail</li>
</ul><br><br>
<u>3.4. Löschen von Sub-Domänen</u><br><br>
Wenn Sie eine Sub-Domäne löschen, müssen Sie alle Dateien,
die diese Sub-Domäne benötigt löschen.
Sie können diese mit einem Standard FTP-Client löschen.
Aber VORSICHT: Löschen Sie keine Sub-Domänen Dateien ohne
diese vorher mittels DTC zu entfernen.
Der Apache-Web Server wird sich sonst über ein fehlendes
Verzeichnis beschweren, da die Web-Site noch immer konfiguriert
ist.<br><br>
<u>3.5. Verzögerung beim Hinzufügen/Löschen von Sub-Domänen</u><br><br>
Der Apache Web-Server muss neu gestartet werden, damit Ihre Änderungen
aktiv werden. Meistens wird die Änderung am Ende des Tages durchgeführt.
Nach spätestens 10 Minuten sind Ihre Änderungen aktiv.<br><br>
<u>3.6. Traffic-Statistik für Ihre Subdomains</u><br><br>
Wir berechnen eine Gesamtübersicht Ihres Datenverkehrs über die letzten
12 Monate mittels: <a href=\"http://www.mrunix.net/webalizer/\">
Webalizer</a>. Diese Statistiken werden jeden Tag um 4 Uhr früh
(wenn normalerweise wenig Datenverkehr stattfindet) erstellt und können
unter dem Verzeichnis \"/stats\" eimer jeden Subdomain abgerufen
werden.<br>
Das heißt, wenn Sie folgendes Domain registriert haben:
<pre>
http://www.meinedomain.de
</pre>
sind die Statistiken unter:
<pre>
http://www.meinedomain.de/stats/
</pre>
zu finden.<br><br>
<font size=\"+1\"><u>4. FTP-Konten</u></font><br>
<u>4.1. Was kann ich damit tun?</u><br><br>
Damit Ihre Webseiten aufgerufen werden können, werden Sie die Seiten mit FTP hochladen müssen.
Sie können auch mehr als nur ein FTP-Konto für Ihre Website
anlegen. Mit DTC können Sie diese Konten und Passwörter verwalten.<br><br>
<u>4.2. Verzögerung beim Hinzufügen/Löschen von FTP-Konten</u><br><br>
Ihre Änderungen an FTP-Konten werden in Echtzeit durchgeführt.<br><br>
<u>4.3. Benutzer auf ein Verzeichnis beschränken</u><br><br>
Sie können beim Anlegen eines FTP-Kontos einen Pfad auswählen, auf den 
die Zugriffe des FTP-Benutzers beschränkt werden.<br><br>
<u>4.4. Keine Piraterie oder Dateien-Austausch auf unseren Servern, bitte!</u><br><br>
Der Benutzer-Bereich auf unseren FTP-Servern sollte dazu genutzt werden, damit
Sie ihren HTML Inhalt hochladen können.
Das heisst, bitte keine binären Dateien, für die Sie nicht die Rechte besitzen!
Bei Missbrauch wird Ihr Konto sofort und ohne vorherige Mitteilung geschlossen!<br><br>
</div>
<center>Für das GPLHost Team,</center><br>
<div align=\"right\"><i><a href=\"mailto:thomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>
",

	"zh" => "
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b> DTC ÔÚÏß°ïÖúÎÄµµ</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1.Ê²Ã´ÊÇDTC</u></font><br><br>
DTC ÊÇÒ»¸öÎÒÃÇÎªÄúÌØ±ğÖÆ×÷µÄÒ»¸ö¹¤¾ß¡£Í¨¹ıËü£¬Äú¿ÉÒÔÇáËÉ¹ÜÀíÄúµÄÕ¾µã£º
Äú¿ÉÒÔ¹ÜÀíÄúµÄ¶ş¼¶ÓòÃû£¬µç×ÓÓÊ¼ş£¬ftpÕÊ»§µÈµÈ¡£<br><br>
Õâ¸ö¹¤¾ßÒÑ¾­ÔÚ<a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence)ÉÏ·¢²¼£¬
Õâ¾ÍÒâÎ¶Äú¿ÉÒÔ¿½±´ÕâÌ×³ÌĞòµÄÔ´ÎÄ¼ş£¬Äú¿ÉÒÔËæÒâĞŞ¸ÄºÍÊ¹ÓÃ£¬Ò²¿ÉÒÔ·¢²¼ÄúĞŞ¸Ä¹ıµÄ°æ±¾¡£

   ÎÒÃÇ(GPLHost)ÖÂÁ¦ÓÚÎª¿ªÔ´Èí¼ş×ö³öÒ»µã¹±Ï×£¬Í¬Ê±ÎÒÃÇÒ²Ï£ÍûÎÒÃÇµÄ¾Ù¶¯ÄÜ¹»¹ÄÀø¸ü¶àµÄ¿ª·¢ÈËÔ±²ÎÓë½øÀ´¡£ÎÒÃÇ¾õµÃ£¬ÒòÎªÎÒÃÇ½ö½öÊ¹ÓÃ¿ªÔ´Èí¼şÀ´Ìá¹©ĞéÄâÖ÷»ú·şÎñ£¬ÎÒÃÇÓ¦¸Ã°ÑÎÒÃÇµÄ¿ª·¢³É¹û¹²Ïí³öÀ´¡£<br><br>

<font size=\"+1\"><u>2. Emails</u></font><br>
<u>2.1.ËüÄÜ×öÊ²Ã´£¿</u><br><br>
Ê¹ÓÃÕâ¸ö¹¤¾ß£¬Äú¿ÉÒÔºÜ·½±ãµÃÌí¼Ó¡¢É¾³ı¡¢ĞŞ¸ÄÒ»¸öµç×ÓÓÊ¼şÕË»§¡£<br><br>


<u>2.2.ÓÊ¼ş×ª·¢Óë±¾µØÍ¶µİ</u><br><br>
Ã¿Ò»¸öµç×ÓĞÅÏä¶¼ÄÜ¹»×ª·¢µ½Ò»¸ö»òÕß¶à¸öµç×ÓĞÅÏä£¬Õâ¾ÍÒâÎ¶×Åµ±Ò»·âÓÊ¼şÍ¶µİµ½Õâ¸öµç×ÓĞÅÏäµÄÊ±ºò£¬Ëü»á±»×ª·¢µ½Ò»¸ö»òÕß¶à¸öµç×ÓĞÅÏä¡£\"Í¶µİµ½±¾µØĞÅÏä\"Ñ¡Ïî»á¸æËßÏµÍ³Õâ¸öĞÅÏäÊÕµ½
µÄËùÓĞÓÊ¼şÊÇ·ñĞ´Èë±¾µØ´ÅÅÌ£¬ÕâÑùÄú²ÅÄÜÍ¨¹ıÓÊ¼ş¿Í»§¶ËµÇÂ½µ½ÎÒÃÇµÄÓÊ¼ş·şÎñÆ÷ÔÄ¶ÁÄúµÄÓÊ¼ş¡£Èç¹ûÄúµÄÓÊ¼ş±È½Ï¶àµÄ»°£¬²»ÒªÍüÁË¾­³£¼ì²éĞÂÓÊ¼ş£¬ÒòÎªµç×ÓĞÅÏäÊ¹ÓÃµÄ´æ´¢¿Õ¼äÊÇ°üº¬ÔÚÄúËùÓµÓĞµÄ×Ü¿Õ¼äÖĞµÄ¡£<br><br>

<u>2.3.Ìí¼Ó»òÉ¾³ıÒ»¸öÓÊ¼şÕË»§Ê±µÄÑÓÊ±</u><br><br>
µ±ÄúÌí¼Ó»òÕßÉ¾³ıÒ»¸öÓÊ¼şÕË»§µÄÊ±ºò£¬Çë²»ÒªÆÚÍûËüÄÜ¹»Á¢¼´ÉúĞ§£ºÎÒÃÇ±ØĞë¸æËßQmail(»òÕßÎÒÃÇÊ¹ÓÃµÄÆäËüÓÊ¼şÏµÍ³)ÖØĞÂ¼ÓÔØÓÃ»§Êı¾İ¿â£¬ÕâÊ±ÄúËù×öµÄ¸ü¸Ä²ÅÄÜ¹»ÉúĞ§¡£<br><br>
¶àÊıÇé¿öÏÂ£¬ÎÒÃÇ»áÔÚÃ¿¸ö¹¤×÷ÈÕ½áÊøµÄÊ±ºò²Å»áÈÃËùÓĞµÄ¸ü¸ÄÉúĞ§¡£Èç¹ûÄúĞèÒªÁ¢¼´ÉúĞ§µÄ»°£¬Çëµã»÷<a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email account 
validation\">ÕâÀï</a>¡£<br><br>

<font size=\"+1\"><u>3.×ÓÓòÃû</u></font><br>
<u>3.1.ËüÄÜ×öÊ²Ã´£¿</u><br><br>
ÔÚÕâ²¿·ÖÒ³ÃæÖĞ£¬Äú¿ÉÒÔÅäÖÃÄúµÄÓòÃûĞÅÏ¢£¬ÕâÑùÄú¾Í¿ÉÒÔÓÃÏÂÃæµÄÕâÖÖURLĞÎÊ½³äÊµÄúÍøÕ¾µÄÄÚÈİ£º
<pre>
http://anything.u.want.mydomain.com
</pre>

<u>3.2.Ä¬ÈÏµÄ×ÓÓòÃûÊÇÊ²Ã´£¿</u><br><br>
µ±ÓĞÈËÊ¹ÓÃ²»´ø×ÓÓòÃûµÄURL·ÃÎÊÄúµÄÍøÕ¾µÄÊ±ºò£¬Ò³Ãæ¾Í»áÌø×ªµ½ÄúËùÉèÖÃµÄÄ¬ÈÏµÄ×ÓÓòÃû¡£»»¾ä»°Ëµ£¬Èç¹ûÄúÉèÖÃ£º
<pre>
www
</pre>
×öÎªÄ¬ÈÏµÄ×ÓÓòÃû£¬µ±ÓĞÈËÊ¹ÓÃÏÂÃæµÄURL·ÃÎÊÄúµÄÍøÕ¾µÄÊ±ºò£º
<pre>
http://mydomain.com
</pre>
Ò³Ãæ¾Í»áÌø×ªµ½£º
<pre>
http://www.mydomain.com
</pre>
ÊÂÊµÉÏ£¬Õâ¸öURLÊÇ±»±£ÁôÏÂÀ´µÄ£¬²¢Ã»ÓĞÔÚHTMLÎÄ¼şÖĞÉèÖÃÌø×ª¡£Êµ¼ÊÉÏºÜ¼òµ¥£¬Õâ¸öURLµÄÕ¾µã±»ÅäÖÃÎªÓë\"www\"×ÓÓòÃûÖ¸ÏòÍ¬ÑùµÄÄÚÈİ£¬ËùÒÔ£¬ËüÃÇÖ¸ÏòÁËÍ¬ÑùµÄhtml(»òÕßphp)ÎÄ¼ş£¬¶øÇÒ£¬ËüÃÇ¹²ÏíÒ»¸öÈÕÖ¾ÎÄ¼ş¡£<br><br>

<u>3.3.½ûÖ¹Ê¹ÓÃµÄ×ÓÓòÃû</u><br><br>
ÒòÎªÎÒÃÇÒÑ½«ÏÂÁĞ×ÓÓòÃûÅäÖÃ¸øÆäËü·şÎñ£¬ËùÒÔÄú²»ÄÜ°ÑÏÂÁĞ×ÓÓòÃûÓÃ×÷Ò»¸öApacheÕ¾µã£º
<ul><li>ftp</li>
<li>pop</li>
<li>smtp</li>
</ul>

<u>3.4.É¾³ı×ÓÓòÃû</u><br><br>
ÄúĞèÒª×Ô¼ºÉ¾³ıÄúµÄ×ÓÓòÃûÊ¹ÓÃµÄÎÄ¼ş¡£Äú¿ÉÒÔÊ¹ÓÃ±ê×¼µÄFTP¿Í»§¶ËÀ´É¾³ıÕâĞ©ÎÄ¼ş¡£µ«ÊÇÇëÇ§ÍòĞ¡ĞÄ²»Òª½ö½öÉ¾³ı×ÓÓòÃûÊ¹ÓÃµÄÎÄ¼ş¶ø²»ÔÚDTC¿ØÖÆÃæ°åÖĞÉ¾³ıÕâ¸ö×ÓÓòÃû¡£ÊÂÊµÉÏ£¬Èç¹ûÒ»¸ö×ÓÓòµÄÎÄ¼şÒÑ¾­²»´æÔÚ£¬¶øÔÚApache·şÎñÆ÷µÄÅäÖÃÎÄ¼şÖĞÈÔÈ»ÓĞÕâ¸ö×ÓÓòµÄÅäÖÃµÄ»°£¬Apache·şÎñÆ÷ÈÔÈ»»á½âÎöÕâ¸ö×ÓÓòµÄ¡£¶øÇÒ£¬ÔÚÖØÆğApache·şÎñÆ÷µÄÊ±ºò»á±¨´í¡£<br><br>


<u>3.5.Ìí¼Ó»òÉ¾³ıÒ»¸ö×ÓÓòÊ±µÄÑÓÊ±</u><br><br>
ÎÒÃÇĞèÒªÖØĞÂÆğ¶¯Apache·şÎñÆ÷²ÅÄÜÊ¹ÄúËù×öµÄ¸ü¸ÄÉúĞ§¡£Ò»°ãÇé¿öÏÂ£¬ÔÚÃ¿¸ö¹¤×÷ÈÕµÄ×îºóÊ±¿ÌÎÒÃÇ»áÖØĞÂÆğ¶¯Apache·şÎñÆ÷¡£Èç¹ûÄúĞèÒªÊ¹ÄúËù×öµÄ¸Ä¶¯Á¢¼´ÉúĞ§µÄ»°£¬Çëµã»÷<a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]subdomain creation
: please restart apache now !\">ÕâÀï</a>.<br><br>

<u>3.6.ÄúµÄ×ÓÓòµÄÁ÷Á¿Í³¼Æ</u><br><br>
ÄúËùÓĞµÄÁ÷Á¿¶¼ÊÇÓĞ¼ÇÂ¼µÄ£¬ÎÒÃÇÊ¹ÓÃ<a href=\"http://www.mrunix.net/webalizer/\">
webalizer</a>Í³¼Æ³öÁËÄúËùÓĞµÄ×ÓÓòÔÚ¹ıÈ¥12¸öÔÂÖĞµÄÁ÷Á¿¡£Í³¼Æ»áÔÚÃ¿ÌìÁè³¿4Ê±¿ªÊ¼½øĞĞ(ÕâÊÇÒ»ÌìÖĞ·şÎñÆ÷¸ºÔØ×îµÍµÄÊ±¿Ì),Äú¿ÉÒÔÍ¨¹ıÃ¿¸ö×ÓÓòµÄ\"/stats\"Ä¿Â¼À´²é¿´Í³¼Æ½á¹û¡£Õâ¾ÍÊÇËµ£¬Èç¹ûÄú×¢²áÁË£º
<pre>
http://www.mydomain.com
</pre>
ËùÓĞµÄÍ³¼Æ½á¹û¶¼»á±»Éú³ÉÔÚÕâ¸öÄ¿Â¼£º
<pre>
http://www.mydomain.com/stats/
</pre>

<font size=\"+1\"><u>4.FTPÕËºÅ</u></font><br>
<u>4.1.ËüÄÜ×öÊ²Ã´£¿</u><br><br>
ÎªÁËÈÃÄúµÄÒ³ÃæÄÜ¹»±»·ÃÎÊµ½£¬ÄúÊ×ÏÈĞèÒªÉÏ´«ËüÃÇ¡£¿ÉÄÜÄú²¢ÊÇ²»Î¨Ò»Ê¹ÓÃÄúµÄÕ¾µãµÄÈË£¬ÕâÑùÄúÒ²Ğí¾Í»áĞèÒª¶à¸öFTPÕËºÅ¡£Ê¹ÓÃDTC¿ØÖÆÃæ°å£¬Äú¾Í¿ÉÒÔÇáËÉ¹ÜÀí¶à¸öÕËºÅºÍÃÜÂë¡£<br><br>


<u>4.2.Ìí¼Ó»òÉ¾³ıÒ»¸öFTPÕËºÅÊ±µÄÑÓÊ±</u><br><br>
ÒòÎªÎÒÃÇÅäÖÃProFTPÔËĞĞÔÚÒ»¸öÌØÊâµÄÄ£Ê½ÏÂ£¬Ëü°ÑËùÓĞµÄÕËºÅĞÅÏ¢¶¼´æÔÚÁËMYSQLÊı¾İ¿âÖĞ¡£Äú¶ÔFTPÕËºÅËù×öµÄÈÎºÎ¸ü¸Ä¶¼ÄÜ¹»ÊµÊ±ÉúĞ§¡£<br><br>

<u>4.3.½«ÓÃ»§ÏŞ¶¨ÔÚÖ¸¶¨µÄÄ¿Â¼ÖĞ</u><br><br>
Ä¿Ç°Äú»¹²»ÄÜ½«ÓÃ»§ÏŞ¶¨µ½Ö¸¶¨µÄÄ¿Â¼ÖĞ¡£Èç¹ûÄãÈ·ÊµĞèÒªÕâÃ´×ö£¬Çë·¢ËÍ<a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Ask for a
user path change in FTP\">ÓÊ¼ş</a>¸øÎÒÃÇ£¬¸æËßÎÒÃÇÓÃ»§ÃûºÍÂ·¾¶¼´¿É¡£<br><br>

<u>4.4.Çë²»ÒªÔÚÎÒÃÇµÄ·şÎñÆ÷ÉÏ½øĞĞµÁ°æĞĞÎª»ò´ó¹æÄ£µÄÎÄ¼ş½»»»</u><br><br>
µ±ÎÒÃÇ¸øÄúÌá¹©FTP¿Õ¼äµÄÊ±ºò£¬Õâ½ö½öÊ±ÎªÁË·½±ãÄúÉÏ´«HTMLÎÄ¼ş¡£Çë×¢Òâ£¬Èç¹ûÄúÎ¥·´¹æ¶¨µÄ»°£¬ÎÒÃÇ¿ÉÒÔÔÚ²»ÌáÇ°Í¨ÖªµÄÇé¿öÏÂ¹Ø±ÕÄúµÄÕËºÅ¡£<br><br>

<font size=\"+1\"><u>5.ÎªÊ²Ã´ÓĞÊ±ºòFTP»òÕßPOP»áºÜÂı£¿</u></font><br><br>
ÕâÓĞºÜ¶àÔ­Òò¡£Ê×ÏÈÊÇÎÒÃÇ²¢Ã»ÓĞÌ«¶àµÄÉÏ´«´ø¿î£¬ÓĞÊ±ºò£¬Í¬Ê±»áÓĞºÜ¶àÓÃ»§ÉÏ´«ÎÄ¼ş¡£Àë·şÎñÆ÷±È½Ï½ü(ÍøÂç¾àÀë)µÄÈËÔÚ¶àÊıÊ±¼ä»áÕ¼¾İ±È½Ï¶àµÄ´ø¿î£¬ËùÒÔÁíÍâÒ»Ğ©ÈË¾ÍÖ»ÓĞ±È½ÏÉÙµÄ´ø¿î¿ÉÒÔÊ¹ÓÃ¡£¶ÔÓÚÕâÑùµÄÇé¿ö£¬ÎÒÃÇÒ²ÎŞÄÜÎªÁ¦...<br><br>
ÁíÍâÒ»¸öÔ­ÒòÊÇ£¬ÎÒÃÇÏŞÖÆÁËpop¡¢smtp¡¢ftpµÈ·şÎñµÄ´ø¿î£¬ÕâÑùÎÒÃÇ·şÎñÆ÷ÉÏµÄweb·ÃÎÊËÙ¶ÈÄÜ¸ü¿ìÒ»Ğ©¡£<br><br><br>
</div>
<center>¹ØÓÚGPLHostÍÅ¶Ó</center><br>
<div align=\"right\"><i><a href=\"mailto:thomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>
",

	"pl" => "
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>POMOC ONLINE DTC
</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. Co to jest DTC</u></font><br><br>
DTC jest zestawem narzêdzi do administrowania domenami, kontami e-mail i ftp dla systemów z rodziny UNIX.<br><br>
Narzêdzia te s± oparte na licencji <a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence). Nale¿y do wolnego oprogramowania (jako  GPLHost), i mo¿e byæ rozwijane i u¿ywane przez innych deweloperów.<br>
Nie mo¿e jednak wchodziæ w sk³ad pakietów komercyjnych ( p³atnych ).<br><br>

<font size=\"+1\"><u>2. Poczta elektroniczna</u></font><br>
<u>2.1. Co mo¿na zrobiæ ?</u><br><br>
Mo¿na dodawaæ, modyfikowaæ lub usuwaæ konta e-mail w ramach obs³ugiwanych domen.<br><br>
<u>2.2. Przekierowanie i dostarczanie lokalne</u><br><br>
Ka¿da wiadomo¶æ e-mail mo¿e zostaæ dostarczona lub przekierowana do jednego lub dwóch kont lokalnych.  Znacznik \"dostarczanie lokalne\" powoduje utworzenie skrzynki pocztowej lokalnie na twardym dysku 
i zapis w niej poczty elektronicznej. Konto takie podlega kwotowaniu limitu przydzia³u dysku dla U¿ytkownika.<br><br>
<u>2.3. Dodawanie i usuwanie kont</u><br><br>
Ka¿da czynno¶æ  dodania, usuniêcia lub modyfikacji konta jest wykonywana natychmiast, ale skutek jest widoczny dopiero po prze³adowaniu serwera pocztowego i jego baz danych. Mo¿e up³yn±æ kilka minut zanim serwer dokona odpowiednich zmian.<br><br>
Je¿eli po up³ywie 30 minut zmiany nie bêd± widoczne kliknij <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC] Awaria konta e-mail\"> tutaj</a>.<br><br>

<font size=\"+1\"><u>3. Subdomeny</u></font><br>
<u>3.1. Co mo¿na zrobiæ ?</u><br><br>
Ta czê¶æ interfejsu odpowiada za konfiguracjê twoich subdomen.
Mo¿esz dodaæ swoj± subdomenê w stylu:
<pre>
http://mojasubdomena.mojadomena.com
</pre>
<u>3.2. Która subdomena jest domy¶ln± ?</u><br><br>
Podczas zak³adania domeny automatycznie jest zak³adana subdomena \"www\", ka¿de wywo³anie adresu URL bez subdomeny bêdzie przekierowane na domy¶ln± subdomenê, np. wywo³anie :
<pre>
http://mojadomena.com
</pre>
zostanie przekierowane na:
<pre>
http://www.mojadomena.com
</pre>
<br>
W ustawieniach subdomen mo¿esz za³o¿yæ subdomenê o innej nazwie i ustawiæ j± jako domy¶ln±.
<br><br>
<u>3.3. Zakazane nazwy subdomen</u><br><br>
W zwi±zku z tym, ¿e system wykorzystuje kilka subdomen dla innych us³ug zakazane s± nastêpuj±ce nazwy subdomen:
<ul>
<li>pop</li>
<li>smtp</li>
<li>ftp</li>
</ul>
<u>3.4. Usuwanie subdomen</u><br><br>
Je¶li chcesz usun±æ subdomenê najpierw klientem ftp ¶ci±gnij skrypty html lub php z subdomeny. Nie usuwaj ich tylko skopiuj !!! 
Nastêpnie poprzez panel DTC usuñ subdomenê. W tym kroku równie¿ zostan± automatycznie usuniête skrypty subdomeny.
Je¿eli usuniesz wcze¶niej skrypty kientem ftp to serwer www bêdzie zg³asza³ b³±d i generowa³ kody b³êdów.<br><br>
<u>3.5. Zw³oka po dodaniu / usuniêciu subdomeny</u><br><br>
Serwer www prze³adowuje siê co 10 minut wiêc zmiany po tym czasie bêd± dopiero widoczne.
Je¶li zmiany nie bêd± widoczne po d³u¿szym czasie proszê kliknij <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]B³±d dzia³ania serwera www\">tutaj</a>.<br><br>

<u>3.6. Statystyki twoich subdomen</u><br><br>
Statystyki s± generowane w cyklu 12 miesiêcznym za pomoc± <a href=\"http://www.mrunix.net/webalizer/\">Webalizera</a>. Generowanie nastêpuje codziennie o godz. 4.00 rano ( je¶li wyst±pi³ transfer ) w katalogu \"/stats\" dla ka¿dej subdomeny, o ile w ustawieniach danej subdomeny w³±czyli¶my t± us³ugê. Np.dla wywo³ania URL :
<pre>
http://www.twojhosting.com
</pre>
statystyki dostêpne s± w :
<pre>
http://www.twojhosting.com/stats/
</pre>
<font size=\"+1\"><u>4. Konta FTP</u></font><br>
<u>4.1. Co mo¿na zrobiæ ?</u><br><br>
Je¶li us³uga dzia³a, mo¿esz dokonywaæ uploadu zawarto¶ci swoich subdomen.
Panel DTC umo¿liwia Ci zak³adanie kont FTP, nadawanie loginów i hase³ oraz ustawienie katalogu podstawowego dla konta w ramach swojej domeny.<br>
UWAGA !! Transfery FTP s± logowane i o tyle zmiejszaj± Ci limit miesiêczny transferów.<br><br>

<u>4.2. Zw³oka po dodaniu / usuniêciu konta FTP</u><br><br>
Zw³oka w dzia³aniu serwera FTP nie wystêpuje, dzia³a on w trybie rzeczywistym.<br><br>
Ewentualny b³±d dzia³ania serwera zg³o¶ klikaj±c <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]B³±d dzia³ania FTP\">tutaj</a>, i opisz problem.<br><br>

<u>4.3. Nie umieszczaj pirackich kopii plików !!!</u><br><br>
Taki proceder skutkuje natychmiastowym zablokowaniem ca³ego konta www, ftp i e-mail bez uprzedzenia.
Obligatoryjnie, z mocy prawa natychmiast i automatycznie zostaj± powiadomione odpowiednie s³u¿by.
Nie przys³uguj± Ci prawa z tytu³u reklamacji.<br><br>

<font size=\"+1\"><u>5. Dlaczego czasami us³ugi dzia³aj± wolniej ?</u></font><br><br>
Spowodowane jest to du¿ym obci±¿eniem ³±cza. Taka sytuacja wystêpuje przy czêstym uploadzie plików.
Spowodowane mo¿e byæ równie¿ ogólnym przeci±¿eniem sieci. Dlatego pamiêtaj, równie¿ w twoim interesie le¿y nie zamieszczanie plików do uploadu w swoim serwisie.
Pomy¶l nad zwiêkszeniem limitu transferu miesiêcznego.
Po wykorzystaniu limitu transferu us³ugi napewno bêd± wolniejsze.

<br><br>
To na tyle ...<br><br>
</div>
<center>Zespó³ GPLHost team,</center><br>
<div align=\"right\"><i><a href=\"mailto:thomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i><i>Jêzyk polski i internacjonalizacja<a href=\"mailto:seeb[ at ] twojhosting [ dot ] com\">Sebastian Pachla</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>",
"se" => "TRANS SVENSKA
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>ONLINE DTC
HELP</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. What is DTC</u></font><br><br>
DTC is a tool we made especialy for you. With it, you can take the
control of your domain administration : you can
manage all your subdomains, emails, and ftp accounts.<br><br>
All this tool had been release under the <a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence),
which means that you can have a copy of this interface source
code, modify it and use it as you wish, as long as you redistribute
all thoses changes. We (at GPLHost) believe in the Free
Software effort, and we hope this participation will encourage
other developpements. We consider that because we use only
open-source software for our hosting service, it is normal
to redistribute our developpements.<br><br>

<font size=\"+1\"><u>2. Emails</u></font><br>
<u>2.1. What will it do ?</u><br><br>
You can add, delete or modify a mailbox with this tool.<br><br>

<u>2.2. Redirection and local delivery</u><br><br>
Each mailbox can be redirected to one or more email addresse, which
means that when a message is recieved, it is forwared to one
or tow email adresse(s). The \"deliver localy\" checkbox
tells wether or not all message for this mailbox will be
written on our hard disk, so thenafter you will be able to
read your message using a mail client, connecting to
our server. Don't forget to checkup your mails often if
you have trafic, because the mailbox are included in the
quota<br><br>
<u>2.3. Delay when adding / deleting accounts</u><br><br>
When you add or delete a mail account, don't expect it to
work immediatly : we will have to validate the changes in
the system in order to have your new accounts changes to
take effect : we have to tell Qmail (our mail server) to
reload it's user database.<br><br>
Most of the time, we validate all changes at the end of
each working days, but if you need an immediate validation,
click <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email account 
validation\">here</a>.<br><br>

<font size=\"+1\"><u>3. Subdomains</u></font><br>
<u>3.1. What will it do ?</u><br><br>
This part of the interface is for configurating your
somain's sites, which means that you will be able
to populate your web site with url of the form :
<pre>
http://anything.u.want.mydomain.com
</pre>
<u>3.2. What is the default subdomain ?</u><br><br>
Whe someone trys to contact your web site with an
URL without a subdomain, he is redirected to the
subdomain you said it was the default. In other
words, if you tell that:
<pre>
www
</pre>
is the default subdomain, someone that trys to
connect using an url starting with:
<pre>
http://mydomain.com
</pre>
will be redirected to:
<pre>
http://www.mydomain.com
</pre>
In fact, the URL is kept, and no URL redirection
in a HTML page has been created, but simply, a
website with that URL has been configurated to
the same location of the \"www\" subdomain, so
it accesses the same html (or php) files, and
shares the same log file.<br><br>

<u>3.3. Forbidden subdomains</u><br><br>
Because we have configurated those subdomains for
other services than web, you cannot use the following
subdomains for apache web sites :
<ul><li>ftp</li>
<li>pop</li>
<li>smtp</li>
</ul>

<u>3.4. Deleting subdomains</u><br><br>
It is up to you to delete the files used by your subdomain.
You can delete all the files using a standard ftp client.
But PLEASE take realy care not to delete a subdomain files
without deleting it using DTC. Indeed, the Apache web server
will complain if the directory does not exist but a web site
is configurated for it, and this will be anoying when restarting
apache.<br><br>

<u>3.5. Delay when adding / deleting subdomain</u><br><br>
We will have to restart our Apache web server in order
to have your changes taking effect. Most of the time, we validate all
changes at the end of
each working days, but if you need an immediate validation,
click <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]subdomain creation
: please restart apache now !\">here</a>.<br><br>

<u>3.6. Trafic statistics for your subdomains</u><br><br>
Because all your trafic is loged, we calculate the overall
last 12 month statistics using <a href=\"http://www.mrunix.net/webalizer/\">
webalizer</a>. The statistics are calculated each days at
4 in the morning (this is when there is less trafic), and
can be reach under the \"/stats\" directory on each
subdomains. That means that if you have registerd :
<pre>
http://www.mydomain.com
</pre>
all statistics will be generated under :
<pre>
http://www.mydomain.com/stats/
</pre>

<font size=\"+1\"><u>4. FTP accounts</u></font><br>
<u>4.1. What will it do ?</u><br><br>
To have your page working and running, you have to upload
them. But because you may not be only one to work on your
web site, you may want to have more that one FTP account
for accessing your web site. DTC will be the tool for
managing thoses accounts and passwords.<br><br>

<u>4.2. Delay when adding / deleting FTP accounts</u><br><br>
Because we use ProFTP with a special module for handling accounts in
our MySql database, all changes to your FTP accounts take effect
in realtime.<br><br>

<u>4.3. Limiting user to specified path</u><br><br>
For the moment you cannont limit one user to access to only
a part of your web site. But we (the administrators) can
do it if you ask sending an <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Ask for a
user path change in FTP\">email</a>, telling what user
and what path you need.<br><br>


<u>4.4. No piracy or file exchange on our servers please !</u><br><br>
If we provide a user space on our ftp servers, this is
for you to upload HTML content. This means no binary
files you don't own the rights ! Take care if you abuse,
we could close your accounts without notification.<br><br>

<font size=\"+1\"><u>5. Why ftp or pop is slow, sometimes ?</u></font><br><br>
There are many reasons for that. The first one is because
we don't have many band with for uploading, and sometimes,
there are realy a lot of people uploading. The one that
are closer to our servers take most of the time all that
band width, so one user has most of it, and some have
not. There is nothing we can do about that...<br><br>
Another reason is because we have decided to limit the
band width for pop, smtp, and ftp, so that web browsing
on our server is faster.<br><br><br>
</div>
<center>For the GPLHost team,</center><br>
<div align=\"right\"><i><a href=\"mailto:thomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>
",
	"es" => "
<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>ONLINE DTC
HELP</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. Qu&eacute; es DTC</u></font><br><br>
DTC es una herramienta hecha especialmente para usted.  
Con ella, usted puede tomar el control en la administraci&oacute;n de sus dominios: 
usted puede manejar todos sus subdominios, cuentas de correo y cuentas ftp.<br><br>
Toda esta herramienta ha sido creada bajo la licencia GPL <a
href=\"http://www.gnu.org/\">GPL</a> (licencia pública de Gnu),  
significa que usted puede tener una cópia del código fuente del interfaz, modificarlo
 y utilizarlo como usted desee, siempre que usted redistribuya todos los cambios que haga. 
Nosotros creemos en el esfuerzo del software libre, y esperamos que esta participaci&oacute;n 
anime a otros desarrolladores.  Consideramos que porque utilizamos solamente software libre 
para nuestro servicio de alojamiento, es normal redistribuir nuestros desarrollos.
<br><br>

<font size=\"+1\"><u>2. Correos</u></font><br>
<u>2.1. ¿ Que puedo hacer ?</u><br><br>
Puedes a&ntilde;adir, borrar o modificar tus cuentas de correo electr&oacute;nico con 
esta herramienta.<br><br>

<u>2.2. Redirecci&oacute;n y envio de correo local</u><br><br>
Cada cuenta de correo se puede redirigir a una o más cuentas 
de correo electrónico, esto significa que cuando un mensaje es 
recibido, es enviado a una o dos direcciones de correo. 
El &quot;envio local&quot; indica si 
todos los mensajes para esta cuenta de correo serán guardados 
en nuestro disco duro, asi usted podra leer su mensaje usando 
un cliente de correo, conectando con nuestro servidor.  
No olvide comprobar sus correos a menudo si usted tiene 
tr&aacute;fico, porque este se incluye en la quota de disco.<br><br>
<u>2.3. Retraso al a&ntilde;adir o borrar cuentas de correo</u><br><br>
Cuando se a&ntilde;ade o borra una cuenta de correo, no espere que
funcione inmediatamente : Nosotros tendremos que validar los
cambios en el sistema para que tengan efecto en estas cuentas de correo:
 Tenemos que indicarle a Qmail (El servidor de correo)
Que reinicie su base de datos de usuarios.<br><br>
La mayoria de las veces, los cambios son realizados al final
del dia de trabajo, pero si necesita una validaci&oacute;n inmediata,
pulsa <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]email account 
validation\">aqu&iacute;</a>.<br><br>

<font size=\"+1\"><u>3. Subdominios</u></font><br>
<u>3.1. ¿ Qué puedo hacer ?</u><br><br>
Esta parte del interfaz es para configurar sus dominios, 
esto significa que podr&aacute; poblar su sitio con url's de la forma:
<pre>
http://algo.que.quiera.midominio.com
</pre>
<u>3.2. ¿ Qu&eacute; es el dominio por defecto ?</u><br><br>
Si alguien intenta entrar a un subdominio que no existe,
ser&aacute; redirigido al subdominio que se indica por defecto.
En otras
palabras, si tu llamas a:
<pre>
www
</pre>
para ser el dominio por defecto, si alguien intenta
conectarse usando una url comenzando por:
<pre>
http://midominio.com
</pre>
sera redirigido a
<pre>
http://www.midominio.com
</pre>
En realidad, se mantiene el URL, y no 
se ha creado ninguna redirecci&oacute;n del URL a 
una p&aacute;gina HTML, simplemente, una p&aacute;gina con este 
URL ha sido configurada a la misma localizaci&oacute;n del 
subdominio &quot;www&quot;, por lo que se accede a los mismos 
ficheros html (o php), y comparte el mismo fichero de 
log.<br><br>

<u>3.3. Subdominios prohibidos</u><br><br>
Porque tenemos configurados esos subdominios 
para otros servicios, usted no puede utilizar los 
subdominios siguientes para los sitios web de apache:
<ul><li>ftp
</li>
<li>pop</li>
<li>smtp</li>
</ul>

<u>3.4. Eliminaci&oacute;n de subdominios</u><br><br>
Esto es para suprimir los archivos usados por su subdominio. 
Usted puede suprimir todos los archivos usando un cliente est&aacute;ndar 
de ftp. Pero POR FAVOR tenga cuidado de no suprimir archivos de 
un subdominio sin eliminarlo usando DTC.  De hecho, el servidor web 
de Apache se quejar&aacute; si no existe el directorio pero esta configurado 
para &eacute;l.<br><br>

<u>3.5. Retraso al a&ntilde;adir / eliminar un subdominio</u><br><br>
Tendremos que reiniciar nuestro servidor web Apache para que 
nuestros cambios tengan efecto.  La mayoría de las veces, 
todos los cambios se validan al final de cada dia laboral, 
pero si se necesita una validacion inmediata,
pulse <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]subdomain creation
: please restart apache now !\">aqu&iacute;</a>.<br><br>

<u>3.6. Estad&iacute;sticas de tr&aacute;fico de tus subdominios</u><br><br>
Dado que todo el tr&aacute;fico es registrado, se calcula la media 
total de los ultimos doce meses usando 
<a href=\"http://www.mrunix.net/webalizer/\">
webalizer</a>. Las estadisticas son calculadas cada dia
a las 4 de la ma&ntilde;ana (este es el momento en que hay menor tr&aacute;fico), y 
puede ser visto en el directorio &quot;/stats&quot; de cada subdominio. 
Esto quiere decir que si has registrado :
<pre>
http://www.midominio.com
</pre>
todas las estadisticas seran generadas en :
<pre>
http://www.midominio.com/stats/
</pre>

<font size=\"+1\"><u>4. Cuentas FTP</u></font><br>
<u>4.1. ¿ Que puedo hacer ?</u><br><br>
Para tener su pagina funcionando y andando, usted tiene 
que subir sus ficheros.  Pero como no tiene porque ser  
solamente uno el que va trabajar en su pagina web, usted puede 
querer tener más de una cuenta ftp para tener acceso a su página 
web. DTC ser&aacute; la herramienta para manejar cuentas y sus 
contrase&ntilde;as.<br><br>

<u>4.2. Retraso al a&ntilde;adir / eliminar una cuenta FTP</u><br><br>
Dado que usamos ProFTP con un m&oacute;dulo especial para crear las cuentas
en nuestra base de datos MySQL, todos los cambios realizados en
nuestras cuentas de FTP tomar&aacute;n efecto en tiempo real.<br><br>

<u>4.3. Limitando un usuario a un directorio espec&iacute;fico</u><br><br>
Por ahora no es posible que un usuario cree un acceso limitado 
solo a una parte de su pagina web. Pero nosotros (los administradores)
podemos hacer esto, si lo consultas mandando un <a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Ask for a
user path change in FTP\">email</a>, especificando que usuario y 
que directorio necesitas.<br><br>

<u>4.4. Ninguna pirater&iacute;a o intercambio de archivos en nuestros servidores 
¡ por favor !</u><br><br>
Si proporcionamos un espacio de usuario en nuestros servidores
ftp, es para subir ficheros HTML. Esto quiere decir 
que no uses dicho servidor para alojar ficheros binarios
de los que no tengas permisos. Ten en cuenta que si abusas,
cerraremos tus cuentas sin previo aviso.<br><br>

<font size=\"+1\"><u>5. ¿Por que ftp o pop es lento a veces
?</u></font><br><br>
Hay varias razones para esto. La primera es por no tener
suficiente ancho de banda para subidas, y a veces hay demasiada 
gente subiendo. Quien est&eacute; mas cercano a nuestros servidores 
la mayoría del tiempo usar&aacute; todo el ancho de banda, así que un usuario 
tiene la mayoria, y otros no. No hay nada que podamos hacer con esto...<br><br>
Otro motivo es porque se ha decidido limitar el ancho de 
banda para pop, smtp, y ftp, para que la navegaci&oacute;n por las 
paginas web de nuestro servidor sea mas r&aacute;pida.<br><br><br>
</div>
<center>El equipo GPLHost,</center><br>
<div align=\"right\"><i><a href=\"mailto:NOSPAMthomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>
",
"pt" => "<font face=\"Arial, Verdana\">
<center><font size=\"+2\"><u><b>Ajuda DTC ON-Line
</b></u></font></center><br><br>
<div align=\"justify\">
<font size=\"+1\"><u>1. O que é DTC</u></font><br><br>
O DTC é um utilitário que foi feito a pensar em si, com este voce pode 
controlar o seu dominio : Poderá gerir todos todas os seus subdominios,
e-mails e contas FTP.<br><br>
Tudo isto foi realizado segundo a licença <a
href=\"http://www.gnu.org/\">GPL</a> (Gnu Public Licence), o que quer dizer
que poderá obter o codigo fonte deste interface, podera modifica-lo e usa-lo  
Nos (GPLHost) acreditamos no software open source e esperamos que esta participação 
incorage outros programadores. Considera-mos já que nós usamos apenas software open
source para o serviço de alojamento é normal que façamos a distribuição do nosso 
desenvolvimento .<br><br>
<font size=\"+1\"><u>2. E-mails</u></font><br>
<u>2.1. O que pderá fazer?</u><br><br>
Voce poderá adiconar, elimnar e modificar a sua conta de e-mail
com este utilitário.<br><br>
<u>2.2. Redirecionamento e entrega local</u><br><br>
Cada conta de e-mail pode ser redirecionada para outro endereço de e-mail,
o que quer dizer que quando uma mensagem é recebida, é redirecionada para 
outro ou outros endereços de e-mail. O parametro \"entrega Local\" faz com que 
todos os e-mails sejam escritos no disco rigido, poderá aceder a estas com um 
cliente de e-mail ligando se ao servidor. Não se esqueça de verificar os seus e-mails
ja que o tamanho destes são incluidos na quota de disco da sua conta.<br><br>
<u>2.3. Atraso quando adicionar / eliminar contas </u><br><br>
Quando adicionar ou apagar uma conta não espere que as alterações surjam de imediato:
Todas as acções serão validadas no sistema, depois disto teremos de dizer ao Qmail 
para reiniciar a base de dados dos utilizadores.<br><br>  
A maior parte das vezes, são validadas todas as  alterações no fim de cada dia de trabalho,
caso necessite de uma validação imediata clique  <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]Conta
de e-mail \">aqui</a>.<br><br>
<font size=\"+1\"><u>3. Subdominios</u></font><br>
<u>3.1. O que pderá fazer?</u><br><br>
Esta parte do interface permite a configuração dos seus subdominios.
<pre>
http://qualquercoisa.dominio.pt
</pre>
<u>3.2. O que é o subdominio pre definido ?</u><br><br>
Se alguem tentar aceder a um site sem o sudominio será redirecionado para 
o subdominio predefenido em outras palavras se disser que 
<pre>
www
</pre>
é o sudominio predefinido 
<pre>
http://mydomain.com
</pre>
será redirecionado para
<pre>
http://www.mydomain.com
</pre>
Na realidade o URL é guardado e nenhuma pagina HTML é criada simplesmente 
esta configurado para apontar para o mesmo local do subdominio\"www\" e assim está 
a aceder ao mesmo ficheiro que acederia usando o \"www\", também é partilhad o 
ficheiro de log.<br><br>
<u>3.3. Subdominios Proibidos</u><br><br>
Porque alguns subdominios são usados noutros serviços, 
não são permitidos usar os seguintes subdominios :
<ul><li>ftp</li>
<li>pop</li>
<li>smtp</li>
</ul>
<u>3.4. Apagar subdominios</u><br><br> 
Você poderá apagar qualquer ficheiro ou todos usando um cliente de ftp.
Por favor tome cuidado para não apagar nenhum subdominio sem apagar primeiro no DTC.
O servidor Apache irá reportar um erro ao iniciar.<br><br>
<u>3.5. Atrasos em apagar / adiconar subdominios</u><br><br>
Quando adicionar ou apagar um subdominio não espere que as alterações surjam de imediato:
Todas as acções serão validadas no sistema, depois disto teremos de dizer ao Apache 
para reiniciar com as alterações.<br><br>  
A maior parte das vezes, são validadas todas as alterações no fim de cada dia de trabalho,
caso necessite de uma validação imediata clique  <a href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]sub 
dominio adicionado !\">aqui</a>.<br><br>
<u>3.6. Estatisticas de trafego</u><br><br>
Porque todo o trafego é registado é calculado e registado o trafego dos 12 Meses 
passados usando o <a href=\"http://www.mrunix.net/webalizer/\">
webalizer</a>. As estatísticassão são calculadas em horas de menor trafego, poderá 
aceder a estas através do directorio \" /stats\" em cada subdominio.
Exemplo : 
<pre>
http://www.meudominio.com
</pre>
todas as estatistícas serão acessiveis em 
<pre>
http://www.meudominio.com/stats/
</pre>
<font size=\"+1\"><u>4. Contas FTP</u></font><br>
<u>4.1. O que pderá fazer?</u><br><br>
Para gerir ficheiros da sua pagina é necessário realizar a tranferencia dos ficheiros
através de FTP. O DTC permite gerir varios utilizadores para acederem a partes diferentes do seu site.
.<br><br>
<u>4.2.  Atrasos em apagar / adicionar contas de FTP </u><br><br>
Porque usamos o ProFTPD com um modulo especial todas as alterações são feitas em tempo real.<br><br>
<u>4.3. Limitando um utilizador a uma pasta</u><br><br>
No momento não é possivel limitar um utilizador a uma pasta no DTC, caso 
necessite poderá enviar um e-mail ao administrador para realizar a tarefa clicando 
<a
href=\"mailto:$conf_webmaster_email_addr?subject=[DTC]FTP limite\">aqui</a>, Indicando o utilizador e a pasta.<br><br>
<u>4.4. Politica de ficheiros !</u><br><br>
O espaço desponibilizado no servidor serve para conteudo Html, isto quer dizer que não é permitido
conteudo de binarios que não tenha os direitos, não é permitido qualquer tipo de ficheiros de pirataria, ou  ilegais.
Tenha cuidado porque caso seja detectado algum abuso, a sua conta será fechada sem qualquer tipo de aviso.<br><br>
<font size=\"+1\"><u>5. As vezes o FTP  está lento ?</u></font><br><br>
Existem inumeras razões a mais provavel é que os nossos servidores não tenham
largura de banda suficiente  para o upload e outras vezes existem inumeras pessoas a 
realizar upload. Podendo no entanto ser outra razão alheia que nós não podemos controlar. 
Algumas vezes a largura de banda é limitada para este serviço para permitir navegar nas 
paginas mais rapidamente <br><br><br>
</div>
<center>A equipa GPLHost,</center><br>
<div align=\"right\"><i><a href=\"mailto:thomas[ at ] gplhost [ dot ] com\">Thomas GOIRAND</a></i></div><br>
<pre>   _____       _____________   (c) 2oo3.2oo4     _____  s!   ____  ___|    .___
 _( ___/______(____     /  |______|    |________(    /______(  _/__\___    ___/
|   \___   \_    |/    /   |\    \_    ___   \_    ___   \________   \|    |   
|    |/     /    _____/    |/     /    |/     /    |/     /    |/     /    |   
|___________\    |    |__________/|____|     /|___________\___________\GPL |   
Opensource driven| hosting worldwide  /_____/ 			|HOST.  </pre>
</font>
"
);
?>
