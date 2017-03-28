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

class Table
{

	private $class;
	private $colspan;
	private $headers;
	private $width;
	private $rows;

	public function __construct( $class, $colspan )
	{
		$this->class = $class;
		$this->colspan = $colspan;
		$this->rows = array();
	}

	public function header()
	{
		$this->headers = func_get_args();
	}

	public function width()
	{
		$this->width = func_get_args();
	}

	public function row( $r )
	{
		$this->rows[] = $r;
	}

	public function output()
	{
		echo "<table class=\"{$this->class}\">";
		echo "<thead><tr>";
		if( $this->colspan !== false ) {
			echo "<th colspan=\"{$this->colspan}\">{$this->headers[0]}</th>";
		} else {
			for( $i = 0; $i < count( $this->headers ); $i++ ) {
				if( isset( $this->width[$i] ) ) {
					$width = $this->width[$i];
				} else {
					$width= 'auto';
				}
				echo "<th width=\"{$width}\">{$this->headers[$i]}</th>";
			}
		}
		echo "</tr></thead>";
		echo "<tbody>";
		foreach( $this->rows as $row ) {
			echo "<tr>";
			foreach( $row as $c ) {
				echo "<td>{$c}</td>";
			}
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}

}
