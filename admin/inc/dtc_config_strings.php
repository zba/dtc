<?php
	/**
	* @package DTC
	* @todo translate to any others language
	* @version  $Id: dtc_config_strings.php,v 1.37 2006/05/08 08:47:53 seeb Exp $
	* New arrays for translate menage_products
	* @see /dtc/admin/dtcrm/product_manager.php
	**/
	// added by seeb 8th may 2006 
$txt_cfg_daemon = array (
  "fr" => "TRANS ",
  "en" => "Daemon",
  "hu" => "TRANS ",
  "it" => "TRANS ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Demony ",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );

$txt_cfg_skin_chooser= array (
  "fr" => "TRANS",
  "en" => "DTC Skin chooser",
  "hu" => "TRANS ",
  "it" => "TRANS ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Wybór skórek DTC",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );

// added 7th may 2006 by seeb

$txt_product_name = array (
  "fr" => "Nom ",
  "en" => "Name ",
  "hu" => "TRANS ",
  "it" => "Nome ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Nazwa ",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );

  $txt_product_price = array (
  "fr" => "Prix ",
  "en" => "Price ",
  "hu" => "TRANS ",
  "it" => "Prezzo ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Cena w ",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );
$txt_product_traffic = array (
  "fr" => "Trafic ",
  "en" => "Traffic ",
  "hu" => "TRANS ",
  "it" => "Traffico ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Transfer ",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );
$txt_product_disk= array (
  "fr" => "Disque ",
  "en" => "Disk ",
  "hu" => "TRANS ",
  "it" => "Disco ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Pojemno¶æ ",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );

$txt_product_action = array (
  "fr" => "Action ",
  "en" => "Action ",
  "hu" => "TRANS ",
  "it" => "Azione ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Operacja: ",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );
$txt_product_adddomain= array (
  "fr" => "Ajout de domaine ",
  "en" => "Add domain ",
  "hu" => "TRANS ",
  "it" => "Aggiungi dominio ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Dodaj domenê",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );
$txt_product_period = array (
  "fr" => "Periode ",
  "en" => "Period ",
  "hu" => "TRANS ",
  "it" => "Periodo ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Wa¿no¶æ ",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );         


$txt_product_mail = array (
  "fr" => "Mail ",
  "en" => "Mail ",
  "hu" => "TRANS ",
  "it" => "Mail ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Skrzynki ",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );	

// end of new arrays (added by seeb)
$txt_user_menu_email = array (
  "fr" => "Mon e-mail",
  "en" => "My e-mail",
  "hu" => "TRANS ",
  "it" => "Mia e-mail ",
  "nl" => "TRANS ",
  "ru" => "TRANS ",
  "de" => "TRANS ",
  "zh" => "TRANS ",
  "pl" => "Mój e-mail",
  "es" => "TRANS ",
  "pt" => "TRANS "
  );

