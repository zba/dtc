CATEGORIES=	sysutils
MASTER_SITES=	ftp://ftp.gplhost.com/pub/dtc/bsd/

MAINTAINER=     thomas@goirand.fr
COMMENT=        A hosting web GUI for admin and accounting apache/named/proftpd/qmail
RUN_DEPENDS=	${LOCALBASE}/sbin/httpd:${PORTSDIR}/www/apache13 \
		${LOCALBASE}/bin/php:${PORTSDIR}/lang/php4:WITH_GD=yes  \
		${LOCALBASE}/bin/php:${PORTSDIR}/lang/php4:install \
                ${LOCALBASE}/share/pear/Crypt/CBC.php:${PORTSDIR}/security/pear-Crypt_CBC \
		${LOCALBASE}/libexec/mysqld:${MYSQL_SERVER} \
		${LOCALBASE}/libexec/proftpd:${PORTSDIR}/ftp/proftpd:WITH_MYSQL=yes \
		${LOCALBASE}/libexec/proftpd:${PORTSDIR}/ftp/proftpd:install \
		${LOCALBASE}/libexec/apache/mod_log_sql.so:${PORTSDIR}/www/mod_log_sql \
		${LOCALBASE}/bin/webalizer:${PORTSDIR}/www/webalizer \
		${QMAIL_DIR}/bin/qmail-start:${PORTSDIR}/mail/qmail

NO_BUILD=       yes
USE_MYSQL=      yes
NO_PACKAGE=     No binary package => Application is written and installed with PHP

do-install:
		@${SH} ${PKGINSTALL} ${PREFIX} ${WRKSRC}

.include <bsd.port.mk>
