import './slkwoo-admin.scss';

import apiFetch from '@wordpress/api-fetch';

import { Button, DateTimePicker, Panel, PanelBody, PanelRow, TextControl, ToggleControl } from '@wordpress/components';

import {
	render,
	useState,
	useEffect
} from '@wordpress/element';

import Credit from './components/credit';

const SlkwooAdmin = () => {

	const [ passphraseText, setPassphraseText ] = useState( slkwoo_data.slkwoo_passphrase );
	const [ passphraseLock, setPassphraseLock ] = useState( Boolean( slkwoo_data.slkwoo_passphrase_lock ) );

	const slkwoologs = JSON.parse( slkwoo_data.slkwoo_logs );
	const [ slkLogs, setSlkLogs ] = useState( slkwoologs );

	useEffect( () => {
		apiFetch( {
			path: 'rf/slkwoo-admin_api/token',
			method: 'POST',
			data: {
				text: passphraseText,
				lock: passphraseLock,
				logs: slkLogs,
			}
		} ).then( ( response ) => {
			//console.log( response );
		} );
	}, [ passphraseText, passphraseLock, slkLogs ] );

	const items = [];
	Object.keys( slkwoologs ).map(
		( key ) => {
			if( slkwoologs.hasOwnProperty( key ) ) {
				let items_texts = [];
				let items_date_expiry = [];
				Object.keys( slkwoologs[ key ] ).map(
					( key2 ) => {
						if ( 'date_expiry' == key2 ) {
							items_date_expiry.push(
								<td>
								<PanelBody
									title = { slkLogs[ key ][ key2 ] }
									icon = ""
									initialOpen = { false }
									className = "date_expiry_color"
								>
									<PanelRow>
										<DateTimePicker
											currentDate = { slkLogs[ key ][ key2 ] }
											onChange = { ( newDate ) =>
												{
													let newDate2 = newDate.replace( 'T', ' ' );
													slkLogs[ key ][ key2 ] = newDate2;
													let data = Object.assign( {}, slkLogs );
													setSlkLogs( data );
												}
											}
										/>
									</PanelRow>
								</PanelBody>
								</td>
							);
						} else {
							if ( 'expiry_stamp' !== key2 ) {
								items_texts.push(
									<td>{ slkwoologs[ key ][ key2 ] }</td>
								);
							}
						}
					}
				);
				items.push(
					<tr>
						{ items_texts }
						{ items_date_expiry }
					</tr>
				);
			}
		}
	);

	const items_generate_lock = [];
	if ( passphraseLock ) {
		items_generate_lock.push(
			<>
				<span className="pass_text">
					{ passphraseText }
				</span>
				&nbsp;&nbsp;&nbsp;&nbsp;
			</>
		);
	} else {
		items_generate_lock.push(
			<>
				<TextControl
					value = { passphraseText }
					onChange = { ( value ) => setPassphraseText( value ) }
				/>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<Button
					className = "button button-large"
					onClick = { () =>
						{
							let str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#$%&=~/*-+";
							let len = 12;
							let result = "";
							for ( var i=0; i<len; i++ ) {
								result += str.charAt( Math.floor( Math.random() * str.length ) );
							}
							setPassphraseText( result );
						}
					}
				>
				{ slkwoo_data.generate }
				</Button>
				&nbsp;&nbsp;&nbsp;&nbsp;
			</>
		);
	}
	items_generate_lock.push(
		<ToggleControl
			label = { slkwoo_data.lock }
			help = {
				passphraseLock
					? slkwoo_data.lock_text
					: slkwoo_data.unlock_text
			}
			checked = { passphraseLock }
			onChange = { () => {
				setPassphraseLock( ( state ) => ! state );
			} }
		/>
	);

	return (
		<div className="wrap">
		<h2>Simple License Key for WooCommerce</h2>
			<Credit />
			<div className="wrap">
				<hr />
				<h3>{ slkwoo_data.passphrase_text }</h3>
				<div className="pass_button_line">
					{ items_generate_lock }
				</div>
				<hr />
				<h3>{ slkwoo_data.decrypt_text }</h3>
				<p className="description">{ slkwoo_data.decrypt_description }</p>
				<div>
					PHP : <code>openssl_decrypt( $encrypt_data, 'aes-256-cfb', </code>
					<span className="pass_code_text">{ "'" + passphraseText + "'" }</span>
					<code>, 0, openssl_cipher_iv_length( 'aes-256-cfb' ) );</code>
				</div>
				<hr />
				<h3>REST API URL</h3>
				<p className="description">{ slkwoo_data.rest_api_description }</p>
				<div>
					<a href={ slkwoo_data.apiurl } target="_blank" rel="noopener noreferrer">{ slkwoo_data.apiurl }</a>
				</div>
				<hr />
				<h3>{ slkwoo_data.logs }</h3>
				<p className="description">{ slkwoo_data.logs_description }</p>
				<table border="1" cellspacing="0" cellpadding="5" bordercolor="#000000" className="tableStyle">
					<tr>
						<td>{ slkwoo_data.product_id }</td>
						<td>{ slkwoo_data.product_name }</td>
						<td>{ slkwoo_data.passphrase }</td>
						<td>{ slkwoo_data.encrypt_data }</td>
						<td>{ slkwoo_data.name }</td>
						<td>{ slkwoo_data.mail }</td>
						<td>{ slkwoo_data.date }</td>
						<td>{ slkwoo_data.expiry_date }</td>
					</tr>
					{ items }
				</table>
			</div>
		</div>
	);

};

render(
	<SlkwooAdmin />,
	document.getElementById( 'slkwooadmin' )
);

