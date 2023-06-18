<?php
abstract class Connection extends PDO
{
    public static function open()
    {
        try
        {

			$host = DB_HOST;
            $name = DB_NAME;
            $user = DB_USER;
            $pass = DB_PASS;
            $type = DB_TYPE;
            $port = DB_PORT;


            switch( $type )
            {
                case 'pgsql':
                    $conn = new PDO( sprintf( 'pgsql:dbname=%s; user=%s; password=%s; host=%s; port=%s', $name, $user, $pass, $host, $port ) );
                    break;
                case 'mysql':
                    $conn = new PDO( sprintf( 'mysql:host=%s; port=%s; dbname=%s', $host, $port, $name ), $user, $pass );
                    break;
                case 'sqlite':
                    $conn = new PDO( sprintf( 'sqlite:%s', $name ) );
                    break;
                case 'ibase':
                    $conn = new PDO( sprintf( 'firebird:dbname=%s', $name ), $user, $pass );
                    break;
                case 'oci8':
                    $conn = new PDO( sprintf( 'oci:dbname=%s', $name ), $user, $pass );
                    break;
                case 'mssql':
                    $conn = new PDO( sprintf( 'mssql:host=%s,1433; dbname=%s', $host, $name ), $user, $pass );
                    break;
            }
            if( $conn instanceof PDO )
            {
				if(SET_UTF8_BD)
					$conn->exec("set names utf8");
                $conn->setAttribute( PDO::ATTR_CASE , PDO::CASE_NATURAL ); //PDO::CASE_LOWER
                $conn->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
                $conn->setAttribute( PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION );
                $conn->setAttribute( PDO::ATTR_AUTOCOMMIT, true );
                $conn->setAttribute( PDO::ATTR_TIMEOUT, 10 );
                //$conn->setAttribute( PDO::ATTR_CASE, PDO::CASE_NATURAL );
            }
        }
        catch( Exception $e )
        {
            die(DEBUG ? 'Erro!: '.$e->getMessage( ).'<br/>' : 'Erro de conexão');
            return false;
        }
        return $conn;
    }
}
?>
