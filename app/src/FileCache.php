<?php

namespace App;

use App\Interfaces\CacheInterface;
use DateTime;
use RuntimeException;
use SplFileObject;

class FileCache implements CacheInterface {
	/**
	 * @var string
	 */
	private $cacheFolder;

	/**
	 * FileCache constructor.
	 *
	 * @param string $cacheFolder
	 */
	public function __construct( string $cacheFolder ) {
		$this->cacheFolder = $cacheFolder;

		$this->initializeCache( $this->cacheFolder );
	}

	/**
	 * Initialize cache
	 *
	 * @param string $cacheFolder
	 */
	public function initializeCache( string $cacheFolder ): void {
		// if cache directory doesn't exist
		if ( ! file_exists( $cacheFolder ) ) {
			// if cache directory wasn't created
			if ( ! mkdir( $cacheFolder, 0777, true ) && ! is_dir( $cacheFolder ) ) {
				throw new RuntimeException( sprintf( 'Directory "%s" was not created', $cacheFolder ) );
			}
		} // if cache directory is not writable
		else if ( is_dir( $cacheFolder ) && ! is_writable( $cacheFolder ) ) {
			throw new RuntimeException( sprintf( 'Directory "%s" is not writable', $cacheFolder ) );
		}
	}

	/**
	 * Set cache
	 *
	 * @inheritdoc
	 * @throws \Exception
	 */
	public function set( string $key, $value, int $duration ) {
		$fileName   = $this->getFilePathByKey( $key );
		$fileObject = new SplFileObject( $fileName, 'w+', true );

		$expiresIn = $this->getCurrentTimeStamp() + $duration;

		if ( ! $fileObject->isWritable() ) {
			throw new RuntimeException( sprintf( 'File "%s" is not writable', $fileName ) );
		}

		$fileContent = json_encode( [ 'expires' => $expiresIn, 'items' => $value['items'], 'rates' => $value['rates'] ] );

		$fileObject->fwrite( $fileContent );
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private function getFilePathByKey( string $key ): string {
		return sprintf( '%s/%s.json', $this->cacheFolder, $key );
	}

	/**
	 * @return int
	 * @throws \Exception
	 */
	public function getCurrentTimeStamp(): int {
		return ( new DateTime() )->getTimestamp();
	}

	/**
	 * Get cache
	 *
	 * @inheritdoc
	 * @throws \Exception
	 */
	public function get( string $key ) {
		$filePath = $this->getFilePathByKey( $key );

		if ( ! is_file( $filePath ) ) {
			return null;
		}

		$fileObject = new SplFileObject( $filePath );

		if ( ! $fileObject->isReadable() ) {
			throw new RuntimeException( 'Cache file exists but not readable' );
		}

		$fileContent = json_decode( $fileObject->fread( $fileObject->getSize() ), true );

		if ( $fileContent['expires'] < $this->getCurrentTimeStamp() ) {
			unlink( $filePath );

			return null;
		}

		return $fileContent;
	}
}
