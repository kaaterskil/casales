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
 * @package     Application\Model\Activity
 * @copyright   Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version     SVN $Id: ActivityType.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

use Application\Stdlib\Enum;

/**
 * Specifies the possible activity types that a user can create. Does not include system
 * activity types such as bulk operation, close and response type.
 *
 * @package     Application\Model\Activity
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		SVN $Revision: 13 $
 */
class ActivityType extends \Application\StdLib\Enum {
	// Appointment activities
	const BREAKFAST = 'BreakfastAppointment';
	const LUNCH = 'LunchAppointment';
	const DINNER = 'DinnerAppointment';
	const MEETING = 'MeetingAppointment';
	
	// Task activities
	const CALLBACK = 'CallbackTask';
	const FOLLOWUP = 'FollowUpTask';
	const OTHER = 'OtherTask';
	
	// Interaction activities
	const EMAIL = 'EmailInteraction';
	const FAX = 'FaxInteraction';
	const TELEPHONE = 'TelephoneInteraction';
	const VISIT = 'VisitInteraction';
	const LETTER = 'LetterInteraction';
	
	// Note activity
	const ACCOUNTCOMMENT = 'AccountNote';
	const USERCOMMENT = 'UserNote';
}
?>