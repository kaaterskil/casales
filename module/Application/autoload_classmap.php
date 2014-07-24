<?php

/**
 * Casales Library PHP version 5.4 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
 * CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
 * TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL KAATERSKIL MANAGEMENT, LLC BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Casales
 * @package Application
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN $Id: autoload_classmap.php 13 2013-08-05 22:53:55Z  $
 */

return array(
	'Application\Controller\AccountController' => __DIR__ . '/src/Application/Controller/AccountController.php',
	'Application\Controller\ActivityController' => __DIR__ . '/src/Application/Controller/ActivityController.php',
	'Application\Controller\AjaxController' => __DIR__ . '/src/Application/Controller/AjaxController.php',
	'Application\Controller\BusinessUnitController' => __DIR__ . '/src/Application/Controller/BusinessUnitController.php',
	'Application\Controller\CampaignController' => __DIR__ . '/src/Application/Controller/CampaignController.php',
	'Application\Controller\ClosedActivityController' => __DIR__ . '/src/Application/Controller/ClosedActivityController.php',
	'Application\Controller\ConfigController' => __DIR__ . '/src/Application/Controller/ConfigController.php',
	'Application\Controller\ContactController' => __DIR__ . '/src/Application/Controller/ContactController.php',
	'Application\Controller\GroupController' => __DIR__ . '/src/Application/Controller/GroupController.php',
	'Application\Controller\IndexController' => __DIR__ . '/src/Application/Controller/IndexController.php',
	'Application\Controller\LeadController' => __DIR__ . '/src/Application/Controller/LeadController.php',
	'Application\Controller\MailController' => __DIR__ . '/src/Application/Controller/MailController.php',
	'Application\Controller\MarketingListController' => __DIR__ . '/src/Application/Controller/MarketingListController.php',
	'Application\Controller\OpportunityController' => __DIR__ . '/src/Application/Controller/OpportunityController.php',
	'Application\Controller\PublicController' => __DIR__ . '/src/Application/Controller/PublicController.php',
	'Application\Controller\RegionController' => __DIR__ . '/src/Application/Controller/RegionController.php',
	'Application\Controller\SalesLiteratureController' => __DIR__ . '/src/Application/Controller/SalesLiteratureController.php',
	'Application\Controller\UserController' => __DIR__ . '/src/Application/Controller/UserController.php',
	
	'Application\Model\AbstractAppointment' => __DIR__ . '/src/Application/Model/Activity/Appointment/AbstractAppointment.php',
	'Application\Model\AppointmentState' => __DIR__ . '/src/Application/Model/Activity/Appointment/AppointmentState.php',
	'Application\Model\AppointmentStatus' => __DIR__ . '/src/Application/Model/Activity/Appointment/AppointmentStatus.php',
	'Application\Model\BreakfastAppointment' => __DIR__ . '/src/Application/Model/Activity/Appointment/BreakfastAppointment.php',
	'Application\Model\DinnerAppointment' => __DIR__ . '/src/Application/Model/Activity/Appointment/DinnerAppointment.php',
	'Application\Model\LunchAppointment' => __DIR__ . '/src/Application/Model/Activity/Appointment/LunchAppointment.php',
	'Application\Model\MeetingAppointment' => __DIR__ . '/src/Application/Model/Activity/Appointment/MeetingAppointment.php',
	
	'Application\Model\AbstractInteraction' => __DIR__ . '/src/Application/Model/Activity/Interaction/AbstractInteraction.php',
	'Application\Model\Attachment' => __DIR__ . '/src/Application/Model/Activity/Interaction/Attachment.php',
	'Application\Model\EmailInteraction' => __DIR__ . '/src/Application/Model/Activity/Interaction/EmailInteraction.php',
	'Application\Model\EmailStatus' => __DIR__ . '/src/Application/Model/Activity/Interaction/EmailStatus.php',
	'Application\Model\FaxInteraction' => __DIR__ . '/src/Application/Model/Activity/Interaction/FaxInteraction.php',
	'Application\Model\FaxStatus' => __DIR__ . '/src/Application/Model/Activity/Interaction/FaxStatus.php',
	'Application\Model\LetterInteraction' => __DIR__ . '/src/Application/Model/Activity/Interaction/LetterInteraction.php',
	'Application\Model\LetterStatus' => __DIR__ . '/src/Application/Model/Activity/Interaction/LetterStatus.php',
	'Application\Model\TelephoneInteraction' => __DIR__ . '/src/Application/Model/Activity/Interaction/TelephoneInteraction.php',
	'Application\Model\TelephoneStatus' => __DIR__ . '/src/Application/Model/Activity/Interaction/TelephoneStatus.php',
	'Application\Model\VisitInteraction' => __DIR__ . '/src/Application/Model/Activity/Interaction/VisitInteraction.php',
	
	'Application\Model\AbstractNote' => __DIR__ . '/src/Application/Model/Activity/Note/AbstractNote.php',
	'Application\Model\AccountNote' => __DIR__ . '/src/Application/Model/Activity/Note/AccountNote.php',
	'Application\Model\UserNote' => __DIR__ . '/src/Application/Model/Activity/Note/UserNote.php',
	
	'Application\Model\AbstractTask' => __DIR__ . '/src/Application/Model/Activity/Task/AbstractTask.php',
	'Application\Model\CallbackTask' => __DIR__ . '/src/Application/Model/Activity/Task/CallbackTask.php',
	'Application\Model\FollowUpTask' => __DIR__ . '/src/Application/Model/Activity/Task/FollowUpTask.php',
	'Application\Model\OtherTask' => __DIR__ . '/src/Application/Model/Activity/Task/OtherTask.php',
	'Application\Model\TaskStatus' => __DIR__ . '/src/Application/Model/Activity/Task/TaskStatus.php',
	
	'Application\Model\AbstractActivity' => __DIR__ . '/src/Application/Model/Activity/AbstractActivity.php',
	'Application\Model\ActivityPriority' => __DIR__ . '/src/Application/Model/Activity/ActivityPriority.php',
	'Application\Model\ActivityState' => __DIR__ . '/src/Application/Model/Activity/ActivityState.php',
	'Application\Model\ActivityStatus' => __DIR__ . '/src/Application/Model/Activity/ActivityStatus.php',
	'Application\Model\ActivityType' => __DIR__ . '/src/Application/Model/Activity/ActivityType.php',
	'Application\Model\BulkOperation' => __DIR__ . '/src/Application/Model/Activity/BulkOperation.php',
	'Application\Model\BulkOperationLog' => __DIR__ . '/src/Application/Model/Activity/BulkOperationLog.php',
	'Application\Model\BulkOperationState' => __DIR__ . '/src/Application/Model/Activity/BulkOperationState.php',
	'Application\Model\BulkOperationStatus' => __DIR__ . '/src/Application/Model/Activity/BulkOperationStatus.php',
	'Application\Model\BulkOperationType' => __DIR__ . '/src/Application/Model/Activity/BulkOperationType.php',
	'Application\Model\CampaignActivity' => __DIR__ . '/src/Application/Model/Activity/CampaignActivity.php',
	'Application\Model\CampaignActivityState' => __DIR__ . '/src/Application/Model/Activity/CampaignActivityState.php',
	'Application\Model\CampaignActivityStatus' => __DIR__ . '/src/Application/Model/Activity/CampaignActivityStatus.php',
	'Application\Model\CampaignActivityType' => __DIR__ . '/src/Application/Model/Activity/CampaignActivityType.php',
	'Application\Model\CampaignResponse' => __DIR__ . '/src/Application/Model/Activity/CampaignResponse.php',
	'Application\Model\CampaignResponseStatus' => __DIR__ . '/src/Application/Model/Activity/CampaignResponseStatus.php',
	'Application\Model\ChannelType' => __DIR__ . '/src/Application/Model/Activity/ChannelType.php',
	'Application\Model\CreatedRecordType' => __DIR__ . '/src/Application/Model/Activity/CreatedRecordType.php',
	'Application\Model\OpportunityClose' => __DIR__ . '/src/Application/Model/Activity/OpportunityClose.php',
	'Application\Model\OpportunityCloseState' => __DIR__ . '/src/Application/Model/Activity/OpportunityCloseState.php',
	'Application\Model\OpportunityCloseStatus' => __DIR__ . '/src/Application/Model/Activity/OpportunityCloseStatus.php',
	'Application\Model\ResponseCode' => __DIR__ . '/src/Application/Model/Activity/ResponseCode.php',
	'Application\Model\ScheduledActivity' => __DIR__ . '/src/Application/Model/Activity/ScheduledActivity.php',
	'Application\Model\StatefulActivity' => __DIR__ . '/src/Application/Model/Activity/StatefulActivity.php',
	'Application\Model\TargetedRecordType' => __DIR__ . '/src/Application/Model/Activity/TargetedRecordType.php',
	'Application\Model\TrackedActivity' => __DIR__ . '/src/Application/Model/Activity/TrackedActivity.php',
	
	'Application\Model\AccessMode' => __DIR__ . '/src/Application/Model/AccessMode.php',
	'Application\Model\Account' => __DIR__ . '/src/Application/Model/Account.php',
	'Application\Model\AccountCategory' => __DIR__ . '/src/Application/Model/AccountCategory.php',
	'Application\Model\AccountGroup' => __DIR__ . '/src/Application/Model/AccountGroup.php',
	'Application\Model\AccountSource' => __DIR__ . '/src/Application/Model/AccountSource.php',
	'Application\Model\AccountState' => __DIR__ . '/src/Application/Model/AccountState.php',
	'Application\Model\AccountStatus' => __DIR__ . '/src/Application/Model/AccountStatus.php',
	'Application\Model\AccountType' => __DIR__ . '/src/Application/Model/AccountType.php',
	'Application\Model\Address' => __DIR__ . '/src/Application/Model/Address.php',
	'Application\Model\AddressDTO' => __DIR__ . '/src/Application/Model/AddressDTO.php',
	'Application\Model\AddressType' => __DIR__ . '/src/Application/Model/AddressType.php',
	'Application\Model\Audit' => __DIR__ . '/src/Application/Model/Audit.php',
	'Application\Model\Auditable' => __DIR__ . '/src/Application/Model/Auditable.php',
	'Application\Model\BusinessUnit' => __DIR__ . '/src/Application/Model/BusinessUnit.php',
	'Application\Model\Campaign' => __DIR__ . '/src/Application/Model/Campaign.php',
	'Application\Model\CampaignState' => __DIR__ . '/src/Application/Model/CampaignState.php',
	'Application\Model\CampaignStatus' => __DIR__ . '/src/Application/Model/CampaignStatus.php',
	'Application\Model\CampaignType' => __DIR__ . '/src/Application/Model/CampaignType.php',
	'Application\Model\Contact' => __DIR__ . '/src/Application/Model/Contact.php',
	'Application\Model\ContactSortField' => __DIR__ . '/src/Application/Model/ContactSortField.php',
	'Application\Model\ContactState' => __DIR__ . '/src/Application/Model/ContactState.php',
	'Application\Model\ContactStatus' => __DIR__ . '/src/Application/Model/ContactStatus.php',
	'Application\Model\Direction' => __DIR__ . '/src/Application/Model/Direction.php',
	'Application\Model\Gender' => __DIR__ . '/src/Application/Model/Gender.php',
	'Application\Model\InitialContact' => __DIR__ . '/src/Application/Model/InitialContact.php',
	'Application\Model\Lead' => __DIR__ . '/src/Application/Model/Lead.php',
	'Application\Model\LeadPriority' => __DIR__ . '/src/Application/Model/LeadPriority.php',
	'Application\Model\LeadQuality' => __DIR__ . '/src/Application/Model/LeadQuality.php',
	'Application\Model\LeadState' => __DIR__ . '/src/Application/Model/LeadState.php',
	'Application\Model\LeadStatus' => __DIR__ . '/src/Application/Model/LeadStatus.php',
	'Application\Model\LicenseType' => __DIR__ . '/src/Application/Model/LicenseType.php',
	'Application\Model\ListState' => __DIR__ . '/src/Application/Model/ListState.php',
	'Application\Model\ListStatus' => __DIR__ . '/src/Application/Model/ListStatus.php',
	'Application\Model\LiteratureType' => __DIR__ . '/src/Application/Model/LiteratureType.php',
	'Application\Model\MarketingList' => __DIR__ . '/src/Application/Model/MarketingList.php',
	'Application\Model\Need' => __DIR__ . '/src/Application/Model/Need.php',
	'Application\Model\Opportunity' => __DIR__ . '/src/Application/Model/Opportunity.php',
	'Application\Model\OpportunityPriority' => __DIR__ . '/src/Application/Model/OpportunityPriority.php',
	'Application\Model\OpportunityRating' => __DIR__ . '/src/Application/Model/OpportunityRating.php',
	'Application\Model\OpportunityState' => __DIR__ . '/src/Application/Model/OpportunityState.php',
	'Application\Model\OpportunityStatus' => __DIR__ . '/src/Application/Model/OpportunityStatus.php',
	'Application\Model\OpportunityTimeline' => __DIR__ . '/src/Application/Model/OpportunityTimeline.php',
	'Application\Model\Organization' => __DIR__ . '/src/Application/Model/Organization.php',
	'Application\Model\PurchaseProcess' => __DIR__ . '/src/Application/Model/PurchaseProcess.php',
	'Application\Model\PurchaseTimeframe' => __DIR__ . '/src/Application/Model/PurchaseTimeframe.php',
	'Application\Model\Region' => __DIR__ . '/src/Application/Model/Region.php',
	'Application\Model\SalesLiterature' => __DIR__ . '/src/Application/Model/SalesLiterature.php',
	'Application\Model\SalesLiteratureItem' => __DIR__ . '/src/Application/Model/SalesLiteratureItem.php',
	'Application\Model\SalesStage' => __DIR__ . '/src/Application/Model/SalesStage.php',
	'Application\Model\Salutation' => __DIR__ . '/src/Application/Model/Salutation.php',
	'Application\Model\Schedulable' => __DIR__ . '/src/Application/Model/Schedulable.php',
	'Application\Model\Telephone' => __DIR__ . '/src/Application/Model/Telephone.php',
	'Application\Model\TelephoneType' => __DIR__ . '/src/Application/Model/TelephoneType.php',
	'Application\Model\Trackable' => __DIR__ . '/src/Application/Model/Trackable.php',
	'Application\Model\User' => __DIR__ . '/src/Application/Model/User.php',
	
	'Application\Service\OrderOpenActivitiesRequest' => __DIR__ . '/src/Application/Service/Order/OrderOpenActivitiesRequest.php',
	'Application\Service\OrderClosedActivitiesRequest' => __DIR__ . '/src/Application/Service/Order/OrderClosedActivitiesRequest.php',
	'Application\Service\OrderRequest' => __DIR__ . '/src/Application/Service/Order/OrderRequest.php',
	'Application\Service\OrderResponse' => __DIR__ . '/src/Application/Service/Order/OrderResponse.php',
	'Application\Service\OrderType' => __DIR__ . '/src/Application/Service/Order/OrderType.php',
	
	'Application\Stdlib\Comparable' => __DIR__ . '/src/Application/Stdlib/Comparable.php',
	'Application\Stdlib\Comparator' => __DIR__ . '/src/Application/Stdlib/Comparator.php',
	'Application\Stdlib\Entity' => __DIR__ . '/src/Application/Stdlib/Entity.php',
	'Application\Stdlib\Enum' => __DIR__ . '/src/Application/Stdlib/Enum.php',
	'Application\Stdlib\Object' => __DIR__ . '/src/Application/Stdlib/Object.php'
);
?>