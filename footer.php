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

	$t = new Table( 'footr', false );
	$t->header( '&copy; 2014-' . date('Y') . ' by Robin "yugecin" C.' );
	foreach( $footertexts as $f ) {
		$t->row( array( $f ) );
	}
	$t->row( array( 'Generated in ' . number_format( microtime( true ) - $__time, 7, '.', ' ' ) . 's, ' . $db->queries . ' queries' ) );
	$t->output();

