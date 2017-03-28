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

include "config.php";

class Database
{

	private $dbh;
	public $queries;

	// -----------------------------------------------------------------------------------------

	public function __construct()
	{
		$this->queries = 0;
		try
		{
			$this->dbh = new PDO( "mysql:host=" . MYSQL_HOST . "; dbname=" . MYSQL_DB, MYSQL_USER, MYSQL_PASSWORD );
			$this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$stmt = $this->dbh->prepare( "SET NAMES utf8" );
			$stmt->execute();
			$this->queries++;
		}
		catch ( PDOException $e )
		{
			die( "Database panic!<br/>" . $e->getMessage() );
		}
	}

	// -----------------------------------------------------------------------------------------

	public function freeStatement( $sql )
	{
		return $this->dbh->prepare( $sql );
	}

	// -----------------------------------------------------------------------------------------

	public function getServerInfo( &$info )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT lastupdate FROM serverinfo" );
			$stmt->execute();
			$this->queries++;
			$info = $stmt->fetchAll();
			$info = $info[0];
		}
		catch ( PDOException $e )
		{
			die( "Failed to load serverinfo.<br/>" . $e->getMessage() );
			return false;
		}
	}

	// -----------------------------------------------------------------------------------------

	public function getActivity( &$tod )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT SUM(count) FROM tod GROUP BY tod" );
			$stmt->execute();
			$this->queries++;
			$info = $stmt->fetchAll( PDO::FETCH_NUM );
			$tod = array();
			foreach( $info as $i ) {
				$tod[] = $i[0];
			}
		}
		catch ( PDOException $e )
		{
			die( "Failed to load activity.<br/>" . $e->getMessage() );
			return false;
		}
	}

	// -----------------------------------------------------------------------------------------

	public function getCountryInfo()
	{
		try
		{
$sql = <<<SQL
SELECT country, COUNT(uid) AS c
FROM users
GROUP BY country
ORDER BY c DESC
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->execute();
			$this->queries++;
			$info = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load countryinfo.<br/>" . $e->getMessage() );
			return false;
		}
		return $info;
	}

	// -----------------------------------------------------------------------------------------

	public function getCountryInfoActivity()
	{
		try
		{
$sql = <<<SQL
SELECT country, SUM(updates) AS c
FROM users
GROUP BY country
ORDER BY c DESC
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->execute();
			$this->queries++;
			$info = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load countryinfo.<br/>" . $e->getMessage() );
			return false;
		}
		return $info;
	}

	// -----------------------------------------------------------------------------------------

	public function getClient( $uid )
	{
		try
		{
$sql = <<<SQL
SELECT uid, timeouts, updates, lastonline, currentname, country
FROM users
WHERE uid = ?
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load client.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 )
		{
			return $data[ 0 ];
		}

		return false;
	}

	// -----------------------------------------------------------------------------------------
	public function getClientTod( $uid )

	{
		try
		{
			$stmt = $this->dbh->prepare( 'SELECT tod, count FROM tod WHERE uid = ?' );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load client.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function findClients( $search, $from, $to )
	{

$sql = <<<SQL
SELECT uid, updates, lastonline, currentname, country,
(SELECT SUM(count) FROM tod WHERE uid = users.uid AND tod BETWEEN 0 AND 5) AS t1,
(SELECT SUM(count) FROM tod WHERE uid = users.uid AND tod BETWEEN 6 AND 11) AS t2,
(SELECT SUM(count) FROM tod WHERE uid = users.uid AND tod BETWEEN 12 AND 17) AS t3,
(SELECT SUM(count) FROM tod WHERE uid = users.uid AND tod BETWEEN 18 AND 23) AS t4
FROM users
WHERE currentname LIKE ?
ORDER BY updates DESC, lastonline DESC
LIMIT ?,?
SQL;
		try
		{
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, "%{$search}%" );
			$stmt->bindValue( 2, $from, PDO::PARAM_INT );
			$stmt->bindValue( 3, $to, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load clients.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getClientList( $from, $to )
	{

$sql = <<<SQL
SELECT uid, updates, lastonline, currentname, country,
(SELECT SUM(count) FROM tod WHERE uid = users.uid AND tod BETWEEN 0 AND 5) AS t1,
(SELECT SUM(count) FROM tod WHERE uid = users.uid AND tod BETWEEN 6 AND 11) AS t2,
(SELECT SUM(count) FROM tod WHERE uid = users.uid AND tod BETWEEN 12 AND 17) AS t3,
(SELECT SUM(count) FROM tod WHERE uid = users.uid AND tod BETWEEN 18 AND 23) AS t4
FROM users
ORDER BY updates DESC, lastonline DESC
LIMIT ?,?
SQL;
		try
		{
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $from, PDO::PARAM_INT );
			$stmt->bindValue( 2, $to, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load clients.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getClientCount()
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT COUNT(uid) AS n FROM users" );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load clients.<br/>" . $e->getMessage() );
			return false;
		}

		return $data[0]->n;
	}

	// -----------------------------------------------------------------------------------------

	public function getClientCountSearch( $name )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT COUNT(uid) AS n FROM users WHERE currentname LIKE ?" );
			$stmt->bindValue( 1, "%{$name}%" );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load clients.<br/>" . $e->getMessage() );
			return false;
		}

		return $data[0]->n;
	}

	// -----------------------------------------------------------------------------------------

	public function getKicksBy( $uid )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT COUNT(invokeruid) AS kicks FROM kicks WHERE invokeruid=?" );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load kick stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) return $data[0]->kicks;
		return 0;
	}

	// -----------------------------------------------------------------------------------------

	public function getKicksTo( $uid )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT COUNT(uid) AS kicks FROM kicks WHERE uid=?" );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load kick stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) return $data[0]->kicks;
		return 0;
	}

	// -----------------------------------------------------------------------------------------

	public function getBansBy( $uid )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT COUNT(invokeruid) AS bans FROM bans WHERE invokeruid=?" );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load ban stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) return $data[0]->bans;
		return 0;
	}

	// -----------------------------------------------------------------------------------------

	public function getBansTo( $uid )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT COUNT(uid) AS bans FROM bans WHERE uid=?" );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load ban stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) return $data[0]->bans;
		return 0;
	}

	// -----------------------------------------------------------------------------------------

	public function getBanTimeBy( $uid )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT SUM(length) AS length FROM bans WHERE invokeruid=?" );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load ban stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) return $data[0]->length;
		return 0;
	}

	// -----------------------------------------------------------------------------------------

	public function getBanTimeTo( $uid )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT SUM(length) AS length FROM bans WHERE uid=?" );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load ban stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) return $data[0]->length;
		return 0;
	}

	// -----------------------------------------------------------------------------------------

	public function getUsedNames( $uid )
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT name, count FROM usednames WHERE uid=? ORDER BY count DESC" );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load names stuff.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getUsedChans( $uid )
	{
		try
		{
$sql = <<<SQL
SELECT usedchannels.count, channels.name, channels.cid
FROM usedchannels
JOIN channels ON channels.cid = usedchannels.cid
WHERE uid = ?
ORDER BY usedchannels.count DESC
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $uid );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channels stuff.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getBans()
	{
		try
		{
$sql = <<<SQL
SELECT b.reason, b.time, b.length, b.uid, b.invokeruid, u1.currentname AS name, u2.currentname AS invokername
FROM bans AS b
JOIN users AS u1 ON u1.uid = b.uid
JOIN users AS u2 ON u2.uid = b.invokeruid
ORDER BY time DESC
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load ban stuff.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getKicks()
	{
		try
		{
$sql = <<<SQL
SELECT k.id, k.reason, k.time, k.uid, k.invokeruid, u1.currentname AS name, u2.currentname AS invokername
FROM kicks AS k
JOIN users AS u1 ON u1.uid = k.uid
JOIN users AS u2 ON u2.uid = k.invokeruid
ORDER BY time DESC
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load kick stuff.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getTotalChannelUsers()
	{
		try
		{
			$stmt = $this->dbh->prepare( "SELECT SUM(totalusers) AS users FROM channels WHERE lastseen > ?" );
			$stmt->bindValue( 1, time() - 600, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channel stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) return $data[0]->users || 0;
		return 0;
	}

	// -----------------------------------------------------------------------------------------

	public function getChannel( $cid )
	{
		try
		{
$sql = <<<SQL
SELECT cid, pid, topic, name, maxclients, lastseen
FROM channels
WHERE cid = ?
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $cid, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channel stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) {
			return $data[0];
		}

		return false;
	}

	// -----------------------------------------------------------------------------------------

	public function getChannelUsage( $cid, $limit )
	{
		try
		{
$sql = <<<SQL
SELECT users.uid, users.currentname, count AS c
FROM usedchannels
JOIN users ON users.uid = usedchannels.uid
WHERE cid = ?
ORDER BY c DESC
LIMIT ?
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $cid, PDO::PARAM_INT );
			$stmt->bindValue( 2, $limit, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channel stuff.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getTotalChannelUsage( $cid )
	{
		try
		{
$sql = <<<SQL
SELECT SUM(count) AS c
FROM usedchannels
WHERE cid = ?
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $cid, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channel stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 ) {
			return $data[0]->c;
		}

		return false;
	}

	// -----------------------------------------------------------------------------------------

	public function getAliveChannels( $lastseen )
	{
		try
		{
$sql = <<<SQL
SELECT cid, pid, name, maxclients, totalusers
FROM channels
WHERE lastseen >= ?
ORDER BY `order` ASC
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $lastseen, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channel stuff.<br/>" . $e->getMessage() );
			return false;
		}

		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getRipChannels( $lastseen )
	{
		try
		{
$sql = <<<SQL
SELECT cid, name, maxclients, lastseen
FROM channels
WHERE lastseen < ?
ORDER BY lastseen DESC
SQL;
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $lastseen, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channel stuff.<br/>" . $e->getMessage() );
			return false;
		}
		return $data;
	}

	// -----------------------------------------------------------------------------------------

	public function getTimes( $limit, $from, $to )
	{
$sql = <<<SQL
SELECT users.uid, users.currentname, SUM(count) AS c
FROM tod
JOIN users ON users.uid = tod.uid
WHERE tod BETWEEN ? and ?
GROUP BY uid
ORDER BY c DESC
LIMIT ?
SQL;
		try
		{
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $from, PDO::PARAM_INT );
			$stmt->bindValue( 2, $to, PDO::PARAM_INT );
			$stmt->bindValue( 3, $limit, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channel stuff.<br/>" . $e->getMessage() );
			return false;
		}
		return $data;
	}

	// -----------------------------------------------------------------------------------------

	/*
	public function getTimesCount( $from, $to )
	{
$sql = <<<SQL
SELECT SUM(count) AS c
FROM tod
WHERE tod BETWEEN ? and ?
SQL;
		try
		{
			$stmt = $this->dbh->prepare( $sql );
			$stmt->bindValue( 1, $from, PDO::PARAM_INT );
			$stmt->bindValue( 2, $to, PDO::PARAM_INT );
			$stmt->execute();
			$this->queries++;
			$data = $stmt->fetchAll( PDO::FETCH_CLASS );
		}
		catch ( PDOException $e )
		{
			die( "Failed to load channel stuff.<br/>" . $e->getMessage() );
			return false;
		}

		if( count( $data ) == 1 )
		{
			return $data[0]->c;
		}

		return false;
	}
	*/
}

