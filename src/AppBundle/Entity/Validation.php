<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Validation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ValidationRepository")
 */
class Validation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validate_at", type="datetime")
     */
    private $validateAt;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set validateAt
     *
     * @param \DateTime $validateAt
     *
     * @return Validation
     */
    public function setValidateAt($validateAt)
    {
        $this->validateAt = $validateAt;

        return $this;
    }

    /**
     * Get validateAt
     *
     * @return \DateTime
     */
    public function getValidateAt()
    {
        return $this->validateAt;
    }
}

