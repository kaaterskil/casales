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
 * @version     SVN $Id: AddressDTO.php 13 2013-08-05 22:53:55Z  $
 */
namespace Application\Model;

/**
 * A simple data transfer object to assist in generating view content
 *
 * @package		Application\Model
 * @author		Blair <blair@kaaterskil.com>
 * @copyright	Copyright (c) 2009-2013 Kaaterskil Management, LLC
 * @version		$Id: AddressDTO.php 13 2013-08-05 22:53:55Z  $
 */
class AddressDTO {
	/**
	 * @var String
	 */
	private $street;

	/**
	 * @var String
	 */
	private $city;

	/**
	 * @var String
	 */
	private $region;

	/**
	 * @var String
	 */
	private $postalCode;
	
	/* ---------- Getter/Setters ---------- */

	/**
	 * @return string
	 */
	public function getStreet() {
		return $this->street;
	}

	/**
	 * @param string $street
	 */
	public function setStreet($street) {
		$this->street = (string) $street;
	}

	/**
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * @param string $city
	 */
	public function setCity($city) {
		$this->city = (string) $city;
	}

	/**
	 * @return string
	 */
	public function getRegion() {
		return $this->region;
	}

	/**
	 * @param string $region
	 */
	public function setRegion($region) {
		$this->region = (string) $region;
	}

	/**
	 * @return string
	 */
	public function getPostalCode() {
		return $this->postalCode;
	}

	/**
	 * @param string $postalCode
	 */
	public function setPostalCode($postalCode) {
		$this->postalCode = (string) $postalCode;
	}
	
	/* ---------- Methods ---------- */
	
	/**
	 * @return string
	 */
	public function getShortStreet() {
		$result = $this->getStreet();
		if(strlen($result) > 20) {
			$result = substr($result, 0, 20) . '...';
		}
		return $result;
	}
	
	/**
	 * @return string
	 */
	public function getFullAddress() {
		$value = '';
		$has_value = false;
		if($this->street != '') {
			$value = $this->street;
			$has_value = true;
		}
		if($this->city != '') {
			$value = $has_value ? $value . ', ' : $value;
			$value .= $this->city;
			$has_value = true;
		}
		if($this->region != null) {
			$value = $has_value ? $value . ', ' : $value;
			$value .= $this->region;
			$has_value = true;
		}
		if($this->postalCode != '') {
			$value = $has_value ? $value . ' ' : $value;
			$value .= $this->postalCode;
		}
		return $value;
	}
	
	/**
	 * @return string
	 */
	public function __toString() {
		return 'AddressDTO[street=' . $this->street
		. ',city=' . $this->city
		. ',region=' . $this->region
		. ',postalCode=' . $this->postalCode
		. ']';
	}
}
?>