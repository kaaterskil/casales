<?php

/**
 * Casales Library
 *
 * @category	Casales
 * @package		Casales_
 * @author		Blair
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: OrderOpenActivitiesRequest.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\Model\AbstractActivity;
use Application\Model\AbstractAppointment;
use Application\Model\CampaignActivity;
use Application\Model\AbstractInteraction;
use Application\Model\AbstractNote;
use Application\Model\AbstractTask;

use Application\Service\OrderRequest;
use Application\Service\OrderResponse;
use Application\Service\OrderType;

use Application\Stdlib\Object;
use Application\Model\CampaignResponse;

/**
 * OrderOpenActivitiesRequest Class
 *
 * @package		package
 * @author		Blair
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: OrderOpenActivitiesRequest.php 13 2013-08-05 22:53:55Z  $
 */
class OrderOpenActivitiesRequest extends OrderRequest {

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
			if (!$obj->getScheduledStart() instanceof \DateTime) {
				$d = new \DateTime( $obj->getScheduledStart() );
				$result = $d->getTimestamp();
			} else {
				$result = $obj->getScheduledStart()->getTimestamp();
			}
		} elseif ($obj instanceof AbstractNote) {
			if (!$obj->getCreationDate() instanceof \DateTime) {
				$d = new \DateTime( $obj->getCreationDate() );
				$result = $d->getTimestamp();
			} else {
				$result = $obj->getCreationDate()->getTimestamp();
			}
		} elseif ($obj instanceof CampaignResponse) {
			if (!$obj->getReceivedOn() instanceof \DateTime) {
				$d = new \DateTime( $obj->getReceivedOn() );
				$result = $d->getTimestamp();
			} else {
				$result = $obj->getReceivedOn()->getTimestamp();
			}
		} elseif ($obj instanceof AbstractTask || $obj instanceof CampaignActivity) {
			if ($obj->getScheduledStart() != null) {
				if (!$obj->getScheduledStart() instanceof \DateTime) {
					$d = new \DateTime( $obj->getScheduledStart() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getScheduledStart()->getTimestamp();
				}
			} elseif ($obj->getActualStart() != null) {
				if (!$obj->getActualStart() instanceof \DateTime) {
					$d = new \DateTime( $obj->getActualStart() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getActualStart()->getTimestamp();
				}
			} else {
				if (!$obj->getCreationDate() instanceof \DateTime) {
					$d = new \DateTime( $obj->getCreationDate() );
					$result = $d->getTimestamp();
				} else {
					$result = $obj->getCreationDate()->getTimestamp();
				}
			}
		} else {
			throw new \InvalidArgumentException('Sort Error - Invalid class: ' . get_class($obj));
		}
		return $result;
	}
}
?>