<?php

namespace Snazzware\Entity\Preferences;

/** @MappedSuperclass **/
class GridColumn extends \Snazzware\Entity {

	/**
	 * 
	 * @ManyToOne(targetEntity="Grid", inversedBy="columns")	 
	 */
	protected $grid;
	
	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $name;
	
	/**
	 * @Column(type="string", nullable=true)
	 */
	protected $caption;
	
	/**
	 * @Column(type="integer", nullable=true)
	 */
	protected $display;
	
	/**
	 * @Column(type="integer", nullable=true)
	 */
	protected $export;
	
	/**
	 * @Column(type="integer", nullable=true)
	 */
	protected $rank;
	
	/**
	 * @Column(type="decimal", nullable=true, precision=2, scale=2)
	 */
	protected $width;
	
}


