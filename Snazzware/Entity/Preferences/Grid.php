<?php

namespace Snazzware\Entity\Preferences;

use \Snazzware\Entity\Utils as EntityUtils;

/** @MappedSuperclass **/
class Grid extends \Snazzware\Entity {

	public function __construct() {
		$this->columns = new \Doctrine\Common\Collections\ArrayCollection();			
	}
	
	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $classname;
	
	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $caption;
	
	/**
	 *
	 * @ManyToOne(targetEntity="\Snazzware\Entity\Security\User")
	 */
	protected $owner;	
	
	/**
	 * @OneToMany(targetEntity="GridColumn", mappedBy="grid", cascade={"all"}, orphanRemoval=true)
	 */
	protected $columns;

	
	public function addColumn($column) {
		$column->setGrid($this);
		$this->columns->add($column);
	}
	
	public function deleteColumn($column) {
		$this->columns->removeElement($column);
	}
	
	
}


