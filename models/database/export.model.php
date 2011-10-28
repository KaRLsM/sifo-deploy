<?php
/**
 * DatabaseExportModel file.
 *
 * @package    Deploy
 * @subpackage Database
 * @author     Carlos Soriano <carlos.soriano@musicjumble.com>
 */

namespace Deploy;

/**
 * Exports the current BBDD to a sql_dump file.
 */
class DatabaseExportModel extends \SeoFramework\Model
{
	/**
	 * The host to dump the SQL.
	 *
	 * @var string
	 */
	protected $dump_host = 'localhost';

	/**
	 * The user to use in mysqldump.
	 *
	 * @var string
	 */
	protected $dump_user = 'user';

	/**
	 * The password to use in mysqldump.
	 *
	 * @var string
	 */
	protected $dump_password = 'password';

	/**
	 * The databases to dump the SQL.
	 *
	 * @var array
	 */
	protected $dump_databases = array(
		'database1',
		'database2',
		'database3',
		'database4'
	);

	/**
	 * Generates a mysql_dump file
	 *
	 * @return string The filename where the dump is.
	 */
	public function generateSqlDump()
	{
		$filename = ROOT_PATH . '/instances/deploy/files/sifo_dump.sql.gz';
		$databases = implode( ' ', $this->dump_databases );
		$command = "mysqldump -h {$this->dump_host} -u {$this->dump_user}";
		$command .= " --password=\"{$this->dump_password}\" --databases $databases";
		$command .= " --skip-lock-tables | gzip > $filename";

		shell_exec( $command );

		return $filename;
	}
}