$txt_cfg_allowed_dns_transfer_list = array(
	"fr" => "Listez ici les IPs des serveurs DNS abilité a faire des zone transfer séparé par des &quot;|&quot; (pipe)<br>
	        (laissez vide si vous n'avez pas de serveur DNS de backup) :",
        "en" => "List here DNS server IPs allowed to do zone transfers separated by &quot;|&quot; (pipe)<br>
	        (leave blank if you don't have backup DNS server):",
        "hu" => "TRANS List here DNS server IPs allowed to do zone transfers &quot;|&quot;-al elválasztva
	        <br>(hagyja üresen, ha nincs tartalék DNS szervere)",
        "it" => "Lista qui gli indirizzi IP dei servers DNS abilitati 
a fare zone transfers separati da &quot;|&quot; (pipe)<br> (lasciare 
vuoto se non si hanno server DNS di backup):",
        "nl" => "TRANS List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
                (leave blank if you don't have backup MX server):",
        "ru" => "TRANS List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
                (leave blank if you don't have backup MX server):",
        "de" => "TRANS List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
                (leave blank if you don't have backup MX server):",
        "zh" => "TRANS List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
                (leave blank if you don't have backup MX server):",
        "pl" => "Lista serwerów DNS (adresy IP), dopuszczone do transferu stref odzielone przez &quot;|&quot; (pipe)<br>
	        (Pozostaw puste jesli nie masz zapasowych serwerów DNS):",
        "es" => "Escribe los servidores MX de respaldo separados por &quot;|&quot; (pipeline)<br>
                (leave blank if you don't have backup MX server):",
		"pt" => "Lista de IPs dos servidores de DNS com permissões para transferir zonas, separados  por  &quot;|&quot; (pipe)<br>
		(deixe em branco se não tem nenhum servidor de DNS secundário) :"
	);

$txt_backup_mx_servers = array(
        "fr" => "Listez ici vos serveur MX de backup séparé par des &quot;|&quot; (pipe)<br>
	        (laissez vide si vous n'avez pas de serveur MX de backup) :",
        "en" => "List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
        	(leave blank if you don't have backup MX server):",
        "hu" => "Sorolja fel a tartalék mx szervereit &quot;|&quot;-al elválasztva
        	<br>(hagyja üresen, ha nincs tartalék mx szervere)",
        "it" => "Lista qui i tuoi servers MX di backup separati da 
&quot;|&quot; (pipe)<br>(lasciare vuoto se non si hanno server MX 
di backup):",
        "nl" => "TRANS List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
                (leave blank if you don't have backup MX server):",
        "ru" => "TRANS List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
                (leave blank if you don't have backup MX server):",
        "de" => "TRANS List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
                (leave blank if you don't have backup MX server):",
        "zh" => "TRANS List here your backup MX servers separated by &quot;|&quot; (pipe)<br>
                (leave blank if you don't have backup MX server):",
        "pl" => "Lista serwerów MX odzielona przez  &quot;|&quot; (pipe/rurka)<br>
                (Pozostaw puste je¶li nie masz zapasowych MX'ow-serwerów poczty):",
        "es" => "Escribe los servidores MX de respaldo separados por &quot;|&quot; (pipeline)<br>
                (leave blank if you don't have backup MX server):",
		"pt" => "Lista dos servidores de backup de e-mail (MX) separados por &quot;|&quot; (pipe)<br>
				(deixe em branco se não tem nenhum servidor de backup de e-mail) :"
		);

$txt_cfg_use_des_or_blowfish = array(
"fr" => "Type d'encryption utilisé pour se connecter au serveur Tucows :",
"en" => "Type of encryption for connecting to Tucows server:",
"hu" => "A titkosítás típusa, ha a Tucows szerverhez kapcsolódunk:",
"it" => "Tipo di encryption usata per connettersi al server Tucows:",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "Klucz blowfish do serwera Tucows:",
"es" => "Tipo de encriptación para conectar al servidor Tucows ",
"pt" => "Tipo de cifra utilizado para se ligar ao servidor Tucows "
);


$txt_cfg_registry_api_title = array(
"fr" => "Configuration de l'API d'enregistrement de nom de domaine",
"en" => "Domain name registry API configuraiton",
"hu" => "Domain név regisztrációs API beállítása",
"it" => "Configurazione delle API di registrazione dei nomi a dominio",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "Konfiguracja API do rejestracji domen",
"es" => "Configuración del API para el registro de nombres",
"pt" => "Nome do dominio do API para o registro de nomes"
);

$txt_cfg_use_test_or_live = array (
"fr" => "Utiliser le serveur LIVE (et non le test) :",
"en" => "Use the LIVE server (and not the test one) :",
"hu" => "Az éles szervert használja (nem a tesztet) : ",
"it" => "Usa il LIVE server (e non il test):",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "U¿ywaj rzeczywistego serwera (nie testuj niczego)",
"es" => "Usar el servidor LIVE (no el de pruebas)",
"pt" => "Usar o Servidor Live (não o de testes)"
);

$txt_cfg_tucows_username = array(
"fr" => "Votre nom d'utilisateur SRS :",
"en" => "Your SRS username:",
"hu" => "Az SRS felhasználóneve:",
"it" => "Tuo username SRS: ",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "Twój login SRS",
"es" => "Tu nombre SRS",
"pt" => "O seu utilizador SRS :"
);

$txt_cfg_tucows_test_server_key = array(
"fr" => "Votre clef d'accès pour le serveur de test :",
"en" => "Your key to access the test server:",
"hu" => "A teszt szerver hozzáférési kulcsa: ",
"it" => "Tua chiave di accesso al test server: ",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "Klucz do serwera testowego",
"es" => "Clave de acceso al servidor de pruebas",
"pt" => "Palavra chave para aceder ao servidor de testes :"
);

$txt_cfg_tucows_live_server_key = array(
"fr" => "Votre clef d'accès pour le serveur LIVE :",
"en" => "Your key to access the LIVE server:",
"hu" => "Az éles szerver hozzáférési kulcsa: ",
"it" => "Tua chiave di accesso al server LIVE: ",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "Klucz dostêpowy do serwera LIVE",
"es" => "Clave de acceso al servidor LIVE",
"pt" => "Palavra chave para aceder ao servidor LIVE :"
);

$txt_cfg_registryapi_menu_entry = array(
"fr" => "Enregistrement de domaine",
"en" => "Domain name registration",
"hu" => "Domain név regisztráció",
"it" => "Registrazione nome a dominio",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "Rejestracja domeny",
"es" => "Registro de dominio",
"pt" => "Registo de dominio"
);

$txt_cfg_backup_and_mx_menu_entry = array(
"fr" => "Serveurs MX et NS de secours",
"en" => "MX and NS backup servers",
"hu" => "MX és NS tartalék szerverek",
"it" => "Servers MX e NS di backup",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "Serwery zapsowe poczty i DNS",
"es" => "Servidores de respaldo MX y NS",
"pt" => "Servidores de backup MX e NS"
);

$txt_cfg_ip_and_network = array(
"fr" => "Addresses IP et réseaux",
"en" => "IP addresses and network",
"hu" => "IP címek és hálózat",
"it" => "Indirizzi IP e network",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "Adres IP i sieci ",
"es" => "Dirección IP y red",
"pt" => "Endereço de IP e rede"
);

$txt_cfg_general_menu_entry = array(
"fr" => "Générale",
"en" => "General",
"hu" => "Általános",
"it" => "Generale",
"nl" => "TRANS ",
"ru" => "TRANS ",
"de" => "TRANS ",
"zh" => "TRANS ",
"pl" => "G³ówne ",
"es" => "General",
"pt" => "Geral"
);


$txt_cfg_paypal_use_sandbox = array(
	"fr" => "Utiliser le serveur de test sandbox :",
	"en" => "Use the sandbox test server:",
	"hu" => "A kövezkezõ \"sandbox\" test szervert használja:",
	"it" => "Utilizza il sandbox test server:",
	"nl" => "TRANS Use the sandbox test server:",
	"ru" => "TRANS Use the sandbox test server:",
	"de" => "TRANS Use the sandbox test server:",
	"zh" => "TRANS Use the sandbox test server:",
	"pl" => "Uzyj sandbox'a do testowania:",
	"es" => "Usar el servidor de pruebas sandbox:",
	"pt" => "Utilizar o servidor de testes \"sandbox\" :"
	);


$txt_cfg_paypal_sandbox_email = array(
"fr" => "Email du compte de test PayPal (sandbox) :",
"en" => "PayPal test account email (sandbox):",
	"hu" => "PayPal teszt hozzáférés email címe (sandbox):",
	"it" => "Email dell'account di test PayPal (sandbox):",
	"nl" => "TRANS PayPal test account email (sandbox):",
	"ru" => "TRANS PayPal test account email (sandbox):",
	"de" => "TRANS PayPal test account email (sandbox):",
	"zh" => "TRANS PayPal test account email (sandbox):",
	"pl" => "Konto testowe Paypal (sandbox):",
	"es" => "Cuenta de correo de pruebas de paypal (sandbox):",
	"pt" => "E-mail da conta de teste Paypal (sandbox) :"
	);


$txt_cfg_allow_following_servers_to_list = array(
	"fr" => "Autoriser les servers suivant a lister les domaines pour les backuper : ",
	"en" => "Allow those servers to list this server domain names for doing backup:¡¡",
	"hu" => "A következõ szerverek backup céljából hozzáférhetnek az ezen a szerveren létezõ domain nevekhez:",
	"it" => "Autorizza i seguenti servers i nomi a dominio di questo server per effettuare backup: ",
	"nl" => "TRANS Allow the following servers to list this server domain names for doing backup: ",
	"ru" => "TRANS Allow the following servers to list this server domain names for doing backup: ",
	"de" => "Folgende Server autorisieren, den Server Domain Name bei Backups einzuschliessen: ",
	"zh" => "ÔÊÐíÏÂÁÐ·þÎñÆ÷Á¬½ÓºÍÅÅÁÐÓòÃû±í£º",
	"pl" => "Pozwól temu serwerowi na wykonanie backup-u domen: ",
	"es" => "Autorizar a los siguientes servidores a añádir este servidor de dominios para realizar copias de seguridad: ",
	"pt" => "Autorizar estes servidores a aceder ao servidor de nomes para realizar cópias de segurança"
	);

$txt_cfg_make_request_to_server_for_update = array(
	"fr" => "Prévenir les serveurs suivant quand un domaine est ajouté ou supprimer : ",
	"en" => "Tell the following servers when a domain is added or removed : ",
	"hu" => "Értesítse a következõ szervereket, ha egy domain hozzáadásra vagy törlésre kerül : ",
	"it" => "Informa i seguenti servers quando un dominio viene aggiunto o rimosso : ",
	"nl" => "TRANS Tell the following servers when a domain is added or removed : ",
	"ru" => "TRANS Tell the following servers when a domain is added or removed : ",
	"de" => "Folgende Server informieren, wenn eine Domaine hinzugefügt oder gelöscht wird: ",
	"zh" => "µ±ÓòÃûÌí¼Ó»òÉ¾³ýÊ±Í¨ÖªÏÂÁÐ·þÎñÆ÷£º",
	"pl" => "Powiadamiaj serwery o dodaniu b±dz usuniêcu domeny : ",
	"es" => "Avisar a los siguientes servidores cuando un dominio es añadido o eliminado : ",
	"pt" => "Emitir um aviso aos seguintes servidores quando um dominio é eliminado : "
	);

$txt_cfg_make_request_to_server_mx_update = array(
	"fr" => "Prévenir les serveurs suivant quand un email est ajouté ou supprimer : ",
	"en" => "Tell the following servers when an email is added or removed : ",
	"hu" => "Értesítse a következõ szervereket, ha egy email hozzáadásra vagy törlésre kerül : ",
	"it" => "Informa i seguenti servers quando un email viene aggiunto o rimosso : ",
	"nl" => "TRANS Tell the following servers when a email is added or removed : ",
	"ru" => "TRANS Tell the following servers when a email is added or removed : ",
	"de" => "Folgende Server informieren, wenn eine email hinzugefügt oder gelöscht wird: ",
	"zh" => "å‘Šè¯‰ä»¥ä¸‹æœåŠ¡å™¨ç”µå­é‚®ä»¶å¢žåŠ æˆ–è¢«åŽ»é™¤",
	"pl" => "Zawiadom nastepuj±ce serwery o skasowaniu b±dz usuniêciu poczty: ",
	"es" => "Avisar a los siguientes servidores cuando un email es añadido o eliminado : ",
	"pt" => "Emitir um aviso aos seguintes servidores quando uma conta de e-mai e adicionada ou removida : "
	);

$txt_cfg_act_as_backup_mail_server = array(
	"fr" => "Ce server sera serveur de backup mail pour les serveurs suivants : ",
	"en" => "Act as backup mail server for the following servers: ",
    "hu" => "Tartalék mail szerverként viselkedjen a következõ szervereknek : ",
	"it" => "Usa come backup mail server per i seguenti servers: ",
	"nl" => "TRANS Act as backup mail server for the following servers: ",
	"ru" => "TRANS Act as backup mail server for the following servers: ",
	"de" => "Als Backup Mail Server für folgende Server verwenden: ",
	"zh" => "¶ÔÏÂÁÐ·þÎñÆ÷×÷Îª±¸·ÝÓÊ¼þ·þÎñÆ÷: ",
	"pl" => "Aktywuj kopie poczty na nastepuj±ce serwery: ",
	"es" => "Actuar como servidor de correo de respaldo para los siguientes servidores: ",
	"pt" => "Actuar como um servidor de backup de e-mail para os seguintes servidores : " 
	);

$txt_cfg_act_as_backup_dns_server = array(
	"fr" => "Ce server sera serveur de backup DNS pour les serveurs suivants : ",
	"en" => "Act as backup DNS server for the following servers: ",
	"hu" => "Tartalék DNS szerverként viselkedjen a következõ szervereknek: ",
	"it" => "Usa come backup DNS server per i seguenti servers: ",
	"nl" => "TRANS Act as backup DNS server for the following servers: ",
	"ru" => "TRANS Act as backup DNS server for the following servers: ",
	"de" => "Als Backup DNS Server für folgende Server verwenden: ",
	"zh" => "¶ÔÏÂÁÐ·þÎñÆ÷×÷Îª±¸·ÝDNS·þÎñÆ÷: ",
	"pl" => "Aktywuj kopie serwerow  DNS na nastepuj±ce serwerys: ",
	"es" => "Actuar como servidor de DNS de respaldo para los siguientes servidores: ",
	"pt" => "Actuar como um servidor de backup de DNS para os seguintes servidores : "
	);

$txt_cfg_use_paypal = array(
	"fr" => "Utiliser paypal : ",
	"en" => "Use paypal: ",
	"hu" => "A paypal-t használja: ",
	"it" => "Usa paypal: ",
	"nl" => "TRANS Use paypal: ",
	"ru" => "TRANS Use paypal: ",
	"de" => "Paypal verwenden: ",
	"zh" => "Ê¹ÓÃ paypal: ",
	"pl" => "U¿yj karty p³atniczej : ",
	"es" => "Utilizar Paypal: ",
	"pt" => "Utilizar Paypal : "
	);

$txt_cfg_paypal_autovalid = array(
	"fr" => "Valider les nouveaux compte si payé : ",
	"en" => "Validate new account if payed: ",
	"hu" => "Akkor aktiválja az új hozzáférést, ha az ki van fizetve: ",
	"it" => "Autorizza il nuovo account se ha pagato: ",
	"nl" => "TRANS Validate new account if payed: ",
	"ru" => "TRANS Validate new account if payed: ",
	"de" => "Neue Konten validieren, wenn Zahlung erfolgt: ",
	"zh" => "Èç¹ûÒÑ¸¶¿îÐÂÕÊ»§ÉúÐ§: ",
	"pl" => "Sprawdzanie konta p³atno¶ci : ",
	"es" => "Validar nueva cuenta si ha sido pagada: ",
	"pt" => "Validar novas contas quando estas estejam pagas : "
	);

$txt_cfg_paytitle = array(
	"fr" => "Configuration du paiement sécurisé",
	"en" => "Secure payment configuration",
	"hu" => "A biztonságos fizetés beállításai",
	"it" => "Configurazione del pagamento sicuro: ",
	"nl" => "TRANS Secure paiment configuration: ",
	"ru" => "TRANS Secure paiment configuration: ",
	"de" => "Secure payment Konfiguration: ",
	"zh" => "°²È«¸¶¿îÅäÖÃ: ",
	"pl" => "Konfiguracja p³atno¶ci elektroniczych",
	"es" => "Configuración del pago seguro: ",
	"pt" => "Configuração de pagamento seguro"
	);

$txt_cfg_paypal_email = array(
	"fr" => "Email du compte business PayPal : ",
	"en" => "PayPal business account email: ",
	"hu" => "A PayPal üzleti hozzáférés e-mail címe: ",
	"it" => "Email dell'account business PayPal: ",
	"nl" => "TRANS PayPal business account email: ",
	"ru" => "TRANS PayPal business account email: ",
	"de" => "E-mail Paypal Geschäftskonto: ",
	"zh" => "PayPal ÉÌÎñÕÊ»§ÎÄ¼þ: ",
	"pl" => "Biznesowe konto p³atno¶ci e-mail : ",
	"es" => "Email de la cuenta de negocio de Paypal: ",
	"pt" => "Conta de e-mail Paypal responsável pelos pagamentos : "
	);

$txt_cfg_paypal_ratefee = array(
	"fr" => "Pourcentage commissionaire PayPal : ",
	"en" => "PayPal fee rate: ",
	"hu" => "PayPal költség ráta: ",
	"it" => "Percentuale commissione PayPal: ",
	"nl" => "TRANS PayPal fee rate: ",
	"ru" => "TRANS PayPal fee rate: ",
	"de" => "PayPal Kommissionen: ",
	"zh" => "PayPal ´¿·ÑÓÃ£¥: ",
	"pl" => "Procent prowizji PayPal : ",
	"es" => "Porcentaje de comisión de Paypal: ",
	"pt" => "Percentagem da comissão do Paypal : "
	);

$txt_cfg_paypal_flatfee = array(
	"fr" => "Commission fixe PayPal : ",
	"en" => "PayPal flat rate: ",
	"hu" => "PayPal alap ráta: ",
	"it" => "Commissione fissa PayPal: ",
	"nl" => "TRANS PayPal flat rate: ",
	"ru" => "TRANS PayPal flat rate: ",
	"de" => "PayPal Festpreis: ",
	"zh" => "PayPal ×Ü·ÑÓÃ£¥: ",
	"pl" => "Wska¼nik liniowy PayPal : ",
	"es" => "Comisión fija de Paypal: ",
	"pt" => "Taxa fixa do Paypal : "
	);

$txt_cfg_new_chroot_path_path = array(
	"fr" => "Chemin du disque cgi-wrapper (chroot pour SBOX) : ",
	"en" => "Path of the cgi-wrapper disk (chroot for SBOX): ",
	"hu" => "A cgi-wrapper lemez elérési útja (chroot az SBOX-nak): ",
	"it" => "Path del disco cgi-wrapper (chroot for SBOX): ",
	"nl" => "TRANS Path of the cgi-wrapper disk (chroot for SBOX): ",
	"ru" => "TRANS Path of the cgi-wrapper disk (chroot for SBOX): ",
	"de" => "Path für CGI-Wrapper Laufwerk (Chroot für SBOX): ",
	"zh" => "Â·¾¶ (chroot for SBOX): ",
	"pl" => "¦cie¿ka do katalogu CGI-Wrapper (chroot SBOX) : ",
	"es" => "Ruta del cgi-wrapper (chroot para SBOX): ",
	"pt" => "Caminho do disco cgi-wrapper (chroot para SBOX) : "
	);

$txt_cfg_main_software_config =array(
	"fr" => "Configuration principale de DTC",
	"en" => "Main software configuration",
	"hu" => "Fõ szoftver konfiguráció",
	"it" => "Configurazione principale di DTC",
	"nl" => "Belangrijke software  configuratie",
	"ru" => "ëÏÎÆÉÇÕÒÁÃÉÑ ÃÅÎÔÒÁÌØÎÏÊ ÓÉÓÔÅÍÙ",
	"de" => "Allgemeine Konfiguration",
	"zh" => "Ö÷ÒªÅäÖÃDTC",
	"pl" => "Adresy IP i sieci",
	"es" => "Configuración principal",
	"pt" => "Configuração principal"
	);

$txt_cfg_general =array(
	"fr" => "General",
	"en" => "General",
	"hu" => "Általános",
	"it" => "Generale",
	"nl" => "Algemeen",
	"ru" => "ïÓÎÏ×ÎÙÅ",
	"de" => "Allgemein",
	"zh" => "Ö÷Òª",
	"pl" => "G³ówne",
	"es" => "General",
	"pt" => "Geral"
	);

$txt_cfg_demo_version =array(
	"fr" => "Version de demo :",
	"en" => "Demo version:",
	"hu" => "Demo verzió:",
    "it" => "Versione demo:",
	"nl" => "Demo versie",
	"ru" => "äÅÍÏ-×ÅÒÓÉÑ",
	"de" => "Demo Version",
	"zh" => "ÊÔÓÃ°æ±¾",
	"pl" => "Wersja DEMO : ",
	"es" => "Versión de Demostración",
	"pt" => "Versão de demonstração :"
	);

$txt_cfg_session_expir_time = array(
	"fr" => "Expiration des sessions utilisateur (mn):",
	"en" => "User session expire time (mn):",
	"hu" => "A felhasználói munkamenet lejárati ideje (perc):",
	"it" => "Tempo scadenza della sessione utente (mn):",
	"nl" => "TRANS User session expire time (mn):",
	"ru" => "TRANS User session expire time (mn):",
	"de" => "Expire Time für User Sitzung (min):",
	"zh" => "³¬Ê±²Ù×÷£¨·ÖÖÓ£©",
	"pl" => "Czas sesji (min) :",
	"es" => "Caducidad de la sesión de usuario (min):",
	"pt" => "Duração para a expiração da sessão (min):"
	);

$txt_cfg_use_multiple_ip =array(
	"fr" => "Utiliser plusieurs adresses IP :",
	"en" => "Use multiple IP:",
	"hu" => "Többszörös IP címet használ:",
        "it" => "Usa IP multipli:",
	"nl" => "!TRANSLATE Use multiple IP",
	"ru" => "éÓÐÏÌØÚÏ×ÁÔØ ÎÅÓËÏÌØËÏ IP",	
	"de" => "Mehrere IP Adressen verwenden:",
	"zh" => "Ê¹ÓÃ¶àÖÖIP:",
	"pl" => "U¿yj adresu IP w trybie multi :",
	"es" => "Usar multiples IPs",
	"pt" => "Utilizar vários IPs"
	);

$txt_cfg_use_cname_for_subdomains =array(
	"fr" => "Employez CNAME au lieu du disque de A pour des subdomains:",
	"en" => "Use CNAME instead of A record for subdomains:",
	"hu" => "TRANS - Use CNAME instead of A record for subdomains:",
        "it" => "Usa CNAME anzichè l'annotazione di A per i 
subdomains:",
	"nl" => "Gebruik CNAME in plaats van A- verslag voor subdomains",
	"ru" => "Ð˜ÑÐ¿Ð¾Ð»ÑŒÐ·ÑƒÐ¹Ñ‚Ðµ CNAME Ð²Ð¼ÐµÑÑ‚Ð¾ Ð¿Ð¾ÐºÐ°Ð·Ð°Ñ‚ÐµÐ»Ñ A Ð´Ð»Ñ ÑÑƒÐ±-domenov",	
	"de" => "Verwenden Sie CNAME anstelle von der A Aufzeichnung fÃ¼r subdomains:",
	"zh" => "ä½¿ç”¨CNAME ä»£æ›¿ A çºªå½•ä¸ºæ¬¡çº§é¢†åŸŸ:",
	"pl" => "U¿yj CNAME rekordu A domeny dla dodawanych subdomen:",
	"es" => "Utilice CNAME en vez del expediente de A para los secundario-dominios:",
	"pt" => "Utilizar registos CNAME em vez de registos A para subdominios:"
	);

$txt_cfg_use_nated_vhost = array(
	"fr" => "Generer toutes les vhost apache sur l'ip du reseau local (NAT)",
	"en" => "Generate all apache vhosts on local network ip (NAT)",
	"hu" => "Az összes apache vhosts fájl generálása helyi hálózati ip-vel(NAT)",
	"it" => "Genera tutti i vhosts apache nell'ip network locale (NAT)",
	"nl" => "TRANS Generate all apache vhosts on local network ip (NAT)",
	"ru" => "TRANS Generate all apache vhosts on local network ip (NAT)",
	"de" => "Alle Apache Vhosts mit IP des lokales Netzwerkes generieren (NAT)",
	"zh" => "ÔÚ¾ÖÓòÍøÖ·Ð´ÏÂËùÓÐapache vhosts (NAT)",
	"pl" => "U¿ycie adresów lokalnych za NAT-em :",
	"es" => "Generar todos los vhosts de apache en una ip de red local (NAT)",
	"pt" => "Gerar todos os vhosts no Apache usando um IP de rede local (NAT)"
	);

$txt_cfg_nated_vhost_ip = array(
	"fr" => "Addrese ip dans le reseau local des vhost utilisant le NAT",
	"en" => "Local network area ip adress of the vhost using NAT",
	"hu" => "A NAT-ot használó vhost helyi hálózati ip címe",
	"it" => "Indirizzi IP area di rete locale del vhost usando NAT",
	"nl" => "TRANS Local network area ip adress of the vhost using NAT",
	"ru" => "TRANS Local network area ip adress of the vhost using NAT",
	"de" => "IP Adresse von Vhost mit NAT im lokalen Netzwerk",
	"zh" => "vhostÕýÔÚÊ¹ÓÃµÄ¾ÖÓòÍøÖ·NAT",
	"pl" => "Adres lokalny dla vhost za NAT-em :",
	"es" => "Dirección ip de red local del vhost que usa NAT",
	"pt" => "Endereço IP de rede local para vhost que usam NAT"
	);

$txt_cfg_use_javascript = array(
	"fr" => "Utiliser le javascript :",
	"en" => "Use javascript:",
	"hu" => "Javascriptet használ:",
	"it" => "Usa javascript:",
	"nl" => "TRANS Use javascript:",
	"ru" => "TRANS Use javascript:",
	"de" => "JavaScript verwenden:",
	"zh" => "Ê¹ÓÃjavascript:",
	"pl" => "U¿yj JavaScript'u :",
	"es" => "Usar javascript:",
	"pt" => "Usar JavaScript  :"
	);

$txt_cfg_use_ssl = array(
	"fr" => "Utiliser le SSL :",
	"en" => "Use SSL:",
	"hu" => "SSL-t használ:",
	"it" => "Usa SSL:",
	"nl" => "TRANS Use SSL",
	"ru" => "TRANS Use SSL",
	"de" => "SSl verwenden",
	"zh" => "Ê¹ÓÃSSL:",
	"pl" => "U¿yj SSL :",
	"es" => "Usar SSL:",
	"pt" => "Usar SSL ;"
	);

$txt_cfg_hide_password = array(
	"fr" => "Cacher les mots de passe dans DTC:",
	"en" => "Hide passwords within DTC:",
	"hu" => "A DTC-n belül elrejti a jelszavakat:",
	"it" => "Parole d'accesso del pellame all'interno di DTC:",
	"nl" => "De wachtwoorden van de huid binnen DTC:",
	"ru" => "TRANS Hide passwords within DTC:",
	"de" => "Passwörter im DTC verstecken:",
	"zh" => "ÓÃDTCÒþ²ØÃÜÂë£º",
	"pl" => "Ukryj has³a z DTC :",
	"es" => "Ocultar contraseñas dentro de DTC:",
	"pt" => "Ofuscar as palavras chave no DTC:"
	);

$txt_cfg_use_domain_based_ftp_logins = array(
	"fr" => "Utiliser des logins ftp @domain.com :",
	"en" => "Use @domain.com ftp logins:",
	"hu" => "A @domain.com formát használja az ftp hozzáféréseknél:",
	"it" => "Usa logins ftp @domain.com:",
	"nl" => "TRANS Use @domain.com ftp logins:",
	"ru" => "TRANS Use @domain.com ftp logins:",
	"de" => "@domain.com ftp Login verwenden:",
	"zh" => "Ê¹ÓÃ@domain.com ftpµÇÈë",
	"pl" => "U¿yj loginu ftp @domena.com :",
	"es" => "Usar @dominio.com para acceso a ftp:",
	"pt" => "Usar utilizadores com @dominio.com para acesso ao ftp"
	);

$txt_cfg_select_type_of_skin = array(
	"fr" => "Selectionner le type d'abillage :",
	"en" => "Select the type of skin:",
	"hu" => "Válasszon témát:",
	"it" => "Scegli il tipo di tema:",
	"nl" => "TRANS Select the type of skin:",
	"ru" => "TRANS Select the type of skin:",
	"de" => "Skin wählen:",
	"zh" => "Ñ¡ÔñÆ¤·ôÀàÐÍ£º",
	"pl" => "Wybierz skórkê :",
	"es" => "Selecciona el tipo de skin:",
	"pt" => "Selecionar o tipo de estilo :"
	);

$txt_cfg_full_hostname = array(
	"fr" => "Hostname de l'administrateur DTC :",
	"en" => "Full hostname of DTC admin panel:",
	"hu" => "A DTC admin panel teljes gazdaneve:",
	"it" => "Hostname completo del pannello admin di DTC:",
	"nl" => "Volledige Hostnaam van de DTC admin panel:",
	"ru" => "ðÏÌÎÏÅ ÉÍÑ ÈÏÓÔÁ Ó ÁÄÍÉÎÉÓÔÒÁÔÉ×ÎÏÊ ÐÁÎÅÌØÀ DTC",
	"de" => "Hostname des DTC Administrators",
	"zh" => "DTC¹ÜÀíÃæ°åµÄÍøÕ¾È«Ãû",
	"pl" => "Nazwa hosta do obs³ugi DTC :",
	"es" => "Nombre completo del hostname del panel de adminitración de DTC:",
	"pt" => "Nome da máquina (hostname) para administração do DTC :" 
	);

$txt_cfg_main_site_ip =array(
	"fr" => "IP principale du serveur :",
	"en" => "Main ip address of the server:",
	"hu" => "A szerver fõ ip címe:",
	"it" => "Indirizzo IP principale del server:",
	"nl" => "Primaire ip-adres van de server",
	"ru" => "ïÓÎÏ×ÎÏÊ ÁÄÒÅÓ ÓÁÊÔÁ",
	"de" => "Haupt IP Adresse:",
	"zh" => "Ö÷ÒªipµØÖ·µÄ·þÎñÆ÷",
	"pl" => "Adres IP serwera :",
	"es" => "IP principal del sitio:",
	"pt" => "IP principal do servidor :"
	);

$txt_cfg_site_addrs =array(
	"fr" => "Adresses IP de votre machine (séparé par des \"|\") :",
	"en" => "Host IP addresses (separated by \"|\"):",
	"hu" => "Gazda IP címek (\"|\" elválasztva):",
	"it" => "Indirizzi IP dell'host (separati da \"|\"):",
	"nl" => "TRANS¡¡Host IP addresses (separated by \"|\"):",
	"ru" => "IP ÁÄÒÅÓÁ ÈÏÓÔÁ (ÒÁÚÄÅÌÅÎÎÙÅ \"|\"):",
	"de" => "IP Adressen des PC (getrennt durch \"|\"):",
	"zh" => "ËùÓÐµÄIPµØÖ·µÄ·þÎñÆ÷(±» \"|\"Çø·Ö):",
	"pl" => "Adresy IP hostów (przedzielone \"|\") :",
	"es" => "Direcciones IP del Host (separado por \"|\"):",
	"pt" => "Lista de endereços IP do servidor (separados por \"|\") :"
	);

$txt_cfg_name_zonefileconf_title =array(
	"fr" => "Zonefiles named",
	"en" => "Named zonefiles",
	"hu" => "Named zónafájlok",
	"it" => "Zonefiles Named",
	"nl" => "Named zone-files",
	"ru" => "ëÏÎÆÉÇÕÒÁÃÉÑ ÚÏÎ äîó",
	"de" => "Bind Zonendatei",
	"zh" => "Named zonefilesÅäÖÃ",
	"pl" => "Konfiguracja stref binda",
	"es" => "Zonas de Named",
	"pt" => "Ficheiros de zonas"  
	);

$txt_cfg_main_mx_addr =array(
	"fr" => "Adresse de votre serveur MX principal:",
	"en" => "Address of your main MX server :",
	"hu" => "A fõ MX szerver címe :",
	"it" => "Indirizzo del tuo MX server primario :",
	"nl" => "Hostname van je primaire MX record",
	"ru" => "áÄÒÅÓ ÷ÁÛÅÇÏ ÏÓÎÏ×ÎÏÇÏ MX-ÓÅÒ×ÅÒÁ",
	"de" => "Adresse des MX Hauptservers:",
	"zh" => "ÄúµÄÖ÷ÒªMX·þÎñÆ÷µÄµØÖ·",
	"pl" => "Nazwa serwera Primary MX :",
	"es" => "Dirección ip de tu servidor MX principal :",
	"pt" => "Endereço do servidor de MX principal:"
	);

$txt_cfg_mail_addr_webmaster =array(
	"fr" => "Adresse email du webmaster :",
	"en" => "Email address of your webmaster:",
	"hu" => "A webmester email címe:",
	"it" => "Indirizzo Email of tuo webmaster:",
	"nl" => "Email adres van de webmaster",
	"ru" => "ðÏÞÔÏ×ÙÊ ÁÄÒÅÓ ÷ÁÛÅÇÏ ×ÅÂÍÁÓÔÅÒÁ:",
	"de" => "E-Mailadresse des Webmasters",
	"zh" => "ÄúµÄÍø¹ÜµÄµç×ÓÓÊÏäµØÖ·",
	"pl" => "Adres e-mail webmastera :",
	"es" => "Dirección de Correo del webmaster:",
	"pt" => "Endereço de correio do Webmaster :"
	);

$txt_cfg_primary_dns_server_addr =array(
	"fr" => "Adresse du serveur DNS primaire :",
	"en" => "Primary dns server addr:",
	"hu" => "Az elsõdleges dns szerver címe:",
	"it" => "Indirizzo server DNS primario:",
	"nl" => "Het primaire DNS serveradres:",
	"ru" => "ðÅÒ×ÉÞÎÙÊ ÁÄÒÅÓ äîó :",
	"de" => "Adresse des primären DNS Servers:",
	"zh" => "µÚÒ»¸öÓòÃû·þÎñÆ÷µØÖ·",
	"pl" => "Nazwa serwera Primary DNS :",
	"es" => "Dirección del servidor Primario de dns:",
	"pt" => "Endereço do Servidor de DNS primário :"
	);

$txt_cfg_secondary_dns_server_addr =array(
	"fr" => "Adresse du serveur DNS secondaire :",
	"en" => "Secondary dns server addr:",
	"hu" => "A másodlagos dns szerver címe:",
	"it" => "Indirizzo server DNS secondario:",
	"nl" => "Secundaire DNS serveradres:",
	"ru" => "÷ÔÏÒÉÞÎÙÊ ÁÄÒÅÓ äîó :",
	"de" => "Adresse des sekundären DNS Servers:",
	"zh" => "µÚ¶þ¸öÓòÃû·þÎñÆ÷µØÖ·",
	"pl" => "Nazwa serwera Secondary DNS :",
	"es" => "Dirección del servidor Secundario de dns:",
	"pt" => "Nome do servidor de DNS secundário :"
	);

$txt_cfg_slave_dns_ip =array(
	"fr" => "Adresse IP du serveur DNS esclave :",
	"en" => "Slave DNS server ip address:",
	"hu" => "A szolga DNS szerver ip címe:",
	"it" => "Indirizzo IP server DNS slave:",
	"nl" => "Tertiaire dns server ip adres:",
	"ru" => "áÄÒÅÓ ÐÏÄÞÉÎÅÎÎÏÇÏ äîó-ÓÅÒ×ÅÒÁ:",
	"de" => "IP Adresse des Slave DNS Servers:",
	"zh" => "±¸ÓÃÓòÃû·þÎñÆ÷µØÖ·",
	"pl" => "Adres IP serwera Secondary DNS :",
	"es" => "Dirección ip del servidor esclavo de DNS:",
	"pt" => "Endereço IP do servidor de DNS secundário"
	);

$txt_cfg_payconf_title = array (
	"fr" => "Portail de paiment",
	"en" => "Pay gateway",
	"hu" => "Fizetési átjáró",
	"it" => "Gateway per pagamento",
	"nl" => "TRANS: Pay gateway",
	"ru" => "TRANS: Pay gateway",
	"de" => "Zahlungsgateway",
	"zh" => "¸¶¿î·½Ê½",
	"pl" => "System p³atno¶ci",
	"es" => "Pasarela de pago",
	"pt" => "Gateway de pagamento"
	);

$txt_cfg_path_conf_title =array(
	"fr" => "Chemins",
	"en" => "Paths",
	"hu" => "Elérési utak",
	"it" => "Paths",
	"nl" => "Paden",
	"ru" => "ëÏÎÆÉÇÕÒÁÃÉÏÎÎÙÅ ÐÕÔÉ",
	"de" => "Dateipfad",
	"zh" => "Â·¾¶ÅäÖÃ",
	"pl" => "¦cie¿ki",
	"es" => "Directorios",
	"pt" => "Localizações"
	);

$txt_cfg_mainpath_conf_title =array(
	"fr" => "Chemins principaux",
	"en" => "Main paths",
	"hu" => "Fõ elérési utak",
	"it" => "Paths principale",
	"nl" => "Belangrijke directorypaden ",
	"ru" => "ïÓÎÏ×ÎÙÅ ÐÕÔÉ",
	"de" => "Hauptpfad",
	"zh" => "Ö÷ÒªÂ·¾¶",
	"pl" => "Inne ¶cie¿ki",
	"es" => "Directorio Principal",
	"pt" => "Localizações principais"
	);

$txt_cfg_dtc_shared_folder =array(
	"fr" => "Chemin du dossier \"shared\" de DTC :",
	"en" => "Filepath of your DTC \"shared\" directory:",
	"hu" => "Fájl elérési út a DTC \"shared\" könyvtárához:",
	"it" => "Filepath della tua directory DTC \"condivisa\":",
	"nl" => "bestandenpad naar jouw DTC \"shared\" directory:",
	"ru" => "æÁÊÌÏ×ÙÊ ÐÕÔØ Ë ×ÁÛÅÊ DTC \"ÏÂÝÅÊ\" ÄÉÒÅËÔÏÒÉÉ:",
	"de" => "Pfad des Ordners\"shared\" DTC:",
	"zh" => "ÄúµÄDTCµÄÂ·¾¶\"shared\"Ä¿Â¼:",
	"pl" => "¦cie¿ka do katalogu DTC \"shared\" :",
	"es" => "Directorio de ficheros \"shared\" de DTC:",
	"pt" => "Localização do directório \"shared\" do DTC :"
	);

$txt_cfg_new_account_defaultpath =array(
	"fr" => "Chemin par defaut pour les nouveaux comptes :",
	"en" => "Your default new account directory:",
	"hu" => "Az alapértelmezett új hozzáférés könyvtára:",
	"it" => "Directory nuovo account di default:",
	"nl" => "Hoofdpad waaronder je nieuwe accounts worden aangemaakt:",
	"ru" => "äÉÒÅËÔÏÒÉÑ ÎÏ×ÏÊ ÕÞÅÔÎÏÊ ÚÁÐÉÓÉ ÐÏ ÕÍÏÌÞÁÎÉÀ:",
	"de" => "Standardpfad für neue Accounts:",
	"zh" => "ÄúµÄÔ¤ÉèÐÂÕÊ»§Ä¿Â¼",
	"pl" => "Domy¶lna ¶cie¿ka do katalogu hostingu :",
	"es" => "Directorio por defecto de tu nueva cuenta:",
	"pt" => "Localização pre-definida para novas contas :" 
	);

$txt_cfg_generated_file_path =array(
	"fr" => "Chemin ou DTC va être restreint pour générer ses fichiers
de configuration pour les daemons.
Chacun des chemin ci-après (qmail, apache et named) seront concaténés à
celui-ci :",
	"en" => "Path where will DTC will be restricted for generating it's
configuration files for daemons.
Each of the following (qmail, apache and named) path will be concatened to
this:",
	"hu" => "Az elérési út ahol a DTC a démonok konfigurációs fájljait elkészítheti.
A (qmail, apache és named) az elérési útja hozzá lesz fûzve a következõhöz:",
	"it" => "Path dove DTC verrà protetto per generare i suoi
file di configurazione per i demoni.
Ognuna delle seguenti path (qmail, apache and named) sarà concatenata a questa:",
	"nl" => "Paden waarin DTC binnen word opgesloten voor het opslaan
van z'n configuratie bestanden voor de daemons.",
	"ru" => "ðÕÔØ ÇÄÅ DTC ÐÏÚ×ÏÌÉÔ ÇÅÎÅÒÉÒÏ×ÁÔØ ÜÔÉ ËÏÎÆÉÇÕÒÁÃÉÏÎÎÙÅ
ÆÁÊÌÙ ÄÌÑ ÄÅÍÏÎÏ×. ëÁÖÄÙÊ ÉÚ ÓÌÅÄÕÀÝÉÈ (qmail, apache and named) ÐÕÔÅÊ ÂÕÄÅÔ ÓÏÐÏÓÔÁ×ÉÍ Ó
ÜÔÉÍ:",
	"de" => "Eingeschränkter Pfad zur Speicherung aller Daemon Konfigurationsdateien durch DTC",
	"zh" => "ÉèÖÃDTC½«Ð´ÏÂÆäÎÄ¼þ(qmail, apache and named)",
	"pl" => "¦cie¿ka do katalogu z plikami konfiguracyjnymi hostingu :",
	"es" => "Directorio donde DTC podrá generar los ficheros de
configuración para los demonios.
Cada uno de los directorios (qmail, apache y named) serán enlazados a
este:",
    "pt" => "Directório onde DTC deverá gerar os ficheiros de configuração para os serviços.
Em cada um dos seguintes (qmail, apache e named) o caminho será concatenado para :"	
);
    

$txt_cfg_apache_file_names =array(
	"fr" => "Nom des fichier pour Apache",
	"en" => "Apache file names",
	"hu" => "Apache fájlnevek",
	"it" => "Nomi dei file per Apache",
	"nl" => "Apache bestandsnamen",
	"ru" => "éÍÅÎÁ ÆÁÊÌÏ× Apache",
	"de" => "Apache Dateinamen",
	"zh" => "ApacheÎÄ¼þÃû×Ö",
	"pl" => "Nazwy plików Apache-a :",
	"es" => "Configuración de Apache",
	"pt" => "Configuração do Apache"
	);

$txt_cfg_vhost_file_path =array(
	"fr" => "Fichier de configuration des Virtual-Host :",
	"en" => "Virtual host config-file:",
	"hu" => "Virtual host konfig fájl:",
	"it" => "File di configurazione del Virtual host:",
	"nl" => "Virtual hosting config-file:",
	"ru" => "ëÏÎÆÉÇÕÒÁÃÉÑ ×ÉÒÔÕÁÌØÎÏÇÏ ÈÏÓÔÁ:",
	"de" => "Virtual Host Konfigurationsdatei:",
	"zh" => "VhostÅäÖÃÎÄ¼þ",
	"pl" => "Nazwa pliku konfiguracyjnego vhost :",
	"es" => "Fichero de configuración de los Virtual host:",
	"pt" => "Ficheiro de configuração dos \"Virtual Hosts\" ",
	);

$txt_cfg_phplib_path = array(
	"fr" => "Librairies PHP open_basedir (séparés par des \":\", initialisé à l'installation de dtc) :",
	"en" => "Php libraries open_basedir (separated by \":\", reset on each dtc install):",
	"hu" => "TRANS LPhp könyvtárak open_basedir (\":\" elválasztva, reset on each dtc install):",
	"it" => "TRANS LLibrerie PHP open_basedir (separate da \":\", reset on each dtc install):",
	"nl" => "TRANS LPhp libraries open_basedir (gescheiden met een \":\", reset on each dtc install):",
	"ru" => "TRANS LPhp ÂÉÂÌÉÏÔÅËÉ open_basedir (ÒÁÚÄÅÌÅÎÎÙÅ \":\", reset on each dtc install):",
	"de" => "TRANS LPHP Bibliotheken open_basedir (getrennt durch \":\", reset on each dtc install):",
	"zh" => "TRANS LPhpµÄÎÄ¼þ¼Ð open_basedir (±»\":\"Çø·Ö, reset on each dtc install):",
	"pl" => "Biblioteki PHP open_basedir (oddzielone \":\", zresetuj pozosta³e podczas instalacji dtc) :",
	"es" => "TRANS LLibrerias de Php open_basedir (separadas por \":\", reset on each dtc install):",
	"pt" => "TRANS Librarias PHP open_basedir (separadas por \":\", reset on each dtc install) :"
	);

$txt_cfg_phplib2_path =array(
	"fr" => "Librairies PHP additionnelles open_basedir (conservé a la réinstallation):",
	"en" => "Php open_basedir additional libraries path (keeped uppon reinstallation):",
	"hu" => "További php könyvtárak elérési útja open_basedir (keeped uppon reinstallation):",
	"it" => "Path librerie addizionali PHP open_basedir (keeped uppon reinstallation):",
	"nl" => "Additionele php library paden open_basedir (keeped uppon reinstallation):",
	"ru" => "ðÕÔØ Ë ÄÏÐÏÌÎÉÔÅÌØÎÙÍ ÂÉÂÌÉÏÔÅËÁÍ PHP open_basedir (keeped uppon reinstallation):",
	"de" => "Zusätzliche PHP Bibliotheken open_basedir (keeped uppon reinstallation):",
	"zh" => "PhpµÄ¸½¼ÓÎÄ¼þ¼ÐÂ·¾¶ open_basedir (keeped uppon reinstallation):",
	"pl" => "Dodatkowe biblioteki PHP w open_basedir (zachowaj przed reinstalacja) :",
	"es" => "Librerias adicionales de Phpopen_basedir  (keeped uppon reinstallation):",
	"pt" => "Librarias adicionais de PHP open_basedir (keeped uppon reinstallation):"
	);

$txt_cfg_named_filenames_title =array(
	"fr" => "Chemins de named",
	"en" => "Named file names",
	"hu" => "Named fájlnevek",
	"it" => "Nomi file Named",
	"nl" => "Named bestandsnamen",
	"ru" => "éÍÅÎÁ ÆÁÊÌÏ× äîó",
	"de" => "Bind Konfigutationsdateien",
	"zh" => "NamedÎÄ¼þÃû×Ö",
	"pl" => "Nazwy plików Bind DNS",
	"es" => "Configuración de Named",
	"pt" => "Configuração do Named (DNS)"
	);

$txt_cfg_named_main_file =array(
	"fr" => "Fichier principale :",
	"en" => "Named main file:",
	"hu" => "Named fõ fájl:",
	"it" => "File principale Named:",
	"nl" => "named hoofdbestand",
	"ru" => "ãÅÎÔÒÁÌØÎÙÊ ÆÁÊÌ äîó:",
	"de" => "Bind Masterdatei:",
	"zh" => "NamedÖ÷ÒªÎÄ¼þ:",
	"pl" => "Nazwa pliku konfiguracyjnego : ",
	"es" => "Fichero principal:",
	"pt" => "Ficheiro principal :"
	);

$txt_cfg_named_slave_file =array(
	"fr" => "Fichier zone esclaves :",
	"en" => "Named slave file:",
	"hu" => "Named másodlagos fájl:",
	"it" => "File slave Named:",
	"nl" => "Names slave bestanden",
	"ru" => "ðÏÄÞÉÎÅÎÎÙÊ ÆÁÊÌ äîó:",
	"de" => "Bind Slavedatei:",
	"zh" => "Named±¸ÓÃ·þÎñÆ÷ÎÄ¼þ:",
	"pl" => "Nazwa pliku konfiguracyjnego slave : ",
	"es" => "Fichero de zonas esclavas:",
	"pt" => "Ficheiro das zonas secundárias :"
	);

$txt_cfg_named_main_zonefile =array(
	"fr" => "Dossier zonefiles principale :",
	"en" => "Named main zonefiles folder:",
	"hu" => "Named fõ zónafájlok mappája:",
	"it" => "Directory principale zonefiles Named:",
	"nl" => "Names zonefiles directory:",
	"ru" => "ðÁÐËÁ ÆÁÊÌÏ× ÃÅÎÔÒÁÌØÎÙÈ ÚÏÎ ÄÌÑ ÓÅÒ×ÅÒÁ äîó:",
	"de" => "Bind Masterzonen Verzeichnis",
	"zh" => "NamedÖ÷ÒªzonefilesÎÄ¼þ¼Ð:",
	"pl" => "Nazwa katalogu g³ównego stref : ",
	"es" => "Directorio principal de los zonefiles:",
	"pt" => "Directório das zonas principais :"
	);

$txt_cfg_named_cache_slave_zonefile =array(
	"fr" => "Dossier zonefile esclave :",
	"en" => "Folder named slave (cache) zonefiles:",
	"hu" => "Mappa a named másodlagos (cache) zónafájloknak:",
	"it" => "Directory slave (cache) zonefiles Named:",
	"nl" => "Directory van de named slave zonebestanden:",
	"ru" => "ðÁÐËÁ ÄÌÑ ËÜÛÉÒÏ×ÁÎÉÑ ÆÁÊÌÏ× ÚÏÎ:",
	"de" => "Bind Slavezonen Verzeichnis",
	"zh" => "Named±¸ÓÃ(¸´ÖÆ)zonefilesÎÄ¼þ¼Ð:",
	"pl" => "Nazwa katalogu slave (cache) stref : ",
	"es" => "Directorio zonefile esclavo (cache):",
	"pt" => "Directório das zonas secundárias :"
	);

$txt_cfg_backup_webalizer_title =array(
	"fr" => "Chemin de backups et Webalizer",
	"en" => "Backups and Webalizer file names",
	"hu" => "Mentések és Webalizer fájlnevek",
	"it" => "Nomi file Backups e Webalizer",
	"nl" => "Backup en Webalizer bestandsnamen",
	"ru" => "òÅÚÅÒ×ÎÙÅ É ÁÎÁÌÉÚÉÒÕÀÝÉÅ ÉÍÅÎÁ ÆÁÊÌÏ×",
	"de" => "Sicherungs und Webalizer Dateinamen",
	"zh" => "±¸ÓÃºÍWebalizerÎÄ¼þÃû×Ö",
	"pl" => "Nazwa katalogu, plików  do Backup-u i Webalizera",
	"es" => "Nombre de ficheros de los Backups y Webalizer",
	"pt" => "Nomes para ficheiros de backup e webalizer"
	);

$txt_cfg_backup_script_filename =array(
	"fr" => "Nom du script de backup :",
	"en" => "Backup bash-script file:",
	"hu" => "Mentés bash-script fájl:",
	"it" => "File bash-script Backup:",
	"nl" => "Backup sh-script file:",
	"ru" => "óËÒÉÐÔ ÒÅÚÅÒ×ÎÏÇÏ ËÏÐÉÒÏ×ÁÎÉÑ (Bash):",
	"de" => "Sicherungs-Script:",
	"zh" => "±¸ÓÃºÍbash³ÌÐòÎÄ¼þÃû×Ö:",
	"pl" => "Nazwa skryptu Backup-u :",
	"es" => "Nombre del fichero de Backup:",
	"pt" => "Nome do ficheiro de backup :"
	);

$txt_cfg_backup_destination_folder =array(
	"fr" => "Dossier de destination du backup :",
	"en" => "Backup destination directory:",
	"hu" => "Mentés célkönyvtár:",
	"it" => "Directory destinazione Backup:",
	"nl" => "Standaard backupdirectory:",
	"ru" => "äÉÒÅËÔÏÒÉÑ ÓÏÚÄÁÎÉÑ ÒÅÚÅÒ×ÎÙÈ ËÏÐÉÊ:",
	"de" => "Backupzielverzeichnis",
	"zh" => "±¸ÓÃÄ¿µÄµØÄ¿Â¼:",
	"pl" => "Katalog docelowy Backup-u :",
	"es" => "Directorio de destino del Backup:",
	"pt" => "Directório de destino do backup :"
	);

$txt_cfg_webalizer_script_filename =array(
	"fr" => "Nom du script Webalizer :",
	"en" => "Webalizer bash-script name:",
  	"hu" => "Webalizer bash-script név:",
	"it" => "Nome bash-script Webalizer:",
	"nl" => "Webalizer sh-script:",
	"ru" => "óËÒÉÐÔÙ(Bash) Webalizer",
	"de" => "Webalizer bash-script:",
	"zh" => "Webalizer bash³ÌÐòÃû×Ö:",
	"pl" => "Nazwa skryptu Webalizera :",
	"es" => "Nombre del script de Webalizer:",
	"pt" => "Nome do script Webalizer :"
	);

?>
