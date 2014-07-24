
/**
 * Casales Library PHP version 5.4 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
 * CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
 * EVENT SHALL KAATERSKIL MANAGEMENT, LLC BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
 * OF SUCH DAMAGE.
 * 
 * @category Casales
 * @package Application
 * @copyright Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version SVN $Id: casales.js 13 2013-08-05 22:53:55Z  $
 */
var Casales = {
	// Initializes listeners
	init : function() {
		// Provide tabbed content functionality
		$( 'a.tab' ).click( function() {
			$( '.front' ).removeClass( 'front' );
			$( this ).addClass( 'front' );

			$( '.tab-content' ).slideUp();

			var v = $( this ).attr( 'title' );
			$( "#" + v ).slideDown();
		} );
		Casales.setInitialFrontTab();
		Casales.initTinyMCE();

		// Wrap form collections to control overflow
		$( '#telephone-container fieldset' ).children().not( 'legend' ).wrapAll( '<div class="collection-container" />' );
		$( '#address-container fieldset' ).children().not( 'legend' ).wrapAll( '<div class="collection-container" />' );

		// Add telephone method from ZF2
		// http://framework.zend.com/manual/2.2/en/modules/zend.form.collections.html
		$( '#telephoneButton' ).click( function() {
			var c = $( '#telephone-container > fieldset > .collection-container > .collection-label' ).length;
			var t = $( '#telephone-container > fieldset > .collection-container > span' ).data( 'template' );
			t = t.replace( /__telephone__/g, c );
			$( '#telephone-container > fieldset > .collection-container' ).append( t );
			return false;
		} );

		// Add address method from ZF2
		// http://framework.zend.com/manual/2.2/en/modules/zend.form.collections.html
		$( '#addressButton' ).click( function() {
			var c = $( '#address-container > fieldset > .collection-container > .collection-label' ).length;
			var t = $( '#address-container > fieldset > .collection-container > span' ).data( 'template' );
			t = t.replace( /__address__/g, c );
			$( '#address-container > fieldset > .collection-container' ).append( t );
			return false;
		} );

		// Email form submit button text toggle
		$( '#issueSend' ).change( function() {
			if ($( this ).prop( 'checked' )) {
				$( 'input[name="submit_btn"]' ).val( 'Save' );
			} else {
				$( 'input[name="submit_btn"]' ).val( 'Send And Save' );
			}
		} );
		// Distribute Campaign Activity Email form submit button text toggle
		$( '#issueSendDistribute' ).change( function() {
			if ($( this ).prop( 'checked' )) {
				$( 'input[name="submit_btn"]' ).val( 'Distribute' );
			} else {
				$( 'input[name="submit_btn"]' ).val( 'Distribute and Send' );
			}
		} );

		// Switches the From/Sender and To/Recipient names depending on the Direction
		$( 'select.activityDirection' ).change( function() {
			$( 'select.activityDirection option:selected' ).each( function() {
				var v1 = $( 'input[name$="from]"]' ).val();
				var v2 = $( 'input[name$="to]"]' ).val();
				$( 'input[name$="from]"]' ).val( v2 );
				$( 'input[name$="to]"]' ).val( v1 );
				if ($( 'input[name^="saveAndClose"]' ).val() == 'Send And Save') {
					$( 'input[name^="saveAndClose"]' ).val( 'Save' );
					$( 'input[name^="saveAndClose"]' ).attr( 'title', 'Save' );
				} else {
					$( 'input[name^="saveAndClose"]' ).val( 'Send And Save' );
					$( 'input[name^="saveAndClose"]' ).attr( 'title', 'Send And Save' );
				}
			} );
		} );

		// Close Activity form
		$( '#openCloseActivityForm' ).click( function() {
			$( '#close-activity-container' ).removeClass( 'hidden' );
		} );
		$( '#closeCloseActivityButton' ).click( function() {
			$( '#close-activity-container' ).addClass( 'hidden' );
		} );

		// Distribute CapaignActivity form
		$( '#openDistributeActivityButton' ).click( function() {
			$( '#distribute-activity-container' ).removeClass( 'hidden' );
		} );
		$( '#closeDistributeActivityButton' ).click( function() {
			$( '#distribute-activity-container' ).addClass( 'hidden' );
		} );

		// Lead qualification form
		$( '#openQualifyLeadButton' ).click( function() {
			$( '#qualify-lead-container' ).removeClass( 'hidden' );
			Casales.rearrangeLeadQualificationItems();
			Casales.setLeadQualificationItems();
		} );
		$( '#closeQualifyLeadButton' ).click( function() {
			$( '#qualify-lead-container' ).addClass( 'hidden' );
		} );

		// WinOpportunity form
		$( '#openWinOpportunityButton' ).click( function() {
			$( '#win-opportunity-container' ).removeClass( 'hidden' );
		} );
		$( '#closeWinOpportunityButton' ).click( function() {
			$( '#win-opportunity-container' ).addClass( 'hidden' );
		} );

		// LoseOpportunity form
		$( '#openLoseOpportunityButton' ).click( function() {
			$( '#lose-opportunity-container' ).removeClass( 'hidden' );
		} );
		$( '#closeLoseOpportunityButton' ).click( function() {
			$( '#lose-opportunity-container' ).addClass( 'hidden' );
		} );

		// Mailbox
		$( 'tr.incoming-email' ).click( function() {
			// Highlight the selected row
			$( 'tr.incoming-email' ).removeClass( 'highlight' );
			$( this ).addClass( 'highlight' );

			// Display the email
			$( '.mail-item-outer-container' ).removeClass( 'hidden' ).addClass( 'hidden' );
			var id = $( this ).attr( 'data-id' );
			$( '#mail-item-' + id ).removeClass( 'hidden' );
		} );

		// Marketing List/Campaign Query forms
		$( 'form input:checkbox[name^="selected"]' ).click( function() {
			if ($( this ).is( ':checked' )) {
				$( this ).parents( 'tr' ).addClass( 'highlight' );
			} else {
				$( this ).parents( 'tr' ).removeClass( 'highlight' );
			}
		} );
		$( '#select-all-remove' ).click( function() {
			if ($( this ).text() == 'Select All') {
				$( 'form[name="member-index-form"] input[type="checkbox"]' ).prop( 'checked', true );
				$( 'form[name="campaign-index-form"] input[type="checkbox"]' ).prop( 'checked', true );
				$( 'form[name="campaignActivity-index-form"] input[type="checkbox"]' ).prop( 'checked', true );
				$( this ).text( 'Unselect All' );
			} else {
				$( 'form[name="member-index-form"] input[type="checkbox"]' ).prop( 'checked', false );
				$( 'form[name="campaign-index-form"] input[type="checkbox"]' ).prop( 'checked', false );
				$( 'form[name="campaignActivity-index-form"] input[type="checkbox"]' ).prop( 'checked', false );
				$( this ).text( 'Select All' );
			}
		} );
		$( '#select-all-add' ).click( function() {
			if ($( this ).text() == 'Select All') {
				$( 'form[name="member-query-index-form"] input[type="checkbox"]' ).prop( 'checked', true );
				$( 'form[name="campaign-query-index-form"] input[type="checkbox"]' ).prop( 'checked', true );
				$( 'form[name="campaignActivity-query-index-form"] input[type="checkbox"]' ).prop( 'checked', true );
				$( this ).text( 'Unselect All' );
			} else {
				$( 'form[name="member-query-index-form"] input[type="checkbox"]' ).prop( 'checked', false );
				$( 'form[name="campaign-query-index-form"] input[type="checkbox"]' ).prop( 'checked', false );
				$( 'form[name="campaignActivity-query-index-form"] input[type="checkbox"]' ).prop( 'checked', false );
				$( this ).text( 'Select All' );
			}
		} );

		// Sales Literature downloads
		$( '#file-attachment' ).click( function() {
			var id = $( this ).attr( 'data-id' );
			location.pathname = '/ajax/downloadFile/' + id;
		} );

		// Email attachment downloads
		$( '.email-attachment' ).click( function() {
			var id = $( this ).attr( 'data-id' );
			location.pathname = '/ajax/downloadAttachment/' + id;
		} );
	},

	initTinyMCE : function() {
		var id = $( "#email_body" ).attr( "id" );
		if (id) {
			tinyMCE.EditorManager.init( {
				browser_spellcheck : true,
				selector : "#email_body",
				setup : function(editor) {
					editor.on( "LoadContent", function(e) {
						var ed = tinymce.get( "email_body" );
						ed.focus();
						// var body = ed.getContentAreaContainer();
						// $(body).find('p').first().focus();

					} );
				},
				skin : 'light',
			} );
		}
	},

	// Places form elements within the two radio buttons
	rearrangeLeadQualificationItems : function() {
		var qi = $( '#qualify-lead-items' );
		var di = $( '#disqualify-lead-items' );
		var p = $( '#qualify-lead > fieldset > label' );
		$( p[0] ).append( qi );
		$( p[1] ).append( di );
	},

	setLeadQualificationItems : function() {
		// Set initial values
		$( "select[name='qualifyStatus']" ).prop( 'checked', 'checked' );
		$( "select[name='disqualifyStatus']" ).prop( 'disabled', true );

		// Set event handler
		$( "input[name='qualify']" ).click( function() {
			if ($( this ).val() == 'Qualified') {
				$( "select[name='qualifyStatus']" ).prop( 'disabled', false );
				$( "input[name='createAccount']" ).prop( 'disabled', false );
				$( "input[name='createContact']" ).prop( 'disabled', false );
				$( "input[name='createOpportunity']" ).prop( 'disabled', false );
				$( "select[name='disqualifyStatus']" ).prop( 'disabled', true );
			} else {
				$( "select[name='qualifyStatus']" ).prop( 'disabled', true );
				$( "input[name='createAccount']" ).prop( 'disabled', true );
				$( "input[name='createContact']" ).prop( 'disabled', true );
				$( "input[name='createOpportunity']" ).prop( 'disabled', true );
				$( "select[name='disqualifyStatus']" ).prop( 'disabled', false );
			}
		} );
	},

	setInitialFrontTab : function() {
		var id = $( 'a.front' ).attr( 'id' );
		if (id) {
			var t = id.slice( -1 );
			for ( var i = 1; i < 10; i++) {
				if (i == t) {
					$( '#tab-content-' + i ).css( 'display', 'block' );
				} else {
					$( '#tab-content-' + i ).css( 'display', 'none' );
				}
			}
		}
	},
};

// Load...
$( document ).ready( function() {
	Casales.init();
} );