CATEGORIES=	sysutils
MASTER_SITES=	ftp://ftp.gplhost.com/pub/dtc/bsd/

MAINTAINER=     thomas@goirand.fr
COMMENT=        A hosting web GUI for admin and accounting apache/named/proftpd/qmail
RUN_DEPENDS=	${LOCALBASE}/sbin/httpd:${PORTSDIR}/www/apache13 \
		${LOCALBASE}/bin/php:${PORTSDIR}/lang/php4:install:WITH_GD=yes  \
		${LOCALBASE}/libexec/mysqld:${PORTSDIR}/databases/mysql323-server \
		${LOCALBASE}/libexec/proftpd:${PORTSDIR}/ftp/proftpd:install:WITH_MYSQL=yes \
		${LOCALBASE}/libexec/apache/mod_log_sql.so:${PORTSDIR}/www/mod_log_sql \
		${LOCALBASE}/bin/webalizer:${PORTSDIR}/www/webalizer \
		/var/qmail/bin/qmail-start:${PORTSDIR}/mail/qmail

NO_BUILD=       yes
USE_MYSQL=      yes
NO_PACKAGE=     No binary package => Application is written and installed with PHP

do-install:
		@${CP} -Rf ${WRKSRC}/dtc ${PREFIX}/share
		@${SH} ${WRKSRC}/install.sh

.include <bsd.port.mk>
