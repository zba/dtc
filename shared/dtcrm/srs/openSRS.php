<?php
/* 
 **************************************************************************
 *
 * OpenSRS-PHP
 *
 * Copyright (C) 2000, 2001, 2002, 2003 Colin Viebrock
 *   and easyDNS Technologies Inc.
 *
 **************************************************************************
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.   
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 **************************************************************************
 *
 * vim: set expandtab tabstop=4 shiftwidth=4:
 * $Id: openSRS.php,v 1.1 2003/12/03 16:32:58 thomas Exp $
 *
 **************************************************************************
 */


/*
 *
 * OpenSRS Protocol Sample Extended Client Class
 *
 */


/* We require the base class */

require_once 'openSRS_base.php';



class openSRS extends openSRS_base {

	var $USERNAME				= '';

	var $TEST_PRIVATE_KEY		= 'abcdef1234567890';
	var $LIVE_PRIVATE_KEY		= 'abcdef1234567890';

	var $HRS_host				= 'horizon.opensrs.net';
	var $HRS_port				= 55000;
	var $HRS_USERNAME			= 'yourOpenHRSname';
	var $HRS_PRIVATE_KEY		= 'abcdef1234567890';

	var $environment			= 'TEST';	# 'TEST' or 'LIVE' or 'HRS'
	var $crypt_type				= 'DES';	# 'DES' or 'BLOWFISH';
	var $protocol				= 'XCP';	# 'XCP' for domains, 'TPP' for email and certs


	var $connect_timeout		= 15;		# seconds
	var $read_timeout		= 15;		# seconds

	var $RELATED_TLDS = array(
		array( '.ca' ),
		array( '.com', '.net', '.org' ),
		array( '.co.uk', '.org.uk' ),
		array( '.vc' ),
		array( '.cc' ),
	);


/*
 * You can put your own functions here to extend the class
 * (e.g. convert your data structures to OpenSRS's, read
 * your nameserver list out of a database, etc.)
 *
 */

	function myFunction() {
	}


}


