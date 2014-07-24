<?php

/**
 * Casales Library
 *
 * @category	Casales
 * @package		Casales_
 * @author		Blair
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: OrderClosedActivitiesRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Service\OrderRequest;
use Application\Service\OrderResponse;
use Application\Service\OrderType;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\CampaignActivity;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;

use Application\Stdlib\Object;
use Application\Model\CampaignResponse;

/**
 * OrderClosedActivitiesRequest Class
 *
 * @package		package
 * @author		Blair
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: OrderClosedActivitiesRequest.php 13 2013-08-05 22:53:55Z  $
 */
class OrderClosedActivitiesRequest extends OrderRequest {
	
	/**
	 * Constructor
	 *
	 * @param array $collection
	 */
	public function __construct(array $collection) {
		parent::__construct($collection);
		$this->orderType = OrderType::instance(OrderType::DESC);
	}

	/**
	 * Returns the property value to be compared
	 *
	 * @param Object $o
	 * @throws \InvalidArgumentException
	 * @return int
	 * @see \Application\Service\OrderRequest::getComparableValue()
	 */
	protected function getComparableValue(Object $obj) {
		$result = time();
		if ($obj instanceof AbstractAppointment) {
			if (!$obj->getScheduledStart() instanceof \DateTime) {
				$d = new \DateTime( $obj->getScheduledStart() );
				$result = $d->getTimestamp();
			} else {
				$result = $obj->getScheduledStart()->getTimestamp();
			}
		} elseif ($obj instanceof AbstractInteraction) {
			if($obj->getActualEnd() != null) {
				if (!$obj->getActualEnd() instanceof \DateTime) {
					$d = new \DateTime( $obj->getActualEnd() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getActualEnd()->getTimestamp();
				}
			} elseif($obj->getActualStart()) {
				if (!$obj->getActualStart() instanceof \DateTime) {
					$d = new \DateTime( $obj->getActualStart() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getActualStart()->getTimestamp();
				}
			} else {
				if (!$obj->getLastUpdateDate() instanceof \DateTime) {
					$d = new \DateTime( $obj->getLastUpdateDate() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getLastUpdateDate()->getTimestamp();
				}
			}
		} elseif ($obj instanceof AbstractNote) {
			if (!$obj->getLastUpdateDate() instanceof \DateTime) {
				$d = new \DateTime( $obj->getLastUpdateDate() );
				$result = $d->getTimestamp();
			} else {
				$result = $obj->getLastUpdateDate()->getTimestamp();
			}
		} elseif ($obj instanceof CampaignResponse) {
			if (!$obj->getLastUpdateDate() instanceof \DateTime) {
				$d = new \DateTime( $obj->getLastUpdateDate() );
				$result = $d->getTimestamp();
			} else {
				$result = $obj->getLastUpdateDate()->getTimestamp();
			}
		} elseif ($obj instanceof AbstractTask || $obj instanceof CampaignActivity) {
			if ($obj->getActualEnd() != null) {
				if (!$obj->getActualEnd() instanceof \DateTime) {
					$d = new \DateTime( $obj->getActualEnd() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getActualEnd()->getTimestamp();
				}
			} elseif ($obj->getActualStart() != null) {
				if (!$obj->getActualStart() instanceof \DateTime) {
					$d = new \DateTime( $obj->getActualStart() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getActualStart()->getTimestamp();
				}
			} else {
				if (!$obj->getLastUpdateDate() instanceof \DateTime) {
					$d = new \DateTime( $obj->getLastUpdateDate() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getLastUpdateDate()->getTimestamp();
				}
			}
		} else {
			throw new \InvalidArgumentException('Sort Error - Invalid class: ' . get_class($obj));
		}
		return $result;
	}
}
?>