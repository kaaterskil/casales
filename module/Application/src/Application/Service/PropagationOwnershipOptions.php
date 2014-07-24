<?php

/**
 * Casales Library
 *
 * PHP version 5.4
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
 * KAATERSKIL MANAGEMENT, LLC BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Casales
 * @package     Application\Service
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: PropagationOwnershipOptions.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Service;

use Application\StdLib\Enum;

/**
 * Specifies the possible values for propagation ownership options.
 *
 *  - CALLER:			Specifies that all created activities are assigned to the caller
 *  					of the API.
 *  - LISTMEMBEROWNER:	Specifies that created activities are assigned to respective owners
 *  					of target members.
 *  - NONE:				Specifies that there is no change in ownership for the created
 *  					activities.
 *
 *  This enumeration is used for the CreateActivitiesListRequest.OwnershipOptions and
 *  ExecuteCampaignActivityRequest.OwnershipOptions properties.
 *
 * @package		Application\Service
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: PropagationOwnershipOptions.php 13 2013-08-05 22:53:55Z  $
 */
class PropagationOwnershipOptions extends Enum {
	const CALLER = 'Caller';
	const LISTMEMBEROWNER = 'List Member Owner';
	const NONE = 'None';
}
?>