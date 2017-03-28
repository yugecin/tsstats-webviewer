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

	$bans = $db->getKicks();

	$t = new Table( 'stick', false );
	$t->header( 'All kicks' );
	$t->output();

	$t = new Table( 'numbered r5 m5', false );
	$t->header( 'n&deg;', 'Name', 'By', 'Reason', 'Date' );
	$t->width( 28 );

	$count = 0;
	foreach( $bans as $b ) {
		$td = array();	

		$td[] = ++$count;
		$td[] = mkclientlink( $b->uid, $b->name, 15 );
		$td[] = mkclientlink( $b->invokeruid, $b->invokername, 15 );
		$td[] = limitlength( escapename( $b->reason ), 40 );
		$td[] = date( 'j/m/Y H:i', $b->time );

		$t->row( $td );
	}

	$t->output();

