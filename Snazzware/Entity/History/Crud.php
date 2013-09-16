<?php

namespace Snazzware\Entity\History;

use \Snazzware\Entity\Utils as EntityUtils;

/** @MappedSuperclass **/
class Crud extends \Snazzware\Entity {
	
	/**
	 *
	 * @ManyToOne(targetEntity="\Snazzware\Entity\Security\User")
	 */
	protected $user;
	
	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $ipaddress;
	
	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $username;
	
	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $targetclass;
	
	/**
	 * @Column(type="integer", nullable=true)
	 */
	protected $targetid;
	
	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $description;
	
	/**
	 * @Column(type="text", nullable=true)
	 */
	protected $manifest;
	
	
}


