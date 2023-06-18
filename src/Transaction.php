<?php
/**
 *  Inclui a classe Connection
 */
include_once( 'Connection.php' );
/**
 *  Classe responsável por gerar objetos PDO
 *  Created on 2011-07-23
 *  PHP version 5.3.0 and later
 *  @author Carlos Coelho <coelhoduda@hotmail.com>
 *  @version 0.1
 */
final class Transaction
{
    /**
     *  @var object Armazena a instância
     *  da classe PDO
     *  @access private
     */
    private static $instance = null;
    /**
     *  Construtor da classe declarado
     *  como private para evitar que se
     *  crie instâncias da classe Transaction
     */
    private function __construct( ){ }
    /**
     *  Abre uma conexão com o banco de dados
     *  @param string O local do arquivo de configuração
     *  @access public
     *  @return void
     */
    public static function open()
    {
        if( is_null( self::$instance ) )
        {
            self::$instance = Connection::open();    
        }
    }
    /**
     *  Pega a conexão ativa
     *  @access public
     *  @return object A instância do PDO
     */
    public static function getInstance( )
    {
        try
        {
            if( ! is_null( self::$instance ) )
            {
                return self::$instance;
            }
            else
            {
                throw new Exception( 'Object PDO não localizado' );
            }
        }
        catch( Exception $e )
        {
            printf( "Error!: %s<br/>", $e->getMessage( ) );
            return false;
        }
    }
    /**
     *  Finaliza a conexão com o banco de dados
     *  @access public
     *  @return void
     */
    public static function close( )
    {
        self::$instance = null;
    }
}
?>
