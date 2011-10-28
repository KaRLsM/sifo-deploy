<?php
/**
 * HomeIndexController file.
 *
 * @package    Deploy
 * @subpackage Home
 * @author     Carlos Soriano <carlos.soriano@musicjumble.com>
 */

namespace Deploy;
use SeoFramework\Filter;
use SeoFramework\FilterPost;
use SeoFramework\Domains;
use SeoFramework\MediaGenerator;
use SeoFramework\Cache;
use SeoFramework\Bootstrap;

/**
 * HomeIndexController class.
 *
 * Manages all the deploy control functions.
 */
class HomeIndexController extends \SeoFramework\Controller
{
	/**
	 * Folder where apache points to.
	 *
	 * @var string
	 */
	protected $folder_a = 'www';

	/**
	 * Temporal folder that only lasts while the renaming proccess.
	 *
	 * @var string
	 */
	protected $folder_b = 'www_tmp';

	/**
	 * Secondary folder where the update is performed.
	 *
	 * @var string
	 */
	protected $folder_c = 'www_2_update';

	/**
	 * Main logic.
	 *
	 * @return void
	 */
	public function build()
	{
		$this->setLayout( 'home/index.tpl' );

		$groups = $this->buildProjectTree( $this->getConfig( 'projects' ) );

		$filter = FilterPost::getInstance();

		if ( $filter->isSent( 'project_id' ) )
		{
			$groups = $this->update( $filter, $groups );
		}
		elseif ( $filter->isSent( 'sql_dump' ) )
		{
			$this->processSqlDump();
		}

		$this->assign( 'groups', $groups );
	}

	/**
	 * Updates the group/project received by POST.
	 *
	 * @param Filter $filter The FilterPost instance.
	 * @param array	 $groups The parsed project groups.
	 *
	 * @return array The updated groups.
	 */
	protected function update( Filter $filter, array $groups )
	{
		$group = $filter->getString( 'group' );
		$project_id = $filter->getString( 'project_id' );
		$log = '';
		if ( $filter->isSent( 'update_head' ) )
		{
			$log = $this->updateToRevision( $groups, $group, $project_id );
		}
		elseif ( $filter->isSent( 'update_revision' ) )
		{
			$revision = $filter->getString( 'revision' );
			if ( $revision )
			{
				$log = $this->updateToRevision( $groups, $group, $project_id, $revision );
			}
			else
			{
				$log = 'Revision not specified. Update not performed.';
			}
		}

		$server_path = $groups[$group][$project_id]['server_path'] . "/{$this->folder_a}";
		if ( Domains::getInstance()->getDevMode() )
		{
			$server_path = ROOT_PATH;
		}

		$groups[$group][$project_id]['log'] = $log;
		$groups[$group][$project_id]['revision'] = MediaGenerator::getCheckoutRevision(
				$server_path
		);

		return $groups;
	}

	/**
	 * Builds all the projects data.
	 *
	 * This is later assigned to the template and a content box will appear per
	 * project.
	 *
	 * @param array $groups The projects config file content.
	 *
	 * @return array
	 */
	protected function buildProjectTree( array $groups )
	{
		$root_path = ROOT_PATH;
		foreach ( $groups as &$projects )
		{
			foreach ( $projects as $instance => &$project )
			{
				if ( !Domains::getInstance()->getDevMode() )
				{
					chdir($project['server_path']);
					$root_path = $project['server_path'] . "/{$this->folder_a}";
				}

				$project += array(
					'revision' => MediaGenerator::getCheckoutRevision( $root_path ),
					'working_copy' => getcwd(),
					'log' => null,
					'instance' => $instance
				);
			}
		}

		return $groups;
	}


