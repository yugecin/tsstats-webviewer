<?php
/*
 * Webviewer for my teamspeak3 statistics bot - see github.com/yugecin/tsstats
 * Copyright (C) 2014-2017  Robin C.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Based on: Teamspeak 3 viewer for php5 version 2013-08-31
 * Original author: Sebastien Gerard <seb@sebastien.me>
 * see http://tsstatus.sebastien.me/
 **/

function ts_unescape( $str )
{
	return str_replace( array(
		'\\\\',
		"\/",
		"\s",
		"\p",
		"\a",
		"\b",
		"\f",
		"\n",
		"\r",
		"\t",
		"\v"
	), array(
		chr(92),
		chr(47),
		chr(32),
		chr(124),
		chr(7),
		chr(8),
		chr(12),
		chr(10),
		chr(3),
		chr(9),
		chr(11)
	), $str );
}

// -------------------------------------------------------------------------------------------------

function ts_parseline( $rawLine )
{
	$datas = array();
	$rawItems = explode( '|', $rawLine );
	foreach( $rawItems as $rawItem )
	{
		$rawDatas = explode( ' ', $rawItem );
		$tempDatas = array();
		foreach( $rawDatas as $rawData )
		{
			$ar = explode( '=', $rawData, 2 );
			$tempDatas[$ar[0]] = isset( $ar[1] ) ? ts_unescape( $ar[1] ) : '';
		}
		$datas[] = $tempDatas;
	}
	return $datas;
}


// -------------------------------------------------------------------------------------------------

function ts_parse( $response, &$sdata, &$cdata, &$udata, &$sgroupicons, &$cgroupicons )
{
	$lines = explode( "error id=0 msg=ok\n\r" , $response );
	if( count( $lines ) && !strlen( $lines[0] ))
	{
		array_shift( $lines );
	}
	if( count( $lines ) >= 5 )
	{
		$sdata = ts_parseline( $lines[0] );
		$sdata = $sdata[0];

		$cdata = ts_parseline( $lines[1] );

		$udata = ts_parseline( $lines[2] );

		$sgroups = ts_parseline($lines[3]);
		foreach( $sgroups as $sg ) {
			if( $sg['iconid'] > 0 ) {
				$sgroupicons[$sg['sgid']] = 'sgroup_' . $sg['sgid'];
			}
		}
		
		$cgroups = ts_parseline( $lines[4] );
		foreach( $cgroups as $cg ) {
			if( $cg['iconid'] > 0 ) {
				$cgroupicons[$cg['cgid']] = 'cgroup_' . $cg['cgid'];
			}
		}
		return true;
	}
	return false;
}

// -------------------------------------------------------------------------------------------------

function ts_render( $response )
{
	if( !ts_parse( $response, $sdata, $cdata, $udata, $sgroupicons, $cgroupicons ) )
	{
		return 'could not parse server response data';
	}
	$data = '';
	ts_row( $data, 0, 'server_green', $sdata['virtualserver_name'], 'p' );
	if( count( $cdata ) > 0 ) {
		ts_renderchannel( $data, $udata, $sgroupicons, $cgroupicons, $cdata, 0, 0 );
	}
	return $data;
}

// -------------------------------------------------------------------------------------------------

function ts_get_user_count( $response )
{
	ts_parse( $response, $sdata, $cdata, $udata, $sgroupicons, $cgroupicons );
	return count( $udata );
}

// -------------------------------------------------------------------------------------------------

function ts_icon( $icon )
{
	return '<img src="imgres/' . $icon . '.png" />';
}

// -------------------------------------------------------------------------------------------------

function ts_flags( $flags )
{
	if( empty( $flags ) ){
		return '';
	}

	if( is_array( $flags ) ) {
		$i = '<em>';
		foreach( $flags as $icon ) {
			$i .= ts_icon( $icon );
		}
		return $i . '</em>';
	} else {
		return '<em>' . ts_icon( $flags ) . '</em>';
	}
}

// -------------------------------------------------------------------------------------------------

function ts_row( &$data, $indent, $icon, $text, $flags )
{
	$data .= '<p><span style="margin-left:' . ( 1.7 * $indent ) . 'em;">' . ts_icon( $icon ) . $text . '</span>'. ts_flags( $flags ) . '</p>';
}


// -------------------------------------------------------------------------------------------------

function ts_renderchannel( &$data, $udata, $sgroupicons, $cgroupicons, $cdata, $pid, $depth )
{
	foreach( $cdata as $chan )
	{
		if( $chan['pid'] == $pid ) {

			$icon = 'channel_green';
			if( $chan['channel_maxclients'] > -1 && $chan['total_clients'] >= $chan['channel_maxclients'] ) {
				$icon = 'channel_red';
			} else if( $chan['channel_maxfamilyclients'] > -1 && $chan['total_clients_family'] >= $chan['channel_maxfamilyclients'] ) {
				$icon = 'channel_red';
			} else if( $chan['channel_flag_password'] == 1) {
				$icon = 'channel_yellow';
			}
			if( preg_match( '/^\[[\*c]?spacer[0-9]+\](.*?)$/is', $chan['channel_name'] ) ) {
				$icon = 'p';
			}
			
			$flags = array();
			if( $chan['channel_flag_default'] == 1) $flags[] = 'default';
			if( $chan['channel_needed_talk_power'] > 0) $flags[] = 'moderated';
			if( $chan['channel_flag_password'] == 1) $flags[] = 'register';
			
			ts_row( $data, $depth, $icon, mkchanlink( $chan['cid'], $chan['channel_name'], 60 ), $flags );

			if( count( $udata ) > 0 ) {
				ts_renderuser( $data, $udata, $sgroupicons, $cgroupicons, $chan['cid'], $depth + 1 );
			}
			
			ts_renderchannel( $data, $udata, $sgroupicons, $cgroupicons, $cdata, $chan['cid'], $depth + 1 );
		}
	}
}

// -------------------------------------------------------------------------------------------------

function ts_renderuser( &$data, $udata, $sgroupicons, $cgroupicons, $pid, $depth )
{
	foreach( $udata as $usr)
	{
		if( $usr['cid'] == $pid && $usr['client_type'] == 0 ) {

			$icon = 'player_off';
			if($usr['client_away'] == 1) {
				$icon = 'away';
			} else if( $usr['client_flag_talking'] == 1 ) {
				$icon = 'player_on';
			} else if( $usr['client_output_hardware'] == 0 ) {
				$icon = 'hardware_output_muted';
			} else if( $usr['client_output_muted'] == 1 ) {
				$icon = 'output_muted';
			} else if( $usr['client_input_hardware'] == 0 ) {
				$icon = 'hardware_input_muted';
			} else if( $usr['client_input_muted'] == 1 ) {
				$icon = 'input_muted';
			}
			
			$flags = array();
			if( isset( $cgroupicons[$usr['client_channel_group_id']] ) ) {
				$flags[] = $cgroupicons[$usr['client_channel_group_id']];
			}
			
			$sgroups = explode( ',', $usr['client_servergroups'] );
			foreach( $sgroups as $sgroup ) {
				if( isset( $sgroupicons[$sgroup] ) ) { 
					$flags[] = $sgroupicons[$sgroup];
				}
			}
			
			ts_row( $data, $depth, $icon, mkclientlink( $usr['client_unique_identifier'], $usr['client_nickname'] ), $flags );
		}
	}
}

