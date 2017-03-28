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

function printgraph( $title, $data )
{
	global $todcolors;

	$sum = array_sum( $data );
	if( $sum == 0 ) return;

	$t = new Table( "stick graph", 24 );
	$t->header( $title );
	
	$height = 100;
	$multiplier = $height / max( $data );
	
	$td = array();
	foreach( $data as $key => $val ) {
	
		$realperc = $val * 100 / $sum;
		//$perc = round( $realperc * $multiplier );
		
		$w = round( $val * $multiplier );
		$m = $height - $w;
		$fpercent = number_format( $realperc, 1 );
		$td[] = "<div style=\"height:{$height}px\"><span style=\"padding-top:{$m}px\">{$fpercent}</span><span style=\"height:{$w}px;background:#{$todcolors[$key/6]}\"></span></div>";
	}
	$t->row( $td );

	$row = array();
	foreach( $data as $key => $val ) {
		$row[] = $key;
	}
	$t->row( $row );

	$t->output();
	largetod( $data );
}

// -------------------------------------------------------------------------------------------------

function largetod( $data )
{
	global $todcolors;

	echo '<div class="bigtod">' . smalltod( largeTodArrayToSmall( $data ) ) . '</div>';
}

// -------------------------------------------------------------------------------------------------

function smalltod( $a )
{
	global $todcolors;

	$sum = array_sum( $a );
	if( $sum == 0 ) return "";
	$left = 0;
	$tod = '<div class="smalltod">';
	for( $i = 0; $i < 4; $i++ ) {
		$perc = round( $a[$i] * 100 / $sum );
		if( $i == 3 ) {
			$perc = 100 - $left;
		}
		$tod .= '<span style="left:' . $left . '%;width:' . $perc . '%;background:#' . $todcolors[$i] . '"></span>';
		$left += $perc;
	}
	$tod .= '</div>';
	return $tod;
}

// -------------------------------------------------------------------------------------------------

function largeTodArrayToSmall( $data )
{
	$new = array( 0, 0, 0, 0 );
	foreach( $data as $key => $value ) {
		$new[$key/6] += $value;
	}
	return $new;
}

// -------------------------------------------------------------------------------------------------

function createTodArray( $data )
{
	$tod = array();
	foreach( $data as $d ) {
		$tod[$d->tod] = $d->count;
	}
	return $tod;
}

// -------------------------------------------------------------------------------------------------

function usedchans( $data, &$max, &$sum ) {
	$max = 0;
	$sum = 0;
	foreach( $data as $chan ) {
		if( $chan->count > $max ) $max = $chan->count;
		$sum += $chan->count;
	}
}

// -------------------------------------------------------------------------------------------------

function usednames( $data, &$max, &$sum ) {
	$max = 0;
	$sum = 0;
	foreach( $data as $name ) {
		if( $name->count > $max ) $max = $name->count;
		$sum += $name->count;
	}
}

// -------------------------------------------------------------------------------------------------

function page() {
	$page = 0;
	if( isset( $_GET[ "page" ] ) && is_numeric( $_GET[ "page" ] ) ) $page = (int)$_GET[ "page" ];
	return max( $page, 0 );
}

// -------------------------------------------------------------------------------------------------

function page_min( $page, $perpage ) {
	return $page * $perpage;
}

// -------------------------------------------------------------------------------------------------

function page_max( $page, $perpage ) {
	return ( $page + 1 ) * $perpage;
}

// -------------------------------------------------------------------------------------------------

function formattime( $time, $includeseconds )
{
	$s = $time%60;
	$minutes = ($time-$s)/60;
	$m = $minutes%60;
	$h = (($minutes-$m)/60)%24;
	$d = ($minutes-$m-$h*60)/60/24;
	
	$ss = ( $includeseconds ) ? two( $s ) . 's' : '';
	$sm = ( $includeseconds && $m == 0 ) ? '' : two( $m ) . 'm';
	$sh = ( $d > 0 || $h > 0 ) ? two( $h ) . 'h' : '';
	$sd = ( $d > 0 ) ? "{$d}d " : '';
	
	return "{$sd}{$sh}{$sm}{$ss}";
}

// -------------------------------------------------------------------------------------------------

function two( $one )
{
	if( $one < 10 ) return "0{$one}";
	return $one;
}

// -------------------------------------------------------------------------------------------------

function parselastonline( $time )
{
	if($time == 0) return "";
	if(time() - $time <= 300) return "now";
	if(date('j/m/Y', $time) == date('j/m/Y')) return "today";
	if(date('m/Y', $time) == date('m/Y') && date('j', $time)+1 == date('j')) return "yesterday";
	return ((int)((time()-$time)/3600/24)+1)." days ago";
}

// -------------------------------------------------------------------------------------------------

function escapename( $name )
{
	return str_replace( array( "&", "<", ">" ), array( "&amp;", "&lt;", "&gt;" ), $name );
}

// -------------------------------------------------------------------------------------------------

function limitlength( $string, $maxlen = null )
{
	if( $maxlen != null && mb_strlen( $string ) > $maxlen ) {
		$string = '<b title="' . str_replace( '"', '\'', $string ) . '">' . mb_substr( $string, 0, $maxlen ) . '&hellip;</b>';
	}
	return $string;
}

// -------------------------------------------------------------------------------------------------

function mknav( $view, $text, $silk )
{
	return '<a href="?view=' . $view . '"><span class="silk ' . $silk . '"></span>' . $text . '</a>';
}

// -------------------------------------------------------------------------------------------------

function mkflag( $code )
{
	$country = getcountry( $code );
	return '<span class="flag flag-' . strtolower( $code ) . '" title="' . $country . '" alt="' . $country . '"></span>';
}

// -------------------------------------------------------------------------------------------------

function mkcountry( $code )
{
	return '<span class="country">' . mkflag( $code ) . getcountry( $code ) . '</span>';
}

// -------------------------------------------------------------------------------------------------

function mkclientlink( $uid, $name, $maxlen = null )
{
	$name = limitlength( escapename( $name ), $maxlen );
	$uid = urlencode( $uid );
	return "<a href=\"?view=client&uid={$uid}\">{$name}</a>";
}

// -------------------------------------------------------------------------------------------------

function mkchanlink( $cid, $name )
{
	return '<a href="?view=channel&cid=' . $cid . '">' . parsespacers( $name, 80 ) . '</a>';
}

// -------------------------------------------------------------------------------------------------

function parsespacers( $name, $length )
{
	$name = escapename( $name );
	$realname = escapename( preg_replace( '/^\[[\*c]?spacer[0-9]+\](.*?)$/is', '$1', $name ) );

	if( stristr( $name, "*spacer" ) ) {
		$realname = substr( str_repeat( $realname, $length / strlen( $realname ) ), 0, $length );
	}
	
	return $realname;
}

// -------------------------------------------------------------------------------------------------

function getcountry( $code )
{
	global $countrycode;
	if( $code == "" || !isset( $countrycode[strtoupper($code)] ) ) return 'UNKNOWN';
	return $countrycode[strtoupper($code)];
}

// -------------------------------------------------------------------------------------------------

