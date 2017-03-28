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

	$tod = $db->getClientTod( $data->uid );
	$kicksby = $db->getKicksBy( $data->uid );
	$kicksto = $db->getKicksTo( $data->uid );
	$bansby = $db->getBansBy( $data->uid );
	$bansto = $db->getBansTo( $data->uid );
	$bantimeby = $db->getBanTimeBy( $data->uid );
	$bantimeto = $db->getBanTimeTo( $data->uid );
	
	if( $data->country == "" ) $c = 'Unknown';
	else $c = $countrycode[ strtoupper( $data->country ) ];
	
	printgraph( 'Activity spreading (%)', createTodArray( $tod ) );

	$names = $db->getUsedNames( $data->uid );
	$chans = $db->getUsedChans( $data->uid );
	usednames( $names, $maxnames, $sumnames );
	usedchans( $chans, $maxchans, $sumchans );
	$countnames = count( $names );
	$countchans = count( $chans );
	$max = max( $countnames, $countchans );
	
	$t = new Table( 'used r1', false );
	$t->header( 'Names', 'n&deg;', 'Channels' );
	$t->width( '354', '28', '354' );
	for( $i = 0; $i < $max; $i++ ) {
		$row = array();
		if( $i < $countnames ) {
			$perc = round( $names[$i]->count * 100 / $sumnames );
			$name = escapename( $names[$i]->name ) . ' (' . $perc . '%)';
			if( $names[$i]->name == $data->currentname ) {
				$name = '<strong>' . $name . '</strong>';
			}
			$row[] = $name . '<div style="margin-left:' . ( 100 - $perc ) . '%;width:' . $perc . '%"></div>';
		} else {
			$row[] = '&nbsp;';
		}
		$row[] = $i + 1;
		if( $i < $countchans ) {
			$perc = round( $chans[$i]->count * 100 / $sumchans );
			$row[] = '(' . $perc . '%) ' . mkchanlink( $chans[$i]->cid, $chans[$i]->name ) . '<div style="width:' . $perc . '%"></div>';
		} else {
			$row[] = '&nbsp;';
		}
		$t->row( $row );
	}
	$t->output();

	$t = new Table( 'various r1', 2 );
	$t->header( 'Various stats' );
	$t->row( array( 'Country', mkcountry( $data->country ) ) );
	$t->row( array( 'Last online', parselastonline( $data->lastonline ) . ' - ' . date( 'j/m/Y H:i:s', $data->lastonline ) ) );
	$t->row( array( 'Time online', formattime( $data->updates * 300, false ) ) );
	$t->row( array( 'Timeouts', $data->timeouts ) );
	$t->row( array( 'Kicks received', $kicksto ) );
	$t->row( array( 'Bans received', $bansto ) );
	$t->row( array( 'Total bantime received', formattime( $bantimeto, true ) ) );
	$t->row( array( 'Kicks given', $kicksby ) );
	$t->row( array( 'Bans given', $bansby ) );
	$t->row( array( 'Total bantime given', formattime( $bantimeby, true ) ) );
	$t->output();

