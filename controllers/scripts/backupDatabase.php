<?php
/**
 * ScriptsBackupDatabaseController file.
 *
 * @package    Deploy
 * @subpackage Scripts
 * @author     Carlos Soriano <carlos.soriano@musicjumble.com>
 */

namespace Deploy;
use SeoFramework\AmazonS3;

/**
 * Exports the database dump to S3 as a backup.
 */
class ScriptsBackupDatabaseController extends \SeoFramework\CommandLineController
{
	/**
	 * Wether there are errors in the execution or not.
	 *
	 * @var boolean
	 */
	protected $errors = false;

	/**
	 * The help message.
	 *
	 * @var string
	 */
	public $help_str = 'Daily backups of the production databases.';

	/**
	 * Initializes the script.
	 *
	 * @return void
	 */
	public function init()
	{
	}

	/**
	 * Script execution.
	 *
	 * @return void
	 */
	public function exec()
	{
		$dumper = new DatabaseExportModel();

		$this->showMessage( 'Starting to dump the database into a file.' );
		$filename = $dumper->generateSqlDump();

		if( is_readable( $filename ) )
		{
			$this->showMessage( "$filename dump generated successfully." );

			$stored_filename = date( 'd_m_Y_' ) . basename( $filename );

			$amazon_s3 = new AmazonS3();

			$this->showMessage( "Saving '$stored_filename' into Amazon S3" );

			if ( $amazon_s3->putObjectFile( $filename, "sifo-backup", $stored_filename ) )
			{
				$this->showMessage( "Backup saved correctly." );
			}
			else
			{
				$this->errors = true;
				$this->showMessage( "ERROR. Could not upload backup to amazon." );
			}

			if ( unlink( $filename ) )
			{
				$this->showMessage( "$filename removed." );
			}
			else
			{
				$this->errors = true;
				$this->showMessage( "$filename could not be removed!." );
			}
		}
		else
		{
			$this->errors = true;
			$this->showMessage( "$filename dump failed. Aborting" );
		}
	}

	/**
	 * Returns the subject of the email.
	 *
	 * @return string
	 */
	protected function getSubject()
	{
		if ( $this->errors )
		{
			return '[ERRORS] Saving DB backup to Amazon S3';
		}

		return 'Save DB backup to Amazon S3';
	}
}
