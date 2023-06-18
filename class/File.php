<?php
class File
{
	static function criar($arquivo,$dados = '')
	{
		try
		{
			file_put_contents ($arquivo , $dados);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function save($arquivo,$destino)
	{
		try
		{
			return move_uploaded_file($arquivo, $destino);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function base64_to_jpg($base64_string, $output_file)
	{
		// open the output file for writing
		$ifp = fopen( $output_file, 'w+' );

		// split the string on commas
		// $data[ 0 ] == "data:image/png;base64"
		// $data[ 1 ] == <actual base64 string>
		$data = explode( ',', $base64_string );

		// we could add validation here with ensuring count( $data ) > 1
		$save = fwrite( $ifp, base64_decode( $data[ 1 ] ) );

		// clean up the file resource
		fclose( $ifp );

		return $save;
	}

	static function file_get_contents($url)
	{
		try
		{
			$ch = curl_init();
			$timeout = 5; // set to zero for no timeout
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			ob_start();
			curl_exec($ch);
			curl_close($ch);
			$file_contents = ob_get_contents();
			ob_end_clean();
			return $file_contents;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function listDir($dir, $types = ['png', 'jpg', 'jpeg', 'gif', 'pdf'])
	{
		try
		{
			$retorno = [];
			if(! file_exists($dir))
			{
				return $retorno;
			}

			if ( $handle = opendir($dir) )
			{
				while ( $entry = readdir( $handle ) )
				{
					$ext = strtolower( pathinfo( $entry, PATHINFO_EXTENSION) );
					if( in_array( $ext, $types ) )
					{
						$retorno[] = $entry;
					}
				}
				closedir($handle);
			}

			return $retorno;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function delete($pathToFile)
	{
		try
		{
			if(! file_exists($pathToFile))
			{
				return true;
			}

			return unlink($pathToFile);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function capa($file)
	{
		try
		{
			$autoRender = ['jpg', 'gif', 'jpeg', 'svg', 'png'];
			$ext = U::getExtensao($file);
			if(in_array($ext, $autoRender))
			{
				return $file;
			}

			return HTTP_ADMIN."/imagens/extensoes/{$ext}.png";
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

}