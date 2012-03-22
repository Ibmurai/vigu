<?php
/**
 * TODO_DOCUMENT_ME
 *
 * PHP version 5
 *
 * @category TODO_DOCUMENT_ME
 * @package  TODO_DOCUMENT_ME
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2012-TODO-
 */
require __DIR__ . '/../../../../handlers/shutdown.php';
/**
 * TODO_DOCUMENT_ME
 *
 * @category   TODO_DOCUMENT_ME
 * @package    TODO_DOCUMENT_ME
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 * @author     Johannes Skov Frandsen <jsf@fynskemedier.dk>
 */
class ApiPublicControllerLog extends ApiPublicController {
	/**
	 * Grid search.
	 *
	 * @param integer $rows   <10>        Number of rows to return
	 * @param integer $page   <1>         Result offset
	 * @param string  $sidx   <timestamp> Field to sort by
	 * @param string  $sord   <desc>      Sort direction (asc or desc)
	 * @param string  $module <null>      TODO Limit search to one module (default is null = all modules)
	 * @param string  $host   <null>      Limit search to one host (default is null = all sites)
	 * @param string  $level  <null>      TODO Limit search to one error level (default is null = all levels)
	 * @param string  $path   <null>      TODO Limit search to match a specific path (default is null = any file path)
	 * @param string  $search <null>      TODO Limit search to match string in error message (default is null = any error message)
	 *
	 * @return void
	 */
	public function gridAction($rows, $page, $sidx, $sord, $module, $host, $level, $path, $search) {
		$qb = $this->_em->createQueryBuilder();
		$query = $qb
			->select('l', $qb->expr()->max('l._timestamp'), $qb->expr()->count('l'))
			->from('ApiPublicModelLogLine', 'l')
			->groupBy('l._file')
			->addGroupBy('l._level')
			->addGroupBy('l._line')
			->orderBy('l._' . $sidx, $sord)
		;

		if ($host !== null) {
			$query
				->where('l._host = :host')
				->setParameter('host', $host)
			;
		}

		$countQuery = clone $query;

		$query = $query
			->setFirstResult($rows * ($page - 1))
			->setMaxResults($rows)
			->getQuery()
		;

		$logLines = array();
		foreach ($query->getResult() as $resLine) {
			list($logLine, $timestamp, $count) = $resLine;
			$logLines[] = array(
				'id'   => $logLine->getId(),
				'cell' => array(
					$logLine->getLevel(),
					$logLine->getMessage(),
					date('Y-m-d H:i:s', $timestamp),
					$count
				),
			);
		}

		// TODO: Fix this query! Its silly to get all them rows and php count the array!
		$countQuery = $countQuery
			->select($qb->expr()->count('l'))
			->getQuery()
		;

		$records = count($countQuery->getScalarResult());

		$this->assign('page', $page);
		$this->assign('total', ceil($records / $rows));
		$this->assign('records', $records);
		$this->assign('rows', $logLines);
	}

	/**
	 * Get the details for a given log line.
	 *
	 * @param integer $id   <>
	 * @param string  $host <null> Filter the count to a given host.
	 *
	 * @return void
	 */
	public function detailsAction($id, $host) {
		$logLine = $this->_em->find('ApiPublicModelLogLine', $id);

		$qb = $this->_em->createQueryBuilder();
		$query = $qb
			->select('l', $qb->expr()->count('l'), $qb->expr()->max('l._timestamp'), $qb->expr()->min('l._timestamp'))
			->from('ApiPublicModelLogLine', 'l')
			->groupBy('l._file')
			->addGroupBy('l._level')
			->addGroupBy('l._line')
			->where('l._file = :file')
			->andWhere('l._level = :level')
			->andWhere('l._line = :line')
			->setParameters(array(
				'file' => $logLine->getFile(),
				'level' => $logLine->getLevel(),
				'line' => $logLine->getLine(),
			))
		;

		if ($host !== null) {
			$query
				->andWhere('l._host = :host')
				->setParameter('host', $host)
			;
		}

		$query = $query
			->getQuery()
		;

		try {
			list($logLine, $count, $timestampMax, $timestampMin) = $query->getSingleResult();

			$this->assign('details', array(
				'host'       => $logLine->getHost(),
				'last'       => date('Y-m-d H:i:s', $timestampMax),
				'first'      => date('Y-m-d H:i:s', $timestampMin),
				'level'      => $logLine->getLevel(),
				'message'    => $logLine->getMessage(),
				'file'       => $logLine->getFile(),
				'line'       => $logLine->getLine(),
				'context'    => $logLine->getContext(),
				'stacktrace' => $logLine->getStacktrace(),
				'count'      => (integer) $count,
				'frequency'  => ($count / (max(1, $timestampMax - $timestampMin))) * 3600,
			));
		} catch (Doctrine\ORM\NoResultException $ex) {
			$this->assign('error', 'No log lines found.');
		}
	}

	/**
	 * Get all hosts.
	 *
	 * @return void
	 */
	public function getHostsAction() {
		$qb = $this->_em->createQueryBuilder();
		$query = $qb
			->select('l._host')
			->from('ApiPublicModelLogLine', 'l')
			->groupBy('l._host')
			->getQuery()
		;

		$hosts = array();
		foreach($query->getArrayResult() as $line) {
			$hosts[] = $line['_host'];
		}

		$this->assign('hosts', $hosts);
	}

	/**
	 * Get all levels.
	 *
	 * @return void
	 */
	public function getLevelsAction() {
		$qb = $this->_em->createQueryBuilder();
		$query = $qb
			->select('l._level')
			->from('ApiPublicModelLogLine', 'l')
			->groupBy('l._level')
			->getQuery()
		;

		$levels = array();
		foreach($query->getArrayResult() as $line) {
			$levels[] = $line['_level'];
		}

		$this->assign('levels', $levels);
	}
}
