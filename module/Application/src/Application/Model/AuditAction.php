<?php

/**
 * Casales Library
 *
 * PHP version 5.4
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL
 * KAATERSKIL MANAGEMENT, LLC BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Casales
 * @package     Application\Model
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: AuditAction.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\StdLib\Enum;

/**
 * Specifies the audit actions.
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 */
class AuditAction extends Enum {
	const UNKNOWN = 'Unknown';
	const ACTIVATE = 'Activate';
	const DEACTIVATE = 'Deactivate';
	const CREATE = 'Create';
	const UPDATE = 'Update';
	const DELETE = 'Delete';
	const CASCADE = 'Cascade';
	const MERGE = 'Merge';
	const RETRIEVE = 'Retrieve';
	const CLOSE = 'Close';
	const CANCEL = 'Cancel';
	const COMPLETE = 'Complete';
	const REOPEN = 'Reopen';
	const QUALIFY = 'Qualify';
	const DISQUALIFY = 'Disqualify';
	const ADDMEMBER = 'Add Member';
	const REMOVEMEMBER = 'Remove Member';
	const ADDMEMBERS = 'Add Members';
	const REMOVEMEMBERS = 'Remove Members';
	const ADDITEM = 'Add Item';
	const REMOVEITEM = 'Remove Item';
	const SETSTATE = 'Set State';
	const WIN = 'Win';
	const LOSE = 'Lose';
	const RESCHEDULE = 'Reschedule';
	const SENDEMAIL = 'Send Email';
}
?>