( function ( $ ) {
	"use strict";

	function hideShowGate( checked, env = null )
	{
		let hml_tkn____ = $( "#triibo_api_services_gate_hml_tkn"     ),
			hml_chn_id_ = $( "#triibo_api_services_gate_hml_chn_id"  ),
			hml_chn_tkn = $( "#triibo_api_services_gate_hml_chn_tkn" ),
			prd_tkn____ = $( "#triibo_api_services_gate_prd_tkn"     ),
			prd_chn_id_ = $( "#triibo_api_services_gate_prd_chn_id"  ),
			prd_chn_tkn = $( "#triibo_api_services_gate_prd_chn_tkn" );

		switch ( checked )
		{
			case 1:
				hml_tkn____.closest( "tr" ).show();
				hml_chn_id_.closest( "tr" ).show();
				hml_chn_tkn.closest( "tr" ).show();
				prd_tkn____.closest( "tr" ).hide();
				prd_chn_id_.closest( "tr" ).hide();
				prd_chn_tkn.closest( "tr" ).hide();

				prd_tkn____.removeAttr( "required" );
				prd_chn_id_.removeAttr( "required" );
				prd_chn_tkn.removeAttr( "required" );

				if ( env )
				{
					hml_tkn____.prop( "required", true );
					hml_chn_id_.prop( "required", true );
					hml_chn_tkn.prop( "required", true );
				}
				else
				{
					hml_tkn____.removeAttr( "required" );
					hml_chn_id_.removeAttr( "required" );
					hml_chn_tkn.removeAttr( "required" );
				}
			break;

			case 2:
				hml_tkn____.closest( "tr" ).hide();
				hml_chn_id_.closest( "tr" ).hide();
				hml_chn_tkn.closest( "tr" ).hide();
				prd_tkn____.closest( "tr" ).show();
				prd_chn_id_.closest( "tr" ).show();
				prd_chn_tkn.closest( "tr" ).show();

				hml_tkn____.removeAttr( "required" );
				hml_chn_id_.removeAttr( "required" );
				hml_chn_tkn.removeAttr( "required" );

				if ( env )
				{
					prd_tkn____.prop( "required", true );
					prd_chn_id_.prop( "required", true );
					prd_chn_tkn.prop( "required", true );
				}
				else
				{
					prd_tkn____.removeAttr( "required" );
					prd_chn_id_.removeAttr( "required" );
					prd_chn_tkn.removeAttr( "required" );
				}
			break;

			case 3:
				hml_tkn____.removeAttr( "required" );
				hml_chn_id_.removeAttr( "required" );
				hml_chn_tkn.removeAttr( "required" );
				prd_tkn____.removeAttr( "required" );
				prd_chn_id_.removeAttr( "required" );
				prd_chn_tkn.removeAttr( "required" );

				if ( env == 1 )
				{
					hml_tkn____.closest( "tr" ).show();
					hml_chn_id_.closest( "tr" ).show();
					hml_chn_tkn.closest( "tr" ).show();
					prd_tkn____.closest( "tr" ).hide();
					prd_chn_id_.closest( "tr" ).hide();
					prd_chn_tkn.closest( "tr" ).hide();
				}
				else // 2
				{
					hml_tkn____.closest( "tr" ).hide();
					hml_chn_id_.closest( "tr" ).hide();
					hml_chn_tkn.closest( "tr" ).hide();
					prd_tkn____.closest( "tr" ).show();
					prd_chn_id_.closest( "tr" ).show();
					prd_chn_tkn.closest( "tr" ).show();
				}
			break;
		}
	}
	function handlerGate()
	{
		let gate_stt = $( "#triibo_api_services_gate_status" ).is( ":checked" );
		let gate_env = $( "#triibo_api_services_gate_env" ).is( ":checked" );
		if ( !gate_stt )
			hideShowGate( 3, gate_env ? 1 : 2 );
		else
			hideShowGate( gate_env ? 1 : 2, gate_stt );
	}


	function hideShowNode( checked, env = null )
	{
		let hml_user = $( "#triibo_api_services_node_hml_user" ),
			hml_pass = $( "#triibo_api_services_node_hml_pass" ),
			prd_user = $( "#triibo_api_services_node_prd_user" ),
			prd_pass = $( "#triibo_api_services_node_prd_pass" );

		switch ( checked )
		{
			case 1:
				hml_user.closest( "tr" ).show();
				hml_pass.closest( "tr" ).show();
				prd_user.closest( "tr" ).hide();
				prd_pass.closest( "tr" ).hide();

				prd_user.removeAttr( "required" );
				prd_pass.removeAttr( "required" );

				if ( env )
				{
					hml_user.prop( "required", true );
					hml_pass.prop( "required", true );
				}
				else
				{
					hml_user.removeAttr( "required" );
					hml_pass.removeAttr( "required" );
				}
			break;

			case 2:
				hml_user.closest( "tr" ).hide();
				hml_pass.closest( "tr" ).hide();
				prd_user.closest( "tr" ).show();
				prd_pass.closest( "tr" ).show();

				hml_user.removeAttr( "required" );
				hml_pass.removeAttr( "required" );

				if ( env )
				{
					prd_user.prop( "required", true );
					prd_pass.prop( "required", true );
				}
				else
				{
					prd_user.removeAttr( "required" );
					prd_pass.removeAttr( "required" );
				}
			break;

			case 3:
				hml_user.removeAttr( "required" );
				hml_pass.removeAttr( "required" );
				prd_user.removeAttr( "required" );
				prd_pass.removeAttr( "required" );

				if ( env == 1 )
				{
					hml_user.closest( "tr" ).show();
					hml_pass.closest( "tr" ).show();
					prd_user.closest( "tr" ).hide();
					prd_pass.closest( "tr" ).hide();
				}
				else // 2
				{
					hml_user.closest( "tr" ).hide();
					hml_pass.closest( "tr" ).hide();
					prd_user.closest( "tr" ).show();
					prd_pass.closest( "tr" ).show();
				}
			break;
		}
	}
	function handlerNode()
	{
		let node_stt = $( "#triibo_api_services_node_status" ).is( ":checked" );
		let node_env = $( "#triibo_api_services_node_env"    ).is( ":checked" );
		if ( !node_stt )
			hideShowNode( 3, node_env ? 1 : 2 );
		else
			hideShowNode( node_env ? 1 : 2, node_stt );
	}


	function hideShowPhp( checked, env = null )
	{
		let hml_key = $( "#triibo_api_services_php_hml_key" ),
			hml_tkn = $( "#triibo_api_services_php_hml_tkn" ),
			prd_key = $( "#triibo_api_services_php_prd_key" ),
			prd_tkn = $( "#triibo_api_services_php_prd_tkn" );

		switch ( checked )
		{
			case 1:
				hml_key.closest( "tr" ).show();
				hml_tkn.closest( "tr" ).show();
				prd_key.closest( "tr" ).hide();
				prd_tkn.closest( "tr" ).hide();

				prd_key.removeAttr( "required" );
				prd_tkn.removeAttr( "required" );

				if ( env )
				{
					hml_key.prop( "required", true );
					hml_tkn.prop( "required", true );
				}
				else
				{
					hml_key.removeAttr( "required" );
					hml_tkn.removeAttr( "required" );
				}
			break;

			case 2:
				hml_key.closest( "tr" ).hide();
				hml_tkn.closest( "tr" ).hide();
				prd_key.closest( "tr" ).show();
				prd_tkn.closest( "tr" ).show();

				hml_key.removeAttr( "required" );
				hml_tkn.removeAttr( "required" );

				if ( env )
				{
					prd_key.prop( "required", true );
					prd_tkn.prop( "required", true );
				}
				else
				{
					prd_key.removeAttr( "required" );
					prd_tkn.removeAttr( "required" );
				}
			break;

			case 3:
				hml_key.removeAttr( "required" );
				hml_tkn.removeAttr( "required" );
				prd_key.removeAttr( "required" );
				prd_tkn.removeAttr( "required" );

				if ( env == 1 )
				{
					hml_key.closest( "tr" ).show();
					hml_tkn.closest( "tr" ).show();
					prd_key.closest( "tr" ).hide();
					prd_tkn.closest( "tr" ).hide();
				}
				else // 2
				{
					hml_key.closest( "tr" ).hide();
					hml_tkn.closest( "tr" ).hide();
					prd_key.closest( "tr" ).show();
					prd_tkn.closest( "tr" ).show();
				}
			break;
		}
	}
	function handlerPhp()
	{
		let php_stt = $( "#triibo_api_services_php_status" ).is( ":checked" );
		let php_env = $( "#triibo_api_services_php_env"    ).is( ":checked" );
		if ( !php_stt )
			hideShowPhp( 3, php_env ? 1 : 2 );
		else
			hideShowPhp( php_env ? 1 : 2, php_stt );
	}


	$( document ).on( "change", "#triibo_api_services_gate_env", function() {
		let checked  = $( this ).is( ":checked" ) ? 1 : 2;
		let gate_stt = $( "#triibo_api_services_gate_status" ).is( ":checked" );
		hideShowGate( checked, gate_stt );
	} );
	$( document ).on( "change", "#triibo_api_services_node_env", function() {
		let checked  = $( this ).is( ":checked" ) ? 1 : 2;
		let node_stt = $( "#triibo_api_services_node_status" ).is( ":checked" );
		hideShowNode( checked, node_stt );
	} );
	$( document ).on( "change", "#triibo_api_services_php_env", function() {
		let checked = $( this ).is( ":checked" ) ? 1 : 2;
		let php_stt = $( "#triibo_api_services_php_status" ).is( ":checked" );
		hideShowPhp( checked, php_stt );
	} );

	$( document ).on( "change", "#triibo_api_services_gate_status", function() {
		handlerGate();
	} );
	$( document ).on( "change", "#triibo_api_services_node_status", function() {
		handlerNode();
	} );
	$( document ).on( "change", "#triibo_api_services_php_status", function() {
		handlerPhp();
	} );

	$( document ).ready( function() {
		handlerGate();
		handlerNode();
		handlerPhp();

		let el1 = $( "#triibo_api_services_node_status" ).parent().parent().parent().parent().prev().prev();
		let el2 = $( "#triibo_api_services_php_status" ).parent().parent().parent().parent().prev().prev();
		el1.css( "border-top", "1px solid #CCC" ).css( "padding-top", "2rem" );
		el2.css( "border-top", "1px solid #CCC" ).css( "padding-top", "2rem" );
	} );

} )( jQuery );
