( function ( $ ) {
	"use strict";

	function hideShowGateway( checked )
	{
		let hml_tkn____ = $( "#triibo_api_services_gate_hml_tkn"     ),
			hml_chn_id_ = $( "#triibo_api_services_gate_hml_chn_id"  ),
			hml_chn_tkn = $( "#triibo_api_services_gate_hml_chn_tkn" ),
			prd_tkn____ = $( "#triibo_api_services_gate_prd_tkn"     ),
			prd_chn_id_ = $( "#triibo_api_services_gate_prd_chn_id"  ),
			prd_chn_tkn = $( "#triibo_api_services_gate_prd_chn_tkn" );

		if ( checked )
		{
			hml_tkn____.closest( "tr" ).show();
			hml_chn_id_.closest( "tr" ).show();
			hml_chn_tkn.closest( "tr" ).show();
			prd_tkn____.closest( "tr" ).hide();
			prd_chn_id_.closest( "tr" ).hide();
			prd_chn_tkn.closest( "tr" ).hide();

			hml_tkn____.prop( "required", true );
			hml_chn_id_.prop( "required", true );
			hml_chn_tkn.prop( "required", true );
			prd_tkn____.removeAttr( "required" );
			prd_chn_id_.removeAttr( "required" );
			prd_chn_tkn.removeAttr( "required" );
		}
		else
		{
			hml_tkn____.closest( "tr" ).hide();
			hml_chn_id_.closest( "tr" ).hide();
			hml_chn_tkn.closest( "tr" ).hide();
			prd_tkn____.closest( "tr" ).show();
			prd_chn_id_.closest( "tr" ).show();
			prd_chn_tkn.closest( "tr" ).show();

			hml_tkn____.removeAttr( "required" );
			hml_chn_id_.removeAttr( "required" );
			hml_chn_tkn.removeAttr( "required" );
			prd_tkn____.prop( "required", true );
			prd_chn_id_.prop( "required", true );
			prd_chn_tkn.prop( "required", true );
		}
	}

	function hideShowNode( checked )
	{
		let hml_user = $( "#triibo_api_services_node_hml_user" ),
			hml_pass = $( "#triibo_api_services_node_hml_pass" ),
			prd_user = $( "#triibo_api_services_node_prd_user" ),
			prd_pass = $( "#triibo_api_services_node_prd_pass" );

		if ( checked )
		{
			hml_user.closest( "tr" ).show();
			hml_pass.closest( "tr" ).show();
			prd_user.closest( "tr" ).hide();
			prd_pass.closest( "tr" ).hide();

			hml_user.prop( "required", true );
			hml_pass.prop( "required", true );
			prd_user.removeAttr( "required" );
			prd_pass.removeAttr( "required" );
		}
		else
		{
			hml_user.closest( "tr" ).hide();
			hml_pass.closest( "tr" ).hide();
			prd_user.closest( "tr" ).show();
			prd_pass.closest( "tr" ).show();

			hml_user.removeAttr( "required" );
			hml_pass.removeAttr( "required" );
			prd_user.prop( "required", true );
			prd_pass.prop( "required", true );
		}
	}

	function hideShowPhp( checked )
	{
		let hml_key = $( "#triibo_api_services_php_hml_key" ),
			hml_tkn = $( "#triibo_api_services_php_hml_tkn" ),
			prd_key = $( "#triibo_api_services_php_prd_key" ),
			prd_tkn = $( "#triibo_api_services_php_prd_tkn" );

		if ( checked )
		{
			hml_key.closest( "tr" ).show();
			hml_tkn.closest( "tr" ).show();
			prd_key.closest( "tr" ).hide();
			prd_tkn.closest( "tr" ).hide();

			hml_key.prop( "required", true );
			hml_tkn.prop( "required", true );
			prd_key.removeAttr( "required" );
			prd_tkn.removeAttr( "required" );
		}
		else
		{
			hml_key.closest( "tr" ).hide();
			hml_tkn.closest( "tr" ).hide();
			prd_key.closest( "tr" ).show();
			prd_tkn.closest( "tr" ).show();

			hml_key.removeAttr( "required" );
			hml_tkn.removeAttr( "required" );
			prd_key.prop( "required", true );
			prd_tkn.prop( "required", true );
		}
	}

	$( document ).on( "change", "#triibo_api_services_gate_env", function() {
		hideShowGateway( $( this ).is( ":checked" ) );
	} );
	$( document ).on( "change", "#triibo_api_services_node_env", function() {
		hideShowNode( $( this ).is( ":checked" ) );
	} );
	$( document ).on( "change", "#triibo_api_services_php_env", function() {
		hideShowPhp( $( this ).is( ":checked" ) );
	} );

	$( document ).ready( function() {
		hideShowGateway( $( "#triibo_api_services_gate_env" ).val() );
		hideShowNode( $( "#triibo_api_services_node_env" ).val() );
		hideShowPhp( $( "#triibo_api_services_php_env"  ).val() );
	} );

} )( jQuery );
