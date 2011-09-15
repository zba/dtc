<?php
// ensure this file is being included by a parent file
if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' );
/**
 * dtc.php
 * @copyright 2011, Thomas Goirand <thomas@goirand.fr>
 * @author The eXtplorer project (http://sourceforge.net/projects/extplorer)
 * @author The	The QuiX project (http://quixplorer.sourceforge.net)
 * 
 * @license
 * This file has been taken written by reading include/authentication/ftp.php
 * from the extplorer project. It is released as LGPL, as with the rest of the
 * DTC project, and that with the agreement of the original author.
 * 
 */
 
/**
 * This file handles DTC mysql authentication
 *
 */
class ext_dtc_authentication {
	function onAuthenticate($credentials, $options=null ) {
		// Load the DTC mysql credentials
		//------------------------------------------------------------------------------
		$dtc_mysql_credential_file = "/usr/share/dtc/shared/mysql_config.php";
		if(! file_exists($dtc_mysql_credential_file)){
			return false;
		}
		$dtc_credential_content = file_get_contents("/usr/share/dtc/shared/mysql_config.php");
		// Load the login
		$ret = preg_match('#[ \t]*\$conf_mysql_login[ \t]*=[ \t]*[\'"](.*)[\'"][ \t]*;#',$dtc_credential_content,$matches);
		if($ret != 1){
			return false;
		}
		$mysql_dtc_login = $matches[1];

		// Load the password
		$ret = preg_match('#[ \t]*\$conf_mysql_pass[ \t]*=[ \t]*[\'"](.*)[\'"][ \t]*;#',$dtc_credential_content,$matches);
		if($ret != 1){
			return false;
		}
		$mysql_dtc_pass = $matches[1];

		// Load the host
		$ret = preg_match('#[ \t]*\$conf_mysql_host[ \t]*=[ \t]*[\'"](.*)[\'"][ \t]*;#',$dtc_credential_content,$matches);
		if($ret != 1){
			return false;
		}
		$mysql_dtc_host = $matches[1];

		// Load the db
		$ret = preg_match('#[ \t]*\$conf_mysql_db[ \t]*=[ \t]*[\'"](.*)[\'"][ \t]*;#',$dtc_credential_content,$matches);
		if($ret != 1){
			return false;
		}
		$mysql_dtc_db = $matches[1];

		$ressource_id = mysql_connect("$mysql_dtc_host", "$mysql_dtc_login", "$mysql_dtc_pass");
		if($ressource_id == false)      return false;
		$sql_db_return = mysql_select_db($mysql_dtc_db)or die("Cannot select db: $conf_mysql_db");
		$q = "SELECT * FROM admin WHERE adm_login='".mysql_real_escape_string($credentials['username'])."' AND (adm_pass='".$credentials['password']."' OR adm_pass=SHA1('".$credentials['password']."'))";
		$r = mysql_query($q);
		$n = mysql_num_rows($r);
		if($n != 1){
			return false;
		}
		$a = mysql_fetch_array($r);
		// 	Set Login
		$_SESSION['credentials_dtc']['username']	= $a["adm_login"];
		$_SESSION['credentials_dtc']['password']	= $credentials['password'];
		$_SESSION['file_mode'] = 'extplorer';
		$GLOBALS["home_dir"]	= $a["path"];
		$GLOBALS["home_url"]	= "http://localhost";
		$GLOBALS["show_hidden"]	= "1";
		$GLOBALS["no_access"]	= "0";
		$GLOBALS["permissions"]	= "1";
		
		return true;
	}
	
	function onShowLoginForm() {
		?>
	{
		xtype: "form",
		<?php if(!ext_isXHR()) { ?>renderTo: "adminForm", <?php } ?>
		title: "<?php echo ext_Lang::msg('actlogin') ?>",
		id: "simpleform",
		labelWidth: 125, // label settings here cascade unless overridden
		url: "<?php echo basename( $GLOBALS['script_name']) ?>",
		frame: true,
		keys: {
		    key: Ext.EventObject.ENTER,
		    fn  : function(){
				if (simple.getForm().isValid()) {
					Ext.get( "statusBar").update( "Please wait..." );
					Ext.getCmp("simpleform").getForm().submit({
						reset: false,
						success: function(form, action) { location.reload() },
						failure: function(form, action) {
							if( !action.result ) return;
							Ext.Msg.alert('<?php echo ext_Lang::err( 'error', true ) ?>', action.result.error);
							Ext.get( 'statusBar').update( action.result.error );
							form.findField( 'password').setValue('');
							form.findField( 'username').focus();
						},
						scope: Ext.getCmp("simpleform").getForm(),
						params: {
							option: "com_extplorer", 
							action: "login",
							type : "extplorer"
						}
					});
    	        } else {
        	        return false;
            	}
            }
		},
		items: [{
            xtype:"textfield",
			fieldLabel: "<?php echo ext_Lang::msg( 'miscusername', true ) ?>",
			name: "username",
			width:175,
			allowBlank:false
		},{
			xtype:"textfield",
			fieldLabel: "<?php echo ext_Lang::msg( 'miscpassword', true ) ?>",
			name: "password",
			inputType: "password",
			width:175,
			allowBlank:false
		}, new Ext.form.ComboBox({
			
			fieldLabel: "<?php echo ext_Lang::msg( 'misclang', true ) ?>",
			store: new Ext.data.SimpleStore({
		fields: ['language', 'langname'],
		data :	[
		<?php 
		$langs = get_languages();
		$i = 0; $c = count( $langs );
		foreach( $langs as $language => $name ) {
			echo "['$language', '$name' ]";
		if( ++$i < $c ) echo ',';
		}
		?>
			]
	}),
			displayField:"langname",
			valueField: "language",
			value: "<?php echo ext_Lang::detect_lang() ?>",
			hiddenName: "lang",
			disableKeyFilter: true,
			editable: false,
			triggerAction: "all",
			mode: "local",
			allowBlank: false,
			selectOnFocus:true
		}),
		{
			xtype: "displayfield",
			id: "statusBar"
		}
		],
		buttons: [{
			text: "<?php echo ext_Lang::msg( 'btnlogin', true ) ?>", 
			type: "submit",
			handler: function() {
				Ext.get( "statusBar").update( "Please wait..." );
				Ext.getCmp("simpleform").getForm().submit({
					reset: false,
					success: function(form, action) { location.reload() },
					failure: function(form, action) {
						if( !action.result ) return;
						Ext.Msg.alert('<?php echo ext_Lang::err( 'error', true ) ?>', action.result.error);
						Ext.get( 'statusBar').update( action.result.error );
						form.findField( 'password').setValue('');
						form.findField( 'username').focus();
					},
					scope: Ext.getCmp("simpleform").getForm(),
					params: {
						option: "com_extplorer", 
						action: "login",
						type : "extplorer"
					}
				});
			}
		},<?php if(!ext_isXHR()) { ?>
		{
			text: '<?php echo ext_Lang::msg( 'btnreset', true ) ?>', 
			handler: function() { simple.getForm().reset(); } 
		}
		<?php 
		} else {?>
		{
			text: "<?php echo ext_Lang::msg( 'btncancel', true ) ?>", 
			handler: function() { Ext.getCmp("dialog").destroy(); }
		}
		<?php 
		} ?>
		]
	}
	
	<?php
	}
	function onLogout() {
		logout();
	}
} 
?>