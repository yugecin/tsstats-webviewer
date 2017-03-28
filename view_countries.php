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

	include "countries.php";


	$users = $db->getCountryInfo();
	$activity = $db->getCountryInfoActivity();

	$totalu = 0;
	foreach( $users as $c ) {
		$totalu += $c->c;
	}

	$totala = 0;
	foreach( $activity as $c ) {
		$totala += $c->c;
	}
	
	$t = new Table( 'stick', false );
	$t->header( 'All countries' );
	$t->output();

	$t = new Table( 'listgraph countries r2', false );
	$t->header( 'n&deg;', '# users', 'activity' );
	$t->width( '28' );
	for( $i = 0; $i < count( $users ); $i++ ) {
		$row = array();
		$row[] = $i + 1;

		$c = $users[$i];
		$perc = round( $c->c * 100 / $totalu );
		$row[] = mkcountry( $c->country ) . ': ' . $perc . '% (' . $c->c . ')<div style="margin-left:' . ( 100 - $perc ) . '%;width:' . $perc . '%"></div>';
		
		$c = $activity[$i];
		$perc = round( $c->c * 100 / $totala );
		$row[] = mkcountry( $c->country ) . ': ' . $perc . '% (' . $c->c . ')<div style="width:' . $perc . '%"></div>';

		$t->row( $row );
	}
	$t->output();
	

