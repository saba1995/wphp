<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Format.
 *
 * @ORM\Table(name="format")
 * @ORM\Entity(repositoryClass="App\Repository\FormatRepository")
 */
class Format {
    /**
     * @var bool
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="abbreviation", type="string", length=10, nullable=true)
     */
    private $abbreviation;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Collection|Title[]
     * @ORM\OneToMany(targetEntity="Title", mappedBy="format")
     */
    private $titles;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->titles = new ArrayCollection();
    }

    public function __toString() : string {
        return $this->name;
    }

    /**
     * Get id.
     *
     * @return bool
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Format
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set abbrevOne.
     *
     * @param string $abbreviation
     *
     * @return Format
     */
    public function setAbbreviation($abbreviation) {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation.
     *
     * @return string
     */
    public function getAbbreviation() {
        return $this->abbreviation;
    }

    /**
     * Add title.
     *
     * @return Format
     */
    public function addTitle(Title $title) {
        $this->titles[] = $title;

        return $this;
    }

    /**
     * Remove title.
     */
    public function removeTitle(Title $title) : void {
        $this->titles->removeElement($title);
    }

    /**
     * Get titles.
     *
     * @return Collection
     */
    public function getTitles() {
        return $this->titles;
    }

    /**
     * Set description.
     *
     * @param null|string $description
     *
     * @return Format
     */
    public function setDescription($description = null) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return null|string
     */
    public function getDescription() {
        return $this->description;
    }
}
