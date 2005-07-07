CATEGORIES=	sysutils
MASTER_SITES=	ftp://ftp.gplhost.com/pub/dtc/bsd/

MAINTAINER=	thomas@goirand.fr
COMMENT=	A hosting web GUI for admin and accounting all hosting services
RUN_DEPENDS=	php:${PHP_PORT} \
		${LOCALBASE}/share/pear/Crypt/CBC.php:${PORTSDIR}/security/pear-Crypt_CBC \
		${LOCALBASE}/libexec/proftpd:${PORTSDIR}/ftp/proftpd-mysql \
		${LOCALBASE}/libexec/apache/mod_log_sql.so:${PORTSDIR}/www/mod_log_sql2 \
		${LOCALBASE}/bin/webalizer:${PORTSDIR}/www/webalizer \
		${LOCALBASE}/www/cgi-bin/sbox:${PORTSDIR}/www/sbox-dtc

PHP_PORT=${PORTSDIR}/www/php4-session

DTCROOT?=	www/dtc
DTCDIR=		${PREFIX}/${DTCROOT}
PLIST_SUB+=	DTCROOT="${DTCROOT}"

# Now tested and working. In fact, under BSD it's easyer to use Apache2 now.
# If you know what to do so all dependancies are set correctly, please help!
.if defined(WITH_APACHE2)
IGNORE=		Untested
.endif

NO_BUILD=	yes
USE_MYSQL=	yes
USE_PHP=        mysql pcre

OPTIONS=        BZ2      "bzip2 library support" on \
                GD       "GD library support" on \
                MYSQL   "MySQL support" on \
                OPENSSL  "OpenSSL support" on \
                ZLIB     "ZLIB support" on \
                MCRYPT   "MCrypt library support" on

do-configure:
	@${SED} -e 's,%%PREFIX%%,${PREFIX},;\
		s,%%LOCALBASE%%,${LOCALBASE},;\
		s,%%QMAIL_DIR%%,${QMAIL_DIR},' \
		${FILESDIR}/dtc-install.in > ${WRKDIR}/dtc-install
	@${SED} -e 's,%%PREFIX%%,${PREFIX},;\
		s,%%LOCALBASE%%,${LOCALBASE},;\
		s,%%QMAIL_DIR%%,${QMAIL_DIR},' \
		${FILESDIR}/dtc-deinstall.in > ${WRKDIR}/dtc-deinstall

do-install:
	@${INSTALL_SCRIPT} ${WRKDIR}/dtc-install ${PREFIX}/sbin/dtc-install
	@${INSTALL_SCRIPT} ${WRKDIR}/dtc-deinstall ${PREFIX}/sbin/dtc-deinstall
	@${MKDIR} ${DTCDIR}
	@cd ${WRKSRC}/dtc ; ${TAR} cfp - . | ${TAR} xfp - -C ${DTCDIR}
	@${CAT} ${PKGMESSAGE}

.include <bsd.port.mk>
