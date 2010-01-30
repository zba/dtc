# New ports collection makefile for:    dtc-postfix-courier
# Date created:                         13 October 2007
# Whom:                                 Thomas Goirand <thomas@goirand.fr>
#
# $FreeBSD$
#

PORTREVISION=	1
PKGNAMESUFFIX=	-postix-courier

MAINTAINER=	thomas@goirand.fr

CONFLICTS=	dtc-[0-9]* dtc-toaster-[0-9]*

WITH_WEBALIZER=	yes
WITH_AWSTATS=	yes
WITH_VISITORS=	yes
WITH_PUREFTPD=	yes
WITH_POSTFIX=	yes
WITH_APACHE2=	yes
WITH_PHP5=	yes
WITH_MAILDROP=	yes
WITH_COURIER_IMAP=yes
WITH_CYRUS_SASL2=yes
WITH_SPAMASSASSIN=yes
WITH_CLAMAV=	yes
WITH_AMAVIS=	yes
WITH_SQUIRRELMAIL=yes
WITH_PHPMYADMIN=yes

DESCR=		${.CURDIR}/pkg-descr

MASTERDIR=	${.CURDIR}/../../sysutils/dtc

.include "${MASTERDIR}/Makefile"
