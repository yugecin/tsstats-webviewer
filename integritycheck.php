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

	if( php_sapi_name() !== 'cli' ) {
		die( 'commandline only kthxbai' );
	}

	include "db.php";

	$db = new Database();

	// fix
	$values = '';
	for( $i = 0; $i < 24; $i++ ) {
		$values .= ",(?,{$i},0)";
	}
	$values = substr( $values, 1 );
$sql = <<<SQL
INSERT IGNORE INTO tod
(uid,tod,`count`)
VALUES
$values
SQL;
	$fix = $db->freeStatement( $sql );
	function fixuser( $uid )
	{
		global $fix;
		for( $i = 0; $i < 24;) {
			$fix->bindValue( ++$i, $uid );
		}
		$fix->execute();
	}
	// fix

	try
	{
$sql = <<<SQL
SELECT uid, count(tod) AS c
FROM tod
GROUP BY uid
SQL;
		$stmt = $db->freeStatement( $sql );
		$stmt->execute();
		//$info = $stmt->fetchAll( PDO::FETCH_CLASS );
		$info = $stmt->fetchAll();

		echo( 'checking 24 hours per user' . "\n" );
		foreach( $info as $user ) {
			if( $user['c'] != 24 ) {
				echo( 'incorrect hours for uid ' . $user['uid'] . ' (' . $user['c'] . ')...' );
				fixuser( $user['uid'] );
				echo( 'fixed' . "\n" );
			}
		}

$sql = <<<SQL
SELECT uid
FROM users
WHERE uid NOT IN (
	SELECT DISTINCT(uid)
	FROM tod
)
SQL;
		$stmt = $db->freeStatement( $sql );
		$stmt->execute();
		//$info = $stmt->fetchAll( PD0::FETCH_CLASS );
		$info = $stmt->fetchAll();

		echo( 'checking users without hours' . "\n" );
		if( count( $info ) > 0 ) {
			echo( 'some users don\'t have hours!' . "\n" );
			foreach( $info as $user ) {
				fixuser( $user['uid'] );
				echo( 'fixed user ' . $user['uid'] . "\n" );
			}
		}
	}
	catch ( PDOException $e )
	{
		die( 'Database panic!<br/>' . $e->getMessage() );
	}



