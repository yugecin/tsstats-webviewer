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

	$db->getActivity( $tod );
	printgraph( "Activity spreading (%)", $tod );

	$perpage = 100;
	$page = page();

	$search = "";
	if( !empty( $_GET[ "search" ] ) )
	{
		$search = $_GET[ "search" ];
		$clients = $db->findClients( $search, page_min( $page, $perpage ), $perpage );
		$nclients = $db->getClientCountSearch( $search );
	}
	else
	{
		$clients = $db->getClientList( page_min( $page, $perpage ), $perpage );
		$nclients = $db->getClientCount();
	}
	$npages = ceil( $nclients / $perpage );

	$thead = new Table( "stick", 4 );
	$thead->header( "Users, ordered by online time, last seen" );
	$row = array();
	$row[] = "Pages";
	$p = "";
	for( $i = 0; $i < $npages; $i++ ) {
		$ni = $i + 1;
		if( $page == $i ) {
			$p .= "$ni&nbsp;";
		} else {
			$p .= "<a href=\"?search={$search}&page={$i}\">{$ni}</a>&nbsp;";
		}
	}
	$row[] = $p;
	$row[] = 'search';
	$row[] = '<form action="." method="get"><input type="text" name="search" value="' . $search . '" placeholder="filter"/><input type="submit" value="filter"/></form>';
	$thead->row( $row );
	$thead->output();

	$t = new Table( "stick numbered clientlist r3 r5 m3", false );
	$t->header( "n&deg;", "Name", "Time online", "When", "Last seen" );
	$t->width( 28, 'auto', 105, 150, 148 );

	$count = $page * $perpage + 1;
	foreach( $clients as $c ) {
		$td = array();	

		$td[] = $count;
		$td[] = mkflag( $c->country ) . mkclientlink( $c->uid, $c->currentname );
		$td[] = formattime( $c->updates * 300, false );
		$td[] = smalltod( array( $c->t1, $c->t2, $c->t3, $c->t4 ) );
		$td[] = parselastonline( $c->lastonline );

		$count++;
		$t->row( $td );
	}

	$t->output();
	$thead->output();

	echo '<br/><br/>';

