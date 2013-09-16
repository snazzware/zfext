<?php

namespace Snazzware\Entity\Preferences;

use \Snazzware\Entity\Utils as EntityUtils;

/** @MappedSuperclass **/
class General extends \Snazzware\Entity {

	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $namespace;
	
	/**
	 * @Column(type="string", name="keyname", nullable=true)
	 */
	protected $key;
	
	/**
	 * @Column(type="text", nullable=true)
	 */
	protected $value;
	
	/**
	 *
	 * @ManyToOne(targetEntity="\Snazzware\Entity\Security\User")
	 */
	protected $owner;	
		
	
	protected function _update($property,$value) {
		// don't log any updates to preferences
	}
}