	/**
	 * Updates a project to the given revision.
	 *
	 * @param array  $groups	 The parsed project groups.
	 * @param string $group		 The current group to be updated.
	 * @param string $project_id The project_id of the project to be updated.
	 * @param string $revision	 The revision to update the code. By default 'HEAD'.
	 *
	 * @return string The git pull output.
	 */
	protected function updateToRevision( array $groups, $group, $project_id, $revision = 'HEAD' )
	{
		$server_path = $groups[$group][$project_id]['server_path'];
		$project_name = $groups[$group][$project_id]['name'];
		$atomic = $groups[$group][$project_id]['atomic'];

		if ( Domains::getInstance()->getDevMode() )
		{
			$log = "Updating the WC is disabled in development environment\n";
			$log .= "Server path selected was: '$server_path'\n";
			$log .= "Project name selected was: '$project_name'\n";
			$log .= "Selected revision was: $revision\n";
			$log .= "Nothing was updated!\n";
		}
		else
		{
			if ( $atomic )
			{
				$update_folder = "/{$this->folder_c}";
				$log = "Atomic update: yes\n";
			}
			else
			{
				$update_folder = "/{$this->folder_a}";
				$log = "Atomic update: no\n";
			}

			chdir( realpath( $server_path . $update_folder ) );
			shell_exec( "git checkout master" );
			$log .= shell_exec( "git pull" );
			shell_exec( "git checkout $revision" );
			$log .= "Checked out at $revision\n";

			if ( $atomic )
			{
				if ( $this->_replaceUpdateFolders( $server_path, $log ) )
				{
					$log .= "Replaced {$this->folder_a} << >> {$this->folder_c}\n";
				}
				else
				{
					$log .= "ERROR replacing {$this->folder_a} <<!!!>> {$this->folder_c}\n";
				}
			}

			$log .= $this->flushCache( $project_id );

			$this->sendUpdateEmail( $project_name, $revision, $log );
		}

		return $log;
	}

	/**
	 * Replaces the update folders so there is no issues while the update lasts.
	 *
	 * @param string $server_path The server path.
	 *
	 * @return boolean If the operation was successful or not.
	 */
	private function _replaceUpdateFolders( $server_path )
	{
		$folder_a = $server_path . '/' . $this->folder_a;
		$folder_b = $server_path . '/' . $this->folder_b;
		$folder_c = $server_path . '/' . $this->folder_c;
		$success = false;

		if ( rename( $folder_a, $folder_b ) )
		{
			if ( rename( $folder_c, $folder_a ) )
			{
				$result = shell_exec( "mv $folder_b $folder_c" );

				if ( empty( $result ) )
				{
					$success = true;
				}
				else
				{
					rename( $folder_a, $folder_c );
				}
			}
			else
			{
				rename( $folder_b, $folder_a );
			}
		}

		return $success;
	}

	/**
	 * Flushes the cache from the servers configured to the given instance.
	 *
	 * @param string $instance The affected instance.
	 *
	 * @return string The action report.
	 */
	protected function flushCache( $instance )
	{
		if ( is_numeric( $instance ) || 'deploy' === $instance )
		{
			$log = "No cache to flush\n";
		}
		else
		{
			$current_instance = Bootstrap::$instance;
			Bootstrap::$instance = $instance;

			if ( Cache::getInstance()->flush() )
			{
				$log = "Cache flushed OK!\n";
			}
			else
			{
				$log = "ERROR flushing the cache\n";
			}

			Bootstrap::$instance = $current_instance;
		}

		return $log;
	}

	/**
	 * Sends a notifying email to the developers about the update.
	 *
	 * @param string $project_name The project name.
	 * @param string $revision	   The revision that was update to.
	 * @param string $log		   The log of the update.
	 *
	 * @return void
	 */
	protected function sendUpdateEmail( $project_name, $revision, $log )
	{
		$mail = \SeoFramework\Mail::getInstance();
		$mail->send( 'myemail@mail.com', "[SDC] {$project_name} updated to $revision", nl2br($log) );
	}

	/**
	 * Compresses and pushes the download of the mysql dump file.
	 *
	 * @return void
	 */
	protected function processSqlDump()
	{
		$dumper = new DatabaseExportModel();
		$filename = $dumper->generateSqlDump();

		if ( is_file($filename) )
		{
			header('Content-Description: File Transfer');
			header('Content-type: application/gzip');
			header('Content-Disposition: attachment; filename=' . basename($filename) );
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filename));
			readfile($filename);
		}
	}
}