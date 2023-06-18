<?php
class Sql
{
	static function checkDuplicate($tabela, $coluna, $valor, $colId = false, $colIdVal = false)
	{

		try
		{
			$primariaAtual = '';
			if($colId && $colIdVal)
			{
				$primariaAtual = " AND {$colId} != '{$colIdVal}'";
			}
			return Sql::_fetch("SELECT {$coluna} FROM {$tabela} WHERE {$coluna}='{$valor}' {$primariaAtual}");
		}

		catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }

	}
	static function toLike($str)
	{
		try
		{
			return " LIKE '%".U::setUrlAmigavel($str,'-', '%')."%'";
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function _fetchAll($sql)
	{
		try
		{
			Transaction::open();
			$conexao = Transaction::getInstance();
			$conexao->beginTransaction();
			$query = $conexao->prepare($sql);
			$query->execute();
			Transaction::close();
			return $query->fetchAll();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}

	static function _fetch($sql,$params = array())
	{
		try
		{
			Transaction::open();
			$conexao = Transaction::getInstance();
			$conexao->beginTransaction();
			$query = $conexao->prepare($sql);
			$query->execute($params);
			Transaction::close();
			return $query->fetch();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}

	static function _fetchOrAll($sql,$params = array())
	{
		try
		{
			Transaction::open();
			$conexao = Transaction::getInstance();
			$conexao->beginTransaction();
			$query = $conexao->prepare($sql);
			$query->execute($params);
			Transaction::close();
			return $query;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}

	static function _fetchAllAssoc($sql,$params = array())
	{
		try
		{
			Transaction::open();
			$conexao = Transaction::getInstance();
			$conexao->beginTransaction();
			$query = $conexao->prepare($sql);
			$query->execute($params);
			Transaction::close();
			return $query->fetchAll(PDO::FETCH_KEY_PAIR);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}

	static function _query($sql,$params = array())
	{
		try
		{
			$sql = trim($sql);
			$update = strtolower(strtok($sql, ' ')) == 'insert' ? true : false; // UPDATE, INSERT
			Transaction::open();
			$conexao = Transaction::getInstance();
			$query = $conexao->prepare($sql);
			$result = $query->execute($params);
			Transaction::close();
			if($result)
			{
			    if($update)
			    {
			        return $conexao->lastInsertId();
			    }
			    return $result;
			}
			throw new Exception('Divisão por zero.');
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}

	static function _rowCount($sql,$params = array())
	{
		try
		{
			Transaction::open();
			$conexao = Transaction::getInstance();
			$conexao->beginTransaction();
			$query = $conexao->prepare($sql);
			$result = $query->execute($params);
			Transaction::close();
			return $query->rowCount();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}
	static function checaTabelaExists($tabela)
	{
		try
		{
			$sql = "SHOW TABLES LIKE '{$tabela}'";
			Transaction::open();
			$conexao = Transaction::getInstance();
			$conexao->beginTransaction();
			$query = $conexao->prepare($sql);
			$result = $query->execute();
			Transaction::close();
			return $query->fetch();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function get($sql)
	{
		try
		{
			$linha = Sql::_fetch($sql);
			return U::clearStr($linha);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getAll($sql)
	{
		try
		{
			return U::clearStr(Sql::_fetchAll($sql));
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getCampos($tabela,$chave = 'id', $id = false)
	{
		try
		{
			if($id && $id != '' &&  $chave)
			{
				$ret = Sql::_fetch("SELECT * FROM {$tabela} WHERE {$chave}='{$id}'");
			}
			else
			{
				$result = Sql::_fetchAll("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='{$tabela}'");
				foreach($result as $res)
				{
					$ret[$res['COLUMN_NAME']]='';
				}
			}
			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}